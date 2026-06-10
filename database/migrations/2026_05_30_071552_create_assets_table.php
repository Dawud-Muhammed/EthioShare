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
        Schema::create('assets', function (Blueprint $table) {
            //--primary key (ULID)
            $table->ulid('id')->primary();
            
            //--foraign key to users table (ULID)
            $table->foreignUlid('owner_id')->constrained('users')->cascadeOnDelete();

            //--asset identity
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('asset_type', [
                'MACHINERY', 'VEHICLE', 'EQUIPMENT', 'TOOLS', 'AGRICULTURAL', 'CONSTRUCTION', 'REAL_ESTATE'
            ]);
            $table->enum('condition', [
                'NEW', 'LIKE_NEW', 'GOOD', 'FAIR', 'NEEDS_REPAIR'
            ]);

            //--valuation and availability
            $table->decimal('hourly_rate', 12, 2);
            $table->decimal('daily_rate', 12, 2);
            $table->decimal('weekly_rate', 12, 2)->nullable();
            $table->decimal('monthly_rate', 12, 2)->nullable();
            $table->decimal('security_deposit', 12, 2);
            $table->decimal('estimated_value', 12, 2);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();

            //geospatial and logistics
            $table->geography('location', subtype: 'point', srid: 4326)->nullable();
            $table->string('region', 100);
            $table->decimal('service_radius_km', 5, 2)->default(50.00);
            $table->enum('delivery_method', [
                'SELF_TRANSPORT', 'PLATFORM_TRANSPORT', 'BUYER_PICKUP'
            ]);

            //--specifications
            $table->jsonb('specifications')->nullable();
            $table->jsonb('features')->nullable();

            //--status and lifecycle
            $table->enum('status', [
                'DRAFT', 'ACTIVE', 'RENTED', 'PAUSED', 'DELISTED', 'ARCHIVED'
            ]);
            $table->timestamp('status_updated_at')->nullable();
            $table->enum('visibility', [
                'PUBLIC', 'PRIVATE', 'REGION_RESTRICTED', 'PARTNER_ONLY'
            ]);

            //--trust and quality
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_reviews')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->bigInteger('total_rental_hours')->default(0);

            //--audit trials (handels created_at, updated_at and deleted_at)
            $table->timestamps();
            $table->softDeletes();

            //--standard indexes
            $table->index('status');
            $table->index('asset_type');
            $table->index('region');

           //--standard spatial index for exact location
            $table->spatialIndex('location');
        });

        //--advanced calculated spatial index
        //--laravel's schema builder cannot natively handle expression-based gist indexes, so i execute the raw postgresql command immediately after table creation

        DB::statement('
            CREATE INDEX idx_service_radius_search
            ON assets
            USING GIST (geography(ST_Buffer(location::geometry, service_radius_km * 1000 / 111000.0)))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //--drop the custom index before dropping the table to prevent lock issues
        DB::statement('DROP INDEX IF EXISTS idx_service_radius_search');
        Schema::dropIfExists('assets');
    }
};