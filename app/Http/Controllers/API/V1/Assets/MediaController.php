<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Assets;

use App\Domains\Assets\Actions\StoreAssetMediaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\StoreMediaRequest;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    public function __construct(
        private readonly StoreAssetMediaAction $storeAssetMediaAction,
    ) {}

    public function store(StoreMediaRequest $request, Asset $asset): JsonResponse
    {
        $mediaCollection = $this->storeAssetMediaAction->execute(
            files: $request->validated()['photos'],
            asset: $asset,
            owner: $request->user(),
            primaryIndex: $request->validated()['primary_index'] ?? 0,
        );

        return response()->json([
            'data' => $mediaCollection->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->public_url,

                'is_primary' => $media->is_primary,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size_bytes' => $media->file_size_bytes,

                'meta' => [
                    'uploaded_count' => $mediaCollection->count(),
                    'asset_id' => $asset->id,
                    'message' => $mediaCollection->count().' photo(s) uploaded successfully.',
                ],
            ]),
        ], 201);
    }
}
