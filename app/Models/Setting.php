<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Keys that are stored encrypted in the database.
     */
    protected static array $encryptedKeys = [
        'github_token',
        'r2_key',
        'r2_secret',
        's3_key',
        's3_secret',
        'build_callback_token',
    ];

    /**
     * Determine if a key should be encrypted.
     */
    public static function isEncrypted(string $key): bool
    {
        return in_array($key, static::$encryptedKeys, true);
    }

    /**
     * Get a setting value by key.
     * Encrypted keys are automatically decrypted on retrieval.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        // Decrypt if this is a sensitive key
        if (static::isEncrypted($key) && !empty($value)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException) {
                // Value may have been stored plain-text before encryption was enabled;
                // return it as-is and let it be re-encrypted on next save.
                return $value;
            }
        }

        return $value;
    }

    /**
     * Set a setting value by key.
     * Sensitive keys are automatically encrypted before storage.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        $storedValue = $value;

        // Encrypt sensitive keys before saving to database
        if (static::isEncrypted($key) && !empty($value)) {
            $storedValue = Crypt::encryptString((string) $value);
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'group' => $group]
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
