<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldBuilds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-old-builds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup downloaded APK/AAB files and keystore data 10 minutes after build completion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $storageService = new \App\Services\StorageService();
        $limitTime = now()->subMinutes(10);

        // We check updated_at because that's when build_status was set to 'completed'
        $apps = \App\Models\App::where('build_status', 'completed')
            ->where('updated_at', '<=', $limitTime)
            ->get();

        foreach ($apps as $app) {
            $this->info("Cleaning up files for App: {$app->app_name}");

            if ($app->apk_path) {
                $storageService->delete($app->apk_path);
            }
            if ($app->aab_path) {
                $storageService->delete($app->aab_path);
            }

            // Keystore logic - clear the base64 data to save DB space, but keep credentials
            $keystoreData = $app->keystore_data ?? [];
            if (isset($keystoreData['base64_keystore'])) {
                $keystoreData['base64_keystore'] = null;
            }

            $app->update([
                'apk_path' => null,
                'apk_url' => null,
                'aab_path' => null,
                'aab_url' => null,
                'build_status' => 'pending',
                'keystore_data' => empty($keystoreData) ? null : $keystoreData,
            ]);
        }

        $this->info("Cleanup finished. Processed {$apps->count()} apps.");
    }
}
