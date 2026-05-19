<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\OverviewController;
use Illuminate\Support\Facades\Route;


Route::get('/', [GridCellController::class, 'index']);
Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

Route::get('/overview', [OverviewController::class, 'index']);
Route::get('/overview/{id}', [OverviewController::class, 'show']);
