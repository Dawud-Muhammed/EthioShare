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
        Schema::create('trust_scores', function (Blueprint $table) {
            //-- trust_scores (Algorithmic Reputation)
            //--primary key
            $table->ulid('id')->primary();

            // Core Relationship
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();

            // Score Components (Immutable snapshots)
            $table->decimal('base_score', 5, 2)->default(50.00); //-- Starting point: 0-100
            $table->decimal('completion_rate', 5, 2)->nullable(); //-- % of bookings completed without dispute
            $table->decimal('payment_punctuality', 5, 2)->nullable(); //-- On-time payment history
            $table->decimal('asset_condition_score', 5, 2)->nullable(); //-- Avg rating of asset condition upon return
            $table->decimal('communication_score', 5, 2)->nullable(); //-- Responsiveness to messages
            $table->decimal('dispute_rate', 5, 2)->nullable(); //-- Inverse penalty for disputes
            $table->decimal('kyc_compliance_score', 5, 2)->nullable(); // -- KYC/regulatory adherence
            
            // Composite Scores
            $table->decimal('final_score', 5, 2); //-- Weighted average of above
            $table->enum('trust_tier', [
            'UNVERIFIED', 'BRONZE', 'SILVER', 'GOLD', 'PLATINUM'
            ]);

            // Risk Flags
            //-- { flags: ['LATE_PAYMENTS', 'DISPUTE_HISTORY', 'UNVERIFIED_KYC'], severity: '..}
            $table->jsonb('risk_factors')->nullable();

            // Calculation Metadata
            $table->string('calculation_method', 100)->nullable(); //-- 'v1_weighted', 'v2_ml_model', etc.
            $table->timestamp('calculation_timestamp')->nullable();
            $table->timestamp('next_recalculation_at')->nullable();

            // Audit
            $table->timestamps();

            // Query Optimization Indexes
            // idx_user_id is automatically created by foreignUlid()
            $table->index('final_score');
            $table->index('trust_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_scores');
    }
};