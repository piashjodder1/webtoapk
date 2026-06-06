<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class UserWelcomeWidget extends Widget
{
    protected static bool $isLazy = false;
    protected string $view = 'filament.user.widgets.user-welcome-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1;
}
