<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProcessScoreController;
use Illuminate\Support\Facades\Route;

Route::apiResource('contacts', ContactController::class);
Route::post('contacts/{id}/process-score', ProcessScoreController::class);
