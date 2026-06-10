@vite(['resources/css/app.css'])
<style>
    @media (max-width: 639px) {
        .fi-header {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .fi-header-actions {
            margin-top: 0 !important;
        }
    }

    /* Add a visible border to the topbar user avatar */
    .fi-topbar img.fi-avatar, .fi-avatar {
        border: 2px solid #e5e7eb !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
        background-color: #ffffff;
    }

    /* Add a border to the right side of the sidebar */
    .fi-sidebar {
        border-right: 1px solid #e5e7eb !important;
    }
</style>
