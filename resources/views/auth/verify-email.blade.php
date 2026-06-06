<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - {{ config('app.name', 'WebToApp') }}</title>
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
                <div class="rounded-3xl border border-slate-200 bg-white/80 backdrop-blur-xl p-6 sm:p-10 shadow-xl shadow-primary-600/10 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-3xl mx-auto mb-5">
                        📧
                    </div>
                    <h1 class="text-3xl font-bubblegum text-gray-900">Verify your email</h1>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent to <strong>{{ auth()->user()->email }}</strong>.
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col gap-3">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl bg-primary-600 px-4 py-3.5 text-base font-bold text-white hover:bg-primary-700 shadow-lg shadow-primary-600/30 transition transform hover:-translate-y-0.5">
                                Resend Verification Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3.5 text-base font-bold text-gray-700 hover:bg-slate-50 transition transform hover:-translate-y-0.5">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
