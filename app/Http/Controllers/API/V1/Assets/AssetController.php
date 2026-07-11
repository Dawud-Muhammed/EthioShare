<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Assets;

use App\Domains\Assets\Actions\CreateAssetAction;
use App\Domains\Assets\Actions\PublishAssetAction;
use App\Domains\Assets\Actions\UpdateAssetAction;
use App\Domains\Assets\Actions\UpdateAssetStatusAction;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\CreateAssetRequest;
use App\Http\Requests\Assets\SearchAssetsRequest;
use App\Http\Requests\Assets\UpdateAssetRequest;
use App\Http\Requests\Assets\UpdateAssetStatusRequest;
use App\Http\Resources\Assets\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;

class AssetController extends Controller
{
    public function __construct(
        private readonly CreateAssetAction $createAssetAction,
        private readonly PublishAssetAction $publishAssetAction,
        private readonly UpdateAssetAction $updateAssetAction,
        private readonly UpdateAssetStatusAction $updateAssetStatusAction,
    ) {}

    public function store(CreateAssetRequest $request): JsonResponse
    {
        $asset = $this->createAssetAction->execute(
            data: $request->validated(),
            owner: $request->user(),
        );

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(201);
    }

    public function publish(Asset $asset): JsonResponse
    {
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

    public function show(Asset $asset): JsonResponse
    {
        $viewer = request()->user();
        $isowner = $viewer && $viewer->id === $asset->owner_id;

        if ($asset->status === StatusEnum::DRAFT && ! $isowner) {
            abort(404);
            // Why not abort(403)? Because 403 tells the renter:
            // "this asset exists but you can't see it."
            // 404 tells them: "nothing here." Safer. Reveals less.
        }

        $asset->loadMissing([
            'media' => fn ($query) => $query->where('purpose', MediaPurpose::ASSET_PHOTO)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc'),
            // TODO: Phase 2 — also load owner's trust score and review summary:
            // 'owner.trustScore', 'reviews'

            'owner',
        ]);

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(200);
    }

    public function myAssets(): JsonResponse
    {
        $owner = request()->user();
        $status = request()->query('status');
        $perPage = (int) request()->query('per_Page', '15');
        $perPage = min($perPage, 50);

        $assets = Asset::query()->where('owner_id', $owner->id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
                                // TODO: Phase 2 — validate $status against StatusEnum values
                                // to prevent ?status=HACKED from hitting the database.
                                // Add a StatusEnum::tryFrom($status) check before the query.
            ->withCount('media')
            ->with(['media' => fn ($query) => $query
                ->where('purpose', MediaPurpose::ASSET_PHOTO)
                ->where('is_primary', true)
                ->limit(1),
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => AssetResource::collection($assets),
            'meta' => [
                'total' => $assets->total(),
                'per_page' => $assets->perPage(),
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'has_more' => $assets->hasMorePages(),
                'status_filter' => $status ?? 'all',
            ],
        ], 200);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $updatedAsset = $this->updateAssetAction->execute(
            data: $request->validated(),
            asset: $asset,
            owner: $request->user(),
        );

        return (new AssetResource($updatedAsset))->response()->setStatusCode(200);
    }

    public function updateStatus(UpdateAssetStatusRequest $request, Asset $asset): JsonResponse
    {
        $validated = $request->validated();

        $updatedAsset = $this->updateAssetStatusAction->execute(
            newStatus: $validated['status'],
            asset: $asset,
            owner: $request->user(),
            reason: $validated['reason'] ?? null,
        );

        return (new AssetResource($updatedAsset))
            ->response()
            ->setStatusCode(200);
    }

    public function index(SearchAssetsRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $perPage = (int) ($filters['per_page'] ?? 15);

        $assets = Asset::query()
            ->where('status', StatusEnum::ACTIVE->value)
            ->when(isset($filters['region']), function ($query) use ($filters) {
                $query->where('region', $filters['region']);
            })
            ->when(isset($filters['asset_type']), function ($query) use ($filters) {
                $query->where('asset_type', $filters['asset_type']);
            })
            ->when(isset($filters['condition']), function ($query) use ($filters) {
                $query->where('condition', $filters['condition']);
            })
            ->when(isset($filters['delivery_method']), function ($query) use ($filters) {
                $query->where('delivery_method', $filters['delivery_method']);
            })
            ->when(isset($filters['min_price']), function ($query) use ($filters) {
                $query->where('daily_rate', '>=', $filters['min_price']);
            })
            ->when(isset($filters['max_price']), function ($query) use ($filters) {
                $query->where('daily_rate', '<=', $filters['max_price']);
            })
            ->with([
                'media' => fn ($query) => $query
                    ->where('purpose', MediaPurpose::ASSET_PHOTO)
                    ->where('is_primary', true)
                    ->limit(1),
                'owner:id,first_name,last_name',
            ])
            ->withCount('media')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => AssetResource::collection($assets),
            'meta' => [
                'total' => $assets->total(),
                'per_page' => $assets->perPage(),
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'has_more' => $assets->hasMorePages(),
                'filters_applied' => array_filter($filters, function ($value, $key) {
                    return $key !== 'per_page' && ! is_null($value);
                }, ARRAY_FILTER_USE_BOTH),
            ],
        ], 200);

    }
}
