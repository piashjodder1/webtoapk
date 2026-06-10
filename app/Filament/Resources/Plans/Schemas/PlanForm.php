<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('$'),
                \Filament\Forms\Components\TextInput::make('max_apps')
                    ->numeric()
                    ->required()
                    ->default(1),
                \Filament\Forms\Components\Repeater::make('features')
                    ->label('Features')
                    ->simple(
                        \Filament\Forms\Components\TextInput::make('feature')
                            ->placeholder('e.g., Priority support')
                            ->required()
                    ),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
