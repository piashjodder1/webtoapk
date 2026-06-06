<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\Build;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Build>
 */
class BuildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_id'        => App::factory(),
            'github_run_id' => null,
            'build_type'    => $this->faker->randomElement(['apk', 'aab', 'both']),
            'build_status'  => 'pending',
            'apk_url'       => null,
            'aab_url'       => null,
            'build_log'     => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'build_status'  => 'completed',
            'github_run_id' => (string) $this->faker->numberBetween(1000000, 9999999),
            'apk_url'       => 'https://example.com/app.apk',
            'aab_url'       => 'https://example.com/app.aab',
            'build_log'     => 'Build completed successfully.',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'build_status' => 'failed',
            'build_log'    => 'Gradle build failed: Could not resolve dependencies.',
        ]);
    }

    public function building(): static
    {
        return $this->state(fn () => [
            'build_status' => 'building',
            'github_run_id' => (string) $this->faker->numberBetween(1000000, 9999999),
        ]);
    }
}
