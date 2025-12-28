<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(AuthenticationController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});
// Route::middleware('auth:sanctum')->group(function () {
// });
