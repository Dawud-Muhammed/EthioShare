<?php

use Illuminate\Support\Facades\Route;
use \App\Livewire\Assets\Browse;
use \App\Livewire\Assets\Show;
use \App\Livewire\Assets\Index;
use \App\Livewire\Assets\Create;
use \App\Livewire\Assets\Edit;

// =====================================================
// PUBLIC MARKETPLACE ROUTES
// No auth required — anyone can browse
// =====================================================
Route::view('/', 'layouts/marketplace')->name('home');
Route::get('/assets', Browse::class)
    ->name('browse.assets');
// ↑ This connects URL /assets directly to a Livewire component class.
// Laravel 13 supports this natively — no controller needed.
// When someone visits /assets, Laravel instantiates Browse::class
// and renders its view automatically.

Route::get('/assets/{asset}', Show::class)
    ->name('assets.show');


// =====================================================
// AUTHENTICATED OWNER ROUTES
// =====================================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Asset management
    Route::get('/my/assets', Index::class)
        ->name('assets.index');

    Route::get('/assets/create', Create::class)
        ->name('assets.create');

    Route::get('/assets/{asset}/edit', Edit::class)
        ->name('assets.edit');

    // =====================================================
    // PLACEHOLDER ROUTES — prevent sidebar link errors
    // Replace each one when you build that domain's frontend
    // =====================================================
    Route::get('/bookings', function () {
        return view('dashboard');
    })->name('bookings.index');

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