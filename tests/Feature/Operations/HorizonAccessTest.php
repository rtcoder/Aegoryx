<?php

namespace Tests\Feature\Operations;

use App\Models\Landlord\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

final class HorizonAccessTest extends TestCase
{
    public function test_horizon_gate_allows_only_landlord_superadmin(): void
    {
        $superadmin = new Identity([
            'email' => 'root@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
        $regularIdentity = new Identity([
            'email' => 'user@example.test',
            'is_super_admin' => false,
            'status' => IdentityStatus::Active,
        ]);

        $this->assertTrue(Gate::forUser($superadmin)->allows('viewHorizon'));
        $this->assertFalse(Gate::forUser($regularIdentity)->allows('viewHorizon'));
        $this->assertContains('auth:landlord', config('horizon.middleware'));
    }
}
