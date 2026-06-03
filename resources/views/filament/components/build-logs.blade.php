<div class="space-y-5 p-1 select-none">
    <!-- Premium Status Header Card -->
    <div class="bg-white dark:bg-gray-900/50 backdrop-blur-md rounded-xl p-4 border border-gray-100 dark:border-gray-800/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="p-2.5 rounded-lg
                @if($build->build_status === 'completed') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400
                @elseif($build->build_status === 'failed') bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400
                @elseif($build->build_status === 'building') bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400
                @else bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 @endif">
                
                @if($build->build_status === 'completed')
                    <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($build->build_status === 'failed')
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($build->build_status === 'building')
                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                @else
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">Build #{{ $build->id }}</h3>
                    <span class="text-[10px] tracking-wider uppercase px-2 py-0.5 rounded font-mono font-bold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        {{ $build->build_type }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $build->created_at->format('M d, Y • h:i A') }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="text-[11px] font-bold tracking-wider uppercase px-3 py-1 rounded-full shadow-sm select-none
                @if($build->build_status === 'completed') bg-emerald-500/10 text-emerald-600 border border-emerald-500/20
                @elseif($build->build_status === 'failed') bg-rose-500/10 text-rose-600 border border-rose-500/20
                @elseif($build->build_status === 'building') bg-blue-500/10 text-blue-600 border border-blue-500/20
                @else bg-amber-500/10 text-amber-600 border border-amber-500/20 @endif">
                {{ $build->build_status }}
            </span>
        </div>
    </div>

    <!-- Details Metadata List -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 dark:bg-gray-900/30 p-4 rounded-xl border border-gray-100/80 dark:border-gray-800/50 text-xs">
        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-gray-800/40 sm:border-b-0 sm:pr-4">
            <span class="text-gray-400 font-medium">GitHub Action Run ID</span>
            <span class="font-mono font-semibold text-gray-700 dark:text-gray-300">
                @if($build->github_run_id)
                    <a href="https://github.com/{{ \App\Models\Setting::get('github_repository') }}/actions/runs/{{ $build->github_run_id }}" target="_blank" class="text-indigo-600 hover:underline inline-flex items-center gap-1">
                        {{ $build->github_run_id }}
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                @else
                    <span class="text-gray-400 italic">Not Assigned</span>
                @endif
            </span>
        </div>
        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-gray-800/40 sm:border-b-0 sm:pl-4">
            <span class="text-gray-400 font-medium">Triggered By</span>
            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $build->app->user->name }}</span>
        </div>
    </div>

    <!-- Terminal Log Console -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Live Console Output</h4>
            @if($build->build_status === 'building')
                <div class="flex items-center space-x-1.5 text-xs text-blue-500">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                    <span class="font-semibold">Runner is compiling...</span>
                </div>
            @endif
        </div>
        
        <div class="bg-slate-950 text-slate-100 rounded-xl overflow-hidden border border-slate-900 shadow-lg flex flex-col">
            <!-- Terminal Header -->
            <div class="bg-slate-900/80 px-4 py-2.5 border-b border-slate-950/80 flex items-center justify-between">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500/90 block"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500/90 block"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500/90 block"></span>
                </div>
                <span class="text-[10px] font-mono tracking-widest text-slate-500 uppercase select-none">bash - flutter builder</span>
            </div>

            <!-- Log Text Body -->
            <div class="p-5 font-mono text-xs overflow-x-auto max-h-[300px] leading-relaxed select-text scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
                @if($build->build_log)
                    <pre class="whitespace-pre-wrap text-emerald-400/90">{{ $build->build_log }}</pre>
                @else
                    <div class="flex flex-col items-center justify-center py-10 space-y-3.5 text-slate-500">
                        <svg class="animate-spin h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-xs tracking-wide">Waiting for GitHub Action logs to stream back...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Download Files Section -->
    @if($build->build_status === 'completed' && ($build->apk_url || $build->aab_url))
        <div class="pt-2 border-t border-gray-100 dark:border-gray-800/40 flex flex-col sm:flex-row justify-end gap-3.5">
            @if($build->apk_url)
                <a href="{{ $build->apk_url }}" target="_blank" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs rounded-lg shadow-sm transition hover:shadow-md gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download APK (Release)
                </a>
            @endif
            @if($build->aab_url)
                <a href="{{ $build->aab_url }}" target="_blank" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-sm transition hover:shadow-md gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download AAB (Play Store)
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Custom Scrollbar Styling inside Blade -->
<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgba(71, 85, 105, 0.4);
        border-radius: 9999px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgba(71, 85, 105, 0.6);
    }
</style>
