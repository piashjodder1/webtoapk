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
        'enable_push_notification',
        'enable_admob',
        'apk_url',
        'aab_url',
        'build_status',
    ];

    protected $casts = [
        'enable_pull_refresh' => 'boolean',
        'enable_offline_page' => 'boolean',
        'enable_push_notification' => 'boolean',
        'enable_admob' => 'boolean',
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
