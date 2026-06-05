<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\GridCellController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

Route::get('/', [SessionController::class, 'create']);
Route::post('/', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

Route::middleware(['auth', 'role:cityplanner,admin'])->group(function () {
    Route::get('/grid', [GridCellController::class, 'index']);

    Route::get('/overview', [FunctionController::class, 'index'])->name('functions.index');
    Route::get('/overview/{id}', [FunctionController::class, 'show'])->name('functions.show');
    Route::get('/functions/{id}/edit', [FunctionController::class, 'edit'])->name('functions.edit');
    Route::patch('/functions/{id}/update', [FunctionController::class, 'update'])->name('functions.update');

    Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);
    Route::post('/grid/move-function', [GridCellController::class, 'moveFunction']);
    Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

    Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/functions/create', [FunctionController::class, 'create'])->name('functions.create');
    Route::post('/functions/store', [FunctionController::class, 'store'])->name('functions.store');

    Route::delete('/functions/{id}', [FunctionController::class, 'destroy'])->name('functions.destroy');
});

Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/add', [GroupController::class, 'add'])->name('groups.add');
Route::post('/groups/add', [GroupController::class, 'store'])->name('groups.store');

Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
