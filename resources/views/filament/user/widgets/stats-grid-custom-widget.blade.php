<x-filament-widgets::widget>
    <div class="grid grid-cols-2 gap-4">
        <!-- Total Apps -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div class="w-10 h-10 bg-[#EEF2FF] rounded-xl flex items-center justify-center mb-3 text-[#4338CA]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Total Apps</p>
                <p class="text-2xl font-bold py-1 text-gray-900">{{ $totalApps ?? 0 }}</p>
            </div>
        </div>
        
        <!-- Total Builds -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-3 text-emerald-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Total Builds</p>
                <p class="text-2xl font-bold py-1 text-gray-900">{{ $totalBuilds ?? 0 }}</p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
