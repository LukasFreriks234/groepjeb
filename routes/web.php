<?php

use App\Http\Controllers\GridCellController;
use Illuminate\Support\Facades\Route;


Route::get('/test', function() {
    return "De route werkt!";
});

Route::get('/', [GridCellController::class, 'index']);