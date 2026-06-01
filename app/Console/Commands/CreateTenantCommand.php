<?php

namespace App\Console\Commands;

use App\Modules\Tenancy\Actions\CreateTenantAction;
use App\Support\Localization\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

final class CreateTenantCommand extends Command
{
    protected $signature = 'tenants:create
        {name : Tenant display name}
        {--slug= : Optional URL-safe slug}
        {--locale=pl : Tenant default locale}
        {--skip-migrations : Create only the landlord row and schema}';

    protected $description = 'Create a tenant with a safe schema name and optional tenant migrations.';

    public function handle(CreateTenantAction $createTenant): int
    {
        $payload = [
            'name' => (string) $this->argument('name'),
            'slug' => $this->option('slug'),
            'locale' => $this->option('locale'),
        ];

        $validator = Validator::make($payload, [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash:ascii'],
            'locale' => ['required', 'string', 'in:'.implode(',', Locale::values())],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $tenant = $createTenant->handle(
            name: $payload['name'],
            slug: is_string($payload['slug']) && $payload['slug'] !== '' ? $payload['slug'] : null,
            locale: Locale::from((string) $payload['locale']),
            migrate: ! (bool) $this->option('skip-migrations'),
        );

        $this->info(__('tenancy.tenant_created', [
            'id' => $tenant->id,
            'slug' => $tenant->slug,
            'schema' => $tenant->schema_name,
        ]));

        return self::SUCCESS;
    }
}
