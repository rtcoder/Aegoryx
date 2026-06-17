@extends('tenant.layout')

@section('title', $file->original_name.' | '.__('files.files').' | '.$tenant->name)
@section('heading', $file->original_name)
@section('subheading', __('files.file_details_description'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <x-ui.button :href="route('tenant.files.index')" variant="secondary">
            {{ __('common.back') }}
        </x-ui.button>
        <x-ui.button :href="route('tenant.files.download', $file)">
            {{ __('files.download') }}
        </x-ui.button>
    </div>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(320px,420px)]">
        <div class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('files.metadata') }}</h2>
                <p class="ui-body mt-1">{{ __('files.metadata_description') }}</p>
            </div>

            <dl class="ui-card-body grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="ui-label">{{ __('files.original_name') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->original_name }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.mime_type') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->mime_type ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.size') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ number_format($file->size_bytes / 1024, 1) }} KB</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.visibility') }}</dt>
                    <dd class="mt-1"><x-ui.badge>{{ $file->visibility->value }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.owner') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->owner?->name ?? __('files.shared_file') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.expires_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->expires_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('common.created_at') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->created_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('common.updated') }}</dt>
                    <dd class="mt-1 text-[var(--ui-text)]">{{ $file->updated_at?->format('Y-m-d H:i') ?? __('common.not_set') }}</dd>
                </div>
            </dl>
        </div>

        <aside class="ui-card">
            <div class="ui-card-header">
                <h2 class="ui-heading-2">{{ __('files.storage') }}</h2>
            </div>

            <dl class="ui-card-body space-y-4">
                <div>
                    <dt class="ui-label">{{ __('files.disk') }}</dt>
                    <dd class="mt-1 font-mono text-sm text-[var(--ui-text)]">{{ $file->disk }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.path') }}</dt>
                    <dd class="mt-1 break-all font-mono text-sm text-[var(--ui-text)]">{{ $file->path }}</dd>
                </div>
                <div>
                    <dt class="ui-label">{{ __('files.checksum') }}</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-[var(--ui-text-muted)]">{{ $file->checksum_sha256 ?? __('common.not_set') }}</dd>
                </div>
            </dl>
        </aside>
    </section>
@endsection
