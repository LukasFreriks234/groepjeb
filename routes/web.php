<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GridCellController::class, 'index']);

Route::get('/overview', [FunctionController::class, 'index'])->name('functions.index');
Route::get('/overview/{id}', [FunctionController::class, 'show'])->name('functions.show');

Route::get('/functions/create', [FunctionController::class, 'create'])->name('functions.create');
Route::post('/functions/store', [FunctionController::class, 'store'])->name('functions.store');

Route::get('/functions/{id}/edit', [FunctionController::class, 'edit'])->name('functions.edit');
Route::patch('/functions/{id}/update', [FunctionController::class, 'update'])->name('functions.update');

Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);
Route::post('/grid/move-function', [GridCellController::class, 'moveFunction']);
Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

Route::post('/remove-function', [GridCellController::class, 'removeFunction']);