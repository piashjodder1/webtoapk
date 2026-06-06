<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class UpgradeCtaWidget extends Widget
{
    protected static bool $isLazy = false;
    protected string $view = 'filament.user.widgets.upgrade-cta-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
}
