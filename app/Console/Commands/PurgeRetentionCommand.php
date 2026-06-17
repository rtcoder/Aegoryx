<?php

namespace App\Console\Commands;

use App\Models\Landlord\AuditLog;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\ActivityEntry;
use App\Models\Tenant\TenantFile;
use App\Modules\Audit\Support\RetentionPolicy;
use App\Services\Tenancy\TenancyManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class PurgeRetentionCommand extends Command
{
    protected $signature = 'aegoryx:retention:purge
        {--dry-run : Report records that would be purged without deleting them}';

    protected $description = 'Purge tenant and landlord data that exceeded configured retention windows.';

    public function __construct(
        private readonly RetentionPolicy $retention,
        private readonly TenancyManager $tenancy,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $auditLogs = $this->purgeLandlordAuditLogs($dryRun);
        $activityEntries = 0;
        $expiredFiles = 0;
        $deletedFiles = 0;

        Tenant::query()
            ->where('status', '!=', 'deleted')
            ->orderBy('id')
            ->each(function (Tenant $tenant) use ($dryRun, &$activityEntries, &$expiredFiles, &$deletedFiles): void {
                $this->tenancy->initialize($tenant);

                try {
                    $activityEntries += $this->purgeActivityEntries($dryRun);
                    $expiredFiles += $this->purgeExpiredFiles($dryRun);
                    $deletedFiles += $this->purgeDeletedFiles($dryRun);
                } finally {
                    $this->tenancy->end();
                }
            });

        $this->components->info(sprintf(
            'Retention purge %s: audit_logs=%d, activity_entries=%d, expired_files=%d, deleted_files=%d',
            $dryRun ? 'dry-run' : 'completed',
            $auditLogs,
            $activityEntries,
            $expiredFiles,
            $deletedFiles,
        ));

        return self::SUCCESS;
    }

    private function purgeLandlordAuditLogs(bool $dryRun): int
    {
        $query = AuditLog::query()
            ->where('created_at', '<', $this->retention->cutoffForDays($this->retention->auditLogsDays()));

        $count = (int) $query->count();

        if (! $dryRun) {
            $query->delete();
        }

        return $count;
    }

    private function purgeActivityEntries(bool $dryRun): int
    {
        $query = ActivityEntry::query()
            ->where('created_at', '<', $this->retention->cutoffForDays($this->retention->activityEntriesDays()));

        $count = (int) $query->count();

        if (! $dryRun) {
            $query->delete();
        }

        return $count;
    }

    private function purgeExpiredFiles(bool $dryRun): int
    {
        return $this->purgeFiles(
            TenantFile::query()
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now()),
            $dryRun,
        );
    }

    private function purgeDeletedFiles(bool $dryRun): int
    {
        return $this->purgeFiles(
            TenantFile::onlyTrashed()
                ->where('deleted_at', '<', $this->retention->cutoffForDays($this->retention->deletedFilesDays())),
            $dryRun,
        );
    }

    private function purgeFiles($query, bool $dryRun): int
    {
        $files = $query->get();

        if (! $dryRun) {
            $files->each(function (TenantFile $file): void {
                Storage::disk($file->disk)->delete($file->path);
                $file->forceDelete();
            });
        }

        return $files->count();
    }
}
