<?php

namespace App\Filament\Resources\Apps\Tables;

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

                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('app_name')
                    ->label('App Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('website_url')
                    ->label('Website URL')
                    ->limit(35),

                TextColumn::make('package_name')
                    ->label('Package Name')
                    ->fontFamily('mono')
                    ->searchable(),

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

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
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
                        ->label('Build App')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->form([
                            Select::make('build_type')
                                ->label('Build Format')
                                ->options([
                                    'apk' => 'Android APK (Release)',
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

                            return redirect(\App\Filament\Resources\Apps\Pages\DownloadAppPage::getUrl(['record' => $record->id]));
                        }),

                    // Download Page
                    Action::make('download_page')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (App $record) => \App\Filament\Resources\Apps\Pages\DownloadAppPage::getUrl(['record' => $record->id]))
                        ->visible(fn (App $record) => in_array($record->build_status, ['queued', 'building', 'completed'])),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
