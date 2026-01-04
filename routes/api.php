<?php

use App\Http\Controllers\Auth\AdminAuthenticationController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WorkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/verify-otp', 'verifyOtp');
});

Route::controller(AdminAuthenticationController::class)->group(function () {
    Route::post('/admin/login', 'adminLogin');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile', [AuthenticationController::class, 'profile']);
    Route::get('/roles', [RolePermissionController::class, 'roles']);

    Route::controller(PackageController::class)->group(function () {
        Route::post('/packages', 'packages');
        Route::get('/packages', 'getPackages');
        // Route::get('/package/statistics', 'packageStatistics');
    });

    Route::controller(FaqCategoryController::class)->group(function () {
        Route::get('/faqs/category', 'index');
        Route::post('/faqs/category', 'store');
        Route::post('/faqs/category/{id}', 'update');
        Route::delete('/faqs/category/{id}', 'destroy');
    });

    Route::controller(FaqController::class)->group(function () {
        Route::get('/faqs', 'index');
        Route::post('/faqs', 'store');
        Route::post('/faqs/{id}', 'update');
        Route::delete('/faqs/{id}', 'destroy');
    });

    Route::controller(WorkController::class)->group(function () {
        Route::get('/works', 'index');
    });

});

Route::controller(StaffController::class)->group(function () {
    Route::post('/staff', 'staff');
});

Route::controller(BannerController::class)->group(function () {
    Route::post('/news-letters', 'newsLetters');
});

Route::controller(BlogController::class)->group(function () {
    Route::post('/blog-category', 'category');
    Route::post('/blogs', 'blogs');
    Route::get('/blogs', 'getBlogs');
    Route::get('/blog-statistics', 'statistics');
    Route::get('/blog-category', 'gatCategory');
});
