<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\PersonalInfoController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\BusRouteStatusController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\HikingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/experiences', [ExperienceController::class, 'index']);
Route::get('/skills', [SkillController::class, 'index']);
Route::get('/personal-info', [PersonalInfoController::class, 'index']);
Route::get('/education', [EducationController::class, 'index']);

// Bus tracker demo routes
Route::prefix('bus')->group(function () {
    Route::get('/data', [BusController::class, 'data']);
    Route::get('/routes', [BusController::class, 'routes']);
    Route::get('/stops', [BusController::class, 'stops']);
    Route::get('/companies', [BusController::class, 'companies']);
    Route::get('/status', [BusRouteStatusController::class, 'index']);
});

// Hiking demo routes
Route::prefix('hiking')->group(function () {
    Route::get('/route', [HikingController::class, 'route']);
    Route::post('/pois', [HikingController::class, 'pois']);
});

// Contact form (rate limited)
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1'); // 5 requests per minute

// Admin Authentication Routes
Route::prefix('admin/auth')->group(function () {
    // Public routes (no auth required)
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
    
    // Protected routes (auth required)
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/refresh', [AdminAuthController::class, 'refresh']);
    });
});


