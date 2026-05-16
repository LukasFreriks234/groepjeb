<?php

use App\Http\Controllers\GridCellController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GridCellController::class, 'index']);

Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);

Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);