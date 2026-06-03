<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'Standard User',
                    ])
                    ->default('user')
                    ->required(),

                Toggle::make('is_suspended')
                    ->label('Suspend User Account')
                    ->default(false),

                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($context) => $context === 'create')
                    ->helperText('Leave blank to keep current password when editing.'),
            ]);
    }
}
