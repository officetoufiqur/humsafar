<?php

use App\Http\Controllers\Auth\AdminAuthenticationController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileAttributeController;
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
});

Route::controller(AdminAuthenticationController::class)->group(function () {
    Route::post('/admin/login', 'adminLogin');
});

 Route::controller(AuthenticationController::class)->group(function () {
        Route::post('/verify-otp', 'verifyOtp');
    });
    

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile', [AuthenticationController::class, 'profile']);
    Route::get('/membership', [AuthenticationController::class, 'membership']);

    Route::controller(RolePermissionController::class)->group(function () {
        Route::get('/roles', 'roles');
        Route::post('/roles', 'roleStore');
        Route::get('/permissions', 'permissions');
    });

    Route::controller(PackageController::class)->group(function () {
        Route::get('/packages', 'getPackages');
        Route::post('/packages', 'packages');
        Route::get('/packages/{id}', 'packagesEdit');
        Route::post('/packages/{id}', 'packagesUpdate');
        Route::post('/packages/status/{id}', 'packagesUpdateStatus');
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

    Route::controller(ProfileAttributeController::class)->group(function () {
        Route::get('/profile-attributes', 'index');
        Route::post('/profile-attributes/{id}', 'update');
    });

    Route::controller(StaffController::class)->group(function () {
        Route::get('/staff', 'index');
        Route::post('/staff', 'staff');
        Route::get('/staff/{id}', 'staffEdit');
        Route::post('/staff/{id}', 'staffUpdate');
        Route::delete('/staff/{id}', 'staffDelete');
    });

    Route::controller(BannerController::class)->group(function () {
        Route::post('/news-letters', 'newsLetters');
    });

    Route::controller(BlogController::class)->group(function () {
        Route::get('/blog-category', 'gatCategory');
        Route::post('/blog-category', 'category');
        Route::get('/blog-category/{id}', 'categoryEdit');
        Route::post('/blog-category/{id}', 'categoryUpdate');
        Route::delete('/blog-category/{id}', 'categoryDelete');
        Route::get('/blogs', 'getBlogs');
        Route::get('/blog-statistics', 'statistics');
        Route::post('/blogs', 'blogs');
        Route::get('/blogs/{id}', 'blogsEdit');
        Route::post('/blogs/{id}', 'blogsUpdate');
        Route::delete('/blogs/{id}', 'blogDelete');
    });

});

Route::controller(WorkController::class)->group(function () {
    Route::get('/works', 'index');
});

 Route::controller(BlogController::class)->group(function () {
        Route::get('/blog-category', 'gatCategory');
        Route::get('/blogs', 'getBlogs');
    });
    
 Route::controller(FaqController::class)->group(function () {
        Route::get('/faqs', 'index');
    });
