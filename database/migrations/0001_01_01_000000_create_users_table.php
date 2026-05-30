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
        Schema::create('users', function (Blueprint $table) {
            //--Primary key
            $table->ulid('id')->primary();

            //--authentication
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 20)->unique();
            $table->string('password', 255);
            $table->rememberToken();
            
            //--fayda or national id intergration
            $table->string('fayda_id', 50)->unique()->nullable();
            $table->timestamp('fayda_verified_at')->nullable();
              // 1 = Phone
              // 2 = Fayda
              // 3 = Enhanced Business
            $table->smallInteger('kyc_tier')->default(1);
            $table->timestamp('kyc_tier_verified_at')->nullable();
            /*
              Example JSONB:{
                 "verified_by": "admin",
                 "verification_date": "2026-01-01",
                 "risk_score": 80,
                 "compliance_flags":
              }
             */
            $table->jsonb('kyc_metadata');

            //--Profile
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('business_name', 255)->nullable();
            $table->string('business_registration_number', 100)->nullable();
            $table->enum('business_type',[
                'INDIVIDUAL',
                'SME',
                'CORPORATIVE',
                'COOPERATIVE'
            ]);

            //--contact and location
            $table->string('country_region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('postal_code', 20)->nullable();

            //postgis geography (requires postgresql + postgis extension)
            $table->geography('location', subtype: 'point', srid: 4326)->nullable();

            //--trust and compliance
            $table->enum('account_status',[
                'ACTIVE', 
                'SUSPENDED',
                'BANNED',
                'PENDING_VERIFICATION'
            ]);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_two_factor_enabled')->default(false);
            $table->decimal('total_trust_score',5,2)->default(0.00);
            $table->timestamp('trust_score_updated_at')->nullable();

            //--audit
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();

            //--custom indexes
            $table->index('kyc_tier');
            $table->index('account_status');

            //--gist index for geography column
            $table->spatialIndex('location');

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
            /*
            CREATE EXTENSION IF NOT EXISTS postgis;
                        ERROR:  extension "postgis" is not available
                        HINT:  The extension must first be installed on the system where PostgreSQL is running. 

                        SQL state: 0A000
            */