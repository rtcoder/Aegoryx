<?php

namespace App\Livewire\Admin\Licenses;

use App\Models\System\License;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.licenses.index', [
            'licenses' => License::query()
                ->with('tenant')
                ->latest()
                ->paginate(20),
        ]);
    }
}
