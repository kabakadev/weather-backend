<?php

use Illuminate\Support\Facades\Route;

// Health check for Railway
Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});


