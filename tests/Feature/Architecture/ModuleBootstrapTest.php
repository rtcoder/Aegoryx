<?php

namespace Tests\Feature\Architecture;

use App\Services\Tenancy\PostgresSchemaTenancyManager;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class ModuleBootstrapTest extends TestCase
{
    public function test_enabled_modules_have_registered_providers(): void
    {
        $modules = config('aegoryx.modules');

        $this->assertIsArray($modules);
        $this->assertArrayHasKey('tenancy', $modules);
        $this->assertArrayHasKey('crm', $modules);
        $this->assertArrayHasKey('cms', $modules);
        $this->assertArrayHasKey('public-api', $modules);

        foreach ($modules as $module) {
            $this->assertTrue($module['enabled']);
            $this->assertTrue(class_exists($module['provider']));
        }
    }

    public function test_tenancy_services_are_bound(): void
    {
        $this->assertInstanceOf(
            PostgresSchemaTenancyManager::class,
            $this->app->make(TenancyManager::class),
        );
    }

    public function test_tenant_aware_migration_commands_are_registered(): void
    {
        $commands = array_keys(Artisan::all());

        $this->assertContains('tenants:create', $commands);
        $this->assertContains('landlord:migrate', $commands);
        $this->assertContains('tenants:migrate', $commands);
        $this->assertContains('tenant:migrate', $commands);
    }
}
