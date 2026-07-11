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
        Schema::create('handoff_locations', function (Blueprint $table) {
            // --handoff_locations (QR-Based Pickup Points)
            // Primary Key
            $table->ulid('id')->primary();

            // Core Relationship
            $table->foreignUlid('asset_id')->constrained('assets')->cascadeOnDelete();

            // Location Identity
            $table->string('name', 255);
            $table->text('description')->nullable();

            // Geospatial
            // Defines a PostGIS geography point for accurate Earth-surface distance calculations
            $table->geography('location', subtype: 'point', srid: 4326);
            $table->string('region', 100);

            // Operational Details
            $table->jsonb('operating_hours_json')->nullable(); // --{ monday: { opens: '08:00', closes: '18:00' }, ... }
            $table->string('contact_person_name', 255)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 255)->nullable();

            // Handoff Capabilities (Hardware & Logistics Integration)
            $table->boolean('qr_checkpoint_enabled')->default(true);
            $table->boolean('thermal_imaging_enabled')->default(false);
            $table->string('access_code', 100)->nullable(); // -- For automated gates
            $table->boolean('parking_available')->default(true);

            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Query Optimization Indexes
            // Note: idx_asset_id is automatically created by foreignUlid()
            $table->spatialIndex('location'); // Creates the GiST index required for fast spatial queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handoff_locations');
    }
};
