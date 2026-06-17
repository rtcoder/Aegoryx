<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\License;
use App\Models\Landlord\Subscription;
use Illuminate\View\View;

final class SectionController extends Controller
{
    public function tenants(): View
    {
        return $this->section(__('common.tenants'), __('landlord.sections.tenants'));
    }

    public function licenses(): View
    {
        return $this->section(__('common.licenses'), __('landlord.sections.licenses'));
    }

    public function billing(): View
    {
        return view('landlord.billing.index', [
            'billingEvents' => BillingEvent::query()
                ->with('tenant')
                ->latest()
                ->limit(10)
                ->get(),
            'subscriptionStatusCounts' => Subscription::query()
                ->selectRaw('status, count(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->all(),
            'licenseStatusCounts' => License::query()
                ->selectRaw('status, count(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->all(),
        ]);
    }

    public function support(): View
    {
        return view('landlord.support.index');
    }

    private function section(string $title, string $description): View
    {
        return view('landlord.section', [
            'title' => $title,
            'description' => $description,
        ]);
    }
}
