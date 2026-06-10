<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.settings';
    protected static \UnitEnum|string|null $navigationGroup = 'System Settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Fill form with current setting values
        $keys = [
            'github_repository',
            'github_token',
            'github_workflow_id',
            'build_callback_token',
            'storage_driver',
            'r2_key',
            'r2_secret',
            'r2_bucket',
            'r2_endpoint',
            'r2_public_url',
            's3_key',
            's3_secret',
            's3_bucket',
            's3_region',
            
            // Site Settings
            'site_logo',
            'favicon',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'seo_author',
            'seo_robots',
            'theme_color',
            'og_title',
            'og_description',
            'og_image',
            'og_url',
            'og_type',
            'twitter_card',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'canonical_url',
            'google_site_verification',
            'bing_verification',
            'default_registration_plan_id',
        ];

        $state = [];
        foreach ($keys as $key) {
            $state[$key] = Setting::get($key);
        }

        // Generate callback token if not exists
        if (empty($state['build_callback_token'])) {
            $state['build_callback_token'] = bin2hex(random_bytes(16));
            Setting::set('build_callback_token', $state['build_callback_token'], 'general');
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Site Settings')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Branding')
                                    ->schema([
                                        FileUpload::make('site_logo')
                                            ->label('Site/Header Logo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings/logos')
                                            ->helperText('Upload the main logo for the website header.'),
                                        
                                        FileUpload::make('favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings/favicons')
                                            ->helperText('Upload the favicon (e.g. .ico or .png).'),
                                    ])->columns(2),

                                Section::make('SEO Metadata')
                                    ->schema([
                                        TextInput::make('seo_title')->label('Website Title'),
                                        Textarea::make('seo_description')->label('Website Description')->rows(2),
                                        TextInput::make('seo_keywords')->label('Keywords (comma separated)'),
                                        TextInput::make('seo_author')->label('Author Name'),
                                        TextInput::make('seo_robots')->label('Robots')->default('index, follow'),
                                        TextInput::make('theme_color')->label('Theme Color')->default('#ffffff'),
                                        TextInput::make('canonical_url')->label('Canonical URL'),
                                    ])->columns(2),

                                Section::make('Open Graph (Facebook/Messenger)')
                                    ->schema([
                                        TextInput::make('og_title')->label('OG Title'),
                                        TextInput::make('og_description')->label('OG Description'),
                                        TextInput::make('og_type')->label('OG Type')->default('website'),
                                        TextInput::make('og_url')->label('OG URL'),
                                        FileUpload::make('og_image')->label('OG Image')->image()->disk('public')->directory('settings/og'),
                                    ])->columns(2),

                                Section::make('Twitter/X Card')
                                    ->schema([
                                        TextInput::make('twitter_card')->label('Twitter Card Type')->default('summary_large_image'),
                                        TextInput::make('twitter_title')->label('Twitter Title'),
                                        TextInput::make('twitter_description')->label('Twitter Description'),
                                        FileUpload::make('twitter_image')->label('Twitter Image')->image()->disk('public')->directory('settings/twitter'),
                                    ])->columns(2),

                                Section::make('Verifications')
                                    ->schema([
                                        TextInput::make('google_site_verification')->label('Google Site Verification'),
                                        TextInput::make('bing_verification')->label('Bing/MSValidate.01 Code'),
                                    ])->columns(2),
                            ]),

                        Tab::make('GitHub Configuration')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('github_repository')
                                            ->label('GitHub Repository')
                                            ->placeholder('owner/repository-name')
                                            ->required()
                                            ->helperText('The repository where the Kotlin Android WebView Template resides.'),

                                        TextInput::make('github_workflow_id')
                                            ->label('GitHub Workflow File')
                                            ->default('build_app.yml')
                                            ->required()
                                            ->helperText('The file name of the build actions workflow under .github/workflows/.'),

                                        TextInput::make('github_token')
                                            ->label('GitHub Personal Access Token (PAT)')
                                            ->password()
                                            ->dehydrated(fn ($state) => !empty($state))
                                            ->helperText('PAT with write permissions to trigger Actions workflow dispatch.'),

                                        TextInput::make('build_callback_token')
                                            ->label('Build Callback Secret Token')
                                            ->required()
                                            ->helperText('Configure this value as a secret named API_CALLBACK_TOKEN in your GitHub repository secrets so runners can authenticate updates.'),
                                    ]),
                            ]),

                        Tab::make('Cloud Storage')
                            ->icon('heroicon-o-cloud')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('storage_driver')
                                            ->label('Active Build Storage Disk')
                                            ->options([
                                                'local' => 'Local Disk (public/storage)',
                                                'r2'    => 'Cloudflare R2',
                                                's3'    => 'Amazon S3',
                                            ])
                                            ->default('local')
                                            ->required()
                                            ->reactive()
                                            ->helperText('Select where generated APK and AAB build files will be stored. Note: Application branding assets (icons, splash screens) are always saved on the server\'s local disk.')
                                            ->columnSpan(2),

                                        // Cloudflare R2 fields
                                        TextInput::make('r2_key')
                                            ->label('Cloudflare R2 Access Key ID')
                                            ->visible(fn ($get) => $get('storage_driver') === 'r2'),
                                        TextInput::make('r2_secret')
                                            ->label('Cloudflare R2 Secret Access Key')
                                            ->password()
                                            ->visible(fn ($get) => $get('storage_driver') === 'r2'),
                                        TextInput::make('r2_bucket')
                                            ->label('Cloudflare R2 Bucket Name')
                                            ->visible(fn ($get) => $get('storage_driver') === 'r2'),
                                        TextInput::make('r2_endpoint')
                                            ->label('Cloudflare R2 Endpoint URL')
                                            ->placeholder('https://xxxxxx.r2.cloudflarestorage.com')
                                            ->visible(fn ($get) => $get('storage_driver') === 'r2'),
                                        TextInput::make('r2_public_url')
                                            ->label('Cloudflare R2 Public URL / Custom Domain')
                                            ->placeholder('https://pub-xxxxxx.r2.dev or https://download.myapp.com')
                                            ->visible(fn ($get) => $get('storage_driver') === 'r2')
                                            ->columnSpan(2),

                                        // Amazon S3 fields
                                        TextInput::make('s3_key')
                                            ->label('AWS Access Key ID')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_secret')
                                            ->label('AWS Secret Access Key')
                                            ->password()
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_bucket')
                                            ->label('S3 Bucket Name')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_region')
                                            ->label('S3 Region')
                                            ->placeholder('us-east-1')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                    ]),
                            ]),

                        Tab::make('Plans & Subscriptions')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('default_registration_plan_id')
                                            ->label('Default Registration Plan')
                                            ->options(\App\Models\Plan::pluck('name', 'id')->prepend('None', ''))
                                            ->helperText('Select the default plan assigned to users automatically upon registration.')
                                            ->nullable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $formData = $this->form->getState();

        foreach ($formData as $key => $value) {
            // Determine settings group
            $group = 'general';
            if (str_starts_with($key, 'github_')) {
                $group = 'github';
            } elseif (in_array($key, [
                'storage_driver',
                'r2_key', 'r2_secret', 'r2_bucket', 'r2_endpoint', 'r2_public_url',
                's3_key', 's3_secret', 's3_bucket', 's3_region',
            ])) {
                $group = 'storage';
            } elseif (in_array($key, [
                'site_logo', 'favicon', 'theme_color'
            ])) {
                $group = 'branding';
            } elseif (str_starts_with($key, 'seo_') || str_starts_with($key, 'og_') || str_starts_with($key, 'twitter_') || str_contains($key, 'verification') || str_contains($key, 'url')) {
                $group = 'seo';
            } elseif ($key === 'default_registration_plan_id') {
                $group = 'plans';
            }

            // Save key
            if ($value !== null) {
                Setting::set($key, $value, $group);
            } elseif ($key === 'default_registration_plan_id') {
                Setting::set($key, '', $group);
            }
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}
