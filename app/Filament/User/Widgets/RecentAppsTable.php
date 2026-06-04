<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Apps\AppResource;
use App\Models\App;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Support\Facades\Auth;

class RecentAppsTable extends BaseTableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                App::where('user_id', Auth::id())
                    ->with('latestBuild')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('app_name')
                    ->label('App Name')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('package_name')
                    ->label('Package')
                    ->color('gray'),
                TextColumn::make('latestBuild.build_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'building' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (App $record): string => AppResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
