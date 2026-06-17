<?php

namespace App\Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CrmContact;
use App\Modules\Crm\Actions\CreateContactAction;
use App\Modules\Crm\Actions\DeleteContactAction;
use App\Modules\Crm\Actions\UpdateContactAction;
use App\Modules\Crm\Http\Requests\StoreContactRequest;
use App\Modules\Crm\Http\Requests\UpdateContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmContact::class);

        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        return view('tenant.crm.contacts.index', [
            'search' => $search,
            'sort' => in_array($sort, ['name', 'position', 'created_at'], true) ? $sort : 'created_at',
            'direction' => $direction,
            'tenant' => $request->attributes->get('tenant'),
            'contacts' => CrmContact::query()
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                }))
                ->when($sort === 'name', fn ($query) => $query->orderBy('last_name', $direction)->orderBy('first_name', $direction))
                ->when($sort === 'position', fn ($query) => $query->orderBy('position', $direction))
                ->when(! in_array($sort, ['name', 'position'], true), fn ($query) => $query->orderBy('created_at', $direction))
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(StoreContactRequest $request, CreateContactAction $createContact): RedirectResponse
    {
        $createContact->handle($request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.index')
            ->with('success', __('flash.crm_contact_created'));
    }

    public function edit(Request $request, CrmContact $contact): View
    {
        Gate::authorize('update', $contact);

        return view('tenant.crm.contacts.edit', [
            'tenant' => $request->attributes->get('tenant'),
            'contact' => $contact,
        ]);
    }

    public function update(UpdateContactRequest $request, CrmContact $contact, UpdateContactAction $updateContact): RedirectResponse
    {
        $updateContact->handle($contact, $request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.index')
            ->with('success', __('flash.crm_contact_updated'));
    }

    public function destroy(Request $request, CrmContact $contact, DeleteContactAction $deleteContact): RedirectResponse
    {
        $deleteContact->handle($contact, $request->user());

        return redirect()
            ->route('tenant.crm.index')
            ->with('success', __('flash.crm_contact_deleted'));
    }
}
