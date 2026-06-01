<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('identities', function (Blueprint $table): void {
            $table->string('locale', 8)->default('pl')->after('status')->index();
        });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('locale', 8)->default('pl')->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('identities', function (Blueprint $table): void {
            $table->dropColumn('locale');
        });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn('locale');
        });
    }
};
