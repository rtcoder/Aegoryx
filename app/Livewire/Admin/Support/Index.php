<?php

namespace App\Livewire\Admin\Support;

use App\Models\System\SupportSession;
use App\Models\System\Tenant;
use App\Modules\AdminConsole\Actions\Support\EndSupportSessionAction;
use App\Modules\AdminConsole\Actions\Support\StartSupportSessionAction;
use App\Modules\AdminConsole\Enums\SupportSessionStatus;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public ?int $tenantId = null;

    public string $reason = '';

    public int $durationMinutes = 30;

    public function mount(): void
    {
        $this->tenantId = Tenant::query()->orderBy('name')->value('id');
        $this->expireCurrentSessionIfNeeded();
    }

    public function start(StartSupportSessionAction $action): void
    {
        $this->validate([
            'tenantId' => ['required', 'integer', Rule::exists('tenants', 'id')],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'durationMinutes' => ['required', 'integer', 'min:5', 'max:240'],
        ]);

        $supportSession = $action->handle(
            tenant: Tenant::query()->findOrFail($this->tenantId),
            actor: auth('admin')->user(),
            reason: $this->reason,
            durationMinutes: $this->durationMinutes,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session([
            'admin_support_session_id' => $supportSession->id,
            'admin_support_tenant_id' => $supportSession->tenant_id,
            'admin_support_expires_at' => $supportSession->expires_at->toISOString(),
        ]);

        $this->reason = '';

        session()->flash('success', __('flash.support_session_started'));
    }

    public function end(EndSupportSessionAction $action): void
    {
        $supportSession = $this->currentSupportSession();

        if (! $supportSession) {
            session()->forget([
                'admin_support_session_id',
                'admin_support_tenant_id',
                'admin_support_expires_at',
            ]);

            return;
        }

        $action->handle(
            supportSession: $supportSession,
            actor: auth('admin')->user(),
            status: SupportSessionStatus::Ended->value,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->forget([
            'admin_support_session_id',
            'admin_support_tenant_id',
            'admin_support_expires_at',
        ]);

        session()->flash('success', __('flash.support_session_ended'));
    }

    public function render()
    {
        $this->expireCurrentSessionIfNeeded();

        return view('livewire.admin.support.index', [
            'currentSupportSession' => $this->currentSupportSession(),
            'supportSessions' => SupportSession::query()
                ->with(['tenant', 'actor'])
                ->latest()
                ->paginate(20),
            'tenants' => Tenant::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    private function currentSupportSession(): ?SupportSession
    {
        $id = session('admin_support_session_id');

        if (! $id) {
            return null;
        }

        return SupportSession::query()
            ->with('tenant')
            ->whereKey($id)
            ->where('actor_id', auth('admin')->id())
            ->where('status', SupportSessionStatus::Active->value)
            ->first();
    }

    private function expireCurrentSessionIfNeeded(): void
    {
        $supportSession = $this->currentSupportSession();

        if (! $supportSession || $supportSession->expires_at->isFuture()) {
            return;
        }

        app(EndSupportSessionAction::class)->handle(
            supportSession: $supportSession,
            actor: auth('admin')->user(),
            status: SupportSessionStatus::Expired->value,
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        );

        session()->forget([
            'admin_support_session_id',
            'admin_support_tenant_id',
            'admin_support_expires_at',
        ]);
    }
}
