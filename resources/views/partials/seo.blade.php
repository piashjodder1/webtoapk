<!-- SEO -->
<meta name="title" content="{{ \App\Models\Setting::get('seo_title', 'Website Title') }}">
<meta name="description" content="{{ \App\Models\Setting::get('seo_description', 'Website Description') }}">
<meta name="keywords" content="{{ \App\Models\Setting::get('seo_keywords', 'keyword1, keyword2, keyword3') }}">
<meta name="author" content="{{ \App\Models\Setting::get('seo_author', 'Author Name') }}">
<meta name="robots" content="{{ \App\Models\Setting::get('seo_robots', 'index, follow') }}">

<!-- Theme Color -->
<meta name="theme-color" content="{{ \App\Models\Setting::get('theme_color', '#ffffff') }}">

<!-- Open Graph (Facebook, Messenger) -->
<meta property="og:title" content="{{ \App\Models\Setting::get('og_title', \App\Models\Setting::get('seo_title', 'Website Title')) }}">
<meta property="og:description" content="{{ \App\Models\Setting::get('og_description', \App\Models\Setting::get('seo_description', 'Website Description')) }}">
@if(\App\Models\Setting::get('og_image'))
<meta property="og:image" content="{{ Storage::url(\App\Models\Setting::get('og_image')) }}">
@else
<meta property="og:image" content="{{ asset('image.jpg') }}">
@endif
<meta property="og:url" content="{{ \App\Models\Setting::get('og_url', url()->current()) }}">
<meta property="og:type" content="{{ \App\Models\Setting::get('og_type', 'website') }}">

<!-- Twitter/X Card -->
<meta name="twitter:card" content="{{ \App\Models\Setting::get('twitter_card', 'summary_large_image') }}">
<meta name="twitter:title" content="{{ \App\Models\Setting::get('twitter_title', \App\Models\Setting::get('seo_title', 'Website Title')) }}">
<meta name="twitter:description" content="{{ \App\Models\Setting::get('twitter_description', \App\Models\Setting::get('seo_description', 'Website Description')) }}">
@if(\App\Models\Setting::get('twitter_image'))
<meta name="twitter:image" content="{{ Storage::url(\App\Models\Setting::get('twitter_image')) }}">
@else
<meta name="twitter:image" content="{{ asset('image.jpg') }}">
@endif

<!-- Favicon -->
@if(\App\Models\Setting::get('favicon'))
<link rel="icon" href="{{ Storage::url(\App\Models\Setting::get('favicon')) }}">
@else
<link rel="icon" href="/favicon.ico">
@endif

<!-- Canonical URL -->
<link rel="canonical" href="{{ \App\Models\Setting::get('canonical_url', url()->current()) }}">

<!-- Verification -->
@if(\App\Models\Setting::get('google_site_verification'))
<meta name="google-site-verification" content="{{ \App\Models\Setting::get('google_site_verification') }}">
@else
<meta name="google-site-verification" content="verification-code">
@endif

@if(\App\Models\Setting::get('bing_verification'))
<meta name="msvalidate.01" content="{{ \App\Models\Setting::get('bing_verification') }}">
@else
<meta name="msvalidate.01" content="bing-verification-code">
@endif
