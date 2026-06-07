<?php
declare(strict_types = 1);

namespace App\Domains\Assets\Actions;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class CreateAssetAction{
    public function execute(array $data, User $owner):Asset{
        return DB::transaction(function () use($data,$owner){
            $asset = Asset::create([
                'owner_id'    => $owner->id,
                'title'       => $data['title'],
                'description' => $data['description']?? null,

                'asset_type'       => $data['asset_type'],
                'condition'        => $data['condition'],

                'hourly_rate'      => $data['hourly_rate'],
                'daily_rate'       => $data['daily_rate'],
                'weekly_rate'      => $data['weekly_rate'] ?? null,
                'monthly_rate'     => $data['monthly_rate'] ?? null,
                'security_deposit' => $data['security_deposit'],
                'estimated_value'  => $data['estimated_value'],

                'available_from'   => $data['available_from'] ?? null,
                'available_until'  => $data['available_until'] ?? null,

                'region'           => $data['region'],
                'address_line'     => $data['address_line'],
                'location'         => null,
                // ↑ PostGIS POINT column. NULL for now.
                // TODO: Phase 2 — geocode $data['address_line'] + $data['region']
                // into a PostGIS POINT using a GeospatialService:
                // 'location' => $this->geospatialService->geocode(
                //     $data['address_line'], $data['region']
                // ),

                'delivery_method'   => $data['delivery_method'] ?? null,
                'service_radius_km' => $data['service_radius_km'] ?? 50,
                // ↑ If the owner didn't send a radius, default to 50km.
                // This matches your migration's DEFAULT 50.
                // Two layers of default: migration (DB level) + here (app level).

                'specifications'   => $data['specifications'] ?? null,
                'features'         => $data['features'] ?? null,
                // ↑ JSONB columns. Eloquent handles array → JSON conversion
                // automatically because your Asset model casts these as 'array'.

                'visibility'       => $data['visibility'] ?? 'PUBLIC',
                // ↑ Default to PUBLIC if not specified.

                // --- Status: always starts as DRAFT ---
                'status'           => 'DRAFT',
                // ↑ An asset is never immediately ACTIVE.
                // Owner must add photos, then publish it.
                // This is a business rule. It lives HERE in the action,
                // not in the request (which only validates input)
                // and not in the controller (which only coordinates).
                // Business rules belong in actions.

                'status_updated_at' => now(),
            ]);

            // TODO: Phase 2 — dispatch AssetCreated event here:
            // event(new \App\Domains\Assets\Events\AssetCreated($asset, $owner));
            // This will trigger listeners: UpdateSearchIndex, NotifyAdminNewAsset, etc.

            return $asset;
            // The controller receives this and passes it to AssetResource.

        });
    }
}