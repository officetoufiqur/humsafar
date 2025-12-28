<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/storage-link', function () {
    $target = storage_path('app/public');
    $link = '/home/nayon/cervannacare.testorbis.com/storage';

    if (file_exists($link)) {
        return 'Symlink already exists.';
    }

    symlink($target, $link);
    return 'Symlink created successfully.';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return 'Cache cleared successfully!';
});

Route::get('/storage', function () {
    Artisan::call('storage:link');

    return 'Storage link created successfully!';
});
