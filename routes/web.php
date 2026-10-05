<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
| Every page exists twice: English at /... (route "home") and Bangla at /bn/...
| (route "bn.home"). The SetLocale middleware reads the language from the URL.
| In views use lroute('home') / lurl('path') to stay in the current language.
*/
$pages = function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/why-us', [PageController::class, 'whyUs'])->name('why-us');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/destinations/{slug}', [DestinationController::class, 'show'])->name('destination');
    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.post');
};

Route::group([], $pages);
Route::prefix('bn')->name('bn.')->group($pages);
