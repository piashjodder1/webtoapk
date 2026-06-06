<x-filament-widgets::widget>
    <!-- Welcome Banner -->
    <div class="rounded-[1.5rem] p-6 relative overflow-hidden bg-[#EEF2FF] flex items-center justify-between shadow-sm border border-indigo-50">
        <div class="relative z-10 w-full flex flex-col items-start space-y-3">
            <div class="flex items-center gap-2">
                <span class="text-xl">👋</span>
                <span class="text-sm font-bold text-[#1E1B4B]">Welcome back,</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-[#1E1B4B] leading-tight">
                {{ auth()->user()->name }}!
            </h2>
            <p class="text-[#4338CA] text-xs mt-1 max-w-sm leading-relaxed">
                Turn any website into a stunning, native Android app in just a few clicks.
            </p>
            <a href="{{ App\Filament\User\Resources\Apps\AppResource::getUrl('create') }}" style="text-decoration: none; background-color: #3B30E8;" onmouseover="this.style.backgroundColor='#5B52FF'" onmouseout="this.style.backgroundColor='#3B30E8'" class="mt-2 text-white px-5 py-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold transition-colors shadow-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New App
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
