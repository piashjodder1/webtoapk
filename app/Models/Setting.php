<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return self::where('key', $key)->first();
        });

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Flush setting cache.
     */
    public static function flushCache(string $key): void
    {
        Cache::forget("setting.{$key}");
    }
}
