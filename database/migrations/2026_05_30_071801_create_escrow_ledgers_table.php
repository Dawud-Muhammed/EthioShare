<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('escrow_ledgers', function (Blueprint $table) {
            //--primary id
            $table->ulid('id')->primary();

            //--core relation ship
            $table->foreignUlid('booking_id')->constrained('bookings')->cascadeOnDelete();

            //--transaction identity
            $table->string('transaction_id', 100)->unique();  //-- External gateway reference (Chapa/Santim)
            $table->enum('gateway_name', [
                'CHAPA', 'SANTIM_PAY', 'TELEBIRR', 'OTHER'
            ]);
            $table->string('gateway_transaction_id', 255)->nullable();

            //--financial flow
            $table->decimal('amount_due', 12, 2);
            $table->decimal('amount_held', 12, 2)->default(0.00);
            $table->decimal('amount_released', 12, 2)->default(0.00);
            $table->decimal('amount_refunded', 12, 2)->default(0.00);    
            
            //-- Ledger Entries (JSONB for immutable transaction history)
            /*
                -- [
                -- { type: 'HOLD', amount: 500, status: 'SUCCESS', timestamp, gatewa
                -- { type: 'RELEASE', amount: 500, status: 'SUCCESS', timestamp, des
                -- { type: 'PARTIAL_REFUND', amount: 50, status: 'PENDING', timestam
                -- ]
             */
            // Ledger Entries (Event Sourcing via JSONB)
            // Ensures we can store complex arrays of objects immutably
            $table->jsonb('transactions');
            
            //--payout details
            $table->enum('owner_payout_status',[
                'PENDING', 'SCHEDULED', 'COMPLETED', 'FAILED', 'REVERSED'
            ])->nullable();
            $table->timestamp('owner_payout_at')->nullable();
            $table->string('owner_account_id', 255)->nullable(); //-- Mobile money / bank account reference
           
            //--status and life cycle
            $table->enum('status', [
                    'PENDING_PAYMENT', 'FUNDED', 'FUNDS_HELD', 'RELEASED', 'PARTIAL_RELEASE', 'DISPUTE_HOLD'
            ]);
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // -- Auto-refund if not captured within X days

            //--audit
            $table->timestamps();
            $table->softDeletes()->nullable();

            //-- Query Optimization Indexes
            // booking_id index is automatically created by the foreignUlid definition
            $table->index('transaction_id');
            $table->index('gateway_transaction_id');
            $table->index('status');
            $table->index('owner_payout_status');
        });

        DB::statement('
            ALTER TABLE escrow_ledgers
            ADD CONSTRAINT chk_amounts_non_negative
            CHECK(amount_held >= 0 AND amount_released >= 0 AND amount_refunded >= 0)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_ledgers');
    }
};
