<?php

use App\Http\Controllers\API\V1\Bookings\BookingController;
use App\Http\Controllers\API\V1\Users\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('api.bookings.store');

    Route::get('/bookings/my-bookings', [BookingController::class, 'myBookings'])
        ->name('api.bookings.my');

    Route::get('/bookings/my-asset-bookings', [BookingController::class, 'myAssetBookings'])
        ->name('api.bookings.my-assets');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('api.bookings.show');

    Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])
        ->name('api.bookings.confirm');

    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('api.bookings.cancel');

    Route::patch('/bookings/{booking}/arrive', [BookingController::class, 'confirmArrival'])
        ->name('api.bookings.arrive');

    Route::patch('/bookings/{booking}/handoff', [BookingController::class, 'completeHandoff'])
        ->name('api.bookings.handoff');

    Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])
        ->name('api.bookings.complete');

    Route::get('/reviews', [ReviewController::class, 'index'])
        ->name('api.reviews.index');

    Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])
        ->name('api.bookings.reviews.store');

    Route::post('/bookings/{booking}/authorize-escrow', [BookingController::class, 'authorizeEscrow'])
        ->name('api.bookings.authorize-escrow');

    Route::post('/assets/{asset}/bookings', [BookingController::class, 'store'])
        ->name('api.bookings.store.asset');
});