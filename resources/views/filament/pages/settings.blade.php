<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::actions
            :actions="[
                \Filament\Actions\Action::make('save')
                    ->label('Save Settings')
                    ->submit('save')
                    ->color('success'),
            ]"
        />
    </form>
</x-filament-panels::page>
