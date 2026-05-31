<?php

namespace Tests\Feature\AdminConsole;

use App\Models\Landlord\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use Tests\TestCase;

final class AdminNavigationTest extends TestCase
{
    public function test_guest_is_redirected_to_landlord_login(): void
    {
        $this
            ->get('http://admin.aegoryx.test/')
            ->assertRedirect('http://admin.aegoryx.test/login');
    }

    public function test_superadmin_can_see_admin_navigation_pages(): void
    {
        $this->actingAs($this->superadmin(), 'landlord');

        foreach ([
            'http://admin.aegoryx.test/',
            'http://admin.aegoryx.test/tenants',
            'http://admin.aegoryx.test/features',
            'http://admin.aegoryx.test/licenses',
            'http://admin.aegoryx.test/billing',
            'http://admin.aegoryx.test/support',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    private function superadmin(): Identity
    {
        return new Identity([
            'email' => 'admin@example.test',
            'is_super_admin' => true,
            'status' => IdentityStatus::Active,
        ]);
    }
}
