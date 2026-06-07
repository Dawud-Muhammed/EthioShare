<?php

use App\Http\Controllers\API\V1\Assets\AssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    // ↑ auth:sanctum means: "every route in this group requires
    // a valid Sanctum token in the Authorization header."
    // If no token → 401 Unauthorized, controller never runs.
    ->group(function () {

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
        });
    });