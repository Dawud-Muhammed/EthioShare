<?php

namespace Database\Seeders;

use App\Domains\Shared\Enums\User\AccountStatusEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================
        // ADMIN / TEST USER
        // =====================
        $adminUser = User::factory()->create([
            'email' => 'dawud2147@gmail.com',
            'phone_number' => '0970706318',
            'password' => 'XT97qcHw4mSM3rw',
            'fayda_id' => '810583097316',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'is_verified' => true,
            'account_status' => AccountStatusEnum::ACTIVE,
            'kyc_tier' => 3,
            'total_trust_score' => 95,
        ]);
    }
}