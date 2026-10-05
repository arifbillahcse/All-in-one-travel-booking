<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/destinations/{slug}', 'destination')->name('destination');
    Route::get('/packages', 'packages')->name('packages');
    Route::get('/why-us', 'whyUs')->name('why-us');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::get('/blog', 'blog')->name('blog');
    Route::get('/blog/{slug}', 'post')->name('blog.post');
    Route::get('/contact', 'contact')->name('contact');
});
