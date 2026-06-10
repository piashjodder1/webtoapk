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
        $headerLogoUrl = $app->header_logo ? $storageService->getUrl($app->header_logo) : '';

        // Prevent caching in GitHub Actions / Android Build
        $timestamp = time();
        if ($iconUrl) $iconUrl .= (str_contains($iconUrl, '?') ? '&' : '?') . 'v=' . $timestamp;
        if ($splashUrl) $splashUrl .= (str_contains($splashUrl, '?') ? '&' : '?') . 'v=' . $timestamp;
        if ($headerLogoUrl) $headerLogoUrl .= (str_contains($headerLogoUrl, '?') ? '&' : '?') . 'v=' . $timestamp;

        $url = "https://api.github.com/repos/{$this->repository}/actions/workflows/{$this->workflowId}/dispatches";

        $buildConfig = [
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
            'enable_exit_confirmation' => $app->enable_exit_confirmation ? 'true' : 'false',
            'enable_loading_progress_bar' => $app->enable_loading_progress_bar ? 'true' : 'false',
            'enable_external_links_in_browser' => $app->enable_external_links_in_browser ? 'true' : 'false',
            'enable_bottom_navigation' => $app->enable_bottom_navigation ? 'true' : 'false',
            'bottom_navigation_items' => $app->bottom_navigation_items ? json_encode($app->bottom_navigation_items) : '[]',
            'enable_custom_header' => $app->enable_custom_header ? 'true' : 'false',
            'header_logo_url' => $headerLogoUrl,
            'onesignal_app_id' => $app->onesignal_app_id ?? '',
            'theme_color' => $app->theme_color ?? '#FFFFFF',
            'version_name' => $app->version_name ?? '1.0.0',
            'version_code' => (string) ($app->version_code ?? 1),

            'build_type' => $build->build_type,
            'callback_url' => route('api.build-callback'),
            'callback_token' => Setting::get('build_callback_token', 'default_callback_secret_token_123'),

            'r2_key'        => Setting::get('r2_key') ?? '',
            'r2_secret'     => Setting::get('r2_secret') ?? '',
            'r2_bucket'     => Setting::get('r2_bucket') ?? '',
            'r2_endpoint'   => Setting::get('r2_endpoint') ?? '',
            'r2_public_url' => Setting::get('r2_public_url') ?? '',
            'storage_driver' => Setting::get('storage_driver') ?? 'local',

            'key_alias' => $app->keystore_data['key_alias'] ?? 'upload',
            'keystore_password' => $app->keystore_data['keystore_password'] ?? 'secret',
            'key_password' => $app->keystore_data['key_password'] ?? 'secret',
            'base64_keystore' => $app->keystore_data['base64_keystore'] ?? '',
        ];

        $inputs = [
            'build_config' => json_encode($buildConfig),
        ];

        Log::info("Triggering GitHub Workflow Dispatch at {$url} with combined JSON config.");

        $response = Http::withHeaders($this->getHeaders())
            ->post($url, [
                'ref' => 'main',
                'inputs' => $inputs
            ]);

        if (!$response->successful()) {
            Log::error('GitHub API Error: ' . $response->body());
            throw new \RuntimeException('GitHub API rejected the request. Error: ' . $response->body());
        }return true;
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
