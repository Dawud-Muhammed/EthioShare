// routes/api.php

<?php

use Illuminate\Support\Facades\Route;

// This tells Laravel: "also load my assets routes file"
require __DIR__ . '/assets.php';

// OR the cleaner Laravel way:
Route::middleware('api')->group(base_path('routes/assets.php'));