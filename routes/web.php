<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GridCellController::class, 'index']);

Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);

Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

Route::get('/functions/{id}/edit', [FunctionController::class, 'edit']);

Route::post('/functions/{id}/update', [FunctionController::class, 'update']);