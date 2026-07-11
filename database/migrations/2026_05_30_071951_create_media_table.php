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
        Schema::create('media', function (Blueprint $table) {
            // Primary Key
            $table->ulid('id')->primary();

            $table->enum('media_type', [
                'PHOTO', 'VIDEO', 'DOCUMENT', 'QR_CODE', 'THERMAL_IMAGE',
            ]);

            // Polymorphic Relationship (Laravel Magic -- follows laravel conventions)
            // This single line creates `mediable_type` (VARCHAR) and `mediable_id` (ULID)
            // AND automatically creates the composite index `idx_mediable`.
            $table->ulidMorphs('mediable'); // -- 'User', 'Asset', 'Booking', 'Dispute'

            // File Metadata
            $table->string('file_name', 255);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size_bytes');
            $table->string('file_hash', 64)->nullable(); // -- SHA-256 for integrity

            // Storage & CDN
            $table->string('disk_name', 100); // -- 's3', 'local', 'gcs'
            $table->string('disk_path', 500);
            $table->string('cdn_url', 500)->nullable();

            // Purpose & Categorization
            $table->enum('purpose', [
                'KYC_VERIFICATION', 'ASSET_PHOTO', 'ASSET_INSPECTION',
                'BOOKING_HANDOFF', 'DISPUTE_EVIDENCE', 'OTHER',
            ]);
            $table->text('upload_reason')->nullable();
            $table->boolean('is_primary')->default(false); // -- For assets: primary photo

            // Compliance
            $table->enum('virus_scan_status', [
                'PENDING', 'PASSED', 'FLAGGED',
            ])->nullable();
            $table->enum('content_moderation_status', [
                'PENDING', 'APPROVED', 'REJECTED',
            ])->nullable();

            // Encryption
            $table->boolean('is_encrypted')->default(true);
            $table->string('encryption_key_id', 100)->nullable(); // -- KMS reference

            // Audit
            $table->foreignUlid('uploaded_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Query Optimization Indexes
            // Note: idx_mediable is handled by ulidMorphs()
            // Note: idx_uploaded_by_id is handled by foreignUlid()
            $table->index('purpose');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
