<?php

namespace App\Filament\Resources\Apps\Tables;

use App\Models\App;
use App\Services\StorageService;
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
