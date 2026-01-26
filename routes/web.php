<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/storage-link', function () {
    $target = storage_path('app/public');
    $link = '/home/nayon/humsafar.testorbis.com/storage';

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

    return 'Storage linked successfully!';
});


Route::get('/', function () {
    return Inertia::render('Home', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/command.php';
