<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AnswerController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Все действия требуют авторизации
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/surveys', [SurveyController::class, 'index']); // Фильтрация/Пагинация здесь
    Route::get('/surveys/{id}', [SurveyController::class, 'show']);

    // Доступно только авторам
    Route::middleware('role:author')->group(function () {
        Route::post('/surveys', [SurveyController::class, 'store']);
        Route::put('/surveys/{id}', [SurveyController::class, 'update']);
        Route::patch('/surveys/{id}/status', [SurveyController::class, 'changeStatus']);
        Route::post('/surveys/{id}/questions', [SurveyController::class, 'addQuestion']);
        Route::post('/questions/{id}/options', [SurveyController::class, 'addOption']);
        
        // Новое задание: Аналитика и Экспорт
        Route::get('/surveys/{id}/analytics', [SurveyController::class, 'analytics']);
        Route::get('/surveys/{id}/export', [SurveyController::class, 'export']);
    });

    // Доступно респондентам
    Route::middleware('role:respondent')->group(function () {
        Route::get('/surveys/available', [SurveyController::class, 'getPublished']);
        Route::post('/surveys/{id}/answers', [AnswerController::class, 'store']);
    });
});