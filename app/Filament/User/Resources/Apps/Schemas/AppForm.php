<?php

namespace App\Filament\User\Resources\Apps\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\ConditionalRender;
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
                        Section::make('App Details')
                            ->description('Configure your application identity and target URL')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('app_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('My WebView App')
                                    ->label('App Name'),

                                TextInput::make('website_url')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://example.com')
                                    ->label('Website URL'),

                                TextInput::make('package_name')
                                    ->required()
                                    ->regex('/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/i')
                                    ->placeholder('com.example.myapp')
                                    ->helperText('Must be a unique Android package name (e.g. com.company.appname)')
                                    ->label('Package Name'),
                            ]),

                        Section::make('App Branding')
                            ->description('Upload launcher icons and loading screen')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('icon_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('icons')
                                    ->label('App Icon')
                                    ->helperText('512x512 PNG format recommended'),

                                FileUpload::make('splash_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('splashes')
                                    ->label('Splash Screen')
                                    ->helperText('1242x2208 PNG format recommended'),
                            ]),

                        Section::make('App Features')
                            ->description('Toggle WebView specific configurations')
                            ->columnSpan(3)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Toggle::make('enable_pull_refresh')
                                            ->label('Pull to Refresh')
                                            ->helperText('Allow pulling down to reload website page'),

                                        Toggle::make('enable_offline_page')
                                            ->label('Offline Page')
                                            ->helperText('Show custom offline view when network is lost'),

                                        Toggle::make('enable_push_notification')
                                            ->label('Push Notification (OneSignal)')
                                            ->helperText('Send push notifications to users via OneSignal')
                                            ->reactive(),

                                    ]),

                                ConditionalRender::make()
                                    ->visible(fn ($get) => $get('enable_push_notification'))
                                    ->schema([
                                        TextInput::make('onesignal_app_id')
                                            ->label('OneSignal App ID')
                                            ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
                                            ->helperText('Required if Push Notification is enabled. Get from OneSignal Dashboard > App Settings > Keys & IDs.')
                                            ->required(fn ($get) => $get('enable_push_notification')),
                                    ]),

                            ])
                    ])
            ]);
    }
}
