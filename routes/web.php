<?php

use App\Http\Controllers\GridCellController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'create']);

Route::post('/', [SessionController::class, 'store']);

Route::post('/logout', [SessionController::class, 'destroy']);

Route::middleware(['auth', 'role:cityplanner,admin'])->group(function (){

    Route::get('/grid', [GridCellController::class, 'index']);

    Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);

    Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

    Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

});