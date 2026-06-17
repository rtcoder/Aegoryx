#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

MAINTENANCE_STARTED="false"
ROLLBACK_STARTED="false"
declare -a APPLIED_MIGRATIONS=()

cd "$ROOT_DIR"
export ROOT_DIR

artisan() {
    "$PHP_BIN" artisan "$@"
}

log() {
    printf '\n[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

strip_ansi() {
    perl -pe 's/\e\[[0-9;]*[A-Za-z]//g'
}

record_successful_migrations() {
    local scope="$1"
    local rollback_scope="$2"
    local output_file="$3"
    local current_schema="public"
    local clean_line
    local migration
    local schema

    while IFS= read -r line; do
        clean_line="$(printf '%s' "$line" | strip_ansi)"

        if [[ "$scope" == "tenant" && "$clean_line" =~ schema[[:space:]]\[([^]]+)\] ]]; then
            current_schema="${BASH_REMATCH[1]}"
        fi

        if [[ "$clean_line" =~ ([0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_[A-Za-z0-9_]+)[[:space:]]+\.\..*DONE ]]; then
            migration="${BASH_REMATCH[1]}"

            if [[ "$scope" == "tenant" ]]; then
                schema="$current_schema"
            else
                schema="public"
            fi

            APPLIED_MIGRATIONS+=("${schema}|${rollback_scope}|${migration}")
        fi
    done < "$output_file"
}

run_migration_command() {
    local scope="$1"
    local rollback_scope="$2"
    shift 2

    local output_file
    local exit_code

    output_file="$(mktemp)"

    set +e
    "$@" 2>&1 | tee "$output_file"
    exit_code="${PIPESTATUS[0]}"
    set -e

    record_successful_migrations "$scope" "$rollback_scope" "$output_file"
    rm -f "$output_file"

    return "$exit_code"
}

rollback_migration() {
    local schema="$1"
    local scope="$2"
    local migration="$3"

    log "Rolling back ${schema}.${migration}."

    if [[ "$scope" == "landlord" ]]; then
        artisan landlord:migrate:rollback --migration="$migration" --force
    else
        artisan tenants:migrate:rollback --schema="$schema" --migration="$migration" --force
    fi
}

rollback_applied_migrations() {
    if [[ "$ROLLBACK_STARTED" == "true" ]]; then
        return
    fi

    ROLLBACK_STARTED="true"

    if [[ "${#APPLIED_MIGRATIONS[@]}" -eq 0 ]]; then
        log "No migrations from this deploy were applied. Skipping migration rollback."
        return
    fi

    log "Rolling back migrations applied during this deploy."

    local index
    local entry
    local schema
    local scope
    local migration
    local rollback_failed="false"

    for ((index=${#APPLIED_MIGRATIONS[@]}-1; index>=0; index--)); do
        entry="${APPLIED_MIGRATIONS[$index]}"
        IFS='|' read -r schema scope migration <<< "$entry"

        if ! rollback_migration "$schema" "$scope" "$migration"; then
            rollback_failed="true"
            log "Rollback failed for ${schema}.${migration}. Continuing with remaining rollback attempts."
        fi
    done

    if [[ "$rollback_failed" == "true" ]]; then
        log "One or more migration rollbacks failed. Manual database review is required."
    fi
}

on_exit() {
    local exit_code=$?

    if [[ "$exit_code" -ne 0 ]]; then
        rollback_applied_migrations

        if [[ "$MAINTENANCE_STARTED" == "true" ]]; then
            log "Deploy failed. Bringing application back up."
            artisan up || true
        fi
    fi

    exit "$exit_code"
}

trap on_exit EXIT

if [[ ! -f artisan ]]; then
    printf 'This script must be run from a Laravel project root or from scripts/deploy.sh.\n' >&2
    exit 1
fi

log "Starting Aegoryx deploy."

log "Installing Composer dependencies."
"$COMPOSER_BIN" install --no-dev --prefer-dist --no-interaction --optimize-autoloader

log "Installing Node dependencies."
"$NPM_BIN" ci

log "Building frontend assets."
"$NPM_BIN" run build

log "Putting application into maintenance mode."
artisan down
MAINTENANCE_STARTED="true"

log "Clearing stale optimized files."
artisan optimize:clear

log "Running landlord migrations."
run_migration_command \
    landlord \
    landlord \
    "$PHP_BIN" artisan landlord:migrate --force

log "Running tenant migrations."
run_migration_command \
    tenant \
    tenant \
    "$PHP_BIN" artisan tenants:migrate --force

log "Caching optimized framework files."
artisan optimize

log "Restarting Horizon workers."
artisan horizon:terminate || true

log "Bringing application back up."
artisan up
MAINTENANCE_STARTED="false"

log "Aegoryx deploy finished successfully."
