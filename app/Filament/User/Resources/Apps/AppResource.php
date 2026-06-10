<?php

namespace App\Filament\User\Resources\Apps;

use App\Filament\User\Resources\Apps\Pages\CreateApp;
use App\Filament\User\Resources\Apps\Pages\EditApp;
use App\Filament\User\Resources\Apps\Pages\ListApps;
use App\Filament\User\Resources\Apps\Schemas\AppForm;
use App\Filament\User\Resources\Apps\Tables\AppsTable;
use App\Models\App;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class AppResource extends Resource
{
    protected static ?string $model = App::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    // protected static ?string $recordTitleAttribute = 'app_name';

    public static function form(Schema $schema): Schema
    {
        return AppForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApps::route('/'),
            'create' => CreateApp::route('/create'),
            'edit' => EditApp::route('/{record}/edit'),
            'download' => \App\Filament\User\Resources\Apps\Pages\DownloadAppPage::route('/{record}/download'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
