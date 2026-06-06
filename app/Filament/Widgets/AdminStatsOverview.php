<?php

namespace App\Filament\Widgets;

use App\Models\App;
use App\Models\Build;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBuilds   = Build::count();
        $successBuilds = Build::where('build_status', 'completed')->count();
        $failedBuilds  = Build::where('build_status', 'failed')->count();
        $activeBuilds  = Build::where('build_status', 'building')->count();
        $successRate   = $totalBuilds > 0
            ? round(($successBuilds / $totalBuilds) * 100, 1)
            : 0;

        return [
            Stat::make('Total Users', User::count())
                ->description('Registered accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Apps', App::count())
                ->description('Apps created across all users')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('primary'),

            Stat::make('Active Builds', $activeBuilds)
                ->description('Currently building')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Successful Builds', $successBuilds)
                ->description('Completed successfully')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Failed Builds', $failedBuilds)
                ->description('Build failures')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Success Rate', $successRate . '%')
                ->description("Out of {$totalBuilds} total builds")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger')),
        ];
    }
}
