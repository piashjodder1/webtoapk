<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'WebToApp') }}</title>
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

        <header class="relative z-10 border-b border-slate-200 bg-[#f8fafc]/80 backdrop-blur-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">W</div>
                    <span class="text-lg font-semibold">{{ config('app.name', 'WebToApp') }}</span>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">Sign in</a>
                </nav>
            </div>
        </header>

        <main class="relative z-10 flex items-center justify-center px-6 py-20">
            <div class="w-full max-w-md">
                <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <h1 class="text-2xl font-bold text-gray-900 text-center">Create an account</h1>
                    <p class="mt-1 text-sm text-gray-500 text-center">Get started with WebToApp Builder</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        </div>
                        <button type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">
                            Create account
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700 transition">Sign in</a>
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
