<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Build ID</span>
            <span class="font-bold text-gray-900 dark:text-white">#{{ $build->id }}</span>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Build Format</span>
            <span class="font-bold text-gray-900 dark:text-white uppercase">{{ $build->build_type }}</span>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Status</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize
                @if($build->build_status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @elseif($build->build_status === 'failed') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @elseif($build->build_status === 'building') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                {{ $build->build_status }}
            </span>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Triggered At</span>
            <span class="text-gray-900 dark:text-white">{{ $build->created_at->format('M d, Y h:i A') }}</span>
        </div>
    </div>

    <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Build Output Console</h4>
        <div class="bg-gray-950 text-emerald-400 font-mono text-xs p-4 rounded-lg overflow-x-auto max-h-72 border border-gray-900 shadow-inner select-text">
            @if($build->build_log)
                <pre class="whitespace-pre-wrap leading-relaxed">{{ $build->build_log }}</pre>
            @else
                <div class="flex items-center space-x-2 text-gray-500 py-4 justify-center">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Building in progress... Logs will be updated once compiled.</span>
                </div>
            @endif
        </div>
    </div>

    @if($build->build_status === 'completed')
    <div class="flex space-x-2 pt-2 justify-end">
        @if($build->apk_url)
            <a href="{{ $build->apk_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-md shadow transition">
                Download APK
            </a>
        @endif
        @if($build->aab_url)
            <a href="{{ $build->aab_url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-md shadow transition">
                Download AAB
            </a>
        @endif
    </div>
    @endif
</div>
