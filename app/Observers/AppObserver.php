<?php

namespace App\Observers;

use App\Models\App;
use App\Services\StorageService;

class AppObserver
{
    /**
     * Handle the App "deleting" event.
     * Cleans up all storage files associated with the app.
     */
    public function deleting(App $app): void
    {
        $storageService = app(StorageService::class);

        // Delete the icon file if it exists
        if ($app->icon_path) {
            $storageService->delete($app->icon_path);
        }

        // Delete the splash file if it exists
        if ($app->splash_path) {
            $storageService->delete($app->splash_path);
        }

        // Delete all build output directories for this app
        $sluggedName = \Illuminate\Support\Str::slug($app->package_name);
        $storageService->deleteDirectory($app->package_name);
        $storageService->deleteDirectory($sluggedName);
        $storageService->deleteDirectory('apps/' . $sluggedName);
    }
}
