<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\App::all() as $appModel) {
    echo "App " . $appModel->id . ":\n";
    print_r($appModel->keystore_data);
    echo "\n";
}
