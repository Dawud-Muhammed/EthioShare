<?php

use App\Http\Controllers\API\V1\Assets\AssetController;
use App\Http\Controllers\API\V1\Assets\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    // ↑ auth:sanctum means: "every route in this group requires
    // a valid Sanctum token in the Authorization header."
    // If no token → 401 Unauthorized, controller never runs.
    ->group(function () {

        // Owner dashboard — must come BEFORE /{asset} routes
        Route::get('/my/assets', [AssetController::class, 'myAssets']);
        // ↑ Why must this come BEFORE /{asset}?
        // Laravel matches routes top to bottom.
        // If /{asset} comes first, Laravel tries to find an Asset
        // with ID "my" — which doesn't exist → 404.
        // By putting /my/assets first, Laravel matches it correctly
        // before even looking at /{asset}.
        // Route ORDER matters. This is a very common Laravel bug.

        Route::prefix('assets')->group(function () {

            Route::post('/', [AssetController::class, 'store']);
            // ↑ POST /api/v1/assets → AssetController@store
            //
            // [AssetController::class, 'store'] is the modern Laravel way.
            // AssetController::class = 'App\Http\Controllers\API\V1\Assets\AssetController'
            // 'store' = the method name to call.
            // This is refactor-safe: if you rename the class, your IDE updates this automatically.

            // TODO: Add these as you build them:
            // Route::get('/',    [AssetController::class, 'index']);   // list my assets
            // Route::get('/{id}', [AssetController::class, 'show']);    // view one asset
            // Route::put('/{id}', [AssetController::class, 'update']);  // update asset
            // Route::delete('/{id}', [AssetController::class, 'destroy']); // delete asset

            Route::get('/{asset}', [AssetController::class, 'show']);
            // ↑ GET /api/v1/assets/{asset}
            // GET = read only. No body. No side effects.
            // The auth:sanctum middleware is already on the group,
            // so this requires a valid token.
            //
            // TODO: Phase 2 — make this public for browsing guests.
            // Remove from the sanctum group and create a separate public group:
            // Route::middleware('api')->get('/{asset}', ...)

            Route::post('/{asset}/media', [MediaController::class, 'store']);
            // ↑ POST /api/v1/assets/{asset}/media
            // {asset} is the ULID of the asset.
            // Laravel reads the Asset model automatically via Route Model Binding.
            // Add the import at the top of the file:
            // use App\Http\Controllers\API\V1\Assets\MediaController;

            Route::patch('/{asset}/publish', [AssetController::class, 'publish']);
            // ↑ PATCH not PUT.
            // PUT   = replace the entire resource with new data
            // PATCH = change one specific thing about the resource
            // We are only changing the status field. PATCH is semantically correct.
            // URL: PATCH /api/v1/assets/{asset}/publish
           Route::put('/{asset}', [AssetController::class, 'update']);
            // ↑ PUT /api/v1/assets/{asset}
            // Technically PATCH is more correct for partial updates.
            // But PUT is more universally understood by frontend developers.
            // Both work identically in Laravel.
            // TODO: Phase 2 — consider supporting both:
            // Route::match(['put', 'patch'], '/{asset}', ...)
          Route::patch('/{asset}/status', [AssetController::class, 'updateStatus']);
            // ↑ PATCH /api/v1/assets/{asset}/status
            // PATCH because we are changing one specific aspect of the asset.
            // The URL says exactly what is changing: the status.
            // Clean, readable, self-documenting API design.
          Route::get('/', [AssetController::class, 'index']);
            // ↑ GET /api/v1/assets
            // This sits INSIDE the auth:sanctum middleware group for now.
            // TODO: Phase 2 — move to a public route group (no auth required).
            // For now, browsing requires a token. Acceptable for Phase 1.
        });
    });