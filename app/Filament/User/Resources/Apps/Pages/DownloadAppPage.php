<?php

namespace App\Filament\User\Resources\Apps\Pages;

use App\Filament\User\Resources\Apps\AppResource;
use App\Models\App;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class DownloadAppPage extends Page
{
    protected static string $resource = AppResource::class;

    protected string $view = 'filament.user.resources.apps.pages.download-app-page';

    public App $record;

    public function mount(App $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Download ' . $this->record->app_name;
    }
}
