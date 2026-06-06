<?php

namespace App\Filament\User\Resources\Apps\Tables;

use App\Jobs\TriggerAppBuildJob;
use App\Models\App;
use App\Models\Build;
use App\Services\StorageService;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppsTable
{
    public static function configure(Table $table): Table
    {
        $storageService = new StorageService();

        return $table
            ->columns([
                ImageColumn::make('icon_path')
                    ->label('Icon')
                    ->state(fn (App $record) => $record->icon_path ? $storageService->getUrl($record->icon_path) : null)
                    ->circular()
                    ->defaultImageUrl(asset('images/default-app-icon.png')),

                TextColumn::make('app_name')
                    ->label('App Name')
                    ->searchable()
                    ->sortable()
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
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),

                    // Trigger Build Action
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
                            // Create build tracking record
                            $build = Build::create([
                                'app_id' => $record->id,
                                'build_type' => $data['build_type'],
                                'build_status' => 'queued',
                            ]);

                            $record->update(['build_status' => 'queued']);

                            // Dispatch queue job
                            TriggerAppBuildJob::dispatch($build);

                            Notification::make()
                                ->title('Build Queued')
                                ->body('The Android build job has been added to queue.')
                                ->success()
                                ->send();
                        }),

                    // Download APK
                    Action::make('download_apk')
                        ->label('Download APK')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (App $record) => $record->apk_url)
                        ->openUrlInNewTab()
                        ->visible(fn (App $record) => !empty($record->apk_url) && $record->build_status === 'completed'),

                    // Download AAB
                    Action::make('download_aab')
                        ->label('Download AAB')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->url(fn (App $record) => $record->aab_url)
                        ->openUrlInNewTab()
                        ->visible(fn (App $record) => !empty($record->aab_url) && $record->build_status === 'completed'),

                    // View Build Logs
                    Action::make('view_logs')
                        ->label('View Build Logs')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->modalHeading('Latest Build Status & Logs')
                        ->modalDescription(fn (App $record) => "Status and logs for app: {$record->app_name}")
                        ->modalContent(function (App $record) {
                            $latestBuild = $record->latestBuild;
                            if (!$latestBuild) {
                                return view('filament.components.build-logs-empty');
                            }
                            return view('filament.components.build-logs', ['build' => $latestBuild]);
                        })
                        ->visible(fn (App $record) => $record->latestBuild()->exists()),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
