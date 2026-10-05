<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <?php
    // GA4 - Set your Measurement ID via Customizer or define MORIMOFLIX_GA4_ID. Leave empty to disable.
    $morimoflix_ga4_id = defined('MORIMOFLIX_GA4_ID') ? MORIMOFLIX_GA4_ID : get_option('morimoflix_ga4_id', '');
    $morimoflix_ga4_id = sanitize_text_field($morimoflix_ga4_id);
    if (!empty($morimoflix_ga4_id) && preg_match('/^G-[A-Z0-9]{4,}$/i', $morimoflix_ga4_id)) :
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($morimoflix_ga4_id); ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?php echo esc_js($morimoflix_ga4_id); ?>');</script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- SAFE perf: early connection for Motion (footer module import); DNS fallback for older browsers. -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&amp;family=Inter:wght@400;500;600&amp;family=JetBrains+Mono:wght@500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=block" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/assets/css/app.css?ver=' . MORIMOFLIX_VERSION ); ?>">
    <style>
        /* Midnight Crimson CSS Variables — Pantone mapped (Theme-Color-Guide §13) */
        :root {
            --bg-base: #0B0B0F; /* Pantone 419 C #101820 approx — page background */
            --bg-elevated: #15151B; /* Pantone Black C #2D2926 elev */
            --bg-overlay: #1F1F26;
            --border: #2A2A33; /* Pantone Cool Gray 11 C #53565A */
            --text-primary: #F5F5F7;
            --text-secondary: #A0A0AA;
            --text-muted: #6B6B75;
            --accent: #E50914; /* Pantone 186 C #F5002F — Crimson CTA */
            --accent-hover: #F6121D; /* Pantone 185 C #FF173D */
            --accent-soft: #3A0A0D;
            --gold: #F5C518; /* Pantone 123 C #FFC20F — IMDB stars */
            --success: #22C55E; /* Pantone 361 C #43B02A */
            --warning: #F59E0B; /* Pantone 144 C #FF8500 */
            --info: #229ED9; /* Pantone 279 C #418FDE — Telegram */
            --focus: #E50914;
            --select-bg: rgba(229, 9, 20, 0.4);
        }
        /* Logo Styles - Midnight Crimson: Pantone 186 C → Pantone 123 C */
        .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: -0.04em;
            background: linear-gradient(90deg, #ff4d4d 0%, #b91c1c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            transition: filter 0.3s ease;
        }
        
        .logo-text:hover {
            filter: brightness(1.2);
        }
        
        /* Pagination Styles */
        .screen-reader-text {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .navigation.pagination {
            margin-top: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .nav-links .page-numbers {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #A0A0AA;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        a.page-numbers:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #E50914;
            color: #FFB4AB;
        }

        span.page-numbers.current {
            background: #FFB4AB;
            color: #0A0505;
            border-color: #FFB4AB;
            font-weight: bold;
        }

        .page-numbers.dots {
            background: none;
            border: none;
            color: #A0A0AA;
            opacity: 0.5;
        }
        
        /* Telegram Logo - Minimal Glow & Flicker */
        .telegram-icon {
            width: 24px;
            height: 24px;
            filter: drop-shadow(0 0 4px rgba(229, 9, 20, 0.3));
            animation: telegram-subtle 4s ease-in-out infinite;
        }
        
        @keyframes telegram-subtle {
            0%, 100% { 
                filter: drop-shadow(0 0 4px rgba(229, 9, 20, 0.3));
                opacity: 0.8;
            }
            50% { 
                filter: drop-shadow(0 0 8px rgba(229, 9, 20, 0.5));
                opacity: 1;
            }
        }
        
        .glass-card {
            background: rgba(21, 21, 27, 0.55);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            backdrop-filter: blur(16px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 8px 32px rgba(0, 0, 0, 0.35);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: #FFB4AB;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.35);
            transform: scale(1.02);
        }
        
        .scanlines {
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.02), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.02));
            background-size: 100% 4px, 3px 100%;
            pointer-events: none;
        }

        @layer base { html { scroll-behavior: smooth; } body { -webkit-font-smoothing: antialiased; } }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0A0505; }
        ::-webkit-scrollbar-thumb { background: #534343; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #A08C8C; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .red-glow { box-shadow: inset 0 0 50px rgba(255, 0, 0, 0.1); }
        *:focus-visible { outline: 2px solid var(--focus); outline-offset: 2px; }
        ::selection { background: #93000A; color: #fff; }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            visibility: hidden;
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            -webkit-font-smoothing: antialiased;
        }
        html.fonts-ready .material-symbols-outlined { visibility: visible; }
        
        .poster-aspect {
            aspect-ratio: 2/3;
        }
        
        .neon-glow-cyan {
            box-shadow: 0 0 10px rgba(229, 9, 20, 0.4), inset 0 0 4px rgba(229, 9, 20, 0.2);
            border: 1px solid rgba(255, 180, 171, 0.6);
        }
        
        .neon-glow-magenta {
            box-shadow: 0 0 10px rgba(254, 0, 254, 0.4), inset 0 0 4px rgba(254, 0, 254, 0.2);
            border: 1px solid rgba(254, 0, 254, 0.6);
        }
        
        .text-glow-cyan {
            text-shadow: 0 0 8px rgba(255, 180, 171, 0.8);
        }
        
        .mobile-menu{overflow-x:hidden;
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            width: 82%;
            max-width: 320px;
            height: calc(100dvh - 64px);
            background: rgba(10, 5, 5, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 120;
            flex-direction: column;
            padding: 1rem 1rem calc(1.5rem + env(safe-area-inset-bottom));
            gap: 0.25rem;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.5);
            border-right: 1px solid rgba(229, 9, 20, 0.25);
        }

        body.mobile-menu-open { overflow: hidden; }
        body.search-open { overflow: hidden; }

        .mobile-menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100dvh;
            background: rgba(0, 0, 0, 0.6);
            z-index: 110;
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        @media (min-width: 768px) {
            .mobile-menu{overflow-x:hidden;
                top: 80px;
                height: calc(100dvh - 80px);
                padding: 2rem;
            }
        }

        .mobile-menu.active {
            display: flex;
        }

        /* Fix logged-in admin-bar overlap — applies on live only */
        body.admin-bar nav.fixed { top: 32px; }
        body.admin-bar .mobile-menu { top: 96px; height: calc(100dvh - 96px); }
        @media screen and (max-width: 782px) {
            body.admin-bar nav.fixed { top: 46px; }
            body.admin-bar .mobile-menu { top: 110px; height: calc(100dvh - 110px); }
        }

        .mobile-menu-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            min-height: 48px;
            color: #F5F5F7;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            transition: all 0.2s ease;
            letter-spacing: 0.1em;
        }

        .mobile-menu-link:hover,
        .mobile-menu-link:active {
            color: #FFB4AB;
            background: rgba(229, 9, 20, 0.12);
        }
        
        /* Neo-Brutalism Orange-Pink Neon Line - Ambient Glow */
        .brutalism-orange-pink {
            border-bottom: 2px solid rgba(255, 102, 0, 0.5);
            box-shadow: 0 2px 15px rgba(255, 102, 0, 0.15), 
                        0 4px 30px rgba(254, 0, 254, 0.08),
                        0 0 40px rgba(255, 102, 0, 0.05);
        }
        
        /* Legacy Movie Post Vars — mapped to Premium Cinema */
        :root {
            --neon-red: var(--accent);
            --neon-pink: var(--accent-hover);
            --obsidian: #0A0505;
        }
        
        .text-gradient {
            background: linear-gradient(90deg, var(--neon-red), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .border-gradient {
            border: 1px solid transparent;
            background: linear-gradient(var(--obsidian), var(--obsidian)) padding-box,
                        linear-gradient(90deg, var(--neon-red), var(--neon-pink)) border-box;
        }
        
        .btn-gradient {
            background: linear-gradient(90deg, var(--neon-red), var(--neon-pink));
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(255, 49, 49, 0.2);
        }

        /* Shared Button API — canonical variants (consolidates 11 ad-hoc styles into 3) */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 48px;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            background: #E50914;
            border: 1px solid #E50914;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary:hover { background: #F6121D; border-color: #F6121D; box-shadow: 0 4px 20px rgba(229, 9, 20, 0.4); }
        .btn-primary:active { transform: scale(0.98); }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #F5F5F7;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .btn-secondary:hover { border-color: #E50914; background: rgba(255, 255, 255, 0.1); color: #fff; }
        .btn-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.08);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
        }

        .poster-frame {
            position: relative;
        }
        
        .poster-frame::after {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(180deg, rgba(255, 49, 49, 0.1), transparent);
            pointer-events: none;
            z-index: 10;
        }
        
        .node-card-border {
            border: 1px solid #ff3131;
            transition: border-color 300ms ease-in-out;
        }
        
        .node-card-border:hover {
            border-color: #8b008b !important;
        }
        
        /* Noise texture */
        .noise {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            opacity: 0.02;
            z-index: 50;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        
        /* Mobile Search Overlay */
        .mobile-search-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(5, 5, 5, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 200;
            padding: 1rem;
        }
        
        .mobile-search-overlay.active {
            display: flex;
            align-items: flex-start;
        }
        
        .mobile-search-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding-top: 1rem;
        }
        
        .mobile-search-form {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(29, 14, 14, 0.9);
            border: 1px solid rgba(229, 9, 20, 0.4);
            border-radius: 12px;
            padding: 12px 16px;
            min-height: 56px;
        }
        
        .mobile-search-input {
            flex: 1;
            min-width: 0;
            background: transparent;
            border: none;
            color: #F5F5F7;
            font-family: 'JetBrains Mono', monospace;
            /* 16px (not 14px): iOS Safari auto-zooms the viewport on focus below 16px */
            font-size: 16px;
            letter-spacing: 0.05em;
        }
        
        .mobile-search-input::placeholder {
            color: rgba(132, 148, 149, 0.5);
        }
        
        /* Mobile Touch Hover Effects */
        @media (max-width: 1023px) {
            .group:active .poster-aspect {
                border-color: #FFB4AB;
                box-shadow: 0 0 25px rgba(229, 9, 20, 0.35);
            }
            
            .group:active .poster-aspect img {
                transform: scale(1.05);
                filter: brightness(1.1);
            }
            
            .group:active h3 {
                color: #FFB4AB;
            }
            
            .group:active .card-overlay,
            .group:active .absolute.inset-0.bg-gradient-to-t {
                opacity: 0.8;
            }
            
            /* Category cards touch effect */
            a.group:active {
                border-color: #FFB4AB;
                box-shadow: 0 0 15px rgba(229, 9, 20, 0.4);
            }

            a.group:active span {
                color: #FFB4AB;
                transform: scale(1.05);
            }
            
            /* Buttons touch effect */
            .btn-gradient:active {
                opacity: 0.8;
                transform: scale(0.98);
            }
            
            button:active,
            a:active {
                opacity: 0.8;
            }
        }
        
        /* Related Cards - Red border default, Orange-Pink gradient on hover */
        .related-card .related-card-image {
            border-color: rgba(220, 38, 38, 0.6);
        }
        
        .related-card:hover .related-card-image {
            border-color: transparent;
            border-image: linear-gradient(135deg, #ff6600, #fe00fe) 1;
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.3), 0 0 30px rgba(254, 0, 254, 0.15);
        }
        
        /* Select Dropdown Styling */
        select {
            background-color: #1a1a1a;
            color: #fff;
        }
        
        select option {
            background-color: #1a1a1a;
            color: #fff;
            padding: 12px 16px;
            font-family: 'JetBrains Mono', monospace;
        }
        
        select option:hover,
        select option:focus {
            background-color: #2A1A1A;
            color: #fff;
        }

        select option:checked {
            background-color: #2A1A1A;
            color: #fff;
        }
        
        /* Hero Carousel Styles */
        #heroCarousel .hero-slide {
            pointer-events: none;
        }
        #heroCarousel .hero-slide.opacity-100,
        #heroCarousel .hero-slide[style*="opacity: 1"] {
            pointer-events: auto;
        }

        /* Premium Cinema — MovieCard (Pantone 186 C #F5002F accent, Pantone 123 C #FFC20F gold) */
        .group .poster-aspect {
            border: 1px solid rgba(255,255,255,0.05) !important;
            box-shadow: inset 0 0 20px rgba(255,0,0,0.05);
            transition: all 0.3s ease !important;
        }
        .group:hover .poster-aspect {
            border-color: rgba(255, 180, 171, 0.5) !important;
            box-shadow: 0 0 25px rgba(255,0,0,0.4), inset 0 0 20px rgba(255,0,0,0.05) !important;
            transform: scale(1.02);
        }
        .group:hover h3 { color: #FFB4AB !important; }
        /* Premium CategoryBar chips override */
        .category-chip { background: rgba(39,18,18,0.4); border: 1px solid rgba(83,67,67,0.3); color: #A0A0AA; }
        .category-chip:hover { color: #fff; background: #3A3939; }
        .category-chip.active { background: rgba(255,180,171,0.1); border-color: rgba(255,180,171,0.5); color: #FFB4AB; box-shadow: 0 0 10px rgba(255,180,171,0.15); }
        /* Shared Glass API — Premium Cinema frosted chrome (header.php owns definitions) */
        .glass {
            position: relative;
            overflow: hidden;
            background: rgba(16, 10, 12, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 8px 32px rgba(0, 0, 0, 0.35);
        }
        .glass::before {
            content: "";
            position: absolute;
            inset: -2px;
            z-index: 0;
            border-radius: inherit;
            pointer-events: none;
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-filter: url(#liquidGlassFilter);
            filter: url(#liquidGlassFilter);
        }
        .glass > *:not(.absolute) { position: relative; z-index: 1; }
        .glass-strong {
            position: relative;
            overflow: hidden;
            background: rgba(10, 5, 5, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 12px 40px rgba(0, 0, 0, 0.5);
        }
        .glass-strong::before {
            content: "";
            position: absolute;
            inset: -2px;
            z-index: 0;
            border-radius: inherit;
            pointer-events: none;
            -webkit-backdrop-filter: blur(22px) saturate(180%);
            backdrop-filter: blur(22px) saturate(180%);
            -webkit-filter: url(#liquidGlassFilter);
            filter: url(#liquidGlassFilter);
        }
        .glass-strong > *:not(.absolute) { position: relative; z-index: 1; }
        /* Cascade fix: .glass-strong position:relative must NOT override mobile-menu / mobile-search-overlay position:fixed */
        .mobile-menu.glass-strong { position: fixed; top: 64px; left: 0; width: 82%; max-width: 320px; height: calc(100dvh - 64px); }
        /* background/border re-declared: .glass-strong is later in this stylesheet (0,1,0) and
           was overriding the near-opaque .mobile-search-overlay background + drawing a
           1px hairline frame around the whole viewport. */
        .mobile-search-overlay.glass-strong { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 200; padding: 1rem; background: rgba(5, 5, 5, 0.98); border: 0; }
        /* Mobile perf guard: no viewport-sized displacement map behind the search overlay
           (mirrors the navbar opt-out at #morimoNavbarGlass.navbar-liquid::before). */
        #mobileSearchOverlay::before { display: none; }
        @media (min-width: 768px) { .mobile-menu.glass-strong { top: 80px; height: calc(100dvh - 80px); } }
        body.admin-bar .mobile-menu.glass-strong { top: 96px; height: calc(100dvh - 96px); }
        @media screen and (max-width: 782px) { body.admin-bar .mobile-menu.glass-strong { top: 110px; height: calc(100dvh - 110px); } }
        .glass-panel {
            position: relative;
            overflow: hidden;
            background: rgba(21, 21, 27, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 8px 32px rgba(0, 0, 0, 0.35);
            border-radius: 12px;
        }
        .glass-panel::before {
            content: "";
            position: absolute;
            inset: -2px;
            z-index: 0;
            border-radius: inherit;
            pointer-events: none;
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-filter: url(#liquidGlassFilter);
            filter: url(#liquidGlassFilter);
        }
        .glass-panel > * { position: relative; z-index: 1; }
        .glass-chip {
            background: rgba(255, 255, 255, 0.08);
            -webkit-backdrop-filter: blur(10px) saturate(140%);
            backdrop-filter: blur(10px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 8px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }
        .glass-chip-red {
            background: rgba(229, 9, 20, 0.22);
            border-color: rgba(229, 9, 20, 0.45);
        }
        .glass-chip-gold {
            background: rgba(245, 197, 24, 0.16);
            border-color: rgba(245, 197, 24, 0.45);
        }
        .glass-input {
            background: rgba(29, 14, 14, 0.6);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            backdrop-filter: blur(16px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
            border-radius: 12px;
        }
        /* @tailwindcss/forms sets [type='search']:focus to blue-600 with a blue ring at
           (0,1,1) specificity — it beat .glass-input and painted an off-brand blue border
           inside the crimson search pill. Restore the brand focus colour. */
        .glass-input:focus, .glass-input:focus-visible {
            border-color: rgba(229, 9, 20, 0.6);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }
        #desktopSearch, .mobile-search-input { padding-top: 0; padding-bottom: 0; background: transparent !important; border: none !important; outline: none !important; border-radius: 0 !important; box-shadow: none !important; }
        #desktopSearch:focus, #desktopSearch:focus-visible, .mobile-search-input:focus, .mobile-search-input:focus-visible { border-color: transparent !important; box-shadow: none !important; outline: none !important; }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .glass, .glass-strong, .glass-panel, .glass-card, .glass-chip, .glass-input { background: #1D0E0E; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .glass::before, .glass-strong::before, .glass-panel::before,
            .liquid-glass-btn::before, .liquid-glass-btn::after { filter: none !important; backdrop-filter: none !important; }
        }

        /* === Apple Liquid Glass Button (rdev/liquid-glass-react style) === */
        /* CSS-only recreation: backdrop-filter + feDisplacementMap (#liquidGlassBtn) + mouse-tracked specular highlight via --lg-x / --lg-y CSS vars */
        .liquid-glass-btn {
            position: relative;
            isolation: isolate;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(229, 9, 20, 0.55), rgba(132, 0, 10, 0.55));
            border: 1px solid rgba(255, 180, 171, 0.55);
            color: #FFFFFF;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            line-height: 1;
            letter-spacing: 0.01em;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            min-height: 48px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.4),
                inset 0 -1px 0 rgba(0, 0, 0, 0.2),
                0 8px 24px rgba(229, 9, 20, 0.4);
            transition: transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.25s ease;
            --lg-x: 50%;
            --lg-y: 20%;
        }
        .liquid-glass-btn::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: inherit;
            pointer-events: none;
            z-index: 0;
            -webkit-backdrop-filter: blur(22px) saturate(180%);
            backdrop-filter: blur(22px) saturate(180%);
            -webkit-filter: url(#liquidGlassBtn);
            filter: url(#liquidGlassBtn);
        }
        .liquid-glass-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            z-index: 1;
            background: radial-gradient(circle at var(--lg-x) var(--lg-y), rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0) 55%);
            opacity: 0;
            transition: opacity 0.3s ease;
            mix-blend-mode: overlay;
        }
        .liquid-glass-btn:hover {
            transform: scale(1.03);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.55),
                inset 0 -1px 0 rgba(0, 0, 0, 0.2),
                0 12px 32px rgba(229, 9, 20, 0.55);
        }
        .liquid-glass-btn:hover::after, .liquid-glass-btn:focus-visible::after { opacity: 1; }
        .liquid-glass-btn:active { transform: scale(0.97); }
        .liquid-glass-btn > * { position: relative; z-index: 2; }
        @media (min-width: 768px) {
            .liquid-glass-btn { padding: 1rem 2.25rem; font-size: 1.05rem; }
        }
        /* Mobile perf guard — lighter blur on the button filter stack */
        @media (max-width: 640px) {
            .liquid-glass-btn::before {
                -webkit-backdrop-filter: blur(14px) saturate(150%);
                backdrop-filter: blur(14px) saturate(150%);
            }
        }
        /* === Navbar Liquid Glass (nikdelvin/liquid-glass port, navbar-only, MIT (c) Nik Delvin) === */
        /* JS below sets a dynamic displacement backdrop-filter inline. */
        /* Static ::before filter is disabled on the navbar so blurs don't stack. */
        #morimoNavbarGlass.navbar-liquid::before { display: none; }
        #morimoNavbarGlass.navbar-liquid {
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            backdrop-filter: blur(18px) saturate(180%);
        }
        @media (max-width: 640px) {
            #morimoNavbarGlass.navbar-liquid {
                -webkit-backdrop-filter: blur(12px) saturate(140%);
                backdrop-filter: blur(12px) saturate(140%);
            }
        }
        /* === Navbar crowding + mobile height fix (surgical, no JS) === */
        #morimoNavbarGlass { gap: 12px; padding-inline: 14px; flex-wrap: nowrap; overflow: visible; min-width: 0; }
        #morimoNavbarGlass > div { min-width: 0; }
        #morimoNavbarGlass form { min-width: 0; }
        #movieRequestBtn, #morimoNavbarGlass a[href*="/list/"] { flex-shrink: 0; }
        #desktopSearch { max-width: 100%; }
        @media (min-width: 1024px) {
            #morimoNavbarGlass { gap: 16px; padding-inline: 20px; }
        }

        /* === Scroll-floating navbar: ONE navbar, two visual states ===
           Resting state (scrollY = 0, i.e. WITHOUT .nav-float): integrated into the page
           header — full width, flush to the top, no pill, no glass blur, no shadow.
           Scrolled state (.nav-float, added by JS past the threshold): the theme's existing
           "glass navbar-liquid rounded-full mx-4 md:mx-8" floating glassmorphism pill.
           The scrolled look is the element's own existing CSS — nothing is duplicated, and
           removing the class restores the resting state, so the transform reverses cleanly.
           Both states are position:fixed, so there is no reflow of page content either way. */
        #morimoNavbarGlass {
            transition:
                max-width 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                margin 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                border-radius 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                border-color 0.4s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        #morimoNavbarGlass:not(.nav-float) {
            max-width: none;
            margin-left: 0;
            margin-right: 0;
            margin-top: 0;
            border-radius: 0;
            border-color: transparent;
            background-color: #0A0505;
            box-shadow: none;
            -webkit-backdrop-filter: none;
            backdrop-filter: none;
        }
        @media (max-width: 640px) {
            #morimoNavbarGlass { min-height: 60px !important; height: auto !important; padding: 10px 14px !important; gap: 10px !important; }
            /* Pill radius only in the scrolled/floating state (it is !important, so it would
               otherwise beat the resting-state border-radius: 0 above). */
            #morimoNavbarGlass.nav-float { border-radius: 9999px !important; }
            #morimoNavbarGlass .logo-text { font-size: 18px !important; line-height: 1 !important; white-space: nowrap !important; }
            #morimoNavbarGlass .telegram-icon { width: 22px !important; height: 22px !important; flex-shrink: 0 !important; }
            #mobileMenuBtn, #mobileSearchBtn, #morimoNavbarGlass button[aria-label="Account"] { width: 44px !important; height: 44px !important; min-width: 44px !important; min-height: 44px !important; }
        }
        @media (max-width: 640px) {
            .glass { -webkit-backdrop-filter: blur(12px) saturate(140%); backdrop-filter: blur(12px) saturate(140%); }
            .glass-strong { -webkit-backdrop-filter: blur(14px) saturate(140%); backdrop-filter: blur(14px) saturate(140%); }
            .glass-panel, .glass-card, .glass-input { -webkit-backdrop-filter: blur(10px) saturate(140%); backdrop-filter: blur(10px) saturate(140%); }
            .glass-chip { -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
            .poster-frame::after { display: none; }
        }
    </style>
    <script>
        (function () {
            function ready() { document.documentElement.classList.add('fonts-ready'); }
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(ready).catch(ready);
                document.fonts.addEventListener('loadingdone', ready, { once: true });
            } else {
                ready();
            }
        })();
    </script>
    <noscript><style>.material-symbols-outlined{visibility:visible!important}</style></noscript>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[var(--bg-base)] bg-background text-on-surface selection:bg-[#93000A] selection:text-white overflow-x-hidden font-body-md text-body-md' ); ?>>
<?php wp_body_open(); ?>

<!-- Liquid Glass SVG Filter — Apple-style refraction via feDisplacementMap -->
<svg aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;">
    <defs>
        <filter id="liquidGlassFilter" x="-15%" y="-15%" width="130%" height="130%" color-interpolation-filters="sRGB">
            <feImage id="lgDisp" x="0" y="0" width="100%" height="100%" result="DISP" href="data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/2wCEAAQDAwMDAwQDAwQGBAMEBgcFBAQFBwgHBwcHBwgLCAkJCQkICwsMDAwMDAsNDQ4ODQ0SEhISEhQUFBQUFBQUFBQBBQUFCAgIEAsLEBQODg4UFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFP/CABEIAQABAAMBEQACEQEDEQH/xAAxAAEBAQEBAQAAAAAAAAAAAAADAgQIAQYBAQEBAQEBAQAAAAAAAAAAAAMCBAEACAf/2gAMAwEAAhADEAAAAPjPor6kOgOiKhKgKhKgOhKhOhKxKgKhOgKhKhKgKxOhKhOgKhKhKgKwKhKgKgKwG841nns9J/nn2KVCdCdCVAVCVCVAdCVCdiVAVidCVAVCVAdiVCVCdAVCVCVAVCVAVAViVZxsBrPPY6R/NvsY6E6ErEqAqE6ErAqE6E7E7ErA0ErArAqAqEuiVAXRLol0S6J0JUBWBUI0BXnG88djpH81+xjoToSoSoCoTsSoYQTsTsTQSsCsCsCsCsCoC6A0JeAuiXSLwn0SoioCoCoBsBrPFH0j+a/Yx0J0JUJUJ2BUMIR2MIRoBoJIBXnJAK840BUA0BdAegXhLpF4S8R+IuiVgVANAL546fSH5r9jHRHQFQlYxYnZQgnYwhQokgEmckzjecazlYD3OPQHoD0S8JcI/EXiPxF0SoSvONBFF0j+a/YpdI7EqA6KLGEKEGFI0AlA0AUzimYbzjecazjWce5w6BdEeCXhPhFwz8R+MuiVgVAdF0j+a/Yp0RUJ0MWUIUWUIUKUIJqBoArnJM4pmBMw3nCsw1mCs4+AegPBLxH4j4Z8KPGXSPojYH0ukfzX7FOiKhiyiylDiylDhBNRNQJAJcwpnBMopmC84XlCswdzj3OPQHwlwS8R8M+HHDPxl0ioDoukfzT7GOhOyiimzmzhDlShBNBNBJc4rmFMwJlBMwXlC82esoVmHucOgXgHxH4j4Zyccg/GfiOiKh6R/NPsY6GLOKObOUObOUI0KEAlEkzimYFygmUEyheXPeULzZ6yhWce5x8BeEuGfCj0HyI5EdM/EdD0h+a/Yx0U0cUflxNnNnCHCCdgSiSZgTMK5c6ZQvLnTLnvJnvKFZgrMHc5dAeiXijhn445E8g/RHTPpdI/mn2KdlFR5RzcTUTZxZwglYGgCmcEzAuUEyZ0y57yZ0yZ7yBeULzBeYazl0T6R9KPRPYj0T2J9B9Ppj8x+wjo4qY7M9iKmKg6MrIrErALzBeYEyZ0y50yZkyZ7x50yheXPeUbzjWcqA6I+lHYnsT6J7E9iOx0z+YfYBUc1MdmexHZjsHRlRBRDYBecEzZ7yAmXNeTOmTOmPOmXOmULyjeYbzlYnQxRx057E9mexPYij6a/L/r86OOzPpjsR6Y7B9MqIaILDPYZ7zZ0y57y50yZ0x5kyAmXPeUEyjeYUznQnYnRTUTUT2JqJ7EUfTn5d9fFRx2Z9EdmPTHjLsF0h6I2OegzXmzJmzplz3lzJjzpkBMudMoplBM5JnOwOyiimzmomomomsHRdO/l318VFHYj0x6I9McgumXiHpDQ56DPebMmbNebMmXMmQEy50yguQEzCmYkA7GLGEKaObobiaOKOKPp38s+vCsj7EeiPTHIP0Hwx6ReMKDP0M95895syZ815cy5c6ZQTKCZRXMKZiQDQYQYsps5uJs5qIsj7Honvyz68KyLpx4z9Mcg+GXoLxl4g6IUGes+a8+e82ZM2dMuZMoJmBcwrlJM5IBoMIUoUWc2c3E0cWRUXT/wCV/XQ2R0RdiPQfDPkFwy9BeIOiHQz0Ges+e82dM2ZM2dMwLmBcwrmJc5qBoMIUIUoU2c2cWRUJ0PT/5V9dFYjZFRF0z8ZeM+QPDLxD4Q6OfoBQhefPeYEz50ziucUzCoEuelCEKFGUKEKLOLI7E6EqHqD8o+uhsRsisSoi6ZeM+QPiHhj0R8IUIdALALzgmcEzimcVAlzioGomgyhQgwhRZHZFQHQlQ9Qfk/10NiVkNiNiVGXiPxj4x8Q9IvEHRHojQjQhecUAUAkEkziomgGgkoxZGgxZFQFQlYnQHQlQ9Pfj/10KCSCKESCNiVkViPSLpD0h6I0Q0I0A2IoBWBIJIBKBIJoJII2R2J0JWBUJ0JUB0XTv479dFZDYiglYigkhEgjZFQjRFQjRFQjQjQigFYigHYigmgEgmglYlYnQlQlYlQHQlQnQ9P/kf1yVkNiNCNkNiVENiNiViNEViNkVCVgKCViViViSCViSCVgdCViVCViVCdgVCVCdD1D+U/XBWQ2I0I2Q2JUQ2I0JWQ0I2JUQ2JUI2J0JWJWJWA2R0BWJ0I2JUJ2BUJUJ0P//EABkQAQEBAQEBAAAAAAAAAAAAAAECABEDEP/aAAgBAQABAgB1atWrVq1atWrVq1atWrVq1atWrVq1atWrVq+OrVq1atWrVq1atWrVq1atWrVq1atWrVq1atXxVppppppdWrVq1atWrVq1NNNNNNNNNNNPVWmmmmms6tWrVq1atWpppppppppppppp6q0000uc51atWrVq1ammmmmmmmmmmmmmmmmt1Vpppc5znVq1atWrVqaaaaaaaaaaaaaeqtNLnOc51atWrVq1ammmmmmmmmmmmmmmmmnnqrS5znOc6tWrVq16222mmmmmmlVppp6tKuc5znOrVq1a9TbbbbTTTTTSq000qtLnOc5zq1atTbbbbbbbbTTTSqqqqqq5znOc6tNTTbbbbbbbbTTTSqqqqrlVznOctNNNttttttttNNNNKqqqrqznKqrTTTTbbbbbbbbbTTTSqqqqrqznOc5aaaabbbbbbbbbaaaaVVVVVdWc5znVq1NNttttttttttNNKqqqqudWc5znVq16tbbbbbbbbbbTTSqqqq5XVnOc6tWrVrb1tttttttttNNKqqqqrWrK5VWmmmm230bbbbbbaaaXOc5zlVa1KuVVppptttt9G22222mmlzlVznK6tWVVWmmmm2222222222mlznOc5znLWppVVWmmmm22222229bTWrOc5znOcq1qaaVpWmm222222229erVqznOc5znKtatStK0rTbTTbbbbberXr1as5znOc5aVpppppWlabaabbbb1ta9WrVnOc5znU0rTTTTTTTTTTbTTbbbTWvVq1as5znOdTTStNNNNNNNNNtNNtNNtNa9WrVq1Z1Z1NNNNNK1q1NNNNNNNNNNtNatWrVq1atWrU00000rWrVq1atWrVq1alaaa1atWrVq1NNNammmmla1atWrVq1aterVq16tWrVnVqa1NK1qaaaVX/xAAWEAADAAAAAAAAAAAAAAAAAAAhgJD/2gAIAQEAAz8AaExf/8QAGhEBAQEBAQEBAAAAAAAAAAAAAQISEQADEP/aAAgBAgEBAgDx48ePHjx48ePHjx48ePHjx48ePHjx48ePHj86IiIiIiInjx48ePHjx48IiIiIj0oooooooooRERER73ve60UUUUUUVrWiiiiiihERERER73ve97ooooorRWiiiiihKERERER73ve973RRRRWtFFFFFFCIiIiIiPe973ve60UUVrRRRRRRQiIlCIiI973ve973RRRWiiiiiiiiiiiiiiihEe973ve973RRWtFFFFFFFFFFFFFFFFFFa13ve973WitaKKKKKKKKKKKKKKKKKKKKK1rWtd1rutFa1oooooooooooosssooorWta1rWta1rRRRRRRRRRRZZZZZZZZZWta1rWta1rRRRRRRRRZZZZZZZZZZZZe9a1rWta1rWitaKLLLLLLLLLLLLLLLLLLL3rWta1rWtFbLLLLLLLLLLLLLLLLLLLL3vWta1rWita1ssssssss+hZZZZZZZZe961rWta0Vre97LLLLLLLLLLLLPoWWWWWXrWta1oorWta3ssss+hZZZZ9Cyyyyyyyyiita1orWta1ve9llllllllllllllllFFaK1rWta1rWiyyyyyyyyyyyyiiiiiiitFFa1rWta1oosoosssssoooosoooorRRRWta1rWta0UUUUUWUUUUUUUUUUUVoooorWta1rWtaKKKKKKmiiiiiiiiiiiiiiitd73ve61oSiiipoqaKKKKKKKKKKKK0UUUVrve973vREREZoSihEooooorRRRRWtd73ve9EREREREoSiiiiitFllllla73ve9ERERERESiiiiiitH0PoWWWWVrXe96IiIiMoiJRRRRRRWjwlFFllllFFd6IiIiIlCUUUUUUUUUePHjx48ePCIiIiIiIiUUUUUUUUUUUePHjx48ePHjx48ePHjx48IiUUUUUUJRRRX//xAAWEQADAAAAAAAAAAAAAAAAAAABYJD/2gAIAQIBAz8AtEV7/8QAFxEBAQEBAAAAAAAAAAAAAAAAAAECEP/aAAgBAwEBAgCtNNNNNNNNNNNNNNNNNNNNNNNNNNNNNcrTTTTTTTTTTTTTTTTTTTTTTTTTTTTTXKrTTTTTTTU000000000000000000001FVpppppqampqaaaaaaaaaaaaaaaaaaaa5Vaaaaampqampqammmmmmmmmmmlaaaaaaiq0001NTU1NTU1NTTTTTTTTTTSqqtNNNcqtNNSyzU1LNTU1NTTTTTTTTTSqqq001ytNLLLLNTU1NTU1NTbbbTTTTTSqqq001ytNLLLLLNTU1NTU3NttttNNNNNKqq001KrSyyyyyzU1NTU1NTU3Nzc02220000qqqqrSqqyyyyyzU1NTU3Nzc3NttttNNNKqqqqqqsssssss1NTU3Nzc3NzbbbbTTTSqqqqqqrLLLLLNTU1Nzc3Nzc22220000qqqqqqqqqqssss1NTU3Nzc3NzbbbbbTTSqqqqqqqqqqzU1NTc3Nzc3Nzc3NzU1KqqqrKqqqqqtNNNNttzc3Nzc3NzU1NSyyyyyyqqqqqqqrTTbbbbc3Nzc3NTU1LLLLLLKsqqqqqqrTTTTbbbc3Nzc1NTUsssssqqqqqqrTTTTTbbbTc3NTU1NTUsssssqqqqqqqq0000222023NTU1NTUsssssqqqqqqqqqq000000003NTU1NTU1LLLLLNKrTSqqqqtNNNNNNtNNTU1NSzUssss00qq0qqqqrTTTTTTTTTU1NTUs1LLLNNNKrTTTSqqq00000000001NTU1LNTU0000qtNNNKqqqtNNNNNNNNTU1NTUs1NNNNNKss1NNNK00qtK0000001NNTU0s000000qq000001NKrStNNNNK1NNNNStNNNNNKqtNNNNNNNK0000000rU0000rTTTTTSq00000rTTTTTTTTTTTTTTTTStNNNNKr/xAAUEQEAAAAAAAAAAAAAAAAAAACg/9oACAEDAQM/AAAf/9k=" preserveAspectRatio="xMidYMid slice"/>
            <feDisplacementMap in="SourceGraphic" in2="DISP" scale="8" xChannelSelector="R" yChannelSelector="B"/>
        </filter>
        <filter id="liquidGlassBtn" x="-25%" y="-25%" width="150%" height="150%" color-interpolation-filters="sRGB">
            <feImage id="lgBtnDisp" x="0" y="0" width="100%" height="100%" result="DISP2" href="data:image/jpeg;base64,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P//EABkQAQEBAQEBAAAAAAAAAAAAAAECABEDEP/aAAgBAQABAgB1atWrVq1atWrVq1atWrVq1atWrVq1atWrVq+OrVq1atWrVq1atWrVq1atWrVq1atWrVq1atXxVppppppdWrVq1atWrVq1NNNNNNNNNNNPVWmmmmms6tWrVq1atWpppppppppppppp6q0000uc51atWrVq1ammmmmmmmmmmmmmmmmt1Vpppc5znVq1atWrVqaaaaaaaaaaaaaeqtNLnOc51atWrVq1ammmmmmmmmmmmmmmmmnnqrS5znOc6tWrVq16222mmmmmmlVppp6tKuc5znOrVq1a9TbbbbTTTTTSq000qtLnOc5zq1atTbbbbbbbbTTTSqqqqqq5znOc6tNTTbbbbbbbbTTTSqqqqrlVznOctNNNttttttttNNNNKqqqrqznKqrTTTTbbbbbbbbbTTTSqqqqrqznOc5aaaabbbbbbbbbaaaaVVVVVdWc5znVq1NNttttttttttNNKqqqqudWc5znVq16tbbbbbbbbbbTTSqqqq5XVnOc6tWrVrb1tttttttttNNKqqqqrWrK5VWmmmm230bbbbbbaaaXOc5zlVa1KuVVppptttt9G22222mmlzlVznK6tWVVWmmmm2222222222mlznOc5znLWppVVWmmmm22222229bTWrOc5znOcq1qaaVpWmm222222229erVqznOc5znKtatStK0rTbTTbbbbberXr1as5znOc5aVpppppWlabaabbbb1ta9WrVnOc5znU0rTTTTTTTTTTbTTbbbTWvVq1as5znOdTTStNNNNNNNNNtNNtNNtNa9WrVq1Z1Z1NNNNNK1q1NNNNNNNNNNtNatWrVq1atWrU00000rWrVq1atWrVq1alaaa1atWrVq1NNNammmmla1atWrVq1aterVq16tWrVnVqa1NK1qaaaVX/xAAWEAADAAAAAAAAAAAAAAAAAAAhgJD/2gAIAQEAAz8AaExf/8QAGhEBAQEBAQEBAAAAAAAAAAAAAQISEQADEP/aAAgBAgEBAgDx48ePHjx48ePHjx48ePHjx48ePHjx48ePHj86IiIiIiInjx48ePHjx48IiIiIj0oooooooooRERER73ve60UUUUUUVrWiiiiiihERERER73ve97ooooorRWiiiiihKERERER73ve973RRRRWtFFFFFFCIiIiIiPe973ve60UUVrRRRRRRQiIlCIiI973ve973RRRWiiiiiiiiiiiiiiihEe973ve973RRWtFFFFFFFFFFFFFFFFFFa13ve973WitaKKKKKKKKKKKKKKKKKKKKK1rWtd1rutFa1oooooooooooosssooorWta1rWta1rRRRRRRRRRRZZZZZZZZZWta1rWta1rRRRRRRRRZZZZZZZZZZZZe9a1rWta1rWitaKLLLLLLLLLLLLLLLLLLL3rWta1rWtFbLLLLLLLLLLLLLLLLLLLL3vWta1rWita1ssssssss+hZZZZZZZZe961rWta0Vre97LLLLLLLLLLLLPoWWWWWXrWta1oorWta3ssss+hZZZZ9Cyyyyyyyyiita1orWta1ve9llllllllllllllllFFaK1rWta1rWiyyyyyyyyyyyyiiiiiiitFFa1rWta1oosoosssssoooosoooorRRRWta1rWta0UUUUUWUUUUUUUUUUUVoooorWta1rWtaKKKKKKmiiiiiiiiiiiiiiitd73ve61oSiiipoqaKKKKKKKKKKKK0UUUVrve973vREREZoSihEooooorRRRRWtd73ve9EREREREoSiiiiitFllllla73ve9ERERERESiiiiiitH0PoWWWWVrXe96IiIiMoiJRRRRRRWjwlFFllllFFd6IiIiIlCUUUUUUUUUePHjx48ePCIiIiIiIiUUUUUUUUUUUePHjx48ePHjx48ePHjx48IiUUUUUUJRRRX//xAAWEQADAAAAAAAAAAAAAAAAAAABYJD/2gAIAQIBAz8AtEV7/8QAFxEBAQEBAAAAAAAAAAAAAAAAAAECEP/aAAgBAwEBAgCtNNNNNNNNNNNNNNNNNNNNNNNNNNNNNcrTTTTTTTTTTTTTTTTTTTTTTTTTTTTTXKrTTTTTTTU000000000000000000001FVpppppqampqaaaaaaaaaaaaaaaaaaaa5Vaaaaampqampqammmmmmmmmmmlaaaaaaiq0001NTU1NTU1NTTTTTTTTTTSqqtNNNcqtNNSyzU1LNTU1NTTTTTTTTTSqqq001ytNLLLLNTU1NTU1NTbbbTTTTTSqqq001ytNLLLLLNTU1NTU3NttttNNNNNKqq001KrSyyyyyzU1NTU1NTU3Nzc02220000qqqqrSqqyyyyyzU1NTU3Nzc3NttttNNNKqqqqqqsssssss1NTU3Nzc3NzbbbbTTTSqqqqqqrLLLLLNTU1Nzc3Nzc22220000qqqqqqqqqqssss1NTU3Nzc3NzbbbbbTTSqqqqqqqqqqzU1NTc3Nzc3Nzc3NzU1KqqqrKqqqqqtNNNNttzc3Nzc3NzU1NSyyyyyyqqqqqqqrTTbbbbc3Nzc3NTU1LLLLLLKsqqqqqqrTTTTbbbc3Nzc1NTUsssssqqqqqqrTTTTTbbbTc3NTU1NTUsssssqqqqqqqq0000222023NTU1NTUsssssqqqqqqqqqq000000003NTU1NTU1LLLLLNKrTSqqqqtNNNNNNtNNTU1NSzUssss00qq0qqqqrTTTTTTTTTU1NTUs1LLLNNNKrTTTSqqq00000000001NTU1LNTU0000qtNNNKqqqtNNNNNNNNTU1NTUs1NNNNNKss1NNNK00qtK0000001NNTU0s000000qq000001NKrStNNNNK1NNNNStNNNNNKqtNNNNNNNK0000000rU0000rTTTTTSq00000rTTTTTTTTTTTTTTTTStNNNNKr/xAAUEQEAAAAAAAAAAAAAAAAAAACg/9oACAEDAQM/AAAf/9k=" preserveAspectRatio="xMidYMid slice"/>
            <feDisplacementMap in="SourceGraphic" in2="DISP2" scale="16" xChannelSelector="R" yChannelSelector="B"/>
        </filter>
    </defs>
</svg>

<!-- Liquid Glass button mouse tracking (Apple specular highlight) -->
<script>
(function() {
    function attachLiquidGlass(el) {
        if (el.dataset.lgAttached) return;
        el.dataset.lgAttached = '1';
        var rect = null;
        function refreshRect() { rect = el.getBoundingClientRect(); }
        el.addEventListener('mouseenter', refreshRect);
        el.addEventListener('mousemove', function(e) {
            if (!rect || rect.width === 0 || rect.height === 0) refreshRect();
            if (!rect || rect.width === 0) return;
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            el.style.setProperty('--lg-x', x.toFixed(2) + '%');
            el.style.setProperty('--lg-y', y.toFixed(2) + '%');
        });
        el.addEventListener('mouseleave', function() {
            rect = null;
            el.style.setProperty('--lg-x', '50%');
            el.style.setProperty('--lg-y', '20%');
        });
        // Invalidate cached rect on viewport changes
        window.addEventListener('scroll', function() { rect = null; }, { passive: true, capture: true });
        window.addEventListener('resize', function() { rect = null; }, { passive: true });
    }
    function init() {
        var btns = document.querySelectorAll('.liquid-glass-btn:not([data-lg-attached])');
        for (var i = 0; i < btns.length; i++) attachLiquidGlass(btns[i]);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    // Watch ONLY the carousel root for dynamic button re-renders (cheap, scoped)
    if (window.MutationObserver) {
        var root = document.getElementById('heroCarousel') || document.body;
        var mo = new MutationObserver(function() { init(); });
        mo.observe(root, { childList: true, subtree: true });
    }
})();
</script>

<!-- Navbar Liquid Glass — dynamic displacement port of nikdelvin/liquid-glass (MIT (c) Nik Delvin). Navbar-only, no anime.js. -->
<script>
(function() {
    function getDisplacementMap(w, h, radius, depth) {
        return 'data:image/svg+xml;utf8,' + encodeURIComponent(
            '<svg height="' + h + '" width="' + w + '" viewBox="0 0 ' + w + ' ' + h + '" xmlns="http://www.w3.org/2000/svg">' +
            '<style>.mix { mix-blend-mode: screen; }</style>' +
            '<defs>' +
            '<linearGradient id="Y" x1="0" x2="0" y1="' + Math.ceil((radius / h) * 15) + '%" y2="' + Math.floor(100 - (radius / h) * 15) + '%">' +
            '<stop offset="0%" stop-color="#0F0" /><stop offset="100%" stop-color="#000" /></linearGradient>' +
            '<linearGradient id="X" x1="' + Math.ceil((radius / w) * 15) + '%" x2="' + Math.floor(100 - (radius / w) * 15) + '%" y1="0" y2="0">' +
            '<stop offset="0%" stop-color="#F00" /><stop offset="100%" stop-color="#000" /></linearGradient>' +
            '</defs>' +
            '<rect x="0" y="0" height="' + h + '" width="' + w + '" fill="#808080" />' +
            '<g filter="blur(2px)">' +
            '<rect x="0" y="0" height="' + h + '" width="' + w + '" fill="#000080" />' +
            '<rect x="0" y="0" height="' + h + '" width="' + w + '" fill="url(#Y)" class="mix" />' +
            '<rect x="0" y="0" height="' + h + '" width="' + w + '" fill="url(#X)" class="mix" />' +
            '<rect x="' + depth + '" y="' + depth + '" height="' + (h - 2 * depth) + '" width="' + (w - 2 * depth) + '" fill="#808080" rx="' + radius + '" ry="' + radius + '" filter="blur(' + depth + 'px)" />' +
            '</g></svg>'
        );
    }
    function getDisplacementFilter(o) {
        var map = getDisplacementMap(o.width, o.height, o.radius, o.depth);
        var svg =
            '<svg height="' + o.height + '" width="' + o.width + '" viewBox="0 0 ' + o.width + ' ' + o.height + '" xmlns="http://www.w3.org/2000/svg">' +
            '<defs><filter id="displace" color-interpolation-filters="sRGB">' +
            '<feImage x="0" y="0" height="' + o.height + '" width="' + o.width + '" href="' + map + '" result="displacementMap" />' +
            '<feDisplacementMap transform-origin="center" in="SourceGraphic" in2="displacementMap" scale="' + (o.strength + o.cab * 2) + '" xChannelSelector="R" yChannelSelector="G" />' +
            '<feColorMatrix type="matrix" values="1 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 0" result="displacedR" />' +
            '<feDisplacementMap in="SourceGraphic" in2="displacementMap" scale="' + (o.strength + o.cab) + '" xChannelSelector="R" yChannelSelector="G" />' +
            '<feColorMatrix type="matrix" values="0 0 0 0 0 0 1 0 0 0 0 0 0 0 0 0 0 0 1 0" result="displacedG" />' +
            '<feDisplacementMap in="SourceGraphic" in2="displacementMap" scale="' + o.strength + '" xChannelSelector="R" yChannelSelector="G" />' +
            '<feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 1 0 0 0 0 0 1 0" result="displacedB" />' +
            '<feBlend in="displacedR" in2="displacedG" mode="screen" />' +
            '<feBlend in2="displacedB" mode="screen" />' +
            '</filter></defs></svg>';
        return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg) + '#displace';
    }
    var supportsUrl = (function() {
        var el = document.createElement('div');
        el.style.cssText = 'backdrop-filter: url(#test)';
        return el.style.backdropFilter === 'url(#test)' || el.style.backdropFilter === 'url("#test")';
    })();
    function plainFallback(nav, isMobile) {
        nav.style.backdropFilter = '';
        nav.style.webkitBackdropFilter = isMobile ? 'blur(12px) saturate(140%)' : 'blur(18px) saturate(180%)';
    }
    function redraw(nav) {
        var rect = nav.getBoundingClientRect();
        var w = Math.round(rect.width), h = Math.round(rect.height);
        if (w < 10 || h < 10) return;
        // Resting state (top of page) is deliberately NOT glassy — the stylesheet sets
        // backdrop-filter:none there. This script writes an INLINE backdrop-filter, which
        // beats any stylesheet rule, so it has to stand down here too. Skipping it also
        // avoids generating a viewport-sized SVG displacement map before first scroll.
        if (!nav.classList.contains('nav-float')) {
            if (nav.dataset.lgW !== String(w) || nav.dataset.lgH !== String(h)) {
                nav.dataset.lgW = String(w); nav.dataset.lgH = String(h);
            }
            // Unconditional: any stale inline value (e.g. from plainFallback) would
            // otherwise survive on a size match, and CSS cannot override inline styles.
            nav.style.backdropFilter = '';
            nav.style.webkitBackdropFilter = '';
            return;
        }
        if (nav.dataset.lgW === String(w) && nav.dataset.lgH === String(h)) return;
        nav.dataset.lgW = String(w); nav.dataset.lgH = String(h);
        var isMobile = window.matchMedia('(max-width: 640px)').matches;
        if (!supportsUrl) { plainFallback(nav, isMobile); return; }
        var depth = 10;
        var radius = Math.min(h / 2, w / 2);
        var strength = isMobile ? 45 : 90;
        var cab = isMobile ? 0 : 2;
        var blur = isMobile ? 8 : 10;
        nav.style.webkitBackdropFilter = isMobile ? 'blur(12px) saturate(140%)' : 'blur(18px) saturate(180%)';
        nav.style.backdropFilter = 'blur(' + (blur / 2) + 'px) url(\'' +
            getDisplacementFilter({ width: w, height: h, radius: radius, depth: depth, strength: strength, chromaticAberration: cab }) +
            '\') blur(' + blur + 'px) brightness(1.1) saturate(1.5)';
    }
    function initNavbarGlass() {
        var nav = document.getElementById('morimoNavbarGlass');
        if (!nav) return;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reducedMotion) {
            initGlassFilter(nav);
        }
        // Scroll state is a visual transform, not a motion animation: it must still apply
        // for prefers-reduced-motion users. The stylesheet drops the transition for them,
        // and #morimoNavbarGlass.navbar-liquid supplies the blur in the floating state.
        initScrollFloat(nav);
    }
    function initGlassFilter(nav) {
        var raf = null;
        function schedule() {
            if (raf) return;
            raf = requestAnimationFrame(function() { raf = null; redraw(nav); });
        }
        redraw(nav);
        if (window.ResizeObserver) {
            var ro = new ResizeObserver(schedule);
            ro.observe(nav);
        }
        window.addEventListener('resize', schedule, { passive: true });
        window.addEventListener('orientationchange', schedule, { passive: true });
        window.addEventListener('load', schedule);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(schedule);
        // Exposed so the scroll-state toggle can rebuild the filter for the pill's new size.
        nav.morimoflixRescheduleGlass = schedule;
    }
    // Scroll-floating navbar: toggles ONE class on the ONE existing navbar. No second
    // navbar is created, and the resting / scrolled looks are pure CSS (see the
    // "Scroll-floating navbar" block in the stylesheet above).
    function initScrollFloat(nav) {
        var FLOAT_AT = 24;
        var floating = null;
        function sync() {
            var shouldFloat = (window.pageYOffset || document.documentElement.scrollTop || 0) > FLOAT_AT;
            if (shouldFloat === floating) return;
            floating = shouldFloat;
            nav.classList.toggle('nav-float', shouldFloat);
            // The glass filter is built for the pill's exact size, and the pill changes
            // margin/size between the two states — invalidate so it is rebuilt.
            nav.dataset.lgW = ''; nav.dataset.lgH = '';
            if (!shouldFloat) {
                // The stylesheet can't undo an INLINE backdrop-filter, and
                // plainFallback() (prefers-reduced-motion) writes one unconditionally.
                nav.style.backdropFilter = '';
                nav.style.webkitBackdropFilter = '';
            }
            if (nav.morimoflixRescheduleGlass) nav.morimoflixRescheduleGlass();
        }
        window.addEventListener('scroll', sync, { passive: true });
        sync();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbarGlass);
    } else {
        initNavbarGlass();
    }
})();
</script>

<div class="fixed inset-0 scanlines z-40"></div>

<!-- TopNavBar — Premium Cinema (z 130; the search overlay is z 200 and owns the viewport while open, so it hides the navbar) -->
<nav class="fixed top-0 left-0 right-0 w-full z-[130] transition-all duration-300 ease-in-out">
    <div id="morimoNavbarGlass" class="glass navbar-liquid rounded-full mx-4 md:mx-8 mt-2 md:mt-3 px-3.5 md:px-5 min-h-[60px] md:min-h-[64px] py-2.5 flex justify-between items-center gap-3 lg:gap-4 flex-nowrap border border-white/10 shadow-[0_4px_20px_rgba(0,0,0,0.4)] max-w-container-max lg:mx-auto">
        <div class="flex items-center gap-3 lg:gap-4 min-w-0 flex-nowrap">
            <!-- Mobile Menu Button -->
            <button type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu" class="lg:hidden material-symbols-outlined text-on-surface-variant hover:text-[#FFB4AB] transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center" id="mobileMenuBtn">menu</button>

            <a class="flex items-center gap-2 shrink-0 min-w-0" href="<?php echo esc_url( home_url( '/' ) ); ?>"><svg class="telegram-icon flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="url(#telegram-grad)"/><defs><linearGradient id="telegram-grad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse"><stop stop-color="#E50914" stop-opacity="0.9"/><stop offset="1" stop-color="#F5C518" stop-opacity="0.6"/></linearGradient></defs></svg><span class="logo-text text-xl md:text-2xl font-display-hero">FlixGarden</span></a>
            <?php
            $morimo_movies_link = get_post_type_archive_link( 'movie' );
            if ( ! $morimo_movies_link ) { $morimo_movies_link = home_url( '/' ); }
            $morimo_tv_link = get_post_type_archive_link( 'tv' );
            if ( ! $morimo_tv_link ) { $morimo_tv_link = home_url( '/' ); }
            $morimo_genres_link = home_url( '/#explore-genres' );
            $morimo_is_home = is_front_page();
            $morimo_is_movies = is_post_type_archive( 'movie' ) || is_singular( 'movie' );
            $morimo_is_tv = is_post_type_archive( 'tv' ) || is_singular( 'tv' );
            $morimo_is_blog = is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date();
            ?>
            <div class="hidden lg:flex items-center gap-1 bg-white/5 border border-white/10 rounded-full px-1.5 py-1">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors <?php echo $morimo_is_home ? 'bg-[#E50914] text-white' : 'text-muted hover:text-white'; ?>"<?php echo $morimo_is_home ? ' aria-current="page"' : ''; ?>><span class="material-symbols-outlined text-sm" aria-hidden="true">home</span>Home</a>
                <a href="<?php echo esc_url( $morimo_movies_link ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors <?php echo $morimo_is_movies ? 'bg-[#E50914] text-white' : 'text-muted hover:text-white'; ?>"<?php echo $morimo_is_movies ? ' aria-current="page"' : ''; ?>><span class="material-symbols-outlined text-sm" aria-hidden="true">movie</span>Movies</a>
                <a href="<?php echo esc_url( $morimo_tv_link ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors <?php echo $morimo_is_tv ? 'bg-[#E50914] text-white' : 'text-muted hover:text-white'; ?>"<?php echo $morimo_is_tv ? ' aria-current="page"' : ''; ?>><span class="material-symbols-outlined text-sm" aria-hidden="true">tv</span>TV</a>
                <a href="<?php echo esc_url( $morimo_genres_link ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors text-muted hover:text-white"><span class="material-symbols-outlined text-sm" aria-hidden="true">grid_view</span>Genres</a>
            </div>
              <!-- Movie Request Button (Desktop) — Premium Cinema -->
            <a href="#" id="movieRequestBtn" class="hidden lg:flex shrink-0 items-center gap-2 px-3 py-2 rounded-lg font-label-caps text-label-caps bg-[#271212]/50 border border-[#E50914]/50 text-[#FFB4AB] hover:bg-[#3A3939] hover:border-[#E50914] transition-all">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">add_circle</span>
                REQUEST
            </a>
            <!-- A-Z List Button (Desktop) -->
            <a href="<?php echo esc_url(home_url('/list/')); ?>" class="hidden lg:flex shrink-0 items-center gap-2 px-4 py-2 rounded-lg font-label-caps text-label-caps bg-[#271212]/40 border border-[#534343]/30 text-muted hover:text-white hover:bg-[#3A3939] transition-all">
                <span class="material-symbols-outlined text-sm">sort_by_alpha</span>
                LIST
            </a>
        </div>

        <div class="flex items-center gap-3 md:gap-4 min-w-0 flex-nowrap">
            <div class="hidden lg:flex items-center min-w-0 shrink bg-[#1D0E0E] rounded-full pl-4 pr-1.5 py-1.5 border border-[#534343]/30 focus-within:border-[#E50914] transition-colors">
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex;align-items:center;flex:1;gap:8px;">
                    <label class="sr-only" for="desktopSearch">Search movies, shows, actors</label>
                    <input id="desktopSearch" type="search" name="s" required class="bg-transparent border-none outline-none focus:ring-0 focus:outline-none font-body-md text-sm placeholder:text-[#6B6B75] w-56 xl:w-64 max-w-full text-white px-2 py-1.5 rounded-none" placeholder="Search movies..." value="<?php echo esc_attr(get_search_query()); ?>">
                    <button type="submit" aria-label="Search" class="material-symbols-outlined text-muted hover:text-[#FFB4AB] transition-colors w-10 h-10 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full shrink-0">search</button>
                </form>
            </div>
            <!-- Mobile Search Button -->
            <button type="button" aria-label="Search" aria-expanded="false" aria-controls="mobileSearchOverlay" class="lg:hidden material-symbols-outlined text-on-surface-variant hover:text-[#FFB4AB] transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center" id="mobileSearchBtn">search</button>
            <button type="button" aria-label="Account" class="material-symbols-outlined text-on-surface-variant hover:text-[#FFB4AB] transition-colors text-xl md:text-base rounded-full border border-white/20 w-10 h-10 min-w-[44px] min-h-[44px] flex items-center justify-center shrink-0">account_circle</button>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu -->
 <div class="mobile-menu glass-strong" id="mobileMenu">
     <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-menu-link">Home</a>
     <!-- A-Z List Link (Mobile) -->
     <a href="<?php echo esc_url(home_url('/list/')); ?>" class="mobile-menu-link">
         <span class="material-symbols-outlined">sort_by_alpha</span>
         A-Z List
     </a>
     <a href="<?php echo esc_url( isset( $morimo_movies_link ) ? $morimo_movies_link : ( get_post_type_archive_link( 'movie' ) ? get_post_type_archive_link( 'movie' ) : home_url( '/' ) ) ); ?>" class="mobile-menu-link">
         <span class="material-symbols-outlined">movie</span>
         Movies
     </a>
     <a href="<?php echo esc_url( isset( $morimo_tv_link ) ? $morimo_tv_link : ( get_post_type_archive_link( 'tv' ) ? get_post_type_archive_link( 'tv' ) : home_url( '/' ) ) ); ?>" class="mobile-menu-link">
         <span class="material-symbols-outlined">tv</span>
         TV
     </a>
     <a href="<?php echo esc_url( isset( $morimo_genres_link ) ? $morimo_genres_link : home_url( '/#explore-genres' ) ); ?>" class="mobile-menu-link">
         <span class="material-symbols-outlined">grid_view</span>
         Genres
     </a>
     <!-- Movie Request Link (Mobile) -->
     <a href="#" id="mobileMovieRequestBtn" class="mobile-menu-link">
         <span class="material-symbols-outlined">add_circle</span>
         Request Movie
     </a>
 </div>

<!-- Mobile Search Overlay -->
<div class="mobile-search-overlay glass-strong" id="mobileSearchOverlay" role="dialog" aria-modal="true" aria-label="Search movies, shows and actors">
    <div class="mobile-search-container">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-search-form glass-input">
            <button type="button" aria-label="Close search" class="material-symbols-outlined text-on-surface-variant hover:text-[#FFB4AB] transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center shrink-0" id="mobileSearchClose">arrow_back</button>
            <label class="sr-only" for="mobileSearchInput">Search movies, shows, actors</label>
            <input id="mobileSearchInput" type="search" name="s" required class="mobile-search-input" placeholder="Search movies, shows, actors..." value="<?php echo esc_attr(get_search_query()); ?>">
            <button type="submit" aria-label="Search" class="material-symbols-outlined text-muted hover:text-primary transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center shrink-0">search</button>
        </form>
    </div>
</div>

<!-- Movie Request Modal (deferred: inert <template> until idle/intent — zero first-paint cost; shell keeps z-[300]) -->
<div id="movieRequestModal" class="fixed inset-0 z-[300] hidden" hidden aria-hidden="true"></div>
<template id="movieRequestTemplate">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" id="movieRequestOverlay"></div>
    
    <!-- Modal Content -->
    <div class="relative flex items-center justify-center min-h-screen p-4 overflow-y-auto">
        <div class="glass-strong relative w-full max-w-md mx-2 bg-surface border border-primary/20 rounded-lg shadow-[0_0_30px_rgba(229,9,20,0.2)] my-4">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 md:p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-xl md:text-2xl" style="font-variation-settings: 'FILL' 1;">movie</span>
                    <h2 class="text-base md:text-lg font-bold uppercase tracking-wider text-white">Request a Movie</h2>
                </div>
                <button type="button" id="movieRequestClose" aria-label="Close dialog" class="text-muted hover:text-white transition-colors p-1">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            
            <!-- Form -->
            <form id="movieRequestForm" class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Movie Name -->
                <div>
                    <label for="request_movie_name" class="block text-xs tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">Movie Name *</label>
                    <input type="text" id="request_movie_name" name="movie_name" required
                        class="w-full bg-surface-container border border-white/10 text-white font-body-md text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder:text-dim"
                        placeholder="Enter movie name">
                </div>
                
                <!-- Release Year -->
                <div>
                    <label for="request_movie_year" class="block text-xs tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">Release Year *</label>
                    <input type="text" id="request_movie_year" name="movie_year" required
                        class="w-full bg-surface-container border border-white/10 text-white font-body-md text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder:text-dim"
                        placeholder="e.g., 2024">
                </div>
                
                <!-- IMDb/TMDB Link -->
                <div>
                    <label for="request_imdb_link" class="block text-xs tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">IMDb/TMDB Link <span class="text-muted">(optional)</span></label>
                    <input type="url" id="request_imdb_link" name="imdb_link"
                        class="w-full bg-surface-container border border-white/10 text-white font-body-md text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder:text-dim"
                        placeholder="https://www.imdb.com/title/...">
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="movieRequestSubmit"
                    class="btn-primary w-full uppercase tracking-wider text-sm md:text-base mt-6">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">send</span>
                    Submit Request
                </button>
                
                <!-- Message -->
                <div id="movieRequestMessage" class="hidden text-center text-sm font-body-md"></div>
            </form>
        </div>
    </div>
</template>

<!-- Movie Request Modal Styles -->
<style>
    .movie-request-modal-active {
        overflow: hidden;
    }
    /* Deferred modal: native hidden + utility class both hide; template content is inert until hydration */
    #movieRequestModal[hidden] {
        display: none !important;
    }
</style>
<style>
    /* Mobile-safe: only prevent horizontal scroll, don't override desktop sizing */
    html,body{overflow-x:hidden!important;}
    @media(min-width:1024px){#mobileSearchBtn{display:none!important}}
    body{position:relative}
    img{max-width:100%}
    .scanlines,.noise{overflow:hidden;max-width:100vw}
    .poster-aspect{aspect-ratio:2/3}
    #heroCarousel,#heroCarousel .g-grid,#heroCarousel .g-stage,#heroCarousel .g-fan-wrap{min-width:0}
    @media(max-width:640px){
        html,body{max-width:100%!important;box-sizing:border-box}
        *,*::before,*::after{box-sizing:border-box}
        img{max-width:100%}
        .max-w-container-max{box-sizing:border-box;min-width:0}
        .grid{box-sizing:border-box;min-width:0}
        .grid > *{min-width:0;box-sizing:border-box}
        .glass,.glass-panel,.glass-card{box-sizing:border-box;min-width:0}
        .movie-grid.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important}
        #heroCarousel,#heroCarousel .g-grid,#heroCarousel .g-stage{overflow:hidden}
    }
    @media(max-width:375px){
        .max-w-container-max.mx-auto.px-5{padding-left:14px!important;padding-right:14px!important}
        #explore-genres .flex.overflow-x-auto{margin-left:-14px!important;margin-right:-14px!important;padding-left:14px!important;padding-right:14px!important;max-width:calc(100% + 28px)!important;box-sizing:border-box!important}
        .flex.flex-wrap.justify-center{gap:8px!important}
        .w-10.h-10{width:36px!important;height:36px!important}
    }
    @media(max-width:350px){
        .movie-grid.grid-cols-2{gap:10px!important}
        .flex.flex-wrap.justify-center{gap:6px!important}
        .w-10.h-10{width:34px!important;height:34px!important}
    }
  </style>

<!-- Movie Request JavaScript (deferred: open triggers bind immediately via
     delegation; full handlers hydrate on DOMContentLoaded-idle/scroll/intent.
     CLS-safe: shell stays display:none until opened. INP-safe: no jQuery work
     on load, focus moved in rAF only on open.) -->
<script>
(function() {
    var hydrated = false;

    function closeRequestModal() {
        if (!hydrated) return;
        var shell = document.getElementById('movieRequestModal');
        if (!shell) return;
        shell.classList.add('hidden');
        shell.setAttribute('hidden', '');
        shell.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('movie-request-modal-active');
        var form = document.getElementById('movieRequestForm');
        if (form) form.reset();
        var msg = document.getElementById('movieRequestMessage');
        if (msg) msg.classList.add('hidden');
    }

    function openRequestModal() {
        if (!hydrateRequestModal()) return;
        var shell = document.getElementById('movieRequestModal');
        shell.classList.remove('hidden');
        shell.removeAttribute('hidden');
        shell.setAttribute('aria-hidden', 'false');
        document.body.classList.add('movie-request-modal-active');
        // Close mobile menu if open
        var mm = document.getElementById('mobileMenu');
        if (mm) mm.classList.remove('active');
        var mo = document.getElementById('mobileMenuOverlay');
        if (mo) mo.classList.remove('active');
        requestAnimationFrame(function() {
            var input = document.getElementById('request_movie_name');
            if (input) input.focus({ preventScroll: true });
        });
    }

    // Parse the inert template + bind handlers once (off the critical path).
    function hydrateRequestModal() {
        if (hydrated) return true;
        var shell = document.getElementById('movieRequestModal');
        var tpl = document.getElementById('movieRequestTemplate');
        if (!shell || !tpl || !tpl.content) return false;
        shell.appendChild(tpl.content.cloneNode(true));
        hydrated = true;
        document.getElementById('movieRequestClose').addEventListener('click', closeRequestModal);
        document.getElementById('movieRequestOverlay').addEventListener('click', closeRequestModal);
        bindRequestSubmit();
        return true;
    }

    // AJAX submit — identical behavior to before, bound only after hydration.
    function bindRequestSubmit() {
        var form = document.getElementById('movieRequestForm');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var submit = document.getElementById('movieRequestSubmit');
            var message = document.getElementById('movieRequestMessage');
            var data = new URLSearchParams({
                action: 'morimoflix_submit_movie_request',
                nonce: morimoflixRequest.nonce,
                movie_name: document.getElementById('request_movie_name').value,
                movie_year: document.getElementById('request_movie_year').value,
                imdb_link: document.getElementById('request_imdb_link').value
            });

            if (submit) { submit.disabled = true; submit.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Submitting...'; }
            fetch(morimoflixRequest.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
                .then(function(response) { return response.json(); })
                .then(function(response) {
                    if (message) {
                        message.classList.remove('hidden');
                        if (response.success) {
                            message.classList.remove('text-red-400');
                            message.classList.add('text-green-400');
                            message.textContent = response.data && response.data.message ? response.data.message : 'Request submitted.';
                            setTimeout(function() { closeRequestModal(); }, 2000);
                        } else {
                            message.classList.remove('text-green-400');
                            message.classList.add('text-red-400');
                            message.textContent = response.data && response.data.message ? response.data.message : 'Unable to submit.';
                        }
                    }
                })
                .catch(function() {
                    if (message) {
                        message.classList.remove('hidden', 'text-green-400');
                        message.classList.add('text-red-400');
                        message.textContent = 'An error occurred. Please try again.';
                    }
                })
                .finally(function() {
                    if (submit) { submit.disabled = false; submit.innerHTML = '<span class="material-symbols-outlined text-sm" style="font-variation-settings: \'FILL\' 1;">send</span> Submit Request'; }
                });
        });
    }

    // Globals for footer welcome-popup trigger (same open path, no duplicate markup).
    window.morimoflixOpenRequest = openRequestModal;
    window.morimoflixCloseRequest = closeRequestModal;

    // Delegated open — works even before hydration, costs nothing on load.
    document.addEventListener('click', function(e) {
        var t = e.target && e.target.closest ? e.target.closest('#movieRequestBtn, #mobileMovieRequestBtn, #seoRequestLink') : null;
        if (!t) return;
        e.preventDefault();
        openRequestModal();
    });

    // Escape closes (guarded pre-hydration).
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRequestModal();
    });

    // Idle/intent hydration: parse template off the critical path.
    function scheduleHydrate() {
        var fired = false;
        function run() { if (fired) return; fired = true; hydrateRequestModal(); }
        if ('requestIdleCallback' in window) {
            requestIdleCallback(run, { timeout: 2500 });
        } else {
            setTimeout(run, 1500);
        }
        window.addEventListener('scroll', run, { passive: true, once: true });
        window.addEventListener('pointerdown', run, { passive: true, once: true });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleHydrate);
    } else {
        scheduleHydrate();
    }
})();
</script>

<!-- Hero Motion driver removed (SAFE): orphaned .hero-slide target — markup uses .g-slide; real fan driver lives in front-page.php/index.php. -->
