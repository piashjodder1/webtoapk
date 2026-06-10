<?php

namespace App\Filament\User\Widgets;

use App\Models\App;
use App\Models\Build;
use Filament\Widgets\Widget;

class StatsGridCustomWidget extends Widget
{
    protected static bool $isLazy = false;
    protected string $view = 'filament.user.widgets.stats-grid-custom-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        $user = auth()->user();
        $userId = $user->id;
        
        $totalApps = App::where('user_id', $userId)->count();
        $totalBuilds = Build::whereHas('app', fn ($query) => $query->where('user_id', $userId))->count();

        $activeSub = $user->activeSubscription;

        return [
            'totalApps' => $totalApps,
            'totalBuilds' => $totalBuilds,
            'activePlanName' => $activeSub ? $activeSub->plan->name : 'No Active Plan',
            'remainingCredits' => $activeSub ? $activeSub->remaining_credits : 0,
            'usedCredits' => $activeSub ? $activeSub->used_credits : 0,
            'hasActivePlan' => (bool)$activeSub,
        ];
    }
}
