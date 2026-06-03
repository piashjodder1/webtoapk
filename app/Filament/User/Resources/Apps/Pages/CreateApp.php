<?php

namespace App\Filament\User\Resources\Apps\Pages;

use App\Filament\User\Resources\Apps\AppResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApp extends CreateRecord
{
    protected static string $resource = AppResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
