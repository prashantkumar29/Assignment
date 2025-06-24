<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/wel',function () {
//     return response()->json(['message' => 'Welcome to the Secure API']);
// });