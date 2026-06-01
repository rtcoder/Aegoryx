<?php

namespace App\Livewire\Landlord\Tenants;

use App\Models\Landlord\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.landlord.tenants.index', [
            'tenants' => Tenant::query()
                ->orderBy('name')
                ->paginate(20),
        ]);
    }
}
