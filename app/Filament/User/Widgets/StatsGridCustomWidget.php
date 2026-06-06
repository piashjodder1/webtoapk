<?php

namespace App\Filament\User\Widgets;

use App\Models\App;
use Filament\Widgets\Widget;

class StatsGridCustomWidget extends Widget
{
    protected static bool $isLazy = false;
    protected string $view = 'filament.user.widgets.stats-grid-custom-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        $userId = auth()->id();
        $totalApps = App::where('user_id', $userId)->count();
        $totalBuilds = App::where('user_id', $userId)->where('build_status', '!=', 'pending')->count(); // Example heuristic for builds

        return [
            'totalApps' => $totalApps,
            'totalBuilds' => $totalBuilds,
        ];
    }
}
