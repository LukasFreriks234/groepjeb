<?php

use App\Http\Controllers\FunctionController;
use App\Http\Controllers\GridCellController;
use Illuminate\Support\Facades\Route;


Route::get('/test', function() {
    return "De route werkt!";
});

Route::get('/', [FunctionController::class, 'index']);
Route::get('/', [GridCellController::class, 'index']);