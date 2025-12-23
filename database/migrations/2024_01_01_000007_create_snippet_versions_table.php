<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippet_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('snippet_id')->constrained()->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('code');
            $table->string('language', 50);
            $table->text('change_summary')->nullable();
            $table->enum('change_type', ['create', 'update', 'restore'])->default('update');
            $table->integer('lines_added')->default(0);
            $table->integer('lines_removed')->default(0);
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['snippet_id', 'version_number']);
            $table->index('snippet_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippet_versions');
    }
};
