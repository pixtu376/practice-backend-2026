<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/surveys', [SurveyController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/surveys/{id}', [SurveyController::class, 'show']);
    Route::post('/surveys/{id}/answers', [SurveyController::class, 'submit']);
    Route::post('/logout', [AuthController::class, 'logout']);
});