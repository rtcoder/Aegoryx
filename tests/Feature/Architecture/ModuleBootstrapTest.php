<?php

namespace Tests\Feature\Architecture;

use App\Services\Tenancy\PostgresSchemaTenancyManager;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
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
        $this->assertContains('tenant-domains:verify', $commands);
        $this->assertContains('aegoryx:preflight', $commands);
        $this->assertContains('aegoryx:smoke', $commands);
        $this->assertContains('aegoryx:launch-check', $commands);
        $this->assertContains('aegoryx:retention:purge', $commands);
    }

    public function test_preflight_command_passes_for_configured_application(): void
    {
        $exitCode = Artisan::call('aegoryx:preflight', [
            '--skip-db' => true,
        ]);

        $this->assertSame(0, $exitCode);
    }

    public function test_preflight_command_fails_when_storage_disk_is_missing(): void
    {
        config(['filesystems.default' => 'missing-disk']);

        $exitCode = Artisan::call('aegoryx:preflight', [
            '--skip-db' => true,
        ]);

        $this->assertSame(1, $exitCode);
    }

    public function test_preflight_command_fails_when_queue_connection_is_missing(): void
    {
        config(['queue.default' => 'missing-queue']);

        $exitCode = Artisan::call('aegoryx:preflight', [
            '--skip-db' => true,
        ]);

        $this->assertSame(1, $exitCode);
    }

    public function test_smoke_command_runs_configured_http_checks(): void
    {
        Http::fake([
            'http://aegoryx.test/up' => Http::response('OK'),
            'http://admin.aegoryx.test/login' => Http::response('<html></html>'),
            'http://acme.aegoryx.test/panel' => Http::response('<html></html>'),
            'http://acme.aegoryx.test/api/public/v1/cms/pages/home' => Http::response(['data' => []]),
        ]);
        config([
            'app.url' => 'http://aegoryx.test',
            'aegoryx.smoke.tenant_url' => 'http://acme.aegoryx.test/panel',
            'aegoryx.smoke.public_api_url' => 'http://acme.aegoryx.test/api/public/v1/cms/pages/home',
        ]);

        $exitCode = Artisan::call('aegoryx:smoke');

        $this->assertSame(0, $exitCode);
        Http::assertSentCount(4);
    }
}
