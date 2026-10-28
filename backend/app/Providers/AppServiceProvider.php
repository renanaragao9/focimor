<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth-login', fn (Request $request) => [
            Limit::perMinute(5)->by('login|'.mb_strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute(30)->by('login|'.$request->ip()),
        ]);

        RateLimiter::for('auth-sensitive', fn (Request $request) => [
            Limit::perMinute(5)->by($request->path().'|'.mb_strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute(30)->by('auth-sensitive|'.$request->ip()),
        ]);

        RateLimiter::for('chat-send', fn (Request $request) => Limit::perMinute(30)->by('chat-send|'.($request->user()?->id ?: $request->ip())));

        RateLimiter::for('ong-register', fn (Request $request) => Limit::perMinute(10)->by('ong-register|'.$request->ip()));

        RateLimiter::for('auth-register', fn (Request $request) => Limit::perMinute(10)->by('register|'.$request->ip()));
    }
}
