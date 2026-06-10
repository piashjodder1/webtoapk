<?php

namespace App\Filament\Resources\Apps\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ColorPicker;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Http;

class AppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('App Ownership & Details')
                    ->columnSpan(2)
                    ->columns(2)
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
                                    ->extraAttributes(['accept' => 'image/*'])
                                    ->disk(fn () => \App\Models\Setting::get('storage_driver', 'local') !== 'local' ? 'cloud_dynamic' : 'public')
                                    ->directory('icons')
                                    ->label('App Icon'),

                                FileUpload::make('splash_path')
                                    ->extraAttributes(['accept' => 'image/*'])
                                    ->disk(fn () => \App\Models\Setting::get('storage_driver', 'local') !== 'local' ? 'cloud_dynamic' : 'public')
                                    ->directory('splashes')
                                    ->label('Splash Screen'),
                                    
                                ColorPicker::make('theme_color')
                                    ->label('Theme Color')
                                    ->default('#FFFFFF')
                                    ->helperText('This color will be used for the app bar and bottom navigation background.'),
                            ]),

                        Section::make('App Version')
                            ->columnSpan(3)
                            ->columns(4)
                            ->schema([
                                TextInput::make('version_name')
                                    ->label('Version Name')
                                    ->placeholder('1.0.0')
                                    ->helperText('Human-readable version (e.g. 1.0.0, 2.5.1)'),

                                TextInput::make('version_code')
                                    ->label('Version Code')
                                    ->placeholder('1')
                                    ->numeric()
                                    ->helperText('Numeric version for Play Store (increment with each release)'),
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

                                        Toggle::make('enable_push_notification')
                                            ->label('Push Notification (OneSignal)')
                                            ->reactive(),
                                            
                                        Toggle::make('enable_exit_confirmation')
                                            ->label('Exit Confirmation Dialog'),

                                        Toggle::make('enable_loading_progress_bar')
                                            ->label('Loading Progress Bar'),

                                        Toggle::make('enable_external_links_in_browser')
                                            ->label('Open External Links in Browser'),
                                    ]),

                                TextInput::make('onesignal_app_id')
                                    ->label('OneSignal App ID')
                                    ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
                                    ->helperText('Required if Push Notification is enabled.')
                                    ->required(fn ($get) => $get('enable_push_notification'))
                                    ->hidden(fn ($get) => !$get('enable_push_notification')),
                            ]),
                            
                        Section::make('Custom Header')
                            ->columnSpan(3)
                            ->schema([
                                Toggle::make('enable_custom_header')
                                    ->label('Enable Custom Header')
                                    ->reactive(),
                                
                                FileUpload::make('header_logo')
                                    ->label('Header Logo')
                                    ->extraAttributes(['accept' => 'image/*'])
                                    ->disk(fn () => \App\Models\Setting::get('storage_driver', 'local') !== 'local' ? 'cloud_dynamic' : 'public')
                                    ->directory('headers')
                                    ->helperText('Upload a logo to show in the app header')
                                    ->visible(fn ($get) => $get('enable_custom_header')),
                            ]),

                        Section::make('Bottom Navigation')
                            ->columnSpan(3)
                            ->schema([
                                Toggle::make('enable_bottom_navigation')
                                    ->label('Enable Bottom Navigation Menu')
                                    ->reactive(),
                                    
                                Repeater::make('bottom_navigation_items')
                                    ->label('Menu Items')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->label('Item Name'),
                                        TextInput::make('url')
                                            ->required()
                                            ->url()
                                            ->label('URL to load'),
                                        Textarea::make('svg')
                                            ->required()
                                            ->label('SVG Code')
                                            ->helperText('Paste raw SVG code for the icon'),
                                    ])
                                    ->columns(3)
                                    ->visible(fn ($get) => $get('enable_bottom_navigation'))
                            ]),
                            
                        Section::make('Keystore Data')
                            ->description('Advanced: Provide your own keystore credentials or leave empty to auto-generate')
                            ->columnSpan(3)
                            ->columns(2)
                            ->schema([
                                TextInput::make('keystore_data.key_alias')
                                    ->label('Key Alias')
                                    ->placeholder('upload')
                                    ->helperText('Leave empty to auto-generate.'),

                                TextInput::make('keystore_data.keystore_password')
                                    ->label('Keystore Password')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Leave empty to auto-generate.'),

                                TextInput::make('keystore_data.key_password')
                                    ->label('Key Password')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Leave empty to auto-generate.'),

                                Textarea::make('keystore_data.base64_keystore')
                                    ->label('Keystore (Base64)')
                                    ->columnSpanFull()
                                    ->helperText('Paste a base64 encoded .jks file, or leave empty to auto-generate.'),
                            ]),
            ]);
    }
}
