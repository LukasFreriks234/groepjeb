<?php

use App\Http\Controllers\GridCellController;
<<<<<<< HEAD
use App\Http\Controllers\FunctionController;
=======
use App\Http\Controllers\SessionController;
>>>>>>> 1a3fa17f37ae940baf97992a0453a84ce2345faf
use Illuminate\Support\Facades\Route;

Route::get('/', [SessionController::class, 'create']);

Route::post('/', [SessionController::class, 'store']);

Route::post('/logout', [SessionController::class, 'destroy']);

<<<<<<< HEAD
Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

Route::get('/functions/{id}/edit', [FunctionController::class, 'edit']);

Route::post('/functions/{id}/update', [FunctionController::class, 'update']);
=======
Route::middleware(['auth', 'role:cityplanner,admin'])->group(function (){

    Route::get('/grid', [GridCellController::class, 'index']);

    Route::post('/grid/assign-function', [GridCellController::class, 'assignFunction']);

    Route::post('/remove-function', [GridCellController::class, 'removeFunction']);

    Route::post('/grid/neighbor-effects', [GridCellController::class, 'neighborEffects']);

});
>>>>>>> 1a3fa17f37ae940baf97992a0453a84ce2345faf
