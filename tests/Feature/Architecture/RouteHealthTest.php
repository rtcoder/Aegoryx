<?php

namespace Tests\Feature\Architecture;

use Tests\TestCase;

final class RouteHealthTest extends TestCase
{
    public function test_root_route_returns_the_public_entry_point(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Aegoryx');
    }

    public function test_health_route_returns_successfully_without_database_access(): void
    {
        $this->get('/up')
            ->assertOk();
    }
}
