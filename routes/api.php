<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\PersonalInfoController;

use App\Http\Controllers\Api\EducationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/experiences', [ExperienceController::class, 'index']);
Route::get('/skills', [SkillController::class, 'index']);
Route::get('/personal-info', [PersonalInfoController::class, 'index']);
Route::get('/education', [EducationController::class, 'index']);
