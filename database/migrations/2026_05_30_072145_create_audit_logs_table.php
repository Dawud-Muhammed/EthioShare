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
        Schema::create('audit_logs', function (Blueprint $table) {
            // Primary Key
            $table->ulid('id')->primary();

            // Event Identity
            // Nullable because system processes or cron jobs might trigger actions without a specific user
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type', 100);

            // Polymorphic Entity Tracking
            // Creates `entity_type`, `entity_id` and the composite index `idx_entity`
            $table->ulidMorphs('entity');

            // Change Tracking
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();

            // Context
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->uuid('request_id')->nullable(); // Useful for tracing the exact HTTP request lifecycle
            
            // Timestamp (Immutable log, so no updated_at or deleted_at)
            $table->timestamp('created_at')->useCurrent();

            // Query Optimization Indexes
            // Note: idx_user_id and idx_entity are handled by their respective helpers
            $table->index('action_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};