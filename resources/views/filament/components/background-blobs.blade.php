<style>
    /* Make Filament background transparent to show blobs */
    body.fi-body {
        background-color: transparent !important;
    }
    
    /* Background blobs styling */
    .blob {
        position: fixed;
        filter: blur(60px);
        opacity: 0.6;
        z-index: -1; /* Place strictly behind everything */
        border-radius: 50%;
        pointer-events: none;
        animation: float 12s infinite alternate;
    }
    .blob-1 {
        background: #C7CAFA; /* primary-100 */
        width: 40vw;
        height: 40vw;
        max-width: 400px;
        max-height: 400px;
        top: -100px;
        left: -100px;
        animation-delay: 0s;
    }
    .blob-2 {
        background: #3B30E8; /* primary-600 */
        width: 30vw;
        height: 30vw;
        max-width: 300px;
        max-height: 300px;
        bottom: 100px;
        right: -50px;
        opacity: 0.3;
        animation-delay: -3s;
    }
    .blob-3 {
        background: #E684FF;
        width: 35vw;
        height: 35vw;
        max-width: 350px;
        max-height: 350px;
        top: 30%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0.2;
        animation-delay: -6s;
    }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(30px, 50px) scale(1.1); }
    }

    /* Glassmorphism for Filament panels and widgets to look better over blobs */
    .fi-ta-content, .fi-wi-widget, .fi-fo-wizard {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
    }
    .dark .fi-ta-content, .dark .fi-wi-widget, .dark .fi-fo-wizard {
        background: rgba(24, 24, 27, 0.85) !important;
    }
    
    /* Center header logo on mobile */
    @media (max-width: 1024px) {
        .fi-topbar nav {
            position: relative;
        }
        .fi-topbar .fi-logo {
            position: absolute !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
            display: flex !important;
        }
        .fi-topbar .fi-sidebar-open-btn {
            position: relative;
            z-index: 10;
        }
    }
</style>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
