<?php

use App\Modules\Crm\Enums\CrmDealStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('contact_id')->nullable()->index();
            $table->string('status')->default(CrmDealStatus::Open->value)->index();
            $table->decimal('value_amount', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->foreignId('updated_by')->nullable()->index();
            $table->foreignId('deleted_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
