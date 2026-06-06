<?php

namespace App\Filament\User\Widgets;

use App\Models\App;
use App\Models\Build;
use App\Jobs\TriggerAppBuildJob;
use App\Services\StorageService;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAppsWidget extends BaseWidget
{
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    protected static ?string $heading = 'Recent Projects';

    public function table(Table $table): Table
    {
        $storageService = new StorageService();

        return $table
            ->query(
                App::query()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)
            ->searchable(false)
            ->columns([
                ImageColumn::make('icon_path')
                    ->label('Icon')
                    ->state(fn (App $record) => $record->icon_path ? $storageService->getUrl($record->icon_path) : null)
                    ->circular()
                    ->defaultImageUrl(asset('images/default-app-icon.png')),

                TextColumn::make('app_name')
                    ->label('App Name')
                    ->weight('bold'),

                TextColumn::make('build_status')
                    ->label('Build Status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'queued',
                        'info' => 'building',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->url(fn (App $record) => \App\Filament\User\Resources\Apps\AppResource::getUrl('edit', ['record' => $record])),
                    DeleteAction::make(),

                    Action::make('triggerBuild')
                        ->label(fn (App $record) => $record->build_status === 'completed' ? 'Rebuild App' : 'Build App')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->form([
                            Select::make('build_type')
                                ->label('Build Format')
                                ->options([
                                    'apk' => 'Android APK (Testing)',
                                    'aab' => 'Android App Bundle - AAB (Play Store)',
                                    'both' => 'Build Both APK & AAB',
                                ])
                                ->default('both')
                                ->required(),
                        ])
                        ->action(function (App $record, array $data) {
                            $build = Build::create([
                                'app_id' => $record->id,
                                'build_type' => $data['build_type'],
                                'build_status' => 'queued',
                            ]);
                            $record->update(['build_status' => 'queued']);
                            TriggerAppBuildJob::dispatch($build);
                            Notification::make()
                                ->title('Build Queued')
                                ->body('The Android build job has been added to queue.')
                                ->success()
                                ->send();
                        }),

                    Action::make('download_apk')
                        ->label('Download APK')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (App $record) => $record->apk_url)
                        ->openUrlInNewTab()
                        ->visible(fn (App $record) => !empty($record->apk_url) && $record->build_status === 'completed'),

                    Action::make('download_aab')
                        ->label('Download AAB')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->url(fn (App $record) => $record->aab_url)
                        ->openUrlInNewTab()
                        ->visible(fn (App $record) => !empty($record->aab_url) && $record->build_status === 'completed'),
                ])
            ]);
    }
}
