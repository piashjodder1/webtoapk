<?php

namespace App\Observers;

use App\Models\App;
use App\Services\StorageService;

class AppObserver
{
    /**
     * Handle the App "creating" event.
     */
    public function creating(App $app): void
    {
        if (empty($app->keystore_data)) {
            $app->keystore_data = [
                'key_alias' => 'upload',
                'keystore_password' => \Illuminate\Support\Str::random(16),
                'key_password' => \Illuminate\Support\Str::random(16),
                'base64_keystore' => null,
            ];
        }
    }

    /**
     * Handle the App "created" event.
     */
    public function created(App $app): void
    {
        $storageService = app(StorageService::class);
        $slug = $app->package_name;
        $updates = [];

        $fields = ['icon_path', 'splash_path', 'header_logo'];
        foreach ($fields as $field) {
            if ($app->{$field} && str_starts_with($app->{$field}, 'temp-branding/')) {
                $newPath = 'apps/' . $slug . '/branding/' . basename($app->{$field});
                if ($storageService->move($app->{$field}, $newPath)) {
                    $updates[$field] = $newPath;
                }
            }
        }

        if (!empty($updates)) {
            App::withoutEvents(fn() => $app->update($updates));
        }
    }

    /**
     * Handle the App "updating" event.
     */
    public function updating(App $app): void
    {
        $storageService = app(StorageService::class);
        $fields = ['icon_path', 'splash_path', 'header_logo'];

        foreach ($fields as $field) {
            if ($app->isDirty($field) && $app->getOriginal($field)) {
                $storageService->delete($app->getOriginal($field));
            }
        }
        
        // Also move files if package name changes or they are in temp-branding
        $slug = $app->package_name;
        foreach ($fields as $field) {
            if ($app->isDirty($field) && $app->{$field} && str_starts_with($app->{$field}, 'temp-branding/')) {
                $newPath = 'apps/' . $slug . '/branding/' . basename($app->{$field});
                if ($storageService->move($app->{$field}, $newPath)) {
                    $app->{$field} = $newPath;
                }
            }
        }
    }

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

        // Delete the header file if it exists
        if ($app->header_logo) {
            $storageService->delete($app->header_logo);
        }

        // Delete all build output directories for this app
        $sluggedName = $app->package_name;
        $storageService->deleteDirectory($app->package_name);
        $storageService->deleteDirectory($sluggedName);
        $storageService->deleteDirectory('apps/' . $sluggedName);
    }
}
