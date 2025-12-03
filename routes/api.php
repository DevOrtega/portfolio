<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\PersonalInfoController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Admin\AdminAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/experiences', [ExperienceController::class, 'index']);
Route::get('/skills', [SkillController::class, 'index']);
Route::get('/personal-info', [PersonalInfoController::class, 'index']);
Route::get('/education', [EducationController::class, 'index']);

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
