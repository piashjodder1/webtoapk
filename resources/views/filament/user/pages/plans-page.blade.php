<x-filament-panels::page>
    <style>
        /* Hide the default left-aligned Filament header to use our custom centered one */
        header.fi-header {
            display: none !important;
        }
        
        /* Force the page background to be pure white */
        body, .fi-main {
            background-color: #ffffff !important;
        }
    </style>
    <div class="w-full py-4 sm:py-8">

        <!-- Custom Centered Header -->
        <div class="text-center w-full mb-16 sm:mb-20 pt-4 pb-4">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight" style="color: #111827;">
                Pricing & Plans
            </h2>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 w-full">
            @foreach($plans as $plan)
                @php
                    $isActive = isset($activePlanId) && $activePlanId == $plan->id;
                    $isPending = in_array($plan->id, $pendingPlanIds ?? []);
                @endphp
                
                <div class="relative flex flex-col rounded-2xl p-8 transition-shadow hover:shadow-lg" 
                     style="background-color: #ffffff; border: 1px solid {{ $isActive ? '#4f46e5' : '#e5e7eb' }}; {{ $isActive ? 'box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);' : 'box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);' }}">
                    
                    @if($isActive)
                        <div class="absolute" style="top: -12px; left: 50%; transform: translateX(-50%);">
                            <span class="inline-flex items-center rounded-full px-3 py-1 font-semibold shadow-sm" style="background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-size: 11px; white-space: nowrap;">
                                Current Plan
                            </span>
                        </div>
                    @endif

                    <div class="mb-6 text-center">
                        <h3 class="text-xl font-semibold" style="color: #111827;">
                            {{ $plan->name }}
                        </h3>
                    </div>

                    <div class="mb-8 flex items-baseline justify-center font-bold" style="color: #111827;">
                        <span style="font-size: 2.25rem; line-height: 2.5rem;">${{ number_format($plan->price, 0) }}</span>
                        <span class="ml-1 text-base font-medium" style="color: #6b7280;">/ forever</span>
                    </div>

                    <ul class="flex-1 space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg style="width: 20px; height: 20px; color: #4f46e5; flex-shrink: 0;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3 text-sm" style="color: #374151;">
                                Build up to <strong style="color: #111827;">{{ $plan->max_apps }}</strong> Apps
                            </span>
                        </li>
                        @if($plan->features)
                            @foreach($plan->features as $feature)
                                <li class="flex items-start">
                                    <svg style="width: 20px; height: 20px; color: #4f46e5; flex-shrink: 0;" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-3 text-sm" style="color: #374151;">
                                        {{ $feature }}
                                    </span>
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <div class="mt-auto">
                        @if($isActive)
                            <button disabled class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-center cursor-not-allowed" style="background-color: #f3f4f6; color: #9ca3af; border: none;">
                                Active Plan
                            </button>
                        @elseif($isPending)
                            <button disabled class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-center cursor-not-allowed" style="background-color: #fef3c7; color: #d97706; border: none;">
                                Pending Approval
                            </button>
                        @elseif($plan->price == 0)
                            <button disabled class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-center cursor-not-allowed" style="background-color: #f3f4f6; color: #9ca3af; border: none;">
                                Free Plan (Default)
                            </button>
                        @else
                            <div class="[&>button]:w-full [&>button]:rounded-xl [&>button]:py-3 [&>button]:font-semibold">
                                {{ ($this->purchaseAction)(['plan_id' => $plan->id]) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
