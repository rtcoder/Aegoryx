<?php

namespace App\Livewire\Admin\Tenants;

use App\Models\System\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.tenants.index', [
            'tenants' => Tenant::query()
                ->orderBy('name')
                ->paginate(20),
        ]);
    }
}
