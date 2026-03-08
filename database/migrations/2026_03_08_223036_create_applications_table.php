<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('health_check_url')->nullable();
            $table->string('app_id')->unique();
            $table->string('app_key');
            $table->text('app_secret'); // Encrypted
            $table->enum('status', ['active', 'inactive', 'error', 'maintenance'])->default('inactive');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional config
            $table->boolean('is_enabled')->default(true);
            $table->integer('max_connections')->default(100);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
