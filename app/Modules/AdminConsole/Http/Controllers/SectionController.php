<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System\BillingEvent;
use App\Models\System\License;
use App\Models\System\Subscription;
use Illuminate\View\View;

final class SectionController extends Controller
{
    public function tenants(): View
    {
        return $this->section(__('common.tenants'), __('admin.sections.tenants'));
    }

    public function licenses(): View
    {
        return $this->section(__('common.licenses'), __('admin.sections.licenses'));
    }

    public function billing(): View
    {
        return view('admin.billing.index', [
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
        return view('admin.support.index');
    }

    private function section(string $title, string $description): View
    {
        return view('admin.section', [
            'title' => $title,
            'description' => $description,
        ]);
    }
}
