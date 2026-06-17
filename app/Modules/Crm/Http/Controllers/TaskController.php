<?php

namespace App\Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;
use App\Models\Tenant\CrmTask;
use App\Models\Tenant\User;
use App\Modules\Crm\Actions\CreateTaskAction;
use App\Modules\Crm\Actions\DeleteTaskAction;
use App\Modules\Crm\Actions\UpdateTaskAction;
use App\Modules\Crm\Enums\CrmSubjectType;
use App\Modules\Crm\Enums\CrmTaskStatus;
use App\Modules\Crm\Http\Requests\StoreTaskRequest;
use App\Modules\Crm\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class TaskController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', CrmTask::class);

        return view('tenant.crm.tasks.index', [
            'tenant' => $request->attributes->get('tenant'),
            'tasks' => CrmTask::query()
                ->with('assignee')
                ->latest()
                ->paginate(20),
            'subjectTypes' => $this->subjectTypes(),
            'subjects' => $this->subjectOptions(),
            'statuses' => $this->statusOptions(),
            'users' => $this->userOptions(),
        ]);
    }

    public function store(StoreTaskRequest $request, CreateTaskAction $createTask): RedirectResponse
    {
        $createTask->handle($request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.tasks.index')
            ->with('success', __('flash.crm_task_created'));
    }

    public function edit(Request $request, CrmTask $task): View
    {
        Gate::authorize('update', $task);

        return view('tenant.crm.tasks.edit', [
            'tenant' => $request->attributes->get('tenant'),
            'task' => $task->load('assignee'),
            'subjectTypes' => $this->subjectTypes(),
            'subjects' => $this->subjectOptions(),
            'statuses' => $this->statusOptions(),
            'users' => $this->userOptions(),
        ]);
    }

    public function update(UpdateTaskRequest $request, CrmTask $task, UpdateTaskAction $updateTask): RedirectResponse
    {
        $updateTask->handle($task, $request->validated(), $request->user());

        return redirect()
            ->route('tenant.crm.tasks.index')
            ->with('success', __('flash.crm_task_updated'));
    }

    public function destroy(Request $request, CrmTask $task, DeleteTaskAction $deleteTask): RedirectResponse
    {
        $deleteTask->handle($task, $request->user());

        return redirect()
            ->route('tenant.crm.tasks.index')
            ->with('success', __('flash.crm_task_deleted'));
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

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return collect(CrmTaskStatus::cases())
            ->mapWithKeys(fn (CrmTaskStatus $status): array => [
                $status->value => __("crm.task_status.{$status->value}"),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function userOptions(): array
    {
        return User::query()->orderBy('name')->pluck('name', 'id')->all();
    }
}
