<?php

namespace App\Models;

use Database\Factories\AppFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class App extends Model
{
    /** @use HasFactory<AppFactory> */
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
        'enable_push_notification',
        'enable_exit_confirmation',
        'enable_loading_progress_bar',
        'enable_external_links_in_browser',
        'onesignal_app_id',
        'version_name',
        'version_code',
        'apk_url',
        'aab_url',
        'build_status',
        'enable_bottom_navigation',
        'bottom_navigation_items',
        'enable_custom_header',
        'header_logo',
    ];

    protected $casts = [
        'enable_pull_refresh' => 'boolean',
        'enable_offline_page' => 'boolean',
        'enable_push_notification' => 'boolean',
        'enable_exit_confirmation' => 'boolean',
        'enable_loading_progress_bar' => 'boolean',
        'enable_external_links_in_browser' => 'boolean',
        'enable_bottom_navigation' => 'boolean',
        'enable_custom_header' => 'boolean',
        'bottom_navigation_items' => 'array',
    ];


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
