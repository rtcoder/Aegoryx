<?php

use App\Modules\Crm\Enums\CrmTaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->text('body');
            $table->boolean('is_sensitive')->default(false)->index();
            $table->foreignId('created_by')->nullable()->index();
            $table->foreignId('updated_by')->nullable()->index();
            $table->foreignId('deleted_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('crm_tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default(CrmTaskStatus::Pending->value)->index();
            $table->date('due_date')->nullable()->index();
            $table->foreignId('assigned_to')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->foreignId('updated_by')->nullable()->index();
            $table->foreignId('deleted_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_notes');
    }
};
