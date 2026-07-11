<?php

namespace Database\Factories;

use App\Domains\Shared\Enums\Media\ContentModerationStatus;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Domains\Shared\Enums\Media\MediaType;
use App\Domains\Shared\Enums\Media\VirusScanStatus;
use App\Models\Asset;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        // Why picsum.photos and not a real upload?
        // It's a free placeholder image service that returns a real,
        // different-looking photo for every unique seed number in the
        // URL. /seed/{anything}/800/600 means the same seed always
        // returns the same image — useful so re-running the seeder
        // produces visually consistent results, not pure random noise
        // every time.
        $seed = fake()->unique()->numberBetween(1, 100000);

        return [
            'media_type' => MediaType::PHOTO,

            // Why no mediable_type/mediable_id here?
            // Polymorphic relations are set by the SEEDER, not the
            // factory's definition(). The factory doesn't know in
            // advance which Asset it will attach to — that's supplied
            // explicitly when we call the factory (see DatabaseSeeder
            // changes below). Leaving these out of definition() and
            // setting them via ->state() at call time is the correct
            // pattern, same as owner_id on AssetFactory.

            'file_name' => "asset-photo-{$seed}.jpg",
            'mime_type' => 'image/jpeg',
            'file_size_bytes' => fake()->numberBetween(80_000, 900_000),
            'file_hash' => hash('sha256', (string) $seed),

            // Why disk_name and disk_path still get realistic-looking
            // values even though we're not storing a real file?
            // is_encrypted, disk_name etc. are NOT NULL columns per
            // your migration. We give them plausible placeholder
            // values so the schema constraints are satisfied, even
            // though publicUrl() will never actually resolve through
            // Storage::disk() because cdn_url is always set below.
            'disk_name' => 'local',
            'disk_path' => "assets/seeded/{$seed}.jpg",

            // Why THIS is the field that actually matters.
            // Your Media model's publicUrl() accessor checks cdn_url
            // FIRST and returns it immediately if present — it never
            // even touches Storage::disk() in that case. Pointing
            // cdn_url at picsum.photos means every seeded asset shows
            // a real, different-looking photo in your UI with zero
            // real file uploads.
            'cdn_url' => "https://picsum.photos/seed/{$seed}/800/600",

            'purpose' => MediaPurpose::ASSET_PHOTO,
            'upload_reason' => null,
            'is_primary' => true,

            // Why PASSED/APPROVED and not PENDING?
            // Your scopeSafeForPublic() filters on exactly these two
            // values. If seeded photos sit at PENDING, any view using
            // that scope would show zero photos despite 669 assets
            // having media rows — invisible, confusing bug. Seeding
            // them as already-verified matches what real photos would
            // look like after passing moderation.
            'virus_scan_status' => VirusScanStatus::PASSED,
            'content_moderation_status' => ContentModerationStatus::APPROVED,

            'is_encrypted' => false,
            'encryption_key_id' => null,

            'uploaded_by_id' => User::factory(),
        ];
    }

    /**
     * Why a dedicated state for "attach to this asset"?
     * Same pattern as Asset::factory()->ownedBy($owner) and
     * Booking::factory()->asRenter($user). Reads cleanly at the
     * call site: Media::factory()->forAsset($asset)->create().
     */
    public function forAsset(Asset $asset): static
    {
        return $this->state(fn (array $attributes) => [
            'mediable_type' => Asset::class,
            'mediable_id' => $asset->id,
            'uploaded_by_id' => $asset->owner_id,
        ]);
    }
}
