<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1\Assets;

use App\Domains\Assets\Actions\CreateAssetAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\CreateAssetRequest;
use App\Http\Resources\Assets\AssetResource;
use Illuminate\Http\JsonResponse;   // ← correct import, capital J

class AssetController extends Controller
{
    public function __construct(
        private readonly CreateAssetAction $createAssetAction,
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
}