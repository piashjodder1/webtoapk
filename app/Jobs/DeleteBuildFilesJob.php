<?php

namespace App\Jobs;

use App\Models\App;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteBuildFilesJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected App $app;
    protected string $apkUrl;
    protected string $aabUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(App $app, string $apkUrl = '', string $aabUrl = '')
    {
        $this->app = $app;
        $this->apkUrl = $apkUrl;
        $this->aabUrl = $aabUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Running DeleteBuildFilesJob for app #{$this->app->id}");

        $r2PublicUrl = rtrim(Setting::get('r2_public_url', ''), '/');

        // Delete APK if it matches R2 domain
        if (!empty($this->apkUrl) && str_starts_with($this->apkUrl, $r2PublicUrl)) {
            $apkPath = str_replace($r2PublicUrl . '/', '', $this->apkUrl);
            Storage::disk('r2')->delete($apkPath);
            Log::info("Deleted APK from R2: {$apkPath}");
        }

        // Delete AAB if it matches R2 domain
        if (!empty($this->aabUrl) && str_starts_with($this->aabUrl, $r2PublicUrl)) {
            $aabPath = str_replace($r2PublicUrl . '/', '', $this->aabUrl);
            Storage::disk('r2')->delete($aabPath);
            Log::info("Deleted AAB from R2: {$aabPath}");
        }

        // Clear the URLs in the database so the download button disappears
        // Make sure the URLs haven't changed (e.g. if a new build happened within the 10 mins)
        if ($this->app->apk_url === $this->apkUrl) {
            $this->app->apk_url = null;
        }
        if ($this->app->aab_url === $this->aabUrl) {
            $this->app->aab_url = null;
        }
        $this->app->save();
        
        Log::info("Cleared download URLs from database for app #{$this->app->id}");
    }
}
