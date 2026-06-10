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
                    'driver'                  => 's3',
                    'key'                     => Setting::get('r2_key'),
                    'secret'                  => Setting::get('r2_secret'),
                    'region'                  => 'auto',
                    'bucket'                  => Setting::get('r2_bucket'),
                    'endpoint'                => Setting::get('r2_endpoint'),
                    'use_path_style_endpoint' => true,
                ]
            ]);
            return Storage::disk('r2_dynamic');
        }

        if ($driver === 's3') {
            config([
                'filesystems.disks.s3_dynamic' => [
                    'driver' => 's3',
                    'key'    => Setting::get('s3_key'),
                    'secret' => Setting::get('s3_secret'),
                    'region' => Setting::get('s3_region', 'us-east-1'),
                    'bucket' => Setting::get('s3_bucket'),
                ]
            ]);
            return Storage::disk('s3_dynamic');
        }

        return Storage::disk('public');
    }

    /**
     * Upload an app icon (always stored on local public disk).
     */
    public function uploadIcon(UploadedFile $file, string $packageName): string
    {
        $disk = $this->getDisk();
        $filename = 'apps/' . Str::slug($packageName) . '/icon.' . $file->getClientOriginalExtension();
        $disk->put($filename, file_get_contents($file), 'public');
        return $filename;
    }

    /**
     * Upload a splash screen.
     */
    public function uploadSplash(UploadedFile $file, string $packageName): string
    {
        $disk = $this->getDisk();
        $filename = 'apps/' . Str::slug($packageName) . '/splash.' . $file->getClientOriginalExtension();
        $disk->put($filename, file_get_contents($file), 'public');
        return $filename;
    }

    /**
     * Upload a compiled APK file.
     */
    public function uploadApk(UploadedFile $file, string $packageName): string
    {
        $disk = $this->getDisk();
        $filename = 'apps/' . Str::slug($packageName) . '/builds/apk/app.apk';
        $disk->put($filename, file_get_contents($file), 'public');
        return $filename;
    }

    /**
     * Upload a compiled AAB file.
     */
    public function uploadAab(UploadedFile $file, string $packageName): string
    {
        $disk = $this->getDisk();
        $filename = 'apps/' . Str::slug($packageName) . '/builds/aab/app.aab';
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

        $driver = Setting::get('storage_driver', 'local');

        if ($driver === 'local') {
            return asset('storage/' . ltrim($path, '/'));
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
        return $this->getDisk()->delete($path);
    }

    /**
     * Delete a directory.
     */
    public function deleteDirectory(string $directory): bool
    {
        return $this->getDisk()->deleteDirectory($directory);
    }

    /**
     * Move a file.
     */
    public function move(string $oldPath, string $newPath): bool
    {
        return $this->getDisk()->move($oldPath, $newPath);
    }

    /**
     * Check if a directory exists (by checking if it contains files or subdirectories).
     */
    public function directoryExists(string $directory): bool
    {
        $disk = $this->getDisk();
        return count($disk->files($directory)) > 0 || count($disk->directories($directory)) > 0;
    }
}
