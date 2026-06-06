<div class="p-4" wire:poll.3s>
    @if ($build)
        @php
            // Calculate elapsed seconds since build started
            $elapsedSeconds = max(0, now()->diffInSeconds($build->created_at));
            $isBuilding = $build->build_status === 'building' || $build->build_status === 'queued';
            $isCompleted = $build->build_status === 'completed';
            $isFailed = $build->build_status === 'failed';
        @endphp

        <!-- Header section with status and timer -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">Status:</span>
                <span class="ml-2 px-2 py-1 text-xs rounded font-bold
                    @if($isBuilding) bg-blue-100 text-blue-800 
                    @elseif($isCompleted) bg-green-100 text-green-800 
                    @elseif($isFailed) bg-red-100 text-red-800 
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ strtoupper($build->build_status) }}
                </span>
            </div>
            <div class="font-mono text-sm text-gray-500 font-bold" id="timer-{{ $build->id }}">
                Elapsed: <span class="timer-value">00:00</span>
            </div>
        </div>

        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto whitespace-pre-wrap leading-relaxed shadow-inner max-h-96 overflow-y-auto" id="log-container-{{ $build->id }}">
            @if ($isAdmin ?? false)
                {{-- ADMIN PANEL: Real Live Log --}}
                @if ($build->build_log)
                    {!! nl2br(e($build->build_log)) !!}
                @else
                    <span class="text-gray-500 italic">Waiting for logs...</span>
                @endif
                @if($isBuilding)
                    <span class="animate-pulse">_</span>
                @endif
            @else
                {{-- USER PANEL: Simulated Log --}}
                <div id="simulated-logs-{{ $build->id }}"></div>
                @if($isBuilding)
                    <span class="animate-pulse" id="cursor-{{ $build->id }}">_</span>
                @endif
                
                @if($isFailed)
                    <div class="text-red-500 mt-2">BUILD FAILED! Please check your configuration.</div>
                @elseif($isCompleted)
                    <div class="text-green-500 font-bold mt-2">BUILD SUCCESSFUL!</div>
                @endif
            @endif
        </div>

        <script>
            (function() {
                // Timer Logic
                const timerEl = document.querySelector('#timer-{{ $build->id }} .timer-value');
                let elapsedSeconds = {{ $elapsedSeconds }};
                const isBuilding = {{ $isBuilding ? 'true' : 'false' }};
                
                function formatTime(sec) {
                    const m = Math.floor(sec / 60).toString().padStart(2, '0');
                    const s = (sec % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                }

                if (timerEl && isBuilding) {
                    timerEl.innerText = formatTime(elapsedSeconds);
                    if (!window.buildTimer_{{ $build->id }}) {
                        window.buildTimer_{{ $build->id }} = setInterval(() => {
                            elapsedSeconds++;
                            timerEl.innerText = formatTime(elapsedSeconds);
                        }, 1000);
                    }
                } else if (timerEl && !isBuilding) {
                    timerEl.innerText = formatTime(elapsedSeconds);
                    if (window.buildTimer_{{ $build->id }}) {
                        clearInterval(window.buildTimer_{{ $build->id }});
                    }
                }

                // Simulated Log Logic for User Panel
                @if (!($isAdmin ?? false))
                    const logContainer = document.getElementById('simulated-logs-{{ $build->id }}');
                    const mainContainer = document.getElementById('log-container-{{ $build->id }}');
                    if (logContainer) {
                        const fakeLogs = [
                            "Initializing build environment...",
                            "Fetching project dependencies...",
                            "Resolving App configuration...",
                            "Injecting custom App Name and Icon...",
                            "Setting up native Android WebView...",
                            "Starting Gradle Daemon...",
                            "Configuring Gradle build script...",
                            "Downloading required libraries...",
                            "Compiling Kotlin source code...",
                            "Generating R.java...",
                            "Processing resources...",
                            "Dexing classes...",
                            "Optimizing Dex output...",
                            "Assembling Release APK...",
                            "Signing Release APK...",
                            "Assembling Release App Bundle (AAB)...",
                            "Signing Release AAB...",
                            "Verifying signatures...",
                            "Finalizing build outputs...",
                            "Uploading to server..."
                        ];

                        // Calculate how many lines to show based on elapsed time
                        // Show a new line every 3-5 seconds roughly
                        let linesToShow = Math.floor(elapsedSeconds / 4);
                        if (!isBuilding) {
                            linesToShow = fakeLogs.length; // Show all if done
                        }

                        let html = '';
                        for (let i = 0; i < Math.min(linesToShow, fakeLogs.length); i++) {
                            html += fakeLogs[i] + "<br>";
                        }
                        
                        // Only update if changed to avoid flicker
                        if (logContainer.innerHTML !== html) {
                            logContainer.innerHTML = html;
                            mainContainer.scrollTop = mainContainer.scrollHeight;
                        }
                    }
                @endif
            })();
        </script>
    @else
        <div class="text-gray-500 italic text-sm">No build tracking record found.</div>
    @endif
</div>
