<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
            's3_key',
            's3_secret',
            's3_bucket',
            's3_region',
            'r2_key',
            'r2_secret',
            'r2_bucket',
            'r2_endpoint'
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
                        Tab::make('GitHub Configuration')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('github_repository')
                                            ->label('GitHub Repository')
                                            ->placeholder('owner/repository-name')
                                            ->required()
                                            ->helperText('The repository where the Flutter WebView Template resides.'),

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
                                            ->label('Active Storage Disk')
                                            ->options([
                                                'local' => 'Local Disk (public/storage)',
                                                's3' => 'Amazon S3',
                                                'r2' => 'Cloudflare R2',
                                            ])
                                            ->default('local')
                                            ->required()
                                            ->reactive()
                                            ->columnSpan(2),

                                        // Amazon S3 fields
                                        TextInput::make('s3_key')
                                            ->label('AWS S3 Access Key ID')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_secret')
                                            ->label('AWS S3 Secret Access Key')
                                            ->password()
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_bucket')
                                            ->label('AWS S3 Bucket Name')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),
                                        TextInput::make('s3_region')
                                            ->label('AWS S3 Region')
                                            ->placeholder('us-east-1')
                                            ->visible(fn ($get) => $get('storage_driver') === 's3'),

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
            } elseif (in_array($key, ['storage_driver', 's3_key', 's3_secret', 's3_bucket', 's3_region', 'r2_key', 'r2_secret', 'r2_bucket', 'r2_endpoint'])) {
                $group = 'storage';
            }

            // Save key
            if ($value !== null) {
                Setting::set($key, $value, $group);
            }
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}
