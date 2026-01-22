<?php

use App\Http\Controllers\AdvanceSearchController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Auth\AdminAuthenticationController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FrontendSettingController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\NotificationConteoller;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileAttributeController;
use App\Http\Controllers\ProfileVisitController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TicketController;
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
    Route::post('/file-upload', [AuthenticationController::class, 'fileUpload']);
    Route::get('/membership', [AuthenticationController::class, 'membership']);

    Route::controller(RolePermissionController::class)->group(function () {
        Route::get('/roles-permissions', 'index');
        Route::get('/roles', 'roles');
        Route::post('/roles', 'roleStore');
        Route::get('/roles/{id}', 'roleEdit');
        Route::get('/roles/view/{id}', 'roleView');
        Route::post('/roles/{id}', 'roleUpdate');
        Route::get('/permissions', 'permissions');
        Route::delete('/roles/delete/{id}', 'roleDelete');
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
        Route::get('/news-letters', 'newsLetters');
        Route::get('/news-letters/{id}', 'newsLettersView');
        Route::post('/news-letters', 'newsLetterStore');
        Route::get('/banners', 'index');
        Route::post('/banners', 'store');
        Route::get('/banners/{id}', 'edit');
        Route::post('/banners/{id}', 'update');
        Route::get('/banners/{id}', 'view');
        Route::post('/banners/status/{id}', 'updateStatus');
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

    Route::controller(MatchController::class)->group(function () {
        Route::get('/matches', 'index');
    });

    Route::controller(MyProfileController::class)->group(function () {
        Route::get('/setting/account', 'index');
        Route::post('/setting/account/update', 'update');
        Route::post('/setting/update/password', 'updatePassword');
        Route::delete('/setting/delete/account', 'deleteAccount');
        Route::get('/setting/photo', 'getPhotoSetting');
        Route::post('/setting/photo', 'photoSetting');
        Route::get('/setting/partner', 'getPartnerSetting');
        Route::post('/setting/partner', 'partnerSetting');
        Route::get('/blocked/profile', 'blockedProfile');
        Route::get('/profile/details/{id}', 'profileDetails');
        Route::post('/blocked/user/{id}', 'blockUser');
        Route::post('/un-blocked/user/{id}', 'unblockUser');
    });

    Route::controller(TicketController::class)->group(function () {
        Route::get('/support/tickets', 'index');
        Route::post('/support/tickets', 'store');
        Route::get('/support/tickets/{id}', 'show');
        Route::post('/support/tickets/reply/{id}', 'reply');
    });

    Route::controller(ProfileVisitController::class)->group(function () {
        Route::get('/profile-visit', 'index');
        Route::post('/profile-visit/{id}', 'store');
    });

    Route::controller(AdvanceSearchController::class)->group(function () {
        Route::get('/search-profiles', 'searchProfiles');
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
        Route::get('/members-you-may-like', 'membersYouMayLike');
    });


    Route::controller(ComplaintController::class)->group(function () {
        Route::get('/complaints', 'index');
        Route::get('/complaints/{id}', 'show');
        Route::post('/complaints', 'store');
        Route::post('/complaints/replay/{id}', 'storeReplay');
        Route::post('/complaints/block/{id}', 'updateBlock');
        Route::post('/complaints/dismiss/{id}', 'updateDismiss');
    });

    Route::get('/chat/messages/{id}', [ChatController::class, 'messages']);
    Route::post('/chat/send/{id}', [ChatController::class, 'send']);

    Route::controller(MemberController::class)->group(function () {
        Route::get('/members', 'index');
        Route::get('/members/view/{id}', 'view');
        Route::post('/members', 'store');
        Route::post('/members/{id}', 'update');
        Route::post('/members/status/{id}', 'statusUpdate');
        Route::delete('/members/{id}', 'destroy');
    });

    Route::controller(FrontendSettingController::class)->group(function () {
        Route::get('/frontends/setting', 'index');
        Route::get('/frontends/setting/edit/{id}', 'edit');
        Route::post('/frontends/setting/update/{id}', 'update');
    });

    Route::controller(NotificationConteoller::class)->group(function () {
        Route::get('/user/notifications/received', 'getReceivedNotifications');
        Route::post('/user/link/{id}', 'userLike');
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
