<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/work/{slug}', [PageController::class, 'project'])->name('work.show');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');

Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots']);
// The Astro build published its sitemap here.
Route::permanentRedirect('/sitemap-index.xml', '/sitemap.xml');

// Unknown URLs render the 404 inside the web middleware group (session, so the contact form works).
Route::fallback(fn () => abort(404));
