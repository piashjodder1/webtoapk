<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'user',
                    ]),

                IconColumn::make('is_suspended')
                    ->boolean()
                    ->label('Suspended'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggleSuspend')
                    ->label(fn (User $record) => $record->is_suspended ? 'Unsuspend' : 'Suspend')
                    ->icon(fn (User $record) => $record->is_suspended ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (User $record) => $record->is_suspended ? 'success' : 'danger')
                    ->action(function (User $record) {
                        $record->update(['is_suspended' => !$record->is_suspended]);

                        Notification::make()
                            ->title($record->is_suspended ? 'User Suspended' : 'User Activated')
                            ->body("User {$record->name} status has been updated.")
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
