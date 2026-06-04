<?php

namespace App\Filament\Resources\Apps\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('App Ownership & Details')
                            ->columnSpan(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->label('App Owner'),

                                TextInput::make('app_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('App Name'),

                                TextInput::make('website_url')
                                    ->required()
                                    ->url()
                                    ->label('Website URL'),

                                TextInput::make('package_name')
                                    ->required()
                                    ->regex('/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/i')
                                    ->label('Package Name'),
                            ]),

                        Section::make('Branding')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('icon_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('icons')
                                    ->label('App Icon'),

                                FileUpload::make('splash_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('splashes')
                                    ->label('Splash Screen'),
                            ]),

                        Section::make('Features')
                            ->columnSpan(3)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Toggle::make('enable_pull_refresh')
                                            ->label('Pull to Refresh'),

                                        Toggle::make('enable_offline_page')
                                            ->label('Offline Page'),


                                    ]),

                            ])
                    ])
            ]);
    }
}
