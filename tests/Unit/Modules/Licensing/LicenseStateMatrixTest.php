<?php

namespace Tests\Unit\Modules\Licensing;

use App\Modules\Licensing\Enums\LicenseStatus;
use App\Modules\Licensing\Support\LicenseStateMatrix;
use Tests\TestCase;

final class LicenseStateMatrixTest extends TestCase
{
    public function test_expired_and_suspended_licenses_block_access_but_keep_data(): void
    {
        $matrix = app(LicenseStateMatrix::class);

        foreach ([LicenseStatus::Expired, LicenseStatus::Suspended] as $state) {
            $decision = $matrix->state($state);

            $this->assertFalse($decision['allows_access']);
            $this->assertTrue($decision['keeps_data']);
        }
    }

    public function test_perpetual_license_does_not_require_saas_subscription(): void
    {
        $decision = app(LicenseStateMatrix::class)->state('perpetual');

        $this->assertTrue($decision['allows_access']);
        $this->assertTrue($decision['keeps_data']);
        $this->assertFalse($decision['requires_subscription']);
    }
}
