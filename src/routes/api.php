<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AnswerController;

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/surveys', [SurveyController::class, 'index']);

// Общие маршруты (нужен только токен)
Route::middleware('auth:api')->group(function () {
    Route::get('/surveys/{id}', [SurveyController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Маршруты для АВТОРА
Route::middleware(['auth:api', 'role:author'])->group(function () {
    Route::post('/surveys', [SurveyController::class, 'store']);
    Route::put('/surveys/{id}', [SurveyController::class, 'update']);
    Route::patch('/surveys/{id}/status', [SurveyController::class, 'changeStatus']);
    
    // Структура опроса
    Route::post('/surveys/{id}/questions', [SurveyController::class, 'addQuestion']);
    Route::post('/questions/{id}/options', [SurveyController::class, 'addOption']);
});

// Маршруты для РЕСПОНДЕНТА
Route::middleware(['auth:api', 'role:respondent'])->group(function () {
    Route::get('/surveys/available', [SurveyController::class, 'getPublished']);
    Route::post('/surveys/{id}/answers', [AnswerController::class, 'store']);
});