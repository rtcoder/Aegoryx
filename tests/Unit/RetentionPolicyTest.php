<?php

namespace Tests\Unit;

use App\Modules\Audit\Support\RetentionPolicy;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class RetentionPolicyTest extends TestCase
{
    public function test_retention_policy_reads_configured_windows(): void
    {
        config()->set('aegoryx.retention.activity_entries_days', 90);
        config()->set('aegoryx.retention.audit_logs_days', 180);
        config()->set('aegoryx.retention.deleted_files_days', 14);
        config()->set('aegoryx.retention.export_files_expire_hours', 6);
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-17 12:00:00'));

        $policy = app(RetentionPolicy::class);

        $this->assertSame(90, $policy->activityEntriesDays());
        $this->assertSame(180, $policy->auditLogsDays());
        $this->assertSame(14, $policy->deletedFilesDays());
        $this->assertSame(6, $policy->exportFilesExpireHours());
        $this->assertTrue($policy->exportFileExpiresAt()->equalTo(CarbonImmutable::parse('2026-06-17 18:00:00')));
        $this->assertTrue($policy->cutoffForDays(10)->equalTo(CarbonImmutable::parse('2026-06-07 12:00:00')));

        CarbonImmutable::setTestNow();
    }
}
