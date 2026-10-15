{{-- Brand styling for the Filament admin. Plain CSS (Filament only ships the utility classes its own views use). --}}
<style>
    :root {
        --fa-navy: #0f1e36;
        --fa-gold: #c9a24b;
        --fa-gold-dark: #8a6a1f;
        --fa-serif: Georgia, 'Times New Roman', serif;
    }

    /* ---- Brand lockup ---- */
    .fa-brand { display: flex; align-items: center; gap: .7rem; }
    .fa-brand-mark {
        display: grid; place-items: center; flex: none;
        width: 2.4rem; height: 2.4rem; border-radius: .65rem;
        background: var(--fa-navy); color: var(--fa-gold);
        font: 700 .95rem/1 var(--fa-serif); letter-spacing: .03em;
        box-shadow: inset 0 0 0 1px rgba(201, 162, 75, .45);
    }
    .fa-brand-name { font: 600 1.1rem/1.1 var(--fa-serif); color: rgb(var(--gray-950)); white-space: nowrap; }
    .fa-brand-name small {
        display: block; margin-top: .2rem;
        font: 600 .62rem/1 'Inter', sans-serif; letter-spacing: .2em; text-transform: uppercase; color: var(--fa-gold-dark);
    }
    .dark .fa-brand-mark { background: var(--fa-gold); color: var(--fa-navy); }
    .dark .fa-brand-name { color: #fff; }
    .dark .fa-brand-name small { color: var(--fa-gold); }

    /* ---- Page chrome ---- */
    .fi-body { background: rgb(var(--gray-50)); }
    .dark .fi-body { background: rgb(var(--gray-950)); }
    .fi-header-heading { font-family: var(--fa-serif); font-weight: 600; letter-spacing: -.01em; }
    .fi-header-subheading { max-width: 46rem; font-size: .92rem; line-height: 1.5; margin-top: .35rem; }
    .fi-topbar > nav { box-shadow: 0 1px 0 rgb(var(--gray-200)); }
    .dark .fi-topbar > nav { box-shadow: 0 1px 0 rgba(255, 255, 255, .08); }

    /* Sidebar: soft tinted active item with a gold edge */
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button {
        background: rgb(var(--primary-50));
        box-shadow: inset 3px 0 0 var(--fa-gold);
    }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button svg { color: rgb(var(--primary-700)); }
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button { background: rgba(255, 255, 255, .06); }
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button .fi-sidebar-item-label,
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button svg { color: #fff; }
    .fi-sidebar-group-label { letter-spacing: .08em; text-transform: uppercase; font-size: .68rem; }

    /* Cards, tables and forms */
    .fi-section, .fi-ta-ctn, .fi-wi-stats-overview-stat, .fi-fo-section { border-radius: .85rem; }
    .fi-ta-header-cell-label { font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; color: rgb(var(--gray-500)); }
    .fi-ta-row-unread { background: rgb(var(--primary-50) / .65); }
    .fi-ta-row-unread > td:first-child { box-shadow: inset 3px 0 0 var(--fa-gold); }
    .dark .fi-ta-row-unread { background: rgba(var(--primary-400), .1); }
    .fi-btn { border-radius: .6rem; }
    .fi-input-wrp { border-radius: .6rem; }

    /* ---- Sign-in screen ---- */
    .fi-simple-layout {
        background:
            radial-gradient(60rem 30rem at 85% -10%, rgba(201, 162, 75, .16), transparent 60%),
            linear-gradient(160deg, #0f1e36 0%, #16294a 100%);
    }
    .fi-simple-main {
        border-radius: 1.1rem; box-shadow: 0 25px 60px -20px rgba(0, 0, 0, .55);
        border-top: 3px solid var(--fa-gold);
    }
    .fi-simple-header-heading { font-family: var(--fa-serif); font-weight: 600; }

    /* ---- Dashboard quick actions ---- */
    .fa-hero { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
    .fa-hero h2 { margin: 0; font: 600 1.45rem/1.2 var(--fa-serif); color: rgb(var(--gray-950)); }
    .dark .fa-hero h2 { color: #fff; }
    .fa-hero p { margin: .25rem 0 0; color: rgb(var(--gray-500)); font-size: .9rem; }
    .fa-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr)); gap: .75rem; margin-top: 1.25rem; }
    .fa-action {
        display: flex; align-items: center; gap: .75rem; padding: .85rem 1rem; border-radius: .75rem;
        border: 1px solid rgb(var(--gray-200)); background: #fff; color: rgb(var(--gray-900)); text-decoration: none;
        transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .fa-action:hover { border-color: var(--fa-gold); box-shadow: 0 6px 18px -10px rgba(15, 30, 54, .35); transform: translateY(-1px); }
    .fa-action svg { flex: none; width: 1.5rem; height: 1.5rem; color: var(--fa-gold-dark); }
    .fa-action strong { display: block; font-size: .9rem; font-weight: 600; }
    .fa-action span { display: block; font-size: .76rem; color: rgb(var(--gray-500)); }
    .dark .fa-action { background: rgba(255, 255, 255, .04); border-color: rgba(255, 255, 255, .1); color: #fff; }
    .dark .fa-action svg { color: var(--fa-gold); }

    @media (prefers-reduced-motion: reduce) { .fa-action { transition: none; } .fa-action:hover { transform: none; } }
</style>

{{-- Filament remembers the sidebar state in localStorage and defaults to "open", which on a phone means
     the menu covers the page on the first visit. Start closed on small screens until the user chooses. --}}
<script>
    try {
        if (window.innerWidth < 1024 && window.localStorage.getItem('isOpen') === null) {
            window.localStorage.setItem('isOpen', 'false')
        }
    } catch (e) {}
</script>
