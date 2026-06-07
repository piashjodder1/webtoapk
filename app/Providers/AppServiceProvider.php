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

        // Prevent FileUpload infinite loading on missing files
        \Filament\Forms\Components\FileUpload::configureUsing(function (\Filament\Forms\Components\FileUpload $component) {
            $component->getUploadedFileUrlUsing(function (\Filament\Forms\Components\FileUpload $component, string $file): ?string {
                try {
                    $disk = $component->getDisk();
                    if ($disk->exists($file)) {
                        return $disk->url($file);
                    }
                } catch (\Exception $e) {
                    // Ignore disk errors
                }
                return null; // File doesn't exist, tell FilePond to ignore it
            });
        });

        // Register model observers
        App::observe(AppObserver::class);
    }
}
