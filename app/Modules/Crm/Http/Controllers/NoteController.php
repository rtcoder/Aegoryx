<?php

namespace App\Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\CrmNote;
use App\Modules\Crm\Actions\CreateNoteAction;
use App\Modules\Crm\Actions\DeleteNoteAction;
use App\Modules\Crm\Actions\UpdateNoteAction;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Http\Requests\StoreNoteRequest;
use App\Modules\Crm\Http\Requests\UpdateNoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class NoteController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmNote::class);

        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $sortColumns = [
            'body' => 'body',
            'created_at' => 'created_at',
        ];
        $sortColumn = $sortColumns[$sort] ?? 'created_at';

        return view('tenant.crm.notes.index', [
            'search' => $search,
            'sort' => array_key_exists($sort, $sortColumns) ? $sort : 'created_at',
            'direction' => $direction,
            'tenant' => $request->attributes->get('tenant'),
            'notes' => CrmNote::query()
                ->when($search !== '', fn ($query) => $query->where('body', 'like', "%{$search}%"))
                ->orderBy($sortColumn, $direction)
                ->paginate(20)
                ->withQueryString(),
            'subjectTypes' => $this->subjectTypes(),
            'subjects' => $this->subjectOptions(),
        ]);
    }

    public function store(StoreNoteRequest $request, CreateNoteAction $createNote): RedirectResponse
    {
        $createNote->handle($request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.notes.index')
            ->with('success', __('flash.crm_note_created'));
    }

    public function edit(Request $request, CrmNote $note): View
    {
        Gate::authorize('update', $note);

        return view('tenant.crm.notes.edit', [
            'tenant' => $request->attributes->get('tenant'),
            'note' => $note,
            'subjectTypes' => $this->subjectTypes(),
            'subjects' => $this->subjectOptions(),
        ]);
    }

    public function update(UpdateNoteRequest $request, CrmNote $note, UpdateNoteAction $updateNote): RedirectResponse
    {
        $updateNote->handle($note, $request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.notes.index')
            ->with('success', __('flash.crm_note_updated'));
    }

    public function destroy(Request $request, CrmNote $note, DeleteNoteAction $deleteNote): RedirectResponse
    {
        $deleteNote->handle($note, $request->user());

        return redirect()
            ->route('tenant.crm.notes.index')
            ->with('success', __('flash.crm_note_deleted'));
    }

    /**
     * @return array<string, string>
     */
    private function subjectTypes(): array
    {
        return collect(CrmSubjectType::cases())
            ->mapWithKeys(fn (CrmSubjectType $type): array => [
                $type->value => __("crm.subject_type.{$type->value}"),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function subjectOptions(): array
    {
        $companies = CrmCompany::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (CrmCompany $company): array => [
                CrmSubjectType::Company->value.':'.$company->id => __('crm.subject_option', [
                    'type' => __('crm.subject_type.company'),
                    'name' => $company->name,
                ]),
            ]);

        $contacts = CrmContact::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (CrmContact $contact): array => [
                CrmSubjectType::Contact->value.':'.$contact->id => __('crm.subject_option', [
                    'type' => __('crm.subject_type.contact'),
                    'name' => trim($contact->first_name.' '.$contact->last_name),
                ]),
            ]);

        $deals = CrmDeal::query()
            ->orderBy('title')
            ->get()
            ->mapWithKeys(fn (CrmDeal $deal): array => [
                CrmSubjectType::Deal->value.':'.$deal->id => __('crm.subject_option', [
                    'type' => __('crm.subject_type.deal'),
                    'name' => $deal->title,
                ]),
            ]);

        return $companies->merge($contacts)->merge($deals)->all();
    }
}
