<?php

namespace App\Filament\Resources\Builds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BuildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('app_id')
                    ->relationship('app', 'app_name')
                    ->disabled()
                    ->label('Application'),

                TextInput::make('github_run_id')
                    ->disabled()
                    ->label('GitHub Actions Run ID'),

                TextInput::make('build_type')
                    ->disabled()
                    ->label('Build Type (apk/aab)'),

                TextInput::make('build_status')
                    ->disabled()
                    ->label('Status'),

                Textarea::make('build_log')
                    ->disabled()
                    ->rows(20)
                    ->fontFamily('mono')
                    ->label('Build Logs Output'),
            ]);
    }
}
