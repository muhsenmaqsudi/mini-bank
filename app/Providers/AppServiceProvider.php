<?php

namespace App\Providers;

use App\Notifications\Supplier;
use App\Notifications\Suppliers\Ghasedak;
use App\Notifications\Suppliers\Kavenegar;
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
        $this->app->bind(Supplier::class, Kavenegar::class);
    }
}
