<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class App extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'app_name',
        'website_url',
        'package_name',
        'icon_path',
        'splash_path',
        'enable_pull_refresh',
        'enable_offline_page',
        'apk_url',
        'aab_url',
        'build_status',
    ];

    protected $casts = [
        'enable_pull_refresh' => 'boolean',
        'enable_offline_page' => 'boolean',

    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($app) {
            $storageService = new \App\Services\StorageService();
            // Delete the icon file if it exists
            if ($app->icon_path) {
                $storageService->delete($app->icon_path);
            }
            // Delete the splash file if it exists
            if ($app->splash_path) {
                $storageService->delete($app->splash_path);
            }
            // Delete all builds and files associated with this app
            $storageService->deleteDirectory($app->package_name);
            $storageService->deleteDirectory(\Illuminate\Support\Str::slug($app->package_name));
            $storageService->deleteDirectory('apps/' . \Illuminate\Support\Str::slug($app->package_name));
        });
    }

    /**
     * Get the user that owns the app.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the builds for the app.
     */
    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }

    /**
     * Get the latest build for the app.
     */
    public function latestBuild(): HasOne
    {
        return $this->hasOne(Build::class)->latestOfMany();
    }
}
