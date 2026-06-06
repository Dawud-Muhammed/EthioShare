<?php

use App\Livewire\Dashboard;
use App\Livewire\Asset;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
  

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('asset', Asset::class)->name('asset');
    });

require __DIR__.'/settings.php';
