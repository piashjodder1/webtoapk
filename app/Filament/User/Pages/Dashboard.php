<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\AppsStatsOverview;
use App\Filament\User\Widgets\RecentAppsTable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    public static function getRoutePath(\Filament\Panel $panel): string
    {
        return static::$routePath;
    }

    public function getWidgets(): array
    {
        return [
            AppsStatsOverview::class,
            RecentAppsTable::class,
        ];
    }
}
