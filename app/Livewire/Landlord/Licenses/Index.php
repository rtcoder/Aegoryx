<?php

namespace App\Livewire\Landlord\Licenses;

use App\Models\Landlord\License;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.landlord.licenses.index', [
            'licenses' => License::query()
                ->with('tenant')
                ->latest()
                ->paginate(20),
        ]);
    }
}
