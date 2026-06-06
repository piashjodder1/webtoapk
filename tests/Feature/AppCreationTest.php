<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppCreationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsVerifiedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Package Name Validation Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_valid_android_package_name_passes(): void
    {
        $validNames = [
            'com.example.myapp',
            'com.google.android',
            'org.mozilla.firefox',
            'io.company.app2',
        ];

        foreach ($validNames as $name) {
            $this->assertMatchesRegularExpression(
                '/^[a-zA-Z][a-zA-Z0-9_]*(\.[a-zA-Z][a-zA-Z0-9_]*)+$/',
                $name,
                "Expected '{$name}' to be a valid package name"
            );
        }
    }

    public function test_invalid_android_package_name_fails(): void
    {
        $invalidNames = [
            'com',            // no second segment
            '.com.example',   // starts with dot
            'com.123invalid', // segment starts with digit
            'com example',    // space in name
            '',               // empty
        ];

        foreach ($invalidNames as $name) {
            $this->assertDoesNotMatchRegularExpression(
                '/^[a-zA-Z][a-zA-Z0-9_]*(\.[a-zA-Z][a-zA-Z0-9_]*)+$/',
                $name,
                "Expected '{$name}' to fail package name validation"
            );
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // App Isolation Tests (users can only see their own apps)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_can_only_access_own_apps(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $app1 = App::factory()->create(['user_id' => $user1->id]);
        $app2 = App::factory()->create(['user_id' => $user2->id]);

        // user1's app is accessible to user1
        $appsForUser1 = App::where('user_id', $user1->id)->get();
        $this->assertTrue($appsForUser1->contains($app1));
        $this->assertFalse($appsForUser1->contains($app2));

        // user2's app is accessible to user2 only
        $appsForUser2 = App::where('user_id', $user2->id)->get();
        $this->assertTrue($appsForUser2->contains($app2));
        $this->assertFalse($appsForUser2->contains($app1));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Package Name Uniqueness Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_two_apps_cannot_have_same_package_name(): void
    {
        $user = User::factory()->create();

        App::factory()->create([
            'user_id'      => $user->id,
            'package_name' => 'com.duplicate.app',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        App::factory()->create([
            'user_id'      => $user->id,
            'package_name' => 'com.duplicate.app',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // App Model Relationship Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_app_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $app  = App::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $app->user);
        $this->assertEquals($user->id, $app->user->id);
    }

    public function test_app_has_many_builds(): void
    {
        $user = User::factory()->create();
        $app  = App::factory()->create(['user_id' => $user->id]);

        \App\Models\Build::factory()->count(3)->create(['app_id' => $app->id]);

        $this->assertCount(3, $app->fresh()->builds);
    }

    public function test_latest_build_returns_most_recent(): void
    {
        $user = User::factory()->create();
        $app  = App::factory()->create(['user_id' => $user->id]);

        $old = \App\Models\Build::factory()->create(['app_id' => $app->id, 'created_at' => now()->subDay()]);
        $new = \App\Models\Build::factory()->create(['app_id' => $app->id, 'created_at' => now()]);

        $this->assertEquals($new->id, $app->fresh()->latestBuild->id);
    }
}
