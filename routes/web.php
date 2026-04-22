<?php

use App\Http\Controllers\GridCellController;
use Illuminate\Support\Facades\Route;


Route::get('/', [GridCellController::class, 'index']);
Route::post('/remove-function', [GridCellController::class, 'removeFunction']);