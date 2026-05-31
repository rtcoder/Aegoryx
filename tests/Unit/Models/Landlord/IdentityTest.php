<?php

namespace Tests\Unit\Models\Landlord;

use App\Models\Landlord\Identity;
use App\Modules\Identity\Enums\IdentityStatus;
use PHPUnit\Framework\TestCase;

final class IdentityTest extends TestCase
{
    public function test_status_is_cast_to_identity_status_enum(): void
    {
        $identity = new Identity([
            'email' => 'admin@example.test',
            'status' => IdentityStatus::Active->value,
        ]);

        $this->assertSame(IdentityStatus::Active, $identity->status);
    }
}
