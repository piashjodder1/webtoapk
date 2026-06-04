<?php

namespace App\Filament\User\Widgets;

use App\Models\App;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AppsStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalApps = App::where('user_id', $userId)->count();
        $totalBuilds = \App\Models\Build::whereHas('app', fn($q) => $q->where('user_id', $userId))->count();
        $completedBuilds = \App\Models\Build::whereHas('app', fn($q) => $q->where('user_id', $userId))
            ->where('build_status', 'completed')
            ->count();

        return [
            Stat::make('Total Apps', $totalApps)
                ->description('Apps you have created')
                ->descriptionIcon('heroicon-o-squares-2x2')
                ->color('primary'),

            Stat::make('Total Builds', $totalBuilds)
                ->description('All time builds')
                ->descriptionIcon('heroicon-o-cog-6-tooth')
                ->color('gray'),

            Stat::make('Successful Builds', $completedBuilds)
                ->description('Completed successfully')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
