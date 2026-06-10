<x-filament-widgets::widget>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Apps -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between" style="min-height: 120px;">
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
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between" style="min-height: 120px;">
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

        <!-- Active Plan -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between" style="min-height: 120px;">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center mb-3 text-[#3B30E8]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Active Plan</p>
                <p class="text-lg font-bold py-1 text-[#3B30E8] truncate leading-tight">{{ $activePlanName }}</p>
            </div>
        </div>

        <!-- Credits Card -->
        <div class="relative overflow-hidden rounded-[1.5rem] shadow-sm border border-gray-100 p-4 flex flex-col justify-between text-white" style="min-height: 120px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);">
            <!-- Glassmorphism overlay circles -->
            <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-white/5 blur-xl"></div>
            <div class="absolute -left-6 -bottom-6 w-24 h-24 rounded-full bg-indigo-500/10 blur-2xl"></div>
            
            <div class="flex justify-between items-start z-10">
                <div>
                    <p class="text-indigo-200 text-[9px] font-bold uppercase tracking-wider">Credits</p>
                    <p class="text-xs font-medium opacity-90 mt-0.5 text-indigo-100">Remaining</p>
                </div>
            </div>
            
            <div class="mt-1 mb-1 z-10">
                <p class="text-2xl font-extrabold tracking-tight">{{ $remainingCredits ?? 0 }} <span class="text-xs font-medium text-indigo-200">Credits</span></p>
            </div>
            
            <div class="border-t border-white/10 pt-1.5 text-[9px] font-mono text-indigo-200 z-10">
                <span class="opacity-60">USED CDIT:</span>
                <span class="font-bold text-white ml-0.5">{{ $usedCredits ?? 0 }}</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
