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
        Schema::create('reviews', function (Blueprint $table) {
            // --reviews = polymorphic rating system
            // --primary key
            $table->ulid('id')->primary();

            // --polymorphic relationships
            // --Creates `reviewable_type`, `reviewable_id` and the composite index
            $table->ulidMorphs('reviewable');

            // --reviewer and context
            $table->foreignUlid('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('booking_id')->nullable()->constrained('bookings'); // --Assuming nullable so a review isn't strictly destroyed if a booking record is purged

            // --rating and feedback
            $table->unsignedTinyInteger('rating');
            $table->string('title', 255)->nullable();
            $table->text('comment')->nullable();

            // --Detailed Scores (for users as renters/owners)
            $table->unsignedTinyInteger('cleanliness_rating')->nullable();
            $table->unsignedTinyInteger('punctuality_rating')->nullable();
            $table->unsignedTinyInteger('communication_rating')->nullable();
            $table->unsignedTinyInteger('condition_upon_return_rating')->nullable();
            $table->unsignedTinyInteger('value_for_money_rating')->nullable();

            // --response and engagement
            $table->foreignUlid('response_from_reviewee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('response_text')->nullable();
            $table->timestamp('responded_at')->nullable();

            // --moderation
            $table->boolean('is_verified_booking')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();

            // audit
            $table->timestamps();
            $table->softDeletes();

            // indexes
            // Note: idx_reviewable, reviewer_id, and booking_id are handled by their respective helpers
            $table->index('created_at');
        });

        // db st

        // rating and feedback
        DB::statement('
            ALTER TABLE reviews 
            ADD CONSTRAINT chk_rating CHECK (rating >= 1 AND rating <= 5),
            ADD CONSTRAINT chk_cleanliness CHECK (cleanliness_rating IS NULL OR (cleanliness_rating >= 1 AND cleanliness_rating <= 5)),
            ADD CONSTRAINT chk_punctuality CHECK (punctuality_rating IS NULL OR (punctuality_rating >= 1 AND punctuality_rating <= 5)),
            ADD CONSTRAINT chk_communication CHECK (communication_rating IS NULL OR (communication_rating >= 1 AND communication_rating <= 5)),
            ADD CONSTRAINT chk_condition CHECK (condition_upon_return_rating IS NULL OR (condition_upon_return_rating >= 1 AND condition_upon_return_rating <= 5)),
            ADD CONSTRAINT chk_value CHECK (value_for_money_rating IS NULL OR (value_for_money_rating >= 1 AND value_for_money_rating <= 5))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
