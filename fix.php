<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$appModel = App\Models\App::latest()->first();
if ($appModel) {
    $k = $appModel->keystore_data ?? [];
    $k['base64_keystore'] = null;
    $appModel->keystore_data = $k;
    $appModel->save();
    echo "Fixed App " . $appModel->id . "\n";
} else {
    echo "No apps found\n";
}
