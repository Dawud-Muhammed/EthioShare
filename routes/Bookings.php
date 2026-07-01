<?php

use App\Http\Controllers\API\V1\Users\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Bookings\BookingController;

Route::middleware('auth:sanctum')->group(function () {

    // =============================================
    // COLLECTION ROUTES
    // Literal string routes MUST come before {booking}
    // wildcard. If {booking} is registered first, Laravel
    // tries to find a Booking with id "my-bookings" → 404.
    // =============================================

    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/my-bookings', [BookingController::class, 'myBookings'])
        ->name('bookings.my');

    Route::get('/bookings/my-asset-bookings', [BookingController::class, 'myAssetBookings'])
        ->name('bookings.my-assets');

    // =============================================
    // INSTANCE ROUTES
    // {booking} is resolved automatically by Laravel
    // via route model binding. Missing booking → 404.
    // =============================================

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');

    // =============================================
    // STATE TRANSITION ROUTES
    // All PATCH — partial update, not full replacement.
    // =============================================

    Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])
        ->name('bookings.confirm');

    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');

    Route::patch('/bookings/{booking}/arrive', [BookingController::class, 'confirmArrival'])
        ->name('bookings.arrive');

    Route::patch('/bookings/{booking}/handoff', [BookingController::class, 'completeHandoff'])
        ->name('bookings.handoff');

    Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])
        ->name('bookings.complete');
    
    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])
        ->name('api.reviews.index');

    Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])
        ->name('bookings.reviews.store');
    // =============================================
    // ASSET-SCOPED BOOKING CREATION
    // Used by the "Book this asset" button on the
    // asset detail page. Asset context in the URL
    // gives route model binding for free.
    // =============================================

    Route::post('/assets/{asset}/bookings', [BookingController::class, 'store'])
        ->name('bookings.store.asset');
});