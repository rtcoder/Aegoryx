<?php

namespace Tests\Unit\Services\Tenancy;

use App\Services\Tenancy\PostgresSchemaManager;
use Tests\TestCase;

final class PostgresSchemaManagerTest extends TestCase
{
    public function test_quote_identifier_escapes_embedded_quotes(): void
    {
        $manager = new PostgresSchemaManager;

        $this->assertSame('"tenant_1"', $manager->quoteIdentifier('tenant_1'));
        $this->assertSame('"tenant""evil"', $manager->quoteIdentifier('tenant"evil'));
    }
}
