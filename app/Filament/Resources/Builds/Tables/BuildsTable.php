<?php

namespace App\Filament\Resources\Builds\Tables;

use App\Jobs\TriggerAppBuildJob;
use App\Models\Build;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BuildsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('app.app_name')
                    ->label('App Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('app.user.name')
                    ->label('Owner')
                    ->searchable(),

                TextColumn::make('github_run_id')
                    ->label('Run ID')
                    ->fontFamily('mono'),

                TextColumn::make('build_type')
                    ->label('Type')
                    ->uppercase()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('build_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'queued',
                        'info' => 'building',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('created_at')
                    ->label('Triggered At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make(),

                // View Logs
                Action::make('view_logs')
                    ->label('Logs')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading('Build Logs')
                    ->modalContent(fn (Build $record) => view('filament.components.build-logs', ['build' => $record])),

                // Rebuild Action
                Action::make('rebuild')
                    ->label('Rebuild')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Build $record) {
                        $record->update(['build_status' => 'queued', 'build_log' => null]);
                        $record->app->update(['build_status' => 'queued']);

                        TriggerAppBuildJob::dispatch($record);

                        Notification::make()
                            ->title('Rebuild Queued')
                            ->body('The rebuild job has been dispatched successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
