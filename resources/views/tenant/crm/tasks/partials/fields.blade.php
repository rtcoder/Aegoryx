@php
    $task = $task ?? null;
    $selectedSubject = $task ? $task->subject_type->value.':'.$task->subject_id : null;
@endphp

<x-form.select
    name="subject"
    :label="__('crm.subject')"
    :options="$subjects"
    :placeholder="__('crm.select_subject')"
    :value="$selectedSubject"
/>

<x-form.input
    name="title"
    :label="__('crm.task_title')"
    :value="$task?->title"
/>

<x-form.select
    name="status"
    :label="__('common.status')"
    :options="$statuses"
    :value="$task?->status?->value ?? \App\Modules\Crm\Enums\CrmTaskStatus::Pending->value"
/>

<x-form.input
    name="due_date"
    type="date"
    :label="__('crm.due_date')"
    :value="$task?->due_date?->toDateString()"
/>

<x-form.select
    name="assigned_to"
    :label="__('crm.assigned_to')"
    :options="$users"
    :placeholder="__('crm.unassigned')"
    :value="$task?->assigned_to"
/>

<x-form.textarea
    name="description"
    :label="__('crm.task_description')"
    :value="$task?->description"
    rows="4"
/>
