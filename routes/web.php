<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'index']);
Route::get('/auth/login', [PageController::class, 'login']);

// Serve the panel logo image (public, no auth needed)
Route::get('/panel/logo/{file}', [AdminController::class, 'getLogo'])->name('panel.logo');

// Serve the panel background image
Route::get('/panel/background', [AdminController::class, 'getBackgroundImage'])->name('panel.background');

// Error pages
Route::get('/maintenance', [ErrorController::class, 'show'])->defaults('code', 'maintenance');
Route::get('/error/{code?}', [ErrorController::class, 'show'])->where('code', 'maintenance|404|403|500|502|503|505');

// Panel SPA — catch-all so any /panel/* route serves the SPA
Route::get('/panel/{any?}', [PageController::class, 'panel'])->where('any', '.*');
