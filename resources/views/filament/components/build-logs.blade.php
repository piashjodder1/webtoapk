<div class="build-logs-container select-none" wire:poll.2s>
    <!-- Premium Status Header Card -->
    <div class="build-header">
        <div class="build-info-group">
            <div class="build-icon-wrapper 
                @if($build->build_status === 'completed') status-completed
                @elseif($build->build_status === 'failed') status-failed
                @elseif($build->build_status === 'building') status-building
                @else status-pending @endif">
                
                @if($build->build_status === 'completed')
                    <svg class="icon-svg animated-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($build->build_status === 'failed')
                    <svg class="icon-svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($build->build_status === 'building')
                    <svg class="icon-svg animated-spin" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                @else
                    <svg class="icon-svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>
            <div class="build-title-area">
                <div class="build-title-row">
                    <span class="build-id">Build #{{ $build->id }}</span>
                    <span class="build-type-badge">
                        {{ $build->build_type }}
                    </span>
                </div>
                <span class="build-time">{{ $build->created_at->format('M d, Y • h:i A') }}</span>
            </div>
        </div>

        <div>
            <span class="status-badge 
                @if($build->build_status === 'completed') badge-completed
                @elseif($build->build_status === 'failed') badge-failed
                @elseif($build->build_status === 'building') badge-building
                @else badge-pending @endif">
                {{ $build->build_status }}
            </span>
        </div>
    </div>

    <!-- Details Metadata List -->
    <div class="metadata-grid">
        <div class="metadata-item pr-sm-4">
            <span class="metadata-label">GitHub Action Run ID</span>
            <span class="metadata-value font-mono">
                @if($build->github_run_id)
                    <a href="https://github.com/{{ \App\Models\Setting::get('github_repository') }}/actions/runs/{{ $build->github_run_id }}" target="_blank" class="github-link">
                        {{ $build->github_run_id }}
                        <svg class="icon-tiny" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                @else
                    <span class="text-italic-gray">Not Assigned</span>
                @endif
            </span>
        </div>
        <div class="metadata-item pl-sm-4">
            <span class="metadata-label">Triggered By</span>
            <span class="metadata-value">{{ $build->app->user->name }}</span>
        </div>
    </div>

    <!-- Terminal Log Console -->
    <div class="console-wrapper">
        <div class="console-title-row">
            <span class="console-title">Live Console Output</span>
            @if($build->build_status === 'building')
                <div class="live-indicator">
                    <span class="ping-dot"></span>
                    <span>Runner is compiling...</span>
                </div>
            @endif
        </div>
        
        <div class="terminal-box">
            <!-- Terminal Header -->
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot dot-red"></span>
                    <span class="dot dot-yellow"></span>
                    <span class="dot dot-green"></span>
                </div>
                <span class="terminal-title">bash - flutter builder</span>
            </div>

            <!-- Log Text Body -->
            <div class="terminal-body scrollbar-thin">
                @if($build->build_log)
                    <pre class="terminal-pre">{{ $build->build_log }}</pre>
                @else
                    <div class="terminal-empty">
                        <div class="spinner"></div>
                        <p>Waiting for GitHub Action logs to stream back...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Download Files Section -->
    @if($build->build_status === 'completed' && ($build->apk_url || $build->aab_url))
        <div class="download-footer">
            @if($build->apk_url)
                <a href="{{ $build->apk_url }}" target="_blank" class="btn-download btn-apk">
                    <svg class="icon-button" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download APK (Release)
                </a>
            @endif
            @if($build->aab_url)
                <a href="{{ $build->aab_url }}" target="_blank" class="btn-download btn-aab">
                    <svg class="icon-button" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download AAB (Play Store)
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Scoped Custom CSS Styling -->
<style>
    .build-logs-container {
        font-family: system-ui, -apple-system, sans-serif;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        color: #1e293b;
    }
    
    .dark .build-logs-container {
        color: #f1f5f9;
    }
    
    /* Header Card */
    .build-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .dark .build-header {
        background: rgba(15, 23, 42, 0.4);
        border-color: #334155;
    }
    
    .build-info-group {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }
    
    .build-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
    }
    
    .status-completed { background: #ecfdf5; color: #059669; }
    .dark .status-completed { background: rgba(6, 78, 59, 0.35); color: #34d399; }
    
    .status-failed { background: #fff5f5; color: #dc2626; }
    .dark .status-failed { background: rgba(153, 27, 27, 0.35); color: #f87171; }
    
    .status-building { background: #eff6ff; color: #2563eb; }
    .dark .status-building { background: rgba(30, 58, 138, 0.35); color: #60a5fa; }
    
    .status-pending { background: #fef3c7; color: #d97706; }
    .dark .status-pending { background: rgba(120, 53, 4, 0.35); color: #fbbf24; }
    
    .icon-svg {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .animated-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    
    .animated-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .build-title-area {
        display: flex;
        flex-direction: column;
    }
    
    .build-title-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .build-id {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }
    .dark .build-id { color: #f8fafc; }
    
    .build-type-badge {
        font-family: monospace;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        background: #f1f5f9;
        color: #475569;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
    }
    .dark .build-type-badge { background: #334155; color: #cbd5e1; }
    
    .build-time {
        font-size: 0.725rem;
        color: #64748b;
        margin-top: 0.125rem;
    }
    .dark .build-time { color: #94a3b8; }
    
    /* Badges */
    .status-badge {
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        border: 1px solid transparent;
        display: inline-block;
    }
    .badge-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
    .badge-failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }
    .badge-building { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    
    /* Details Metadata Grid */
    .metadata-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.875rem 1.25rem;
    }
    
    .dark .metadata-grid {
        background: rgba(30, 41, 59, 0.25);
        border-color: #334155;
    }
    
    @media (min-width: 640px) {
        .metadata-grid {
            grid-template-columns: 1fr 1fr;
        }
        .pr-sm-4 { padding-right: 1.25rem; border-right: 1px solid #e2e8f0; }
        .dark .pr-sm-4 { border-right-color: #334155; }
        .pl-sm-4 { padding-left: 1.25rem; }
    }
    
    .metadata-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
    }
    
    .metadata-label {
        color: #64748b;
        font-weight: 500;
    }
    .dark .metadata-label { color: #94a3b8; }
    
    .metadata-value {
        color: #1e293b;
        font-weight: 600;
    }
    .dark .metadata-value { color: #e2e8f0; }
    
    .github-link {
        color: #6366f1;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .github-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }
    
    .icon-tiny {
        width: 0.8rem;
        height: 0.8rem;
        display: inline-block;
        vertical-align: middle;
    }
    
    .text-italic-gray {
        color: #94a3b8;
        font-style: italic;
    }
    
    /* Console Header */
    .console-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .console-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 0.25rem;
    }
    
    .console-title {
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
    }
    
    .live-indicator {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.725rem;
        color: #3b82f6;
        font-weight: 600;
    }
    
    .ping-dot {
        width: 0.45rem;
        height: 0.45rem;
        background: #3b82f6;
        border-radius: 50%;
        display: inline-block;
        animation: blink-ping 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes blink-ping {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.6); opacity: 0.4; }
    }
    
    /* Terminal Console Window */
    .terminal-box {
        background: #0b0f19;
        border: 1px solid #1e293b;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    
    .terminal-header {
        background: #151d30;
        padding: 0.625rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #070a12;
    }
    
    .terminal-dots {
        display: flex;
        gap: 0.375rem;
    }
    
    .dot {
        width: 0.65rem;
        height: 0.65rem;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-red { background: #ef4444; }
    .dot-yellow { background: #f59e0b; }
    .dot-green { background: #10b981; }
    
    .terminal-title {
        font-family: monospace;
        font-size: 0.65rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        user-select: none;
    }
    
    .terminal-body {
        padding: 1.25rem;
        max-height: 260px;
        overflow-y: auto;
        background: #090d16;
    }
    
    .terminal-pre {
        margin: 0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.725rem;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-all;
        color: #10b981;
    }
    
    .terminal-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 0;
        color: #64748b;
        gap: 0.75rem;
    }
    
    .spinner {
        width: 1.5rem;
        height: 1.5rem;
        border: 2px solid rgba(148, 163, 184, 0.2);
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    /* Downloads Section */
    .download-footer {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e2e8f0;
    }
    .dark .download-footer {
        border-color: #334155;
    }
    
    @media (min-width: 640px) {
        .download-footer {
            flex-direction: row;
            justify-content: flex-end;
            gap: 0.875rem;
        }
    }
    
    .btn-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.55rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        color: #ffffff !important;
        cursor: pointer;
    }
    
    .btn-apk {
        background: #4f46e5;
    }
    .btn-apk:hover {
        background: #4338ca;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }
    .btn-apk:active {
        background: #3730a3;
    }
    
    .btn-aab {
        background: #059669;
    }
    .btn-aab:hover {
        background: #047857;
        box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.2);
    }
    .btn-aab:active {
        background: #065f46;
    }
    
    .icon-button {
        width: 1rem;
        height: 1rem;
    }
    
    /* Scrollbars */
    .scrollbar-thin::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgba(148, 163, 184, 0.5);
    }
</style>
