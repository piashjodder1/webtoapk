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
        return true;
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
            ->label('Build App')
            ->icon('heroicon-o-rocket-launch');
    }

    protected function afterCreate(): void
    {
        $app = $this->record;

        $build = \App\Models\Build::create([
            'app_id' => $app->id,
            'build_type' => 'both',
            'build_status' => 'queued',
        ]);

        \App\Jobs\TriggerAppBuildJob::dispatch($build);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index') . '?tableAction=view_logs&tableActionRecord=' . $this->record->getKey();
    }
}
