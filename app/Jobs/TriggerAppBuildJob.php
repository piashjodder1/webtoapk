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

        // Trigger GitHub Actions run — re-throw on failure so queue can retry
        $success = $githubService->triggerBuild($app, $build);

        if (!$success) {
            $log = Setting::get('github_token') && Setting::get('github_repository')
                ? 'GitHub API rejected the request. Check your token has "workflow" scope and the repository exists.'
                : 'GitHub settings not configured. Go to Admin > Settings and set GitHub Token and Repository.';

            Log::error("GitHub workflow dispatch failed for build #{$build->id}");

            // Only mark as failed on final attempt, otherwise let the job retry
            if ($this->attempts() >= $this->tries) {
                $build->update(['build_status' => 'failed', 'build_log' => $log]);
                $app->update(['build_status' => 'failed']);
            } else {
                // Re-throw a generic exception to trigger retry
                throw new \RuntimeException($log);
            }
        } else {
            Log::info("GitHub workflow dispatch succeeded for build #{$build->id}");
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
