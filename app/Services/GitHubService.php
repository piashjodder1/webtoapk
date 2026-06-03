<?php

namespace App\Services;

use App\Models\App;
use App\Models\Build;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    protected ?string $token;
    protected ?string $repository;
    protected ?string $workflowId;

    public function __construct()
    {
        $this->token = Setting::get('github_token');
        $this->repository = Setting::get('github_repository');
        $this->workflowId = Setting::get('github_workflow_id', 'build_app.yml');
    }

    /**
     * Get HTTP headers for GitHub API request.
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ];
    }

    /**
     * Trigger the GitHub Actions build workflow.
     */
    public function triggerBuild(App $app, Build $build): bool
    {
        if (!$this->token || !$this->repository) {
            Log::error('GitHub Configuration is missing. Token or Repository not set.');
            return false;
        }

        $storageService = new StorageService();
        $iconUrl = $app->icon_path ? $storageService->getUrl($app->icon_path) : '';
        $splashUrl = $app->splash_path ? $storageService->getUrl($app->splash_path) : '';

        $url = "https://api.github.com/repos/{$this->repository}/actions/workflows/{$this->workflowId}/dispatches";

        $inputs = [
            'app_id' => (string) $app->id,
            'build_id' => (string) $build->id,
            'app_name' => $app->app_name,
            'package_name' => $app->package_name,
            'website_url' => $app->website_url,
            'icon_url' => $iconUrl,
            'splash_url' => $splashUrl,
            'enable_pull_refresh' => $app->enable_pull_refresh ? 'true' : 'false',
            'enable_offline_page' => $app->enable_offline_page ? 'true' : 'false',
            'enable_push_notification' => $app->enable_push_notification ? 'true' : 'false',
            'enable_admob' => $app->enable_admob ? 'true' : 'false',
            'build_type' => $build->build_type,
            'callback_url' => route('api.build-callback'),
        ];

        // Fetch settings for integrations to pass them to actions if required
        $inputs['admob_app_id'] = Setting::get('admob_app_id_android') ?? '';
        $inputs['admob_banner_unit_id'] = Setting::get('admob_banner_unit_id') ?? '';
        $inputs['admob_interstitial_unit_id'] = Setting::get('admob_interstitial_unit_id') ?? '';

        Log::info("Triggering GitHub Workflow Dispatch at {$url}", $inputs);

        $response = Http::withHeaders($this->getHeaders())
            ->post($url, [
                'ref' => 'main',
                'inputs' => $inputs
            ]);

        if ($response->successful()) {
            return true;
        }

        Log::error('GitHub Workflow Dispatch failed: ' . $response->body());
        return false;
    }

    /**
     * Fetch the run status from GitHub Actions.
     */
    public function getRunStatus(string $runId): array
    {
        $url = "https://api.github.com/repos/{$this->repository}/actions/runs/{$runId}";

        $response = Http::withHeaders($this->getHeaders())->get($url);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'status' => $data['status'], // queued, in_progress, completed
                'conclusion' => $data['conclusion'], // success, failure, cancelled, etc.
                'html_url' => $data['html_url'],
            ];
        }

        return ['status' => 'unknown', 'conclusion' => null];
    }

    /**
     * Get run logs from GitHub Actions.
     */
    public function getRunLogs(string $runId): ?string
    {
        $url = "https://api.github.com/repos/{$this->repository}/actions/runs/{$runId}/logs";

        $response = Http::withHeaders($this->getHeaders())->get($url);

        if ($response->successful()) {
            return $response->body();
        }

        return null;
    }

    /**
     * Find latest workflow run for our build ID.
     */
    public function findLatestRunForBuild(int $buildId): ?string
    {
        $url = "https://api.github.com/repos/{$this->repository}/actions/runs";

        $response = Http::withHeaders($this->getHeaders())->get($url, [
            'event' => 'workflow_dispatch',
            'per_page' => 10
        ]);

        if ($response->successful()) {
            $runs = $response->json()['workflow_runs'] ?? [];
            foreach ($runs as $run) {
                // Fetch run details to see if inputs match
                // Note: inputs are not returned in the list endpoint in old GitHub API version, 
                // but we can query specific run job or wait for webhook callback.
            }
        }

        return null;
    }
}
