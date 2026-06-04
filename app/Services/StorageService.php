<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Get the dynamically configured storage disk instance.
     */
    protected function getDisk()
    {
        $driver = Setting::get('storage_driver', 'local');

        if ($driver === 'r2') {
            config([
                'filesystems.disks.r2_dynamic' => [
                    'driver' => 's3',
                    'key' => Setting::get('r2_key'),
                    'secret' => Setting::get('r2_secret'),
                    'region' => 'auto',
                    'bucket' => Setting::get('r2_bucket'),
                    'endpoint' => Setting::get('r2_endpoint'),
                    'use_path_style_endpoint' => true,
                ]
            ]);
            return Storage::disk('r2_dynamic');
        }

        return Storage::disk('public');
    }

    /**
     * Upload an app icon.
     */
    public function uploadIcon(UploadedFile $file, string $packageName): string
    {
        $disk = Storage::disk('public');
        $filename = 'apps/' . Str::slug($packageName) . '/icon.' . $file->getClientOriginalExtension();
        $disk->put($filename, file_get_contents($file), 'public');
        return $filename;
    }

    /**
     * Upload a splash screen.
     */
    public function uploadSplash(UploadedFile $file, string $packageName): string
    {
        $disk = Storage::disk('public');
        $filename = 'apps/' . Str::slug($packageName) . '/splash.' . $file->getClientOriginalExtension();
        $disk->put($filename, file_get_contents($file), 'public');
        return $filename;
    }

    /**
     * Get the public URL of a file.
     */
    public function getUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // App icons and splash images are always stored locally
        if (str_starts_with($path, 'apps/')) {
            return Storage::disk('public')->url($path);
        }

        $driver = Setting::get('storage_driver', 'local');

        if ($driver === 'local') {
            return Storage::disk('public')->url($path);
        }

        if ($driver === 'r2') {
            $publicUrl = Setting::get('r2_public_url');
            if ($publicUrl) {
                return rtrim($publicUrl, '/') . '/' . ltrim($path, '/');
            }
        }

        return $this->getDisk()->url($path);
    }

    /**
     * Delete a file.
     */
    public function delete(string $path): bool
    {
        if (str_starts_with($path, 'apps/')) {
            return Storage::disk('public')->delete($path);
        }
        return $this->getDisk()->delete($path);
    }

    /**
     * Delete a directory.
     */
    public function deleteDirectory(string $directory): bool
    {
        if (str_starts_with($directory, 'apps/')) {
            return Storage::disk('public')->deleteDirectory($directory);
        }
        return $this->getDisk()->deleteDirectory($directory);
    }
}
