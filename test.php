<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$form = new App\Filament\User\Resources\Apps\Schemas\AppForm();
$schema = \Filament\Schemas\Schema::make();
$form->configure($schema);
echo "loaded successfully";
