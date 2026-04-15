<?php

use App\Http\Controllers\FunctionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ! # = URL
Route::get('#', [FunctionController::class, 'index']);