<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}!</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100">
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="h-6 w-6 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $this->getAppsCount() }}</p>
                        <p class="text-sm text-gray-500">Total Apps</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Apps</h2>
            @if ($this->getLatestApps()->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($this->getLatestApps() as $app)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                @if ($app->icon)
                                    <img src="{{ Storage::url($app->icon) }}" alt="" class="h-10 w-10 rounded-lg object-cover">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 font-semibold text-sm">
                                        {{ substr($app->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $app->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $app->package_name }}</p>
                                </div>
                            </div>
                            <a href="{{ \App\Filament\User\Resources\Apps\AppResource::getUrl('edit', ['record' => $app]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">Edit</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center">
                    <p class="text-gray-500">No apps yet.</p>
                    <a href="{{ \App\Filament\User\Resources\Apps\AppResource::getUrl('create') }}" class="mt-3 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">Create Your First App</a>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
