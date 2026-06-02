@extends('tenant.layout')

@section('title', __('crm.contacts').' | '.$tenant->name.' | '.__('app.tenant_panel_title'))
@section('heading', __('crm.contacts'))
@section('subheading', __('crm.contacts_description'))

@section('content')
    <div class="grid gap-5 xl:grid-cols-[360px_1fr]">
        <section class="rounded border border-neutral-800 bg-neutral-900 p-5">
            <h2 class="text-lg font-semibold">{{ __('crm.create_contact') }}</h2>

            <form method="POST" action="{{ route('tenant.crm.contacts.store') }}" class="mt-5 space-y-4">
                @csrf

                @include('tenant.crm.contacts.partials.fields')

                <button type="submit" class="w-full rounded bg-sky-500 px-4 py-2 font-medium text-white hover:bg-sky-400">
                    {{ __('crm.create_contact') }}
                </button>
            </form>
        </section>

        <section class="rounded border border-neutral-800 bg-neutral-900">
            <div class="border-b border-neutral-800 px-5 py-4">
                <h2 class="text-lg font-semibold">{{ __('crm.contact_list') }}</h2>
                <p class="mt-1 text-sm text-neutral-400">{{ __('crm.contact_sensitive_note') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-800 text-left text-sm">
                    <thead class="bg-neutral-950 text-xs uppercase tracking-wide text-neutral-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">{{ __('common.name') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('common.email') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('crm.phone') }}</th>
                            <th class="px-5 py-3 font-medium">{{ __('crm.position') }}</th>
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
                                        <a href="{{ route('tenant.crm.contacts.edit', $contact) }}" wire:navigate class="text-sky-300 hover:text-sky-200">
                                            {{ __('common.manage') }}
                                        </a>
                                        <form method="POST" action="{{ route('tenant.crm.contacts.destroy', $contact) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-300 hover:text-red-200">
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-neutral-400">{{ __('crm.no_contacts') }}</td>
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
