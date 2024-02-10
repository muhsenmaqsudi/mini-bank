<?php

namespace App\Providers;

use App\Notifications\Channels\Sms\Kavenegar;
use App\Notifications\SmsChannel;
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
        $this->app->bind(SmsChannel::class, Kavenegar::class);
    }
}
