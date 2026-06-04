<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'SaaS Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Demo User
        User::create([
            'name' => 'SaaS User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Seed Default Settings
        $settings = [
            // GitHub Settings
            ['key' => 'github_repository', 'value' => 'your-username/flutter-webview-template', 'group' => 'github'],
            ['key' => 'github_token', 'value' => '', 'group' => 'github'],
            ['key' => 'github_workflow_id', 'value' => 'build_app.yml', 'group' => 'github'],

            // Firebase Settings
            ['key' => 'firebase_server_key', 'value' => '', 'group' => 'firebase'],
            ['key' => 'firebase_api_key', 'value' => '', 'group' => 'firebase'],



            // Storage Settings
            ['key' => 'storage_driver', 'value' => 'local', 'group' => 'storage'], // local, s3, r2
            ['key' => 's3_key', 'value' => '', 'group' => 'storage'],
            ['key' => 's3_secret', 'value' => '', 'group' => 'storage'],
            ['key' => 's3_bucket', 'value' => '', 'group' => 'storage'],
            ['key' => 's3_region', 'value' => '', 'group' => 'storage'],
            ['key' => 'r2_key', 'value' => '', 'group' => 'storage'],
            ['key' => 'r2_secret', 'value' => '', 'group' => 'storage'],
            ['key' => 'r2_bucket', 'value' => '', 'group' => 'storage'],
            ['key' => 'r2_endpoint', 'value' => '', 'group' => 'storage'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }
    }
}
