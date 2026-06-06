<?php

namespace Database\Factories;

use App\Models\App;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<App>
 */
class AppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = $this->faker->word();
        $appWord = $this->faker->word();

        return [
            'user_id'                  => User::factory(),
            'app_name'                 => $this->faker->words(2, true) . ' App',
            'website_url'              => $this->faker->url(),
            'package_name'             => 'com.' . strtolower($company) . '.' . strtolower($appWord),
            'icon_path'                => null,
            'splash_path'              => null,
            'enable_pull_refresh'      => $this->faker->boolean(),
            'enable_offline_page'      => $this->faker->boolean(),
            'enable_push_notification' => false,
            'onesignal_app_id'         => null,
            'version_name'             => '1.0.0',
            'version_code'             => 1,
            'build_status'             => 'pending',
            'apk_url'                  => null,
            'aab_url'                  => null,
        ];
    }
}
