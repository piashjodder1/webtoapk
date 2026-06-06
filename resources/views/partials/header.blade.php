@props(['fixed' => false])

<nav id="navbar" class="{{ $fixed ? '' : 'scrolled' }}" style="{{ $fixed ? '' : 'position: relative; background: rgba(255,255,255,1);' }}">
  <div class="nav-inner">
    <a href="/" class="logo">
      @if(\App\Models\Setting::get('site_logo'))
          <img src="{{ Storage::url(\App\Models\Setting::get('site_logo')) }}" alt="Site Logo" style="height: 34px;">
      @else
          <div class="logo-icon">W</div>
          {{ config('app.name', 'WebToApp') }}
      @endif
    </a>
    <div class="nav-actions">
      @if (Route::has('login'))
          @auth
              <a href="{{ url('/userdashboard') }}">
                  <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl(auth()->user()) }}" alt="Avatar" style="height: 36px; width: 36px; border-radius: 50%; border: 2px solid var(--primary-pale);">
              </a>
          @else
              <a href="{{ route('login') }}" class="btn-primary" style="display:inline-flex; align-items:center; text-decoration:none;">Log In</a>
          @endauth
      @endif
    </div>
  </div>
</nav>
