<?php

namespace App\Jobs;

use App\Models\Build;
use App\Services\GitHubService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TriggerAppBuildJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
        $app = $build->app;

        Log::info("Running TriggerAppBuildJob for build #{$build->id} (App: {$app->app_name})");

        // Update status to building
        $build->update(['build_status' => 'building']);
        $app->update(['build_status' => 'building']);

        // Trigger GitHub Actions run
        $success = $githubService->triggerBuild($app, $build);

        if ($success) {
            Log::info("GitHub workflow dispatch succeeded for build #{$build->id}");
        } else {
            Log::error("GitHub workflow dispatch failed for build #{$build->id}");
            $build->update([
                'build_status' => 'failed',
                'build_log' => 'Failed to trigger GitHub Action workflow. Please check your GitHub system settings (Token, Repository).'
            ]);
            $app->update(['build_status' => 'failed']);
        }
    }
}
