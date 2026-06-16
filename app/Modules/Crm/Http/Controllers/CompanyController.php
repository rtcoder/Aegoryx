<?php

namespace App\Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Modules\Crm\Actions\CreateCompanyAction;
use App\Modules\Crm\Actions\DeleteCompanyAction;
use App\Modules\Crm\Actions\UpdateCompanyAction;
use App\Modules\Crm\Http\Requests\StoreCompanyRequest;
use App\Modules\Crm\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmCompany::class);

        return view('tenant.crm.companies.index', [
            'tenant' => $request->attributes->get('tenant'),
            'companies' => CrmCompany::query()
                ->with('contacts')
                ->latest()
                ->paginate(20),
            'contacts' => $this->contactOptions(),
        ]);
    }

    public function store(StoreCompanyRequest $request, CreateCompanyAction $createCompany): RedirectResponse
    {
        $createCompany->handle($request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.companies.index')
            ->with('success', __('flash.crm_company_created'));
    }

    public function edit(Request $request, CrmCompany $company): View
    {
        Gate::authorize('update', $company);

        return view('tenant.crm.companies.edit', [
            'tenant' => $request->attributes->get('tenant'),
            'company' => $company->load('contacts'),
            'contacts' => $this->contactOptions(),
        ]);
    }

    public function update(UpdateCompanyRequest $request, CrmCompany $company, UpdateCompanyAction $updateCompany): RedirectResponse
    {
        $updateCompany->handle($company, $request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.companies.index')
            ->with('success', __('flash.crm_company_updated'));
    }

    public function destroy(Request $request, CrmCompany $company, DeleteCompanyAction $deleteCompany): RedirectResponse
    {
        $deleteCompany->handle($company, $request->user());

        return redirect()
            ->route('tenant.crm.companies.index')
            ->with('success', __('flash.crm_company_deleted'));
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
}
