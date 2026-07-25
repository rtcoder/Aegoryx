<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System\BillingEvent;
use App\Modules\Billing\Actions\SyncSubscriptionFromProviderEventAction;
use App\Modules\Billing\Enums\BillingEventStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BillingEventController extends Controller
{
    public function show(BillingEvent $event): View
    {
        return view('admin.billing.show', [
            'event' => $event->load(['subscription', 'tenant']),
        ]);
    }

    public function retry(Request $request, BillingEvent $event, SyncSubscriptionFromProviderEventAction $syncSubscription): RedirectResponse
    {
        if ($event->status !== BillingEventStatus::Failed) {
            return redirect()
                ->route('admin.billing.events.show', $event)
                ->with('error', __('admin.billing.retry_only_failed'));
        }

        $syncSubscription->retry(
            event: $event,
            actorId: $request->user('admin')?->id,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()
            ->route('admin.billing.events.show', $event)
            ->with('success', __('admin.billing.retry_succeeded'));
    }
}
