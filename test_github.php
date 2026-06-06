<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$a = App\Models\App::first();
$b = App\Models\Build::latest()->first();
$s = new App\Services\GitHubService();
try {
    $s->triggerBuild($a, $b);
    echo "Success\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}
