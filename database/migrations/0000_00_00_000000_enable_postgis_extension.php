<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Activate PostGIS for all tables to use geography types
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Drop the extension if you want a complete wipe.
        // Usually left empty in production to protect spatial data types,
        // but safe for local development:
        DB::statement('DROP EXTENSION IF EXISTS postgis CASCADE;');
    }
};
