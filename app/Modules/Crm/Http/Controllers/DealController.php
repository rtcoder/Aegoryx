<?php

namespace App\Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;
use App\Modules\Crm\Actions\CreateDealAction;
use App\Modules\Crm\Actions\DeleteDealAction;
use App\Modules\Crm\Actions\UpdateDealAction;
use App\Modules\Crm\Enums\CrmDealStatus;
use App\Modules\Crm\Http\Requests\StoreDealRequest;
use App\Modules\Crm\Http\Requests\UpdateDealRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class DealController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmDeal::class);

        return view('tenant.crm.deals.index', [
            'tenant' => $request->attributes->get('tenant'),
            'deals' => CrmDeal::query()
                ->with(['company', 'contact'])
                ->latest()
                ->paginate(20),
            'companies' => $this->companyOptions(),
            'contacts' => $this->contactOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(StoreDealRequest $request, CreateDealAction $createDeal): RedirectResponse
    {
        $createDeal->handle($request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.deals.index')
            ->with('success', __('flash.crm_deal_created'));
    }

    public function edit(Request $request, CrmDeal $deal): View
    {
        Gate::authorize('update', $deal);

        return view('tenant.crm.deals.edit', [
            'tenant' => $request->attributes->get('tenant'),
            'deal' => $deal->load(['company', 'contact']),
            'companies' => $this->companyOptions(),
            'contacts' => $this->contactOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateDealRequest $request, CrmDeal $deal, UpdateDealAction $updateDeal): RedirectResponse
    {
        $updateDeal->handle($deal, $request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.deals.index')
            ->with('success', __('flash.crm_deal_updated'));
    }

    public function destroy(Request $request, CrmDeal $deal, DeleteDealAction $deleteDeal): RedirectResponse
    {
        $deleteDeal->handle($deal, $request->user());

        return redirect()
            ->route('tenant.crm.deals.index')
            ->with('success', __('flash.crm_deal_deleted'));
    }

    /**
     * @return array<int, string>
     */
    private function companyOptions(): array
    {
        return CrmCompany::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function contactOptions(): array
    {
        return CrmContact::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (CrmContact $contact): array => [
                $contact->id => trim($contact->first_name.' '.$contact->last_name),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return collect(CrmDealStatus::cases())
            ->mapWithKeys(fn (CrmDealStatus $status): array => [
                $status->value => __("crm.deal_status.{$status->value}"),
            ])
            ->all();
    }
}
