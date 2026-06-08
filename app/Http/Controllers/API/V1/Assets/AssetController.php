<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Assets;

use App\Domains\Assets\Actions\CreateAssetAction;
use App\Domains\Assets\Actions\PublishAssetAction;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\CreateAssetRequest;
use App\Http\Resources\Assets\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;   // ← correct import, capital J

class AssetController extends Controller
{
    public function __construct(
        private readonly CreateAssetAction $createAssetAction,
        private readonly PublishAssetAction $publishAssetAction,
    ) {}

    public function store(CreateAssetRequest $request): JsonResponse  // ← capital J
    {
        $asset = $this->createAssetAction->execute(
            data:  $request->validated(),
            owner: $request->user(),
        );

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(201);
    }

    public function publish(Asset $asset): JsonResponse{
        $this->authorize('update', $asset);
        $publishedAsset = $this->publishAssetAction->execute(
            asset: $asset,
            owner: request()->user(),
        );

        return (new AssetResource($publishedAsset))
            ->response()
            ->setStatusCode(200);
        // 200 = something happened successfully to an existing resource.
        // 201 = a brand new resource was created.    
    }

    public function show(Asset $asset): JsonResponse{
        $viewer = request()->user();
        $isowner = $viewer && $viewer->id === $asset->owner_id;

        if($asset->status === StatusEnum::DRAFT && !$isowner){
            abort(404);
        // Why not abort(403)? Because 403 tells the renter:
        // "this asset exists but you can't see it."
        // 404 tells them: "nothing here." Safer. Reveals less.
        }

        $asset->loadMissing([
            'media' => fn($query) 
                    => $query->where('purpose', MediaPurpose::ASSET_PHOTO)
                             ->orderBy('is_primary', 'desc')
                             ->orderBy('created_at', 'desc'),
            // TODO: Phase 2 — also load owner's trust score and review summary:
            // 'owner.trustScore', 'reviews'

            'owner',
        ]);
        return (new AssetResource ($asset))
               ->response()
               ->setStatusCode(200);
    }

    public function myAssets(): JsonResponse{
        $owner = request()->user();
        $status = request()->query('status');
        $perPage = (int) request()->query('per_Page', 15);
        $perPage = min($perPage, 50);

        $assets = Asset::query()->where('owner_id', $owner->id)
                                ->when($status, function ($query) use ($status){
                                    $query->where('status', $status);
                                })
                                // TODO: Phase 2 — validate $status against StatusEnum values
                                // to prevent ?status=HACKED from hitting the database.
                                // Add a StatusEnum::tryFrom($status) check before the query.
                                ->withCount('media')
                                ->with(['media' => fn($query) => $query
                                    ->where('purpose', MediaPurpose::ASSET_PHOTO)
                                    ->where('is_primary', true)
                                    ->limit(1)
                                ])
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage);
        return response()->json([
            'data' => AssetResource::collection($assets),
            'meta' => [
                'total'        => $assets->total(),
                'per_page'     => $assets->perPage(),
                'current_page' => $assets->currentPage(),
                'last_page'    => $assets->lastPage(),
                'has_more'     => $assets->hasMorePages(),
                'status_filter' => $status ?? 'all',
            ],
        ], 200);                        
    }
}