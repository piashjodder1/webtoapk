<?php

namespace App\Providers;

use App\Models\App;
use App\Observers\AppObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\AppRepositoryInterface::class,
            \App\Repositories\Eloquent\AppRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\BuildRepositoryInterface::class,
            \App\Repositories\Eloquent\BuildRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Dynamically override public storage URL to use the current domain dynamically
        config(['filesystems.disks.public.url' => asset('storage')]);

        // Register model observers
        App::observe(AppObserver::class);
    }
}
