<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Throwable;

final class SmokeCommand extends Command
{
    protected $signature = 'aegoryx:smoke
        {--app-url= : Base application URL to check}
        {--landlord-url= : Landlord login URL to check}
        {--tenant-url= : Optional tenant panel URL to check}
        {--public-api-url= : Optional public API URL to check}
        {--timeout=5 : HTTP timeout in seconds}';

    protected $description = 'Run post-deploy smoke checks for the Aegoryx application.';

    public function handle(): int
    {
        $failed = false;

        if (Artisan::call('aegoryx:preflight') === self::SUCCESS) {
            $this->components->info('Preflight passed');
        } else {
            $failed = true;
            $this->components->error('Preflight failed');
        }

        foreach ($this->checks() as $label => $url) {
            if ($url === null || $url === '') {
                $this->components->warn("{$label} skipped");

                continue;
            }

            if ($this->urlIsReachable($url)) {
                $this->components->info("{$label} reachable");

                continue;
            }

            $failed = true;
            $this->components->error("{$label} failed");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<string, string|null>
     */
    private function checks(): array
    {
        $appUrl = rtrim($this->optionString('app-url') ?: (string) config('app.url'), '/');
        $landlordDomain = (string) config('aegoryx.landlord.domain');

        return [
            'Application health' => $appUrl.'/up',
            'Landlord login' => $this->optionString('landlord-url') ?: "http://{$landlordDomain}/login",
            'Tenant route' => $this->optionString('tenant-url') ?: config('aegoryx.smoke.tenant_url'),
            'Public API' => $this->optionString('public-api-url') ?: config('aegoryx.smoke.public_api_url'),
        ];
    }

    private function urlIsReachable(string $url): bool
    {
        try {
            $response = Http::timeout((int) $this->option('timeout'))->get($url);

            return $response->status() < 500;
        } catch (Throwable) {
            return false;
        }
    }

    private function optionString(string $key): ?string
    {
        $value = $this->option($key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
