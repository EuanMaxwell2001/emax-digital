<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Contact form: a few messages in a burst, and a daily cap, per IP.
        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinutes(10, 5)->by('contact:'.$request->ip()),
            Limit::perDay(20)->by('contact-day:'.$request->ip()),
        ]);
    }
}
