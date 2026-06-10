<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('active'),
                \Filament\Forms\Components\TextInput::make('remaining_credits')
                    ->numeric()
                    ->required()
                    ->default(0),
                \Filament\Forms\Components\TextInput::make('used_credits')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }
}
