<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_events', function (Blueprint $table): void {
            $table->text('failure_reason')->nullable()->after('payload');
            $table->timestamp('failed_at')->nullable()->after('processed_at');
        });
    }

    public function down(): void
    {
        Schema::table('billing_events', function (Blueprint $table): void {
            $table->dropColumn(['failure_reason', 'failed_at']);
        });
    }
};
