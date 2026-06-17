<div class="mb-5 flex flex-wrap items-center gap-3">
    <x-ui.button :href="route('tenant.crm.index')" wire:navigate :variant="request()->routeIs('tenant.crm.index') || request()->routeIs('tenant.crm.contacts.*') ? 'primary' : 'secondary'" size="sm">
        {{ __('crm.contacts') }}
    </x-ui.button>
    <x-ui.button :href="route('tenant.crm.companies.index')" wire:navigate :variant="request()->routeIs('tenant.crm.companies.*') ? 'primary' : 'secondary'" size="sm">
        {{ __('crm.companies') }}
    </x-ui.button>
    <x-ui.button :href="route('tenant.crm.deals.index')" wire:navigate :variant="request()->routeIs('tenant.crm.deals.*') ? 'primary' : 'secondary'" size="sm">
        {{ __('crm.deals') }}
    </x-ui.button>
    <x-ui.button :href="route('tenant.crm.notes.index')" wire:navigate :variant="request()->routeIs('tenant.crm.notes.*') ? 'primary' : 'secondary'" size="sm">
        {{ __('crm.notes') }}
    </x-ui.button>
    <x-ui.button :href="route('tenant.crm.tasks.index')" wire:navigate :variant="request()->routeIs('tenant.crm.tasks.*') ? 'primary' : 'secondary'" size="sm">
        {{ __('crm.tasks') }}
    </x-ui.button>
</div>
