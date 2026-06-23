<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\GridCellController;
use App\Http\Controllers\SavedGridController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\FunctionController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'create'])->name('login');
Route::post('/', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

Route::middleware(['auth', 'role:cityplanner,admin'])->group(function () {
    Route::get('/grid', [GridCellController::class, 'index']);
    Route::get('/saved-grids', [SavedGridController::class, 'index'])->name('saved-grids.index');
    Route::post('/saved-grids/load', [SavedGridController::class, 'load'])->name('saved-grids.load');

    Route::get('/overview', [FunctionController::class, 'index'])->name('functions.index');
    Route::get('/overview/{id}', [FunctionController::class, 'show'])->name('functions.show');
    Route::get('/functions/{id}/edit', [FunctionController::class, 'edit'])->name('functions.edit');
    Route::patch('/functions/{id}/update', [FunctionController::class, 'update'])->name('functions.update');

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');

    Route::post('/grid/save', [GridCellController::class, 'saveGrid'])->name('grid.save');
    Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);
    Route::post('/grid/move-function', [GridCellController::class, 'moveFunction']);
    Route::post('/grid/assign-event', [GridCellController::class, 'assignEvent']);
    Route::post('/grid/check-expired-events',[GridCellController::class, 'checkExpiredEvents']);
    Route::post('/grid/remove-event', [GridCellController::class, 'removeEvent']);
    Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

    Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events/store', [EventController::class, 'store'])->name('events.store');

    Route::post('/event/route', [EventController::class, 'saveRoute']);

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/add', [GroupController::class, 'add'])->name('groups.add');
    Route::post('/groups/add', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/functions/create', [FunctionController::class, 'create'])->name('functions.create');
    Route::post('/functions/store', [FunctionController::class, 'store'])->name('functions.store');
    Route::post('/functions/restore', [FunctionController::class, 'restore'])->name('functions.restore');
    Route::delete('/functions/{id}', [FunctionController::class, 'destroy'])->name('functions.destroy');
});