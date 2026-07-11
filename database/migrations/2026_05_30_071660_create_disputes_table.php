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
        Schema::create('disputes', function (Blueprint $table) {
            // disputes - conflict resolution between parties (renter/owner) for a booking
            $table->ulid('id')->primary();

            // --// Core Relationships
            $table->foreignUlid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUlid('initiator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('respondent_id')->constrained('users')->cascadeOnDelete();

            // --dispute identity
            $table->enum('dispute_reason', [
                'ASSET_DAMAGE', 'MISSING_HOURS', 'LATE_RETURN', 'RENTER_NO_SHOW', 'OTHER',
            ]);
            $table->string('title', 255);
            $table->text('description');

            // -- Evidence & Documentation (JSONB for flexible attachment tracking)
            /*
              -- [
                -- { type: 'PHOTO', media_id, uploaded_by, uploaded_at, description },
                -- { type: 'VIDEO', media_id, uploaded_by, uploaded_at, description },
                -- { type: 'DOCUMENT', media_id, uploaded_by, uploaded_at, description }
                -- { type: 'REPORT', media_id, uploaded_by, uploaded_at, description }
              -- ]
            */
            $table->jsonb('evidence');

            // resolution workflow
            $table->enum('dispute_status', [
                'OPEN', 'UNDER_REVIEW', 'AWAITING_RESPONSE', 'MEDIATED', 'RESOLVED', 'ESCALATED',
            ]);
            // Support Staff Assignment
            // i use nullOnDelete() so if a staff member leaves and their account is deleted,
            // the historical dispute record is retained.
            $table->foreignUlid('assigned_to_id')->nullable()->constrained('users')->noActionOnDelete(); // -- Support specialist
            $table->decimal('resolution_amount_credited', 12, 2)->nullable(); // -- Refund to renter
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // --appeal and escalation
            $table->integer('appeal_count')->default(0);
            $table->timestamp('last_appeal_at')->nullable();
            $table->text('escalation_reason')->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Query Optimization Indexes
            // Note: PostgreSQL does not automatically index foreign keys,
            // so explicitly defining them here based on SQL is correct.
            $table->index('booking_id');
            $table->index('initiator_id');
            $table->index('respondent_id');
            $table->index('dispute_status');
            $table->index('assigned_to_id');
            $table->index('created_at');

        });

        // AFTER disputes table is created, safely attach the deferred foreign key to bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint first to avoid breaking database integrity checks during a rollback
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['dispute_id']);
        });

        Schema::dropIfExists('disputes');
    }
};
