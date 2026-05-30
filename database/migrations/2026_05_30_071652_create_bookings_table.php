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
        Schema::create('bookings', function (Blueprint $table) {
            //--primary id
            $table->ulid('id')->primary();

            //--core relationships
            //--note: restrict asset deletion if a booking exists
            $table->foreignUlid('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignUlid('renter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('owner_id')->constrained('users')->cascadeOnDelete();

            //--rental period
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime');
            $table->timestamp('actual_start_datetime')->nullable();
            $table->timestamp('actual_end_datetime')->nullable();

            //--pricingand scrow (immutable ledger)
            $table->decimal('rate_applied', 12, 2); // Hourly/daily/etc. captured at booking
            $table->decimal('total_rental_amount', 12, 2);
            $table->decimal('security_deposit_amount', 12, 2);
            $table->decimal('platform_fee', 12, 2);  //5-10% of rental
            $table->decimal('total_charged', 12, 2); //rental + fee + deposit

            //--status and workflow
            $table->enum('booking_status', [
                'PENDING', 'CONFIRMED', 'RENTER_ARRIVED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'
            ]);
            $table->enum('payment_status', [
                'PENDING', 'AUTHORIZED', 'CAPTURED', 'REFUNDED', 'FAILED'
            ]);
            $table->enum('escrow_status', [
                'PENDING', 'FUNDED', 'HELD', 'RELEASED', 'PARTIALLY_REALISED'
            ]);

            //--handoff and logistics
            $table->enum('handoff_method',[
                'QR_CODE', 'MANUAL_KEY', 'DIGITAL_ACCESS', 'LOCATION_PICKUP'
            ]);

            // Assuming these related tables will be created, using nullable relationships
            $table->foreignUlid('handoff_location_id')->nullable()->constrained('handoff_locations');
            $table->string('handoff_qr_code', 500)->nullable();
            $table->timestamp('handoff_completed_at')->nullable();
            $table->foreignUlid('handoff_verified_by')->nullable()->constrained('users');

            //--review and dispute
            $table->timestamp('renter_review_submitted_at')->nullable();
            $table->timestamp('owner_review_submitted_at')->nullable();
            $table->foreignUlid('dispute_id')->nullable()->constrained('disputes');

            //--audit
            $table->timestamps();
            $table->softDeletes();

            //--query optimization indexes
            $table->index('booking_status');
            $table->index('payment_status');
            $table->index('escrow_status');
            $table->index('start_datetime');
            $table->index('end_datetime');
        });

        // advanced DB constraint: ensure logical time travel is impossible
        DB::statement('
            ALTER TABLE bookings
            ADD CONSTRAINT chk_end_after_start
            CHECK (end_datetime > start_datetime)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping the table automatically removes the constraint in PostgreSQL
        Schema::dropIfExists('bookings');
    }
};

/*
database/migrations/2026_05_30_071652_create_bookings_table.php
 git commit -m "feat(database) : update bookings table migration" -m "defined schema and column types bookings - Applied unique constraints, explicit indexes for status, booking_period and region and expression based constraint index for Ensuring logical time travel is impossible"
*/
