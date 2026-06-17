@extends('tenant.layout')

@section('title', __('files.files').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('files.files'))
@section('subheading', __('files.files_description'))

@section('content')
    <section class="ui-card">
        <div class="ui-card-header">
            <h2 class="ui-heading-2">{{ __('files.file_list') }}</h2>
            <p class="ui-body mt-1">{{ __('files.private_access_note') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('files.original_name') }}</th>
                        <th>{{ __('files.mime_type') }}</th>
                        <th>{{ __('files.size') }}</th>
                        <th>{{ __('files.owner') }}</th>
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
                            <td colspan="6" class="py-10 text-center text-[var(--ui-text-muted)]">{{ __('files.no_files') }}</td>
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
