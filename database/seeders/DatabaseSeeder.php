<?php

namespace Database\Seeders;

use App\Domains\Shared\Enums\User\AccountStatusEnum;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Why wrap the ENTIRE seeder in one transaction?
        // Same lesson as CreateBookingAction. If something throws
        // halfway through generating 500 bookings, you don't want
        // 230 orphaned half-correct rows sitting in your database.
        // Either the whole seed succeeds, or none of it does.
        DB::transaction(function () {

            // =====================
            // YOUR REAL ADMIN USER — unchanged
            // =====================
            $adminUser = User::factory()->create([
                'email'             => 'dawud2147@gmail.com',
                'phone_number'      => '0970706318',
                'password'          => 'XT97qcHw4mSM3rw',
                'fayda_id'          => '810583097316',
                'first_name'        => 'Admin',
                'last_name'         => 'User',
                'is_verified'       => true,
                'account_status'    => AccountStatusEnum::ACTIVE,
                'kyc_tier'          => 3,
                'total_trust_score' => 95,
            ]);

            // =====================
            // STEP 1 — USERS (100 total, including admin)
            // =====================
            // Why create() and not make()?
            // create() persists to the database immediately.
            // make() only builds in-memory objects. We need real
            // database rows so Asset and Booking foreign keys
            // actually resolve.
            $users = User::factory()->count(99)->create();

            // Why merge admin back into the collection?
            // Some assets/bookings should involve the admin too,
            // so you have a known login to test with that also
            // has real activity, not just an empty account.
            $allUsers = $users->push($adminUser);

            $this->command->info('✓ 100 users created.');

            // =====================
            // STEP 2 — ASSETS (1000 total)
            // =====================
            // Why loop ownerships instead of one giant factory call?
            // Asset::factory()->count(1000)->create() would create
            // 1000 BRAND NEW fake owners (1000 extra users!) because
            // owner_id defaults to User::factory(). We want assets
            // OWNED BY our existing 100 users instead, distributed
            // realistically — some owners have many assets, some have
            // one or none, like a real marketplace.
            $assets = collect();

            $allUsers->each(function (User $owner) use (&$assets) {
                // Why randomNumberBetween(0, 20) per user?
                // Real platforms have power-sellers (owners with many
                // listings) and casual owners (1-2 listings). Random
                // count per user produces that natural distribution
                // instead of an artificial even split.
                $count = fake()->numberBetween(0, 20);

                if ($count > 0) {
                    $assets = $assets->merge(
                        Asset::factory()
                            ->count($count)
                            ->ownedBy($owner)
                            ->create()
                    );
                }
            });

            // Why a top-up loop after the per-user pass?
            // If the random distribution above lands short of 1000
            // (likely, since it's randomized), this tops up the
            // remainder attached to random existing owners so you
            // reliably end up with ~1000 total assets.
            $remaining = max(0, 1000 - $assets->count());
            if ($remaining > 0) {
                $topUp = Asset::factory()
                    ->count($remaining)
                    ->state(fn () => ['owner_id' => $allUsers->random()->id])
                    ->create();
                $assets = $assets->merge($topUp);
            }

            $this->command->info("✓ {$assets->count()} assets created.");
            // Why loop $assets here instead of doing it inside AssetFactory itself?
            // Media needs a real, already-persisted Asset id to attach to via
            // mediable_id. AssetFactory runs BEFORE the asset has an id assigned
            // in some Eloquent factory flows, and more importantly, keeping photo
            // generation as a separate explicit step (not buried inside another
            // factory) makes it easy to skip, adjust, or remove without touching
            // AssetFactory at all. Separation of concerns, same as Booking's
            // asRenter()/asOwner() being separate from definition().
            $assets->each(function (Asset $asset) {
                \App\Models\Media::factory()
                    ->forAsset($asset)
                    ->create();
            });

            $this->command->info("✓ {$assets->count()} photos attached.");
            // Why only ACTIVE assets for booking?
            // Your own CreateBookingAction guard rejects non-ACTIVE
            // assets. Generating bookings against DRAFT/PAUSED assets
            // would create fake data that your real validation would
            // never allow — meaningless test data.
            $bookableAssets = $assets->filter(
                fn (Asset $asset) => $asset->status->value === 'ACTIVE'
            );

            // =====================
            // STEP 3 — BOOKINGS (500+, weighted by status)
            // =====================
            // Why these specific ratios?
            // A healthy, mature marketplace has MOST history as
            // COMPLETED, a meaningful chunk CONFIRMED/IN_PROGRESS
            // (active pipeline), some PENDING (recent activity),
            // and few CANCELLED. Even splits across all 6 statuses
            // would look nothing like a real platform.
            $statusDistribution = [
                'completed'     => 220, // 44%
                'confirmed'     => 90,  // 18%
                'pending'       => 75,  // 15%
                'inProgress'    => 50,  // 10%
                'renterArrived' => 35,  // 7%
                'cancelled'     => 30,  // 6%
            ];
            // Total = 500

            $bookingsCreated = 0;

            foreach ($statusDistribution as $state => $count) {
                for ($i = 0; $i < $count; $i++) {

                    // Why pick asset/renter/owner manually instead of
                    // letting factory defaults handle it?
                    // This is the ONE guard rule that must never break:
                    // renter_id must never equal owner_id. We loop until
                    // we get a renter who is genuinely not the owner.
                    $asset = $bookableAssets->random();
                    $owner = $asset->owner;

                    do {
                        $renter = $allUsers->random();
                    } while ($renter->id === $owner->id);

                    Booking::factory()
                        ->{$state}()
                        ->state([
                            'asset_id'  => $asset->id,
                            'owner_id'  => $owner->id,
                            'renter_id' => $renter->id,
                        ])
                        ->create();

                    $bookingsCreated++;
                }
            }

            $this->command->info("✓ {$bookingsCreated} bookings created.");
            $this->command->info('Seeding complete.');
        });
    }
}