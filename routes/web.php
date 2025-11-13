<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated.',
    ], 401);
})->name('login');

