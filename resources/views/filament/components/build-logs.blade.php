<div class="p-4">
    @if ($build && $build->build_log)
        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto whitespace-pre-wrap leading-relaxed shadow-inner">
            {{ $build->build_log }}
        </div>
    @else
        <div class="text-gray-500 italic text-sm">No logs available for this build yet.</div>
    @endif
</div>
