<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status')->default('draft')->index();
            $table->json('draft_content')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->index();
            $table->foreignId('created_by')->nullable()->index();
            $table->foreignId('updated_by')->nullable()->index();
            $table->foreignId('deleted_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_page_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cms_page_id')->index();
            $table->unsignedInteger('version');
            $table->string('title');
            $table->string('slug');
            $table->string('status');
            $table->json('content')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->timestamps();

            $table->unique(['cms_page_id', 'version']);
        });

        Schema::create('published_pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cms_page_id')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('content')->nullable();
            $table->timestamp('published_at');
            $table->foreignId('published_by')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('published_pages');
        Schema::dropIfExists('cms_page_revisions');
        Schema::dropIfExists('cms_pages');
    }
};
