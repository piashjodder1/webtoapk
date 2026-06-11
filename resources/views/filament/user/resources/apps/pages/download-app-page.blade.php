<x-filament-panels::page>
    <style>
        /* Force page background to white to match the card */
        body, .fi-main {
            background-color: #ffffff !important;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid #e5e7eb;
            border-top-color: #4f46e5;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <div wire:poll.3s style="width: 100%; max-width: 600px; margin: 0 auto; padding: 2rem 1rem;">
        <div style="display: flex; flex-direction: column; align-items: center;">
            
            {{-- App Header --}}
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
                @if($record->icon_path)
                    <img src="{{ app(\App\Services\StorageService::class)->getUrl($record->icon_path) }}" alt="App Icon" style="width: 120px; height: 120px; border-radius: 24px; object-fit: cover; border: 1px solid #f3f4f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 24px; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; border: 1px solid #f3f4f6; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width: 48px; height: 48px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-6 3h.008v.008H6.75V15z" />
                        </svg>
                    </div>
                @endif
                <div style="text-align: center;">
                    <h1 style="font-size: 1.75rem; font-weight: 800; color: #111827; letter-spacing: -0.025em; margin: 0;">{{ $record->app_name }}</h1>
                    <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem; font-family: monospace;">{{ $record->package_name }}</p>
                </div>
            </div>

            {{-- Status Section --}}
            <div style="width: 100%;">
                @if(in_array($record->build_status, ['queued', 'building']))
                    
                    <div style="display: flex; flex-direction: column; align-items: center; padding: 3rem 2rem; background-color: #f5f3ff; border-radius: 32px; border: 1px solid #ede9fe; margin-bottom: 2rem;">
                        <div class="spinner" style="margin-bottom: 1.5rem;"></div>
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #312e81; margin: 0 0 0.5rem 0;">
                            @if($record->build_status === 'queued') Waiting in Queue... @else Compiling App... @endif
                        </h2>
                        <p style="color: #4f46e5; text-align: center; font-size: 0.95rem; max-width: 280px; margin: 0 0 2rem 0; opacity: 0.8;">
                            Our cloud servers are building your app. This usually takes 2–4 minutes.
                        </p>
                        
                        <div style="background: white; padding: 1rem 1.5rem; border-radius: 16px; border: 1px solid #e0e7ff; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; align-items: center;"
                             x-data="{ seconds: 0 }"
                             x-init="setInterval(() => { seconds++ }, 1000)">
                            <span style="font-size: 10px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem;">Elapsed Time</span>
                            <div style="font-family: monospace; font-size: 1.75rem; font-weight: 700; color: #1f2937;">
                                <span x-text="Math.floor(seconds / 60).toString().padStart(2, '0')"></span><span style="opacity: 0.3; margin: 0 4px;">:</span><span x-text="(seconds % 60).toString().padStart(2, '0')"></span>
                            </div>
                        </div>
                    </div>

                @elseif($record->build_status === 'failed')

                    <div style="display: flex; flex-direction: column; align-items: center; padding: 3rem 2rem; background-color: #fef2f2; border-radius: 32px; border: 1px solid #fee2e2; margin-bottom: 2rem;">
                        <div style="width: 64px; height: 64px; border-radius: 50%; background-color: #fecaca; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" style="width: 32px; height: 32px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: #7f1d1d; margin: 0 0 0.5rem 0;">Build Failed</h2>
                        <p style="color: #dc2626; text-align: center; font-size: 0.95rem; max-width: 280px; margin: 0; opacity: 0.8;">
                            An error occurred while compiling your app. Please verify your settings and try rebuilding.
                        </p>
                    </div>

                @elseif($record->build_status === 'completed')

                    <div style="display: flex; flex-direction: column; align-items: center; padding: 2.5rem 2rem; background-color: #f0fdf4; border-radius: 32px; border: 1px solid #dcfce7; margin-bottom: 2rem;">
                        <div style="width: 64px; height: 64px; border-radius: 50%; background-color: #dcfce7; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#16a34a" style="width: 32px; height: 32px;">
                                <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; color: #14532d; margin: 0 0 0.5rem 0;">App is Ready!</h2>
                        <p style="color: #15803d; text-align: center; font-size: 0.95rem; margin: 0;">
                            Files will be <strong>auto-deleted after 10 minutes</strong>.
                        </p>
                    </div>

                    {{-- Download Buttons --}}
                    <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
                        @if(!empty($record->apk_url))
                            <a href="{{ $record->apk_url }}" target="_blank" style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1.25rem; background-color: #111827; color: white; border-radius: 20px; text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,0.1); transition: transform 0.2s;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="background-color: rgba(255,255,255,0.1); padding: 0.5rem; border-radius: 12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 24px; height: 24px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-6 3h.008v.008H6.75V15z" />
                                        </svg>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <span style="font-weight: 700; font-size: 1.125rem; line-height: 1.2;">Download APK</span>
                                        <span style="font-size: 0.75rem; color: #9ca3af; font-weight: 500; margin-top: 2px;">For manual installation</span>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 24px; height: 24px; color: #9ca3af;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        @endif

                        @if(!empty($record->aab_url))
                            <a href="{{ $record->aab_url }}" target="_blank" style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1.25rem; background: linear-gradient(to right, #4f46e5, #7c3aed); color: white; border-radius: 20px; text-decoration: none; box-shadow: 0 4px 14px rgba(79,70,229,0.3); transition: transform 0.2s;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="background-color: rgba(255,255,255,0.15); padding: 0.5rem; border-radius: 12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                                            <path d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347c-.75.412-1.667-.13-1.667-.986V5.653Z" />
                                        </svg>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <span style="font-weight: 700; font-size: 1.125rem; line-height: 1.2;">Download AAB</span>
                                        <span style="font-size: 0.75rem; color: #c7d2fe; font-weight: 500; margin-top: 2px;">For Google Play Store</span>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 24px; height: 24px; color: #a5b4fc;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        @endif

                        @if(!empty($record->keystore_data['keystore_url']))
                            <div style="margin-top: 1.5rem; width: 100%;">
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #4b5563; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Keystore Backup (Keep Safe!)</h3>
                                <div style="display: flex; gap: 0.75rem; flex-direction: column;">
                                    <a href="{{ $record->keystore_data['keystore_url'] }}" target="_blank" style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1rem; background-color: #f9fafb; color: #374151; border-radius: 16px; border: 1px solid #e5e7eb; text-decoration: none; transition: all 0.2s;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #6b7280;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                            <span style="font-weight: 600; font-size: 0.95rem;">Download release.jks</span>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px; color: #9ca3af;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                    </a>

                                    @if(!empty($record->keystore_data['credentials_url']))
                                        <a href="{{ $record->keystore_data['credentials_url'] }}" target="_blank" style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1rem; background-color: #f9fafb; color: #374151; border-radius: 16px; border: 1px solid #e5e7eb; text-decoration: none; transition: all 0.2s;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #6b7280;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                </svg>
                                                <span style="font-weight: 600; font-size: 0.95rem;">Download credentials.txt</span>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px; color: #9ca3af;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    <div style="text-align: center; color: #9ca3af; padding: 3rem 0;">Status unknown.</div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
