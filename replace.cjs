const fs = require('fs');
const files = [
    'resources/views/userdashboard/pages/apps/create-app.blade.php',
    'resources/views/userdashboard/pages/apps/edit-app.blade.php'
];

files.forEach(f => {
    if (!fs.existsSync(f)) return;
    let c = fs.readFileSync(f, 'utf8');

    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2\.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"\/><\/svg>/g, '<i data-lucide="check" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14\.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7\.5L14\.5 2z"\/><polyline points="14 2 14 8 20 8"\/><\/svg>/g, '<i data-lucide="app-window" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24\" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"\/><circle cx="9" cy="9" r="2"\/><path d="m21 15-3\.086-3\.086a2 2 0 0 0-2\.828 0L6 21"\/><\/svg>/g, '<i data-lucide="image" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"\/><\/svg>/g, '<i data-lucide="settings" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke=\"currentColor\" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11\.08V12a10 10 0 1 1-5\.93-9\.14"\/><polyline points="22 4 12 14\.01 9 11\.01"\/><\/svg>/g, '<i data-lucide="check-circle" width="16" height="16"></i>');

    c = c.replace(/<div class="card-icon">📋<\/div>/g, '<div class="card-icon"><i data-lucide="clipboard-list" width="24" height="24"></i></div>');
    c = c.replace(/<div class="card-icon\">🔢<\/div>/g, '<div class="card-icon"><i data-lucide="hash" width="24" height="24"></i></div>');
    c = c.replace(/<div class="card-icon">🎨<\/div>/g, '<div class="card-icon"><i data-lucide="palette" width="24" height="24"></i></div>');
    c = c.replace(/<div class="card-icon">⚡<\/div>/g, '<div class="card-icon"><i data-lucide="zap" width="24" height="24"></i></div>');
    c = c.replace(/<div class="card-icon">✅<\/div>/g, '<div class="card-icon"><i data-lucide="check-square" width="24" height="24"></i></div>');
    
    c = c.replace(/<div class="success-icon-wrap">🎉<\/div>/g, '<div class="success-icon-wrap" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="party-popper" width="40" height="40"></i></div>');
    c = c.replace(/<div class="drop-icon">🖼️<\/div>/g, '<div class="drop-icon"><i data-lucide="image" width="28" height="28" style="color:var(--muted);"></i></div>');
    c = c.replace(/<div class="drop-icon">🌅<\/div>/g, '<div class="drop-icon"><i data-lucide="monitor" width="28" height="28" style="color:var(--muted);"></i></div>');

    c = c.replace(/<div class="toggle-icon">🔄<\/div>/g, '<div class="toggle-icon"><i data-lucide="refresh-cw" width="20" height="20"></i></div>');
    c = c.replace(/<div class="toggle-icon">📡<\/div>/g, '<div class="toggle-icon"><i data-lucide="wifi-off" width="20" height="20"></i></div>');
    c = c.replace(/<div class="toggle-icon">🔔<\/div>/g, '<div class="toggle-icon"><i data-lucide="bell" width="20" height="20"></i></div>');
    c = c.replace(/<div class="toggle-icon">⚙️<\/div>/g, '<div class="toggle-icon"><i data-lucide="settings" width="20" height="20"></i></div>');
    c = c.replace(/<div class="toggle-icon">🔍<\/div>/g, '<div class="toggle-icon"><i data-lucide="search" width="20" height="20"></i></div>');
    c = c.replace(/<div class="toggle-icon">📍<\/div>/g, '<div class="toggle-icon"><i data-lucide="map-pin" width="20" height="20"></i></div>');

    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2\.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"\/><polyline points="12 5 19 12 12 19"\/><\/svg>/g, '<i data-lucide="arrow-right" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2\.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"\/><polyline points="12 19 5 12 5"\/><\/svg>/g, '<i data-lucide="arrow-left" width="16" height="16"></i>');
    c = c.replace(/<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2\.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"\/><polyline points="12 19 5 12 12 5"\/><\/svg>/g, '<i data-lucide="arrow-left" width="16" height="16"></i>');

    fs.writeFileSync(f, c);
});
