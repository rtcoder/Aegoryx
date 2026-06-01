<?php

namespace App\Livewire\Landlord\Licenses;

use App\Models\Landlord\License;
use App\Modules\Licensing\Actions\VerifyLicenseAction;
use Livewire\Component;

final class Show extends Component
{
    public License $license;

    public function mount(License $license): void
    {
        $this->license = $license->load('tenant');
    }

    public function verify(VerifyLicenseAction $action): void
    {
        $this->license = $action->handle(
            license: $this->license,
            actor: auth('landlord')->user(),
            ip: request()->ip(),
            userAgent: request()->userAgent(),
        )->load('tenant');

        session()->flash('success', __('flash.license_verified'));
    }

    public function render()
    {
        return view('livewire.landlord.licenses.show');
    }
}
