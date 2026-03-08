<?php

namespace App\Providers;

use App\Broadcasting\Reverb\DatabaseApplicationProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Reverb\Contracts\ApplicationProvider;

class ReverbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the custom database application provider
        $this->app->singleton(ApplicationProvider::class, function ($app) {
            return new DatabaseApplicationProvider();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
