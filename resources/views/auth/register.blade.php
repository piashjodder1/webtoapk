<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ \App\Models\Setting::get('seo_title', config('app.name', 'Website To App Builder')) }}</title>
    @include('partials.seo')
    @include('partials.styles')
    <link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { primary: { 50: '#EEF0FF', 100: '#C7CAFA', 500: '#5B52FF', 600: '#3B30E8', 700: '#2921A3' } },
            fontFamily: { sans: ['Nunito', 'sans-serif'], bubblegum: ['"Bubblegum Sans"', 'cursive'] }
          }
        }
      }
    </script>
    @endif
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen w-full bg-white relative overflow-hidden">
        <!-- Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="absolute inset-0 z-0 pointer-events-none"
            style="background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 30px; -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%); mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%);">
        </div>

        @include('partials.header')

        <main class="relative z-10 flex items-center justify-center px-4 py-20">
            <div class="w-full max-w-md">
                <div class="rounded-3xl border border-slate-200 bg-white/80 backdrop-blur-xl p-6 sm:p-10 shadow-xl shadow-primary-600/10">
                    <h1 class="text-3xl font-bubblegum text-gray-900 text-center">Create an account</h1>
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
                                class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" required
                                class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition">
                        </div>
                        <button type="submit"
                            class="w-full rounded-xl bg-primary-600 px-4 py-3.5 text-base font-bold text-white hover:bg-primary-700 shadow-lg shadow-primary-600/30 transition transform hover:-translate-y-0.5">
                            Create account
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 transition">Sign in</a>
                    </p>
                </div>
            </div>
        </main>
    </div>
    @include('partials.scripts')
</body>
</html>
