<?php

namespace App\Filament\User\Resources\Apps\Pages;

use App\Filament\User\Resources\Apps\AppResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use Filament\Resources\Pages\EditRecord\Concerns\HasWizard;

class EditApp extends EditRecord
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
            DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Rebuild App')
            ->icon('heroicon-o-rocket-launch');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index') . '?tableAction=view_logs&tableActionRecord=' . $this->record->getKey();
    }

    protected function afterSave(): void
    {
        $app = $this->record;

        $build = \App\Models\Build::create([
            'app_id' => $app->id,
            'build_type' => 'both',
            'build_status' => 'queued',
        ]);

        $app->update(['build_status' => 'queued']);

        \App\Jobs\TriggerAppBuildJob::dispatch($build);
    }
}
