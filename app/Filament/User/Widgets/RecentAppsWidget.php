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
                        ->tooltip(fn () => ! auth()->user()->hasCredits() ? 'No build credits remaining. Click to buy a plan.' : null)
                        ->url(function (App $record) {
                            if (! auth()->user()->hasCredits()) {
                                session()->flash('error', 'You do not have enough credits to build this app. Please buy a plan.');
                                return \App\Filament\User\Pages\PlansPage::getUrl();
                            }
                            return null;
                        })
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
                            if (! auth()->user()->hasCredits()) {
                                Notification::make()
                                    ->title('No Credits Remaining')
                                    ->body('You do not have enough credits to build this app. Please buy a plan.')
                                    ->danger()
                                    ->send();
                                return;
                            }

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

                            return redirect(\App\Filament\User\Resources\Apps\Pages\DownloadAppPage::getUrl(['record' => $record->id]));
                        }),

                    // Download Page
                    Action::make('download_page')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (App $record) => \App\Filament\User\Resources\Apps\Pages\DownloadAppPage::getUrl(['record' => $record->id]))
                        ->visible(fn (App $record) => in_array($record->build_status, ['queued', 'building', 'completed'])),
                ])
            ]);
    }
}
