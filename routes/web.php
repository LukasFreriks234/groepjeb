<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\CssSelector\Node\FunctionNode;

Route::get('/', [GridCellController::class, 'index']);
Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

Route::get('/overview', [FunctionController::class, 'index']);
Route::get('/overview/{id}', [FunctionController::class, 'show'])->name('functions.show');

Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);

Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);