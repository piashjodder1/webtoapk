<?php

namespace App\Filament\Resources\Builds\Pages;

use App\Filament\Resources\Builds\BuildResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Actions\Action;

class ListBuilds extends ListRecords
{
    protected static string $resource = BuildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_logs')
                ->label('Clear All Build Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    \App\Models\Build::truncate();
                    \Filament\Notifications\Notification::make()
                        ->title('All build logs have been cleared successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
