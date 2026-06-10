<?php

namespace App\Filament\User\Resources\Apps\Pages;

use App\Filament\User\Resources\Apps\AppResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Actions\Action;

class CreateApp extends CreateRecord
{
    use HasWizard;

    protected static string $resource = AppResource::class;

    protected function getSteps(): array
    {
        return \App\Filament\User\Resources\Apps\Schemas\AppForm::getSteps();
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Create App')
            ->icon('heroicon-m-check');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
