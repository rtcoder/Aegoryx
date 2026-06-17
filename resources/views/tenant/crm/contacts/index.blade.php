@extends('tenant.layout')

@section('title', __('crm.contacts').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.contacts'))
@section('subheading', __('crm.contacts_description'))

@section('content')
    @include('tenant.crm.partials.navigation')

    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <x-ui.card :title="__('crm.create_contact')">

            <form method="POST" action="{{ route('tenant.crm.contacts.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.contacts.partials.fields')

                <x-ui.button type="submit" class="w-full">
                    {{ __('crm.create_contact') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <section class="rounded border border-neutral-800 bg-neutral-900">
            <div class="border-b border-neutral-800 px-5 py-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ __('crm.contact_list') }}</h2>
                        <p class="mt-1 text-sm text-neutral-400">{{ __('crm.contact_sensitive_note') }}</p>
                    </div>
                    <form method="GET" action="{{ route('tenant.crm.index') }}" class="flex flex-col gap-2 sm:flex-row">
                        <input name="search" value="{{ $search }}" class="ui-input min-w-64" placeholder="{{ __('common.search_placeholder') }}">
                        <x-ui.button type="submit" variant="secondary">{{ __('common.search') }}</x-ui.button>
                        @if ($search !== '')
                            <x-ui.button :href="route('tenant.crm.index')" variant="ghost">{{ __('common.clear_search') }}</x-ui.button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-800 text-left text-sm">
                    <thead class="bg-neutral-950 text-xs uppercase tracking-wide text-neutral-500">
                        <tr>
                            <th class="px-5 py-3 font-medium"><x-table.sort-link sort="name" :current-sort="$sort" :current-direction="$direction">{{ __('common.name') }}</x-table.sort-link></th>
                            <th class="px-5 py-3 font-medium">{{ __('common.email') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('crm.phone') }}</th>
                            <th class="px-5 py-3 font-medium"><x-table.sort-link sort="position" :current-sort="$sort" :current-direction="$direction">{{ __('crm.position') }}</x-table.sort-link></th>
                            <th class="px-5 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse ($contacts as $contact)
                            <tr>
                                <td class="px-5 py-4 font-medium text-neutral-100">{{ trim($contact->first_name.' '.$contact->last_name) }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $contact->email ?? __('common.not_set') }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $contact->phone ?? __('common.not_set') }}</td>
                                <td class="px-5 py-4 text-neutral-400">{{ $contact->position ?? __('common.not_set') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('tenant.crm.contacts.edit', $contact) }}" wire:navigate class="ui-link">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.contacts.destroy', $contact) }}">
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
                                <td colspan="5" class="px-5 py-10 text-center text-neutral-400">{{ $search === '' ? __('crm.no_contacts') : __('common.no_results') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-800 px-5 py-4">
                {{ $contacts->links() }}
            </div>
        </section>
    </div>
@endsection
