<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('snippet_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('shared_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('shared_with')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('share_type', 20)->default('link'); // link, user, team, email
            $table->string('share_token', 64)->unique()->nullable();
            $table->string('permission', 20)->default('view'); // view, edit
            $table->string('email')->nullable(); // For email shares
            $table->timestamp('expires_at')->nullable();
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('share_token');
            $table->index('shared_by');
            $table->index('shared_with');
            $table->index('share_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
