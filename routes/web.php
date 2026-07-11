<?php

use App\Http\Controllers\API\V1\Bookings\ChapaCallbackController;
use App\Http\Controllers\API\V1\Bookings\ChapaWebhookController;
use App\Livewire\Assets\Browse;
use App\Livewire\Assets\Create;
use App\Livewire\Assets\Edit;
use App\Livewire\Assets\Index;
use App\Livewire\Assets\Show;
use App\Livewire\Bookings\BookingCreate;
use App\Livewire\Bookings\BookingIndex;
use App\Livewire\Bookings\BookingShow;
use App\Livewire\Bookings\ReviewIndex;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// =====================================================
// PUBLIC MARKETPLACE ROUTES
// No auth required — anyone can browse
// =====================================================
Route::get('/', Browse::class)->name('home');
Route::get('/assets', Browse::class)->name('browse.assets');

// =====================================================
// AUTHENTICATED ROUTES
// =====================================================
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Asset management
    Route::get('/my/assets', Index::class)->name('assets.index');
    Route::get('/assets/create', Create::class)->name('assets.create');
    Route::get('/assets/{asset}/edit', Edit::class)->name('assets.edit');

    // Booking frontend
    // Why {booking} before /bookings?
    // Not strictly necessary here since they're different patterns,
    // but registering the more specific route first is good habit —
    // it makes the ordering intention clear to anyone reading this file.
    Route::get('/bookings/{booking}', BookingShow::class)
        ->name('bookings.show');

    Route::get('/bookings', BookingIndex::class)
        ->name('bookings.index');
    Route::get('/assets/{asset}/book', BookingCreate::class)
        ->name('bookings.create');
    // =====================================================
    // PLACEHOLDERS — replace as each frontend module is built
    // =====================================================
    Route::get('/transactions', function () {
        return view('dashboard');
    })->name('transactions.index');

    Route::get('/disputes', function () {
        return view('dashboard');
    })->name('disputes.index');

    Route::get('/reviews', function () {
        return view('dashboard');
    })->name('reviews.index');

    // Reviews
    Route::get('/reviews', ReviewIndex::class)
        ->name('reviews.index');
});
Route::get('/payments/chapa/callback', ChapaCallbackController::class)
    ->name('chapa.callback');
Route::post('/webhooks/chapa', ChapaWebhookController::class);

// =====================================================
// PUBLIC WILDCARD — must come LAST
// =====================================================
Route::get('/assets/{asset}', Show::class)->name('assets.show');

require __DIR__.'/settings.php';
