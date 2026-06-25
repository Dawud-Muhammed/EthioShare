<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Assets\Browse;
use App\Livewire\Assets\Show;
use App\Livewire\Assets\Index;
use App\Livewire\Assets\Create;
use App\Livewire\Assets\Edit;

// =====================================================
// PUBLIC MARKETPLACE ROUTES
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

    // Booking frontend — placeholder until BookingIndex Livewire component is built
    // TODO: replace with Route::get('/bookings', BookingIndex::class)->name('bookings.index');
    Route::get('/bookings', function () {
        return view('dashboard');
    })->name('bookings.index');

    // These placeholders stay until each frontend module is built
    Route::get('/transactions', function () {
        return view('dashboard');
    })->name('transactions.index');

    Route::get('/disputes', function () {
        return view('dashboard');
    })->name('disputes.index');

    Route::get('/reviews', function () {
        return view('dashboard');
    })->name('reviews.index');
});

// =====================================================
// PUBLIC WILDCARD — must come LAST
// =====================================================
Route::get('/assets/{asset}', Show::class)->name('assets.show');

require __DIR__.'/settings.php';