<?php

namespace App\Console\Commands;

use App\Support\Localization\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Throwable;

final class PreflightCommand extends Command
{
    protected $signature = 'aegoryx:preflight
        {--skip-db : Skip database connectivity check}';

    protected $description = 'Run deployment preflight checks for the Aegoryx application.';

    public function handle(): int
    {
        $checks = [
            'Application key configured' => $this->hasAppKey(),
            'Landlord domain configured' => $this->hasLandlordDomain(),
            'Supported locales match Locale enum' => $this->localesMatchEnum(),
            'All configured modules are enabled and loadable' => $this->modulesAreLoadable(),
            'Required operational commands are registered' => $this->commandsAreRegistered(),
            'Default storage disk is configured' => $this->storageDiskIsConfigured(),
            'Default queue connection is configured' => $this->queueConnectionIsConfigured(),
        ];

        if (! $this->option('skip-db')) {
            $checks['Database connection is reachable'] = $this->databaseIsReachable();
        }

        $failed = false;

        foreach ($checks as $label => $passed) {
            if ($passed) {
                $this->components->info($label);

                continue;
            }

            $failed = true;
            $this->components->error($label);
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function hasAppKey(): bool
    {
        return is_string(config('app.key')) && config('app.key') !== '';
    }

    private function hasLandlordDomain(): bool
    {
        return is_string(config('aegoryx.landlord.domain')) && config('aegoryx.landlord.domain') !== '';
    }

    private function localesMatchEnum(): bool
    {
        $configured = config('aegoryx.localization.supported_locales');

        if (! is_array($configured)) {
            return false;
        }

        sort($configured);

        $enumValues = Locale::values();
        sort($enumValues);

        return $configured === $enumValues;
    }

    private function modulesAreLoadable(): bool
    {
        $modules = config('aegoryx.modules');

        if (! is_array($modules) || $modules === []) {
            return false;
        }

        foreach ($modules as $module) {
            if (($module['enabled'] ?? false) !== true) {
                return false;
            }

            $provider = $module['provider'] ?? null;

            if (! is_string($provider) || ! class_exists($provider)) {
                return false;
            }
        }

        return true;
    }

    private function commandsAreRegistered(): bool
    {
        $commands = array_keys(Artisan::all());

        foreach (['landlord:create', 'landlord:migrate', 'tenants:create', 'tenants:migrate', 'tenant:migrate', 'tenant-domains:verify'] as $command) {
            if (! in_array($command, $commands, true)) {
                return false;
            }
        }

        return true;
    }

    private function storageDiskIsConfigured(): bool
    {
        $defaultDisk = config('filesystems.default');

        return is_string($defaultDisk)
            && $defaultDisk !== ''
            && is_array(config("filesystems.disks.{$defaultDisk}"))
            && is_string(config("filesystems.disks.{$defaultDisk}.driver"));
    }

    private function queueConnectionIsConfigured(): bool
    {
        $defaultConnection = config('queue.default');

        return is_string($defaultConnection)
            && $defaultConnection !== ''
            && is_array(config("queue.connections.{$defaultConnection}"))
            && is_string(config("queue.connections.{$defaultConnection}.driver"));
    }

    private function databaseIsReachable(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
