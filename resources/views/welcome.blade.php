<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Website To App Builder') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="font-[instrument-sans] text-gray-900 antialiased">
    <div class="min-h-screen w-full bg-[#f8fafc] relative">
        <div class="absolute inset-0 z-0 pointer-events-none"
            style="background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 30px; -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%); mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%);">
        </div>

        <header class="relative z-10 fixed inset-x-0 top-0 border-b border-slate-200 bg-[#f8fafc]/80 backdrop-blur-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">W</div>
                    <span class="text-lg font-semibold">{{ config('app.name', 'WebToApp') }}</span>
                </a>
                <nav class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <div class="flex items-center gap-2">
                                <a href="{{ url('/userdashboard') }}">
                                    <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl(auth()->user()) }}" alt="Avatar" class="h-9 w-9 rounded-full ring-2 ring-indigo-100 hover:ring-indigo-300 transition">
                                </a>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <main class="relative z-10">
            <section class="pt-32 pb-20 lg:pt-40 lg:pb-28">
                <div class="mx-auto max-w-7xl px-6 text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 mb-8">
                        <span class="h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Convert any website into a native Android app
                    </div>
                    <h1 class="text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                        Turn Your Website<br>
                        <span class="text-indigo-600">Into an Android App</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 leading-relaxed">
                        Enter your website URL, upload your branding, and we'll build a fully native Android 
                        WebView app — complete with APK and AAB files ready for the Play Store.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-4">
                        @auth
                            <a href="{{ url('/userdashboard') }}" class="rounded-xl bg-indigo-600 px-8 py-3.5 text-base font-semibold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Go to Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-8 py-3.5 text-base font-semibold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Create Your First App</a>
                            <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 px-8 py-3.5 text-base font-semibold text-gray-700 hover:bg-slate-50 transition">Sign In</a>
                        @endauth
                    </div>
                </div>
            </section>

            <section class="border-t border-slate-200 bg-white py-20">
                <div class="mx-auto max-w-7xl px-6">
                    <h2 class="text-center text-3xl font-bold text-gray-900">How It Works</h2>
                    <p class="mx-auto mt-3 max-w-xl text-center text-gray-500">Three simple steps to get your Android app live.</p>
                    <div class="mt-14 grid gap-8 md:grid-cols-3">
                        <div class="rounded-2xl bg-[#f8fafc] p-8 border border-slate-200">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 font-bold text-lg">1</div>
                            <h3 class="mt-5 text-lg font-semibold text-gray-900">Configure Your App</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">Enter your website URL, app name, package name, and upload your icon and splash screen.</p>
                        </div>
                        <div class="rounded-2xl bg-[#f8fafc] p-8 border border-slate-200">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 font-bold text-lg">2</div>
                            <h3 class="mt-5 text-lg font-semibold text-gray-900">Build Triggers</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">We dispatch a GitHub Actions workflow that compiles your native Android APK and AAB.</p>
                        </div>
                        <div class="rounded-2xl bg-[#f8fafc] p-8 border border-slate-200">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 font-bold text-lg">3</div>
                            <h3 class="mt-5 text-lg font-semibold text-gray-900">Download & Publish</h3>
                            <p class="mt-2 text-sm text-gray-500 leading-relaxed">Download your APK for testing or upload the AAB directly to Google Play Store.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20">
                <div class="mx-auto max-w-7xl px-6">
                    <h2 class="text-center text-3xl font-bold text-gray-900">Features</h2>
                    <p class="mx-auto mt-3 max-w-xl text-center text-gray-500">Everything you need to build a production-ready Android WebView app.</p>
                    <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">📱</div>
                            <div><h3 class="font-semibold text-gray-900">Native WebView</h3><p class="mt-1 text-sm text-gray-500">Full-featured Android WebView with JavaScript enabled.</p></div>
                        </div>
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">🎨</div>
                            <div><h3 class="font-semibold text-gray-900">Custom Branding</h3><p class="mt-1 text-sm text-gray-500">Upload your own icon, splash screen, and app name.</p></div>
                        </div>
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">🔄</div>
                            <div><h3 class="font-semibold text-gray-900">Pull to Refresh</h3><p class="mt-1 text-sm text-gray-500">Native swipe-to-reload for your WebView content.</p></div>
                        </div>
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">📡</div>
                            <div><h3 class="font-semibold text-gray-900">Offline Page</h3><p class="mt-1 text-sm text-gray-500">Custom offline screen when network connectivity is lost.</p></div>
                        </div>
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">🔔</div>
                            <div><h3 class="font-semibold text-gray-900">Push Notifications</h3><p class="mt-1 text-sm text-gray-500">OneSignal integration for push notification support.</p></div>
                        </div>
                        <div class="flex gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="shrink-0 mt-1">📦</div>
                            <div><h3 class="font-semibold text-gray-900">APK + AAB</h3><p class="mt-1 text-sm text-gray-500">Build both APK for testing and AAB for Play Store release.</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-t border-slate-200 bg-indigo-600 py-20">
                <div class="mx-auto max-w-3xl px-6 text-center">
                    <h2 class="text-3xl font-bold text-white">Ready to build your app?</h2>
                    <p class="mt-3 text-lg text-indigo-100">Enter your website URL and get a native Android app in minutes.</p>
                    <div class="mt-8">
                        @auth
                            <a href="{{ url('/userdashboard') }}" class="inline-flex rounded-xl bg-white px-8 py-3.5 text-base font-semibold text-indigo-600 hover:bg-indigo-50 transition">Go to Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex rounded-xl bg-white px-8 py-3.5 text-base font-semibold text-indigo-600 hover:bg-indigo-50 transition">Get Started Free</a>
                        @endauth
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-slate-200 bg-[#f8fafc] py-10">
            <div class="mx-auto max-w-7xl px-6 text-center text-sm text-slate-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'WebToApp Builder') }}. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
