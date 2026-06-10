<?php

namespace App\Observers;

use App\Models\Build;

class BuildObserver
{
    /**
     * Handle the Build "creating" event.
     */
    public function creating(Build $build): void
    {
        $app = $build->app;
        if ($app) {
            // Delete previous builds from storage when a new build is requested
            $storageService = app(\App\Services\StorageService::class);
            $sluggedName = $app->package_name;
            $storageService->deleteDirectory('apps/' . $sluggedName . '/build');
        }
    }

    /**
     * Handle the Build "created" event.
     */
    public function created(Build $build): void
    {
        $app = $build->app;
        if (! $app) {
            return;
        }

        $user = $app->user;
        if (! $user) {
            return;
        }

        $activeSub = $user->activeSubscription;
        if ($activeSub && $activeSub->remaining_credits > 0) {
            // Deduct remaining credits and increment used credits
            $activeSub->decrement('remaining_credits');
            $activeSub->increment('used_credits');
        }
    }
}
