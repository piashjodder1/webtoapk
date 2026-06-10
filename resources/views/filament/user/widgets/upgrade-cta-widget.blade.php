<x-filament-widgets::widget>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="w-10 h-10 shrink-0 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-sm text-gray-900">Upgrade to Plan</h4>
                <p class="text-xs text-gray-400 mt-0.5">build unlimited apps</p>
            </div>
        </div>
        <a href="{{ \App\Filament\User\Pages\PlansPage::getUrl() }}" 
            style="background-color: #3B30E8;" 
            onmouseover="this.style.backgroundColor='#5B52FF'" 
            onmouseout="this.style.backgroundColor='#3B30E8'" 
            class="w-full md:w-auto text-white px-4 py-2 rounded-lg flex items-center justify-center gap-1.5 text-xs font-bold whitespace-nowrap transition">
            Upgrade Now <span class="text-sm ml-0.5">→</span>
        </a>
    </div>
</x-filament-widgets::widget>
