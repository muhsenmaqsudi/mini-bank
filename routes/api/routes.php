<?php

use Illuminate\Support\Facades\Route;

Route::get('/', static fn () => ['message' => 'MiniBank api is up & running'])->name('up');

Route::prefix('api/v1')->group(base_path('routes/api/v1/routes.php'));
