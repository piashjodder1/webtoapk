<link href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #3B30E8;
    --primary-light: #5B52FF;
    --primary-pale: #EEF0FF;
    --accent: #F97316;
    --bg: #FFFFFF;
    --surface: #F8F9FF;
    --surface2: #F1F3FF;
    --muted: #7B7FA8;
    --dark: #0F1033;
    --text: #1A1C40;
    --border: #E4E6F5;
  }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    color: var(--text);
    overflow-x: hidden;
  }

  h1,h2,h3,h4,.display { font-family: 'Bubblegum Sans', cursive; }

  /* ── NAV ── */
  nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    padding: 16px 0;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border);
    transition: all .3s;
  }
  nav.scrolled { box-shadow: 0 4px 24px rgba(59,48,232,.08); }

  .nav-inner {
    max-width: 1180px; margin: 0 auto;
    padding: 0 24px;
    display: flex; align-items: center; justify-content: space-between;
  }

  .logo {
    display: flex; align-items: center; gap: 10px;
    font-family: 'Bubblegum Sans', cursive; font-size: 20px; color: var(--primary);
    text-decoration: none; letter-spacing: .5px;
  }
  .logo-icon {
    width: 34px; height: 34px; background: var(--primary);
    border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 15px; font-weight: 800; color: #fff;
    font-family: 'Bubblegum Sans', cursive;
    box-shadow: 0 4px 12px rgba(59,48,232,.3);
  }

  .nav-links { display: flex; align-items: center; gap: 32px; }
  .nav-links a {
    color: var(--muted); text-decoration: none; font-size: 15px; font-weight: 600;
    transition: color .2s;
  }
  .nav-links a:hover { color: var(--primary); }

  .nav-actions { display: flex; align-items: center; gap: 10px; }

  .btn-ghost {
    padding: 9px 20px; border-radius: 10px; font-size: 14px; font-weight: 700;
    color: var(--text); background: transparent;
    border: 1.5px solid var(--border); cursor: pointer; transition: all .2s;
    font-family: 'Nunito', sans-serif;
  }
  .btn-ghost:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-pale); }

  .btn-primary {
    padding: 10px 22px; border-radius: 10px; font-size: 14px; font-weight: 700;
    color: #fff; background: var(--primary); border: none; cursor: pointer;
    transition: all .25s; font-family: 'Nunito', sans-serif;
    box-shadow: 0 4px 16px rgba(59,48,232,.3);
  }
  .btn-primary:hover {
    background: var(--primary-light);
    box-shadow: 0 6px 24px rgba(59,48,232,.4);
    transform: translateY(-1px);
  }
  .btn-primary:active, .btn-hero:active, .btn-cta:active { transform: scale(0.97); }

  .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px; }
  .hamburger span { display: block; width: 22px; height: 2px; background: var(--text); border-radius: 2px; transition: all .3s; }

  /* ── HERO ── */
  .hero {
    min-height: 100vh; position: relative;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center; padding: 130px 24px 80px; overflow: hidden;
    background: #fff;
  }

  /* dot grid */
  .hero::after {
    content: '';
    position: absolute; inset: 0; z-index: 0;
    background-image: radial-gradient(circle, #C7CAFA 1px, transparent 1px);
    background-size: 36px 36px;
    mask-image: radial-gradient(ellipse 70% 65% at 50% 40%, black 0%, transparent 100%);
    opacity: .6;
  }

  /* soft color blobs */
  .blob {
    position: absolute; border-radius: 50%;
    filter: blur(80px); pointer-events: none; z-index: 0;
  }
  .blob-1 { width: 420px; height: 420px; background: rgba(59,48,232,.08); top: -60px; left: -80px; }
  .blob-2 { width: 300px; height: 300px; background: rgba(249,115,22,.07); top: 80px; right: -60px; }
  .blob-3 { width: 500px; height: 260px; background: rgba(59,48,232,.06); bottom: 0; left: 50%; transform: translateX(-50%); }

  .badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--primary-pale); border: 1.5px solid rgba(59,48,232,.2);
    padding: 6px 16px; border-radius: 100px; font-size: 13px; font-weight: 700;
    color: var(--primary); margin-bottom: 28px; position: relative; z-index: 2;
  }
  .badge-dot { width: 7px; height: 7px; background: var(--accent); border-radius: 50%; animation: pulse 2s infinite; }

  @keyframes pulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50% { opacity: .5; transform: scale(1.5); }
  }

  .hero h1 {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(46px, 7.5vw, 84px);
    line-height: 1.05;
    letter-spacing: .5px;
    color: var(--dark);
    position: relative; z-index: 2;
    max-width: 820px;
  }
  .hero h1 .highlight {
    color: var(--primary);
    display: block;
    position: relative;
  }
  .hero h1 .highlight::after {
    content: '';
    position: absolute; left: 0; bottom: -6px; right: 0; height: 5px;
    background: linear-gradient(90deg, var(--primary) 0%, rgba(59,48,232,0) 100%);
    border-radius: 3px; opacity: .25;
  }

  .hero p {
    max-width: 520px; font-size: 17px; line-height: 1.75;
    color: var(--muted); margin: 26px auto 0; position: relative; z-index: 2;
    font-weight: 400;
  }

  .hero-actions {
    display: flex; align-items: center; gap: 14px; margin-top: 40px;
    position: relative; z-index: 2; flex-wrap: wrap; justify-content: center;
  }

  .btn-hero {
    padding: 14px 32px; border-radius: 12px; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: all .25s; font-family: 'Nunito', sans-serif;
    display: flex; align-items: center; gap: 8px;
  }
  .btn-hero-primary {
    background: var(--primary); color: #fff; border: none;
    box-shadow: 0 8px 32px rgba(59,48,232,.35);
  }
  .btn-hero-primary:hover {
    background: var(--primary-light);
    box-shadow: 0 12px 40px rgba(59,48,232,.45);
    transform: translateY(-2px);
  }
  .btn-hero-secondary {
    background: #fff; color: var(--text);
    border: 1.5px solid var(--border);
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
  }
  .btn-hero-secondary:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }

  .hero-stats {
    display: flex; gap: 0; margin-top: 64px; position: relative; z-index: 2;
    flex-wrap: wrap; justify-content: center;
    background: #fff; border: 1.5px solid var(--border);
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(59,48,232,.07);
  }
  .stat {
    text-align: center; padding: 20px 40px;
    border-right: 1.5px solid var(--border);
  }
  .stat:last-child { border-right: none; }
  .stat-num { font-family: 'Bubblegum Sans', cursive; font-size: 28px; color: var(--primary); }
  .stat-label { font-size: 12px; color: var(--muted); margin-top: 2px; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }

  /* ── SECTION COMMON ── */
  .section { padding: 96px 24px; position: relative; }
  .section-inner { max-width: 1180px; margin: 0 auto; }
  .section-tag {
    display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: 2px;
    text-transform: uppercase; color: var(--primary);
    margin-bottom: 10px;
    background: var(--primary-pale); padding: 4px 12px; border-radius: 100px;
  }
  .section-title {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(30px, 4.5vw, 48px); line-height: 1.1;
    color: var(--dark);
  }
  .section-sub {
    font-size: 16px; color: var(--muted); margin-top: 12px; font-weight: 400; max-width: 480px; line-height: 1.7;
  }

  /* ── HOW IT WORKS ── */
  .how-section { background: var(--surface); }

  .steps-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
    margin-top: 52px;
  }

  .step-card {
    background: #fff; border: 1.5px solid var(--border);
    border-radius: 20px; padding: 32px 28px;
    transition: all .3s; position: relative; overflow: hidden;
  }
  .step-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 20px 20px 0 0;
    opacity: 0; transition: opacity .3s;
  }
  .step-card:hover { border-color: rgba(59,48,232,.3); box-shadow: 0 8px 40px rgba(59,48,232,.1); transform: translateY(-4px); }
  .step-card:hover::before { opacity: 1; }

  .step-num {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--primary-pale);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Bubblegum Sans', cursive; font-size: 17px; color: var(--primary);
    margin-bottom: 22px;
  }
  .step-icon { margin-bottom: 10px; display: inline-flex; }
  .step-title { font-family: 'Bubblegum Sans', cursive; font-size: 21px; color: var(--dark); margin-bottom: 10px; }
  .step-desc { font-size: 14px; color: var(--muted); line-height: 1.75; font-weight: 500; }

  /* ── FEATURES ── */
  .features-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 52px;
  }

  .feature-card {
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: 18px; padding: 28px 24px;
    transition: all .3s; position: relative; overflow: hidden;
  }
  .feature-card:hover {
    border-color: rgba(59,48,232,.25);
    box-shadow: 0 6px 32px rgba(59,48,232,.09);
    transform: translateY(-3px);
    background: #fff;
  }

  .feature-icon-wrap {
    width: 46px; height: 46px; border-radius: 12px;
    background: var(--primary-pale);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 18px;
  }
  .feature-title { font-family: 'Bubblegum Sans', cursive; font-size: 18px; color: var(--dark); margin-bottom: 8px; }
  .feature-desc { font-size: 13px; color: var(--muted); line-height: 1.75; font-weight: 500; }

  /* ── CTA ── */
  .cta-section { padding: 72px 24px; }
  .cta-inner {
    max-width: 820px; margin: 0 auto;
    background: linear-gradient(135deg, var(--primary) 0%, #6047FF 100%);
    border-radius: 28px; padding: 72px 48px; text-align: center;
    position: relative; overflow: hidden;
    box-shadow: 0 20px 80px rgba(59,48,232,.3);
  }
  .cta-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.15) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none;
  }
  .cta-inner h2 {
    font-family: 'Bubblegum Sans', cursive;
    font-size: clamp(28px, 4vw, 44px); color: #fff;
    position: relative; z-index: 1;
  }
  .cta-inner p { color: rgba(255,255,255,.8); margin-top: 12px; font-size: 17px; position: relative; z-index: 1; font-weight: 500; }
  .btn-cta {
    display: inline-flex; align-items: center; gap: 8px;
    margin-top: 36px; padding: 15px 38px;
    background: #fff; color: var(--primary);
    font-family: 'Bubblegum Sans', cursive; font-size: 18px; letter-spacing: .5px;
    border: none; border-radius: 14px; cursor: pointer; transition: all .25s;
    position: relative; z-index: 1;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
  }
  .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 10px 36px rgba(0,0,0,.2); text-decoration: none; }

  /* ── FOOTER ── */
  footer {
    border-top: 1.5px solid var(--border);
    padding: 28px 24px;
    text-align: center;
    font-size: 14px; color: var(--muted); font-weight: 600;
  }

  /* ── MOBILE MENU ── */
  .mobile-menu {
    display: none; position: fixed; inset: 0; z-index: 99;
    background: rgba(255,255,255,.98); backdrop-filter: blur(20px);
    flex-direction: column; align-items: center; justify-content: center; gap: 28px;
  }
  .mobile-menu.open { display: flex; }
  .mobile-menu a {
    font-family: 'Bubblegum Sans', cursive; font-size: 30px;
    color: var(--text); text-decoration: none; transition: color .2s;
  }
  .mobile-menu a:hover { color: var(--primary); }
  .mobile-close {
    position: absolute; top: 20px; right: 24px;
    font-size: 28px; color: var(--muted); cursor: pointer; background: none; border: none;
  }

  /* ── ANIMATIONS ── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .fade-up { opacity: 0; animation: fadeUp .7s ease forwards; }
  .delay-1 { animation-delay: .12s; }
  .delay-2 { animation-delay: .25s; }
  .delay-3 { animation-delay: .4s; }
  .delay-4 { animation-delay: .55s; }

  .reveal { opacity: 0; transform: translateY(22px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.visible { opacity: 1; transform: translateY(0); }

  /* ── RESPONSIVE ── */
  @media (max-width: 900px) {
    .steps-grid { grid-template-columns: 1fr; gap: 14px; }
    .features-grid { grid-template-columns: repeat(2, 1fr); }
    .nav-links { display: none; }
    .hamburger { display: flex; }
    .nav-actions .btn-ghost { display: none; }
    .cta-inner { padding: 48px 28px; }
    .stat { padding: 18px 24px; }
  }
  
  @media (max-width: 560px) {
    .features-grid { grid-template-columns: 1fr; gap: 12px; }
    .steps-grid { gap: 12px; }
    
    .step-card { padding: 24px 20px; }
    .step-num { width: 36px; height: 36px; font-size: 15px; margin-bottom: 16px; }
    .step-icon { width: 28px; height: 28px; }
    .step-title { font-size: 19px; }
    .step-desc { font-size: 13.5px; line-height: 1.6; }

    .feature-card { padding: 24px 20px; }
    .feature-icon-wrap { width: 40px; height: 40px; margin-bottom: 14px; }
    .feature-icon-wrap svg { width: 20px; height: 20px; }
    .feature-title { font-size: 17px; }
    .feature-desc { font-size: 13px; line-height: 1.6; }

    .hero-stats { flex-direction: column; gap: 0; border-radius: 14px; margin-top: 32px; width: 100%; }
    .stat { border-right: none; border-bottom: 1.5px solid var(--border); padding: 16px 20px; }
    .stat:last-child { border-bottom: none; }
    
    .btn-hero { font-size: 16px; padding: 14px 24px; width: 100%; box-sizing: border-box; justify-content: center; text-align: center; }
    .hero-actions { flex-direction: column; width: 100%; padding: 0; gap: 12px; }
    
    /* Make typography smaller on very small screens */
    .hero h1 { font-size: clamp(34px, 9vw, 42px); }
    .hero p { font-size: 15px; margin-top: 16px; padding: 0 10px; }
    
    /* Better padding for sections */
    .hero { padding: 110px 16px 50px; }
    .section { padding: 50px 16px; }
    .section-title { font-size: clamp(26px, 8vw, 36px); }
    .cta-section { padding: 40px 16px; }
    .cta-inner { padding: 36px 20px; border-radius: 20px; }
    .cta-inner h2 { font-size: clamp(24px, 7vw, 32px); }
    .cta-inner p { font-size: 15px; }
    
    .btn-cta { width: 100%; box-sizing: border-box; justify-content: center; font-size: 17px; padding: 15px 24px; text-align: center; }
  }

  @media (max-width: 380px) {
    .logo { font-size: 18px; }
    .logo-icon { width: 28px; height: 28px; font-size: 12px; }
    .nav-actions .btn-primary { padding: 8px 16px; font-size: 13px; }
    .badge { font-size: 12px; padding: 5px 12px; }
    .hero h1 { font-size: 34px; }
  }
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
 document.addEventListener('DOMContentLoaded', function() {
   lucide.createIcons();
 });
</script>
