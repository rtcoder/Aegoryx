@php
    $note = $note ?? null;
    $selectedSubject = $note ? $note->subject_type->value.':'.$note->subject_id : null;
@endphp

<x-form.select
    name="subject"
    :label="__('crm.subject')"
    :options="$subjects"
    :placeholder="__('crm.select_subject')"
    :value="$selectedSubject"
/>

<x-form.textarea
    name="body"
    :label="__('crm.note_body')"
    :value="$note?->body"
    rows="6"
/>

<x-form.checkbox
    name="is_sensitive"
    :label="__('crm.sensitive_note')"
    :help="__('crm.sensitive_note_help')"
    :checked="$note?->is_sensitive ?? false"
/>
