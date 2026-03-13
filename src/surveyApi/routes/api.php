<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/surveys', [SurveyController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::post('/surveys', [SurveyController::class, 'store']); // Создать опрос
    Route::get('/surveys/{id}', [SurveyController::class, 'show']); // Деталка
    
    Route::post('/surveys/{id}/questions', [SurveyController::class, 'addQuestion']);
    Route::post('/questions/{id}/options', [SurveyController::class, 'addOption']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});