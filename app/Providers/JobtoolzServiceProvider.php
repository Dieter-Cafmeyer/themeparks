<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JobtoolzService;

class JobtoolzServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(JobtoolzService::class, function ($app) {
            return new JobtoolzService();
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