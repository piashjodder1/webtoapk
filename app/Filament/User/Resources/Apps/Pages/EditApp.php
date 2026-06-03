<?php

namespace App\Filament\User\Resources\Apps\Pages;

use App\Filament\User\Resources\Apps\AppResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApp extends EditRecord
{
    protected static string $resource = AppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
