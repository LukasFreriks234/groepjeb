<?php

use App\Http\Controllers\GridCellController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() {
    return "De route werkt!";
});

Route::get('/grid', [GridCellController::class, 'index']);