<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\CalculationAws;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CalculationAws::class, function ($app) {
            return new CalculationAws();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
