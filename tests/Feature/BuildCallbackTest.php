<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\Build;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildCallbackTest extends TestCase
{
    use RefreshDatabase;

    private string $validToken = 'test-secret-token-12345';

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('build_callback_token', $this->validToken, 'general');
    }

    private function createBuildWithApp(): Build
    {
        $user = User::factory()->create();
        $app  = App::factory()->create(['user_id' => $user->id]);
        return Build::factory()->create(['app_id' => $app->id, 'build_status' => 'building']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Token Validation Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_rejects_request_with_missing_token(): void
    {
        $build = $this->createBuildWithApp();

        $response = $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'completed',
        ]);

        $response->assertStatus(422); // token is required
    }

    public function test_rejects_request_with_wrong_token(): void
    {
        $build = $this->createBuildWithApp();

        $response = $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'completed',
            'token'    => 'wrong-token',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized token']);
    }

    public function test_accepts_request_with_correct_token(): void
    {
        $build = $this->createBuildWithApp();

        $response = $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'completed',
            'token'    => $this->validToken,
        ]);

        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Status Update Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_updates_build_status_to_building(): void
    {
        $build = $this->createBuildWithApp();

        $this->postJson('/api/build-callback', [
            'build_id'       => $build->id,
            'status'         => 'building',
            'github_run_id'  => '99887766',
            'token'          => $this->validToken,
        ]);

        $this->assertDatabaseHas('builds', [
            'id'            => $build->id,
            'build_status'  => 'building',
            'github_run_id' => '99887766',
        ]);
    }

    public function test_updates_build_status_to_failed(): void
    {
        $build = $this->createBuildWithApp();

        $this->postJson('/api/build-callback', [
            'build_id'  => $build->id,
            'status'    => 'failed',
            'token'     => $this->validToken,
            'build_log' => 'Gradle error: dependency missing.',
        ]);

        $this->assertDatabaseHas('builds', [
            'id'           => $build->id,
            'build_status' => 'failed',
        ]);

        $this->assertDatabaseHas('apps', [
            'id'           => $build->app_id,
            'build_status' => 'failed',
        ]);
    }

    public function test_updates_build_status_to_completed_with_urls(): void
    {
        $build = $this->createBuildWithApp();

        $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'completed',
            'token'    => $this->validToken,
            'apk_url'  => 'https://cdn.example.com/app.apk',
            'aab_url'  => 'https://cdn.example.com/app.aab',
        ]);

        $this->assertDatabaseHas('builds', [
            'id'           => $build->id,
            'build_status' => 'completed',
            'apk_url'      => 'https://cdn.example.com/app.apk',
            'aab_url'      => 'https://cdn.example.com/app.aab',
        ]);

        $this->assertDatabaseHas('apps', [
            'id'           => $build->app_id,
            'build_status' => 'completed',
            'apk_url'      => 'https://cdn.example.com/app.apk',
        ]);
    }

    public function test_rejects_invalid_build_id(): void
    {
        $response = $this->postJson('/api/build-callback', [
            'build_id' => 99999,
            'status'   => 'completed',
            'token'    => $this->validToken,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('error.build_id', fn ($v) => !empty($v));
    }

    public function test_rejects_invalid_status(): void
    {
        $build = $this->createBuildWithApp();

        $response = $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'invalid_status',
            'token'    => $this->validToken,
        ]);

        $response->assertStatus(422);
    }

    public function test_completed_callback_returns_download_urls(): void
    {
        $build = $this->createBuildWithApp();

        $response = $this->postJson('/api/build-callback', [
            'build_id' => $build->id,
            'status'   => 'completed',
            'token'    => $this->validToken,
            'apk_url'  => 'https://cdn.example.com/app.apk',
            'aab_url'  => 'https://cdn.example.com/app.aab',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'apk_url', 'aab_url']);
        $response->assertJsonPath('apk_url', 'https://cdn.example.com/app.apk');
    }
}
