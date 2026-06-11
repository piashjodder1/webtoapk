<?php

namespace App\Jobs;

use App\Models\Build;
use App\Models\Setting;
use App\Services\GitHubService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TriggerAppBuildJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of attempts before marking as failed.
     */
    public int $tries = 3;

    /**
     * Backoff durations in seconds between retries (30s, 60s, 120s).
     */
    public array $backoff = [30, 60, 120];

    protected Build $build;

    /**
     * Create a new job instance.
     */
    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * Execute the job.
     */
    public function handle(GitHubService $githubService): void
    {
        $build = $this->build;
        $app   = $build->app;

        Log::info("Running TriggerAppBuildJob for build #{$build->id} (App: {$app->app_name})");

        // Update status to building
        $build->update(['build_status' => 'building']);
        $app->update(['build_status' => 'building']);

        $keystoreData = $app->keystore_data ?? [];
        $keystoreData['key_alias'] = 'release';
        if (empty($keystoreData['base64_keystore'])) {
            if (empty($keystoreData['keystore_password']) || empty($keystoreData['key_password'])) {
                $pwd = \Illuminate\Support\Str::random(16);
                $keystoreData['keystore_password'] = $pwd;
                $keystoreData['key_password'] = $pwd;
            }
        }
        $app->update(['keystore_data' => $keystoreData]);

        try {
            // Trigger GitHub Actions run
            $success = $githubService->triggerBuild($app, $build);
            
            if (!$success) {
                throw new \RuntimeException('GitHub Service returned false.');
            }
            
            Log::info("GitHub workflow dispatch succeeded for build #{$build->id}");
        } catch (\RuntimeException $e) {
            $errorMsg = $e->getMessage();
            
            // Permanent errors (401, 404, etc.) - do not retry
            if (str_contains($errorMsg, 'Bad credentials') || str_contains($errorMsg, 'Not Found')) {
                Log::error("Permanent GitHub Configuration Error for build #{$build->id}: {$errorMsg}");
                $build->update([
                    'build_status' => 'failed', 
                    'build_log' => "GitHub Setup Error: The GitHub token is invalid or the repository does not exist. Please check your Admin Settings.\n\nDetails: " . $errorMsg
                ]);
                $app->update(['build_status' => 'failed']);
                return;
            }
            
            $log = Setting::get('github_token') && Setting::get('github_repository')
                ? 'GitHub API rejected the request. Check your token has "workflow" scope.'
                : 'GitHub settings not configured. Go to Admin > Settings and set GitHub Token and Repository.';

            Log::error("GitHub workflow dispatch failed for build #{$build->id}: {$errorMsg}");

            // Only mark as failed on final attempt, otherwise let the job retry
            if ($this->attempts() >= $this->tries) {
                $build->update(['build_status' => 'failed', 'build_log' => $log . "\n" . $errorMsg]);
                $app->update(['build_status' => 'failed']);
            } else {
                // Re-throw to trigger retry
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        $build = $this->build;
        $app   = $build->app;

        Log::error("TriggerAppBuildJob permanently failed for build #{$build->id}: " . $exception->getMessage());

        $build->update([
            'build_status' => 'failed',
            'build_log'    => 'All retry attempts exhausted. Error: ' . $exception->getMessage(),
        ]);

        $app->update(['build_status' => 'failed']);
    }
}
