<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'create']);
Route::post('/', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

Route::middleware(['auth', 'role:cityplanner,admin'])->group(function () {
    Route::get('/grid', [GridCellController::class, 'index']);

    Route::get('/overview', [FunctionController::class, 'index'])->name('functions.index');
    Route::get('/overview/{id}', [FunctionController::class, 'show'])->name('functions.show');

    Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);
    Route::post('/grid/move-function', [GridCellController::class, 'moveFunction']);
    Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

    Route::post('/remove-function', [GridCellController::class, 'removeFunction']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/functions/create', [FunctionController::class, 'create'])->name('functions.create');
    Route::post('/functions/store', [FunctionController::class, 'store'])->name('functions.store');

    Route::get('/functions/{id}/edit', [FunctionController::class, 'edit'])->name('functions.edit');
    Route::patch('/functions/{id}/update', [FunctionController::class, 'update'])->name('functions.update');
});