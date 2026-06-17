@extends('tenant.layout')

@section('title', __('files.files').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('files.files'))
@section('subheading', __('files.files_description'))

@section('content')
    <section class="ui-card mb-6">
        <div class="ui-card-header">
            <h2 class="ui-heading-2">{{ __('files.upload_file') }}</h2>
            <p class="ui-body mt-1">{{ __('files.upload_description') }}</p>
        </div>
        <form method="POST" action="{{ route('tenant.files.store') }}" enctype="multipart/form-data" class="ui-card-body space-y-4">
            @csrf
            @if (session('success'))
                <div class="rounded border border-emerald-700 bg-emerald-950 px-4 py-3 text-sm text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            <div>
                <label for="file" class="ui-label">{{ __('files.choose_file') }}</label>
                <input id="file" name="file" type="file" class="ui-input mt-2">
                @error('file') <p class="ui-error">{{ $message }}</p> @enderror
            </div>

            <x-ui.button type="submit">{{ __('files.upload') }}</x-ui.button>
        </form>
    </section>

    <section class="ui-card">
        <div class="ui-card-header flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="ui-heading-2">{{ __('files.file_list') }}</h2>
                <p class="ui-body mt-1">{{ __('files.private_access_note') }}</p>
            </div>
            <form method="POST" action="{{ route('tenant.files.exports.activity.store') }}">
                @csrf
                <x-ui.button type="submit" variant="secondary" size="sm">
                    {{ __('files.create_activity_export') }}
                </x-ui.button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('files.original_name') }}</th>
                        <th>{{ __('files.mime_type') }}</th>
                        <th>{{ __('files.size') }}</th>
                        <th>{{ __('files.owner') }}</th>
                        <th>{{ __('files.expires_at') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($files as $file)
                        <tr>
                            <td class="font-medium text-[var(--ui-text)]">{{ $file->original_name }}</td>
                            <td class="text-[var(--ui-text-muted)]">{{ $file->mime_type ?? __('common.not_set') }}</td>
                            <td class="text-[var(--ui-text-muted)]">{{ number_format($file->size_bytes / 1024, 1) }} KB</td>
                            <td class="text-[var(--ui-text-muted)]">{{ $file->owner?->name ?? __('files.shared_file') }}</td>
                            <td class="text-[var(--ui-text-muted)]">{{ $file->expires_at?->format('Y-m-d') ?? __('common.not_set') }}</td>
                            <td class="text-[var(--ui-text-muted)]">{{ $file->created_at?->format('Y-m-d') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('tenant.files.download', $file) }}" class="ui-link">
                                        {{ __('files.download') }}
                                    </a>
                                    <form method="POST" action="{{ route('tenant.files.destroy', $file) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-[var(--ui-danger)] hover:brightness-110">
                                            {{ __('common.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('files.no_files') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[var(--ui-border)] px-5 py-4">
            {{ $files->links() }}
        </div>
    </section>
@endsection
