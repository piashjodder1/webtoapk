<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('seo_title', config('app.name', 'Website To App Builder')) }}</title>
    @include('partials.seo')
    @include('partials.styles')
</head>
<body>

@include('partials.header', ['fixed' => true])

<!-- HERO -->
<section class="hero">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="badge fade-up">
    <span class="badge-dot"></span>
    Convert any website into a native Android app
  </div>

  <h1 class="fade-up delay-1">
    Turn Your Website<br>
    <span class="highlight">Into an Android App</span>
  </h1>

  <p class="fade-up delay-2">
    Enter your website URL, upload your branding, and we'll build a fully native Android WebView app — complete with APK and AAB files ready for the Play Store.
  </p>

  <div class="hero-actions fade-up delay-3">
    @auth
        <a href="{{ url('/userdashboard') }}" class="btn-hero btn-hero-primary" style="text-decoration:none;">
            Go to Dashboard
        </a>
    @else
        <a href="{{ route('register') }}" class="btn-hero btn-hero-primary" style="text-decoration:none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Create Your First App
        </a>
        <a href="{{ route('login') }}" class="btn-hero btn-hero-secondary" style="text-decoration:none;">Sign In</a>
    @endauth
  </div>

  <div class="hero-stats fade-up delay-4">
    <div class="stat">
      <div class="stat-num">12K+</div>
      <div class="stat-label">Apps Created</div>
    </div>
    <div class="stat">
      <div class="stat-num">4.9★</div>
      <div class="stat-label">User Rating</div>
    </div>
    <div class="stat">
      <div class="stat-num">99%</div>
      <div class="stat-label">Success Rate</div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section how-section" id="how">
  <div class="section-inner">
    <div class="reveal" style="text-align:center;">
      <span class="section-tag">Process</span>
    </div>
    <h2 class="section-title reveal" style="text-align:center;margin-top:10px;">How It Works</h2>
    <p class="section-sub reveal" style="text-align:center;margin:12px auto 0;">Three simple steps to get your Android app live.</p>

    <div class="steps-grid">
      <div class="step-card reveal">
        <div class="step-num">01</div>
        <div class="step-icon"><i data-lucide="settings" width="32" height="32"></i></div>
        <div class="step-title">Configure Your App</div>
        <div class="step-desc">Enter your website URL, app name, package name, and upload your icon and splash screen to get started.</div>
      </div>
      <div class="step-card reveal">
        <div class="step-num">02</div>
        <div class="step-icon"><i data-lucide="wrench" width="32" height="32"></i></div>
        <div class="step-title">Build Triggers</div>
        <div class="step-desc">We dispatch a GitHub Actions workflow that compiles your native Android APK and AAB automatically.</div>
      </div>
      <div class="step-card reveal">
        <div class="step-num">03</div>
        <div class="step-icon"><i data-lucide="rocket" width="32" height="32"></i></div>
        <div class="step-title">Download & Publish</div>
        <div class="step-desc">Download your APK for testing or upload the AAB directly to Google Play Store with one click.</div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
  <div class="section-inner">
    <span class="section-tag reveal">Capabilities</span>
    <h2 class="section-title reveal" style="margin-top:10px;">Everything You Need</h2>
    <p class="section-sub reveal">Build a production-ready Android WebView app with all the essentials baked in.</p>

    <div class="features-grid">
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="smartphone"></i></div>
        <div class="feature-title">Native WebView</div>
        <div class="feature-desc">Full-featured Android WebView with JavaScript enabled and hardware acceleration out of the box.</div>
      </div>
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="palette"></i></div>
        <div class="feature-title">Custom Branding</div>
        <div class="feature-desc">Upload your own icon, splash screen, and app name for a fully branded experience.</div>
      </div>
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="refresh-cw"></i></div>
        <div class="feature-title">Pull to Refresh</div>
        <div class="feature-desc">Native swipe-to-reload gesture for your WebView content, just like native apps.</div>
      </div>
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="wifi-off"></i></div>
        <div class="feature-title">Offline Page</div>
        <div class="feature-desc">Custom offline screen when network connectivity is lost — no blank white screens.</div>
      </div>
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="bell"></i></div>
        <div class="feature-title">Push Notifications</div>
        <div class="feature-desc">OneSignal integration for push notification support right out of the box.</div>
      </div>
      <div class="feature-card reveal">
        <div class="feature-icon-wrap"><i data-lucide="package"></i></div>
        <div class="feature-title">APK + AAB</div>
        <div class="feature-desc">Build both APK for testing and AAB for Play Store release in a single workflow run.</div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" id="pricing">
  <div class="cta-inner reveal">
    <div class="cta-dots"></div>
    <h2>Ready to Build Your App?</h2>
    <p>Enter your website URL and get a native Android app in minutes.</p>
    @auth
        <a href="{{ url('/userdashboard') }}" class="btn-cta" style="text-decoration:none;">
            Go to Dashboard
        </a>
    @else
        <a href="{{ route('register') }}" class="btn-cta" style="text-decoration:none;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          Get Started Free
        </a>
    @endauth
  </div>
</section>

@include('partials.footer')
@include('partials.scripts')

</body>
</html>
