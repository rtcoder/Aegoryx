<?php

namespace App\Modules\Audit\Support;

use Carbon\CarbonImmutable;

final readonly class RetentionPolicy
{
    public function activityEntriesDays(): int
    {
        return (int) config('aegoryx.retention.activity_entries_days', 365);
    }

    public function auditLogsDays(): int
    {
        return (int) config('aegoryx.retention.audit_logs_days', 730);
    }

    public function deletedFilesDays(): int
    {
        return (int) config('aegoryx.retention.deleted_files_days', 30);
    }

    public function exportFilesExpireHours(): int
    {
        return (int) config('aegoryx.retention.export_files_expire_hours', 24);
    }

    public function exportFileExpiresAt(): CarbonImmutable
    {
        return CarbonImmutable::now()->addHours($this->exportFilesExpireHours());
    }

    public function cutoffForDays(int $days): CarbonImmutable
    {
        return CarbonImmutable::now()->subDays($days);
    }
}
