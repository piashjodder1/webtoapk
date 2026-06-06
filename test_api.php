<?php

$http = Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => 'Bearer ' . App\Models\Setting::get('github_token'),
    'Accept' => 'application/vnd.github+json',
    'X-GitHub-Api-Version' => '2022-11-28'
])->post('https://api.github.com/repos/' . App\Models\Setting::get('github_repository') . '/actions/workflows/build_app.yml/dispatches', [
    'ref' => 'main',
    'inputs' => [
        'app_id' => '1',
        'build_id' => '1',
        'app_name' => 'Test',
        'package_name' => 'com.test.app',
        'website_url' => 'https://test.com',
        'build_type' => 'apk',
        'callback_url' => 'https://test.com',
        'callback_token' => 'x'
    ]
]);

print_r([
    'status' => $http->status(),
    'body' => $http->json()
]);
