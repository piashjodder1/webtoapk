<?php

namespace App\Providers;

use App\Models\App;
use App\Models\Build;
use App\Models\Payment;
use App\Observers\AppObserver;
use App\Observers\BuildObserver;
use App\Observers\PaymentObserver;
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
        // We rely on TrustProxies middleware in bootstrap/app.php instead of forcing scheme.
        // This allows the app to work seamlessly on both http://127.0.0.1:8000 and https://app.domain.com

        // Dynamically override public storage URL to use the current domain dynamically
        config(['filesystems.disks.public.url' => asset('storage')]);

        // Register model observers
        App::observe(AppObserver::class);
        Payment::observe(PaymentObserver::class);
        Build::observe(BuildObserver::class);

        // Dynamically register cloud_dynamic disk if enabled
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $driver = \App\Models\Setting::get('storage_driver', 'local');
                if ($driver === 'r2') {
                    config([
                        'filesystems.disks.cloud_dynamic' => [
                            'driver'                  => 's3',
                            'key'                     => \App\Models\Setting::get('r2_key'),
                            'secret'                  => \App\Models\Setting::get('r2_secret'),
                            'region'                  => 'auto',
                            'bucket'                  => \App\Models\Setting::get('r2_bucket'),
                            'endpoint'                => \App\Models\Setting::get('r2_endpoint'),
                            'use_path_style_endpoint' => true,
                            'url'                     => \App\Models\Setting::get('r2_public_url'),
                            'visibility'              => 'public',
                        ]
                    ]);
                } elseif ($driver === 's3') {
                    config([
                        'filesystems.disks.cloud_dynamic' => [
                            'driver' => 's3',
                            'key'    => \App\Models\Setting::get('s3_key'),
                            'secret' => \App\Models\Setting::get('s3_secret'),
                            'region' => \App\Models\Setting::get('s3_region', 'us-east-1'),
                            'bucket' => \App\Models\Setting::get('s3_bucket'),
                            'visibility' => 'public',
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during migrations or empty DB
        }
    }
}
