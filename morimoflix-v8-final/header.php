<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&amp;family=Inter:wght@400;500;600&amp;family=JetBrains+Mono:wght@500;600&amp;family=Sora:wght@400;600;700;800&amp;family=Geist:wght@400;600&amp;family=Space+Mono:wght@400;700&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        // Premium Cinema — Pantone-mapped
                        // Pantone 419 C #101820 → background #0A0505, Pantone 186 C #F5002F → primary #E50914, Pantone 123 C #FFC20F → gold
                        "background": "#0A0505",
                        "base": "#0B0B0F",
                        "elev": "#15151B",
                        "overlay": "#1F1F26",
                        "border": "#2A2A33",
                        "ink": "#F5F5F7",
                        "muted": "#A0A0AA",
                        "dim": "#6B6B75",
                        "accent": {
                            "DEFAULT": "#E50914",
                            "hover": "#F6121D",
                            "soft": "#3A0A0D"
                        },
                        "gold": "#F5C518",
                        "success": "#22C55E",
                        "warning": "#F59E0B",
                        "telegram": "#229ED9",
                        // Premium Cinema specific
                        "primary": "#FFB4AB",
                        "primary-hover": "#E50914",
                        "primary-container": "#93000A",
                        "on-primary": "#680003",
                        "tertiary": "#4CD6FF",
                        "on-surface": "#FFEAE9",
                        "on-surface-variant": "#D8C2C2",
                        "outline-variant": "#534343",
                        "surface-container-high": "#271212",
                        "surface-bright": "#3A3939",
                        // Legacy aliases — kept for existing classes (bg-surface etc.)
                        "primary-fixed": "#6ff6ff",
                        "tertiary-container": "#36fd0f",
                        "primary-fixed-dim": "#00dce6",
                        "on-primary-container": "#006b71",
                        "primary": "#e3fdff",
                        "on-secondary-container": "#500050",
                        "surface-container-high": "#2a2a2a",
                        "surface-container": "#201f1f",
                        "tertiary": "#e8ffda",
                        "on-primary": "#00373a",
                        "on-surface-variant": "#b9cacb",
                        "secondary-fixed-dim": "#ffabf3",
                        "outline": "#849495",
                        "on-primary-fixed-variant": "#004f53",
                        "on-error-container": "#ffdad6",
                        "surface-container-lowest": "#0e0e0e",
                        "secondary-container": "#fe00fe",
                        "surface": "#131313",
                        "tertiary-fixed-dim": "#2ae500",
                        "surface-variant": "#353534",
                        "on-tertiary-fixed": "#022100",
                        "secondary": "#ffabf3",
                        "inverse-surface": "#e5e2e1",
                        "surface-bright": "#3a3939",
                        "inverse-on-surface": "#313030",
                        "secondary-fixed": "#ffd7f5",
                        "on-secondary": "#5b005b",
                        "on-tertiary-container": "#107000",
                        "on-tertiary": "#053900",
                        "on-surface": "#e5e2e1",
                        "inverse-primary": "#00696f",
                        "primary-container": "#00f3ff",
                        "on-secondary-fixed-variant": "#810081",
                        "on-primary-fixed": "#002022",
                        "surface-container-highest": "#353534",
                        "outline-variant": "#3a494b",
                        "tertiary-fixed": "#79ff5b",
                        "on-background": "#e5e2e1",
                        "on-error": "#690005",
                        "error": "#ffb4ab",
                        "surface-tint": "#00dce6",
                        "surface-dim": "#131313",
                        "background": "#131313",
                        "surface-container-low": "#1c1b1b",
                        "on-secondary-fixed": "#380038",
                        "on-tertiary-fixed-variant": "#095300",
                        "error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "64px",
                        "base": "8px",
                        "gutter": "24px",
                        "margin-mobile": "20px",
                        "container-max": "1440px",
                        "stack-sm": "12px",
                        "stack-md": "16px",
                        "section-gap": "48px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Montserrat"],
                        "headline-lg": ["Montserrat"],
                        "display-hero": ["Montserrat"],
                        "headline-md": ["Montserrat"],
                        "display-lg": ["Montserrat"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "label-mono-md": ["JetBrains Mono"],
                        "label-mono-sm": ["JetBrains Mono"],
                        "label-caps": ["JetBrains Mono"],
                        "metadata": ["JetBrains Mono"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["32px", {"lineHeight": "1.2", "fontWeight": "700"}],
                        "headline-lg": ["40px", {"lineHeight": "1.2", "fontWeight": "700"}],
                        "display-hero": ["56px", {"lineHeight": "1.05", "fontWeight": "800"}],
                        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
                        "label-mono-md": ["14px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                        "label-mono-sm": ["12px", {"lineHeight": "1", "letterSpacing": "0.1em", "fontWeight": "600"}],
                        "label-caps": ["11px", {"lineHeight": "1", "letterSpacing": "0.14em", "fontWeight": "600"}],
                        "metadata": ["13px", {"lineHeight": "1.4", "fontWeight": "500"}],
                        "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
                        "display-lg": ["64px", {"lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "800"}]
                    }
                }
            }
        }
    </script>
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
        .page-numbers {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .page-numbers .page-numbers,
        .page-numbers a,
        .page-numbers span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #b9cacb;
            font-family: 'Space Mono', monospace;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 4px;
        }
        
        .page-numbers a:hover,
        .page-numbers .page-numbers:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #00dce6;
            color: #e3fdff;
        }
        
        .page-numbers .page-numbers.current {
            background: #e3fdff;
            color: #00373a;
            border-color: #e3fdff;
            font-weight: bold;
        }
        
        .page-numbers .dots {
            background: none;
            border: none;
            color: #b9cacb;
            opacity: 0.5;
        }
        
        /* Telegram Logo - Minimal Glow & Flicker */
        .telegram-icon {
            width: 24px;
            height: 24px;
            filter: drop-shadow(0 0 4px rgba(0, 220, 230, 0.3));
            animation: telegram-subtle 4s ease-in-out infinite;
        }
        
        @keyframes telegram-subtle {
            0%, 100% { 
                filter: drop-shadow(0 0 4px rgba(0, 220, 230, 0.3));
                opacity: 0.8;
            }
            50% { 
                filter: drop-shadow(0 0 8px rgba(0, 220, 230, 0.5));
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
            border-color: #6ff6ff;
            box-shadow: 0 0 15px rgba(111, 246, 255, 0.3);
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
        ::selection { background: #93000A; color: #FFEAE9; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        .poster-aspect {
            aspect-ratio: 2/3;
        }
        
        .neon-glow-cyan {
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.4), inset 0 0 4px rgba(0, 243, 255, 0.2);
            border: 1px solid rgba(0, 243, 255, 0.6);
        }
        
        .neon-glow-magenta {
            box-shadow: 0 0 10px rgba(254, 0, 254, 0.4), inset 0 0 4px rgba(254, 0, 254, 0.2);
            border: 1px solid rgba(254, 0, 254, 0.6);
        }
        
        .text-glow-cyan {
            text-shadow: 0 0 8px rgba(0, 243, 255, 0.8);
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

        .mobile-menu-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            min-height: 48px;
            color: #FFEAE9;
            font-family: 'JetBrains Mono', 'Space Mono', monospace;
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
            background: transparent;
            border: none;
            outline: none;
            color: #000000;
            font-family: 'Space Mono', monospace;
            font-size: 14px;
            letter-spacing: 0.05em;
        }
        
        .mobile-search-input::placeholder {
            color: rgba(132, 148, 149, 0.5);
        }
        
        /* Mobile Touch Hover Effects */
        @media (max-width: 1023px) {
            .group:active .poster-aspect {
                border-color: #6ff6ff;
                box-shadow: 0 0 25px rgba(0, 243, 255, 0.3);
            }
            
            .group:active .poster-aspect img {
                transform: scale(1.05);
                filter: brightness(1.1);
            }
            
            .group:active h3 {
                color: #e3fdff;
            }
            
            .group:active .card-overlay,
            .group:active .absolute.inset-0.bg-gradient-to-t {
                opacity: 0.8;
            }
            
            /* Category cards touch effect */
            a.group:active {
                border-color: #6ff6ff;
                box-shadow: 0 0 15px rgba(0, 243, 255, 0.4);
            }
            
            a.group:active span {
                color: #e3fdff;
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
            font-family: 'Space Mono', monospace;
        }
        
        select option:hover,
        select option:focus {
            background-color: #0066ff;
            color: #fff;
        }
        
        select option:checked {
            background-color: #0066ff;
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
        .category-chip { background: rgba(39,18,18,0.4); border: 1px solid rgba(83,67,67,0.3); color: #D8C2C2; }
        .category-chip:hover { color: #FFEAE9; background: #3A3939; }
        .category-chip.active { background: rgba(255,180,171,0.1); border-color: rgba(255,180,171,0.5); color: #FFB4AB; box-shadow: 0 0 10px rgba(255,180,171,0.15); }
        /* Shared Glass API — Premium Cinema frosted chrome (header.php owns definitions) */
        .glass {
            background: rgba(16, 10, 12, 0.55);
            -webkit-backdrop-filter: blur(18px) saturate(140%);
            backdrop-filter: blur(18px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 8px 32px rgba(0, 0, 0, 0.35);
        }
        .glass-strong {
            background: rgba(10, 5, 5, 0.78);
            -webkit-backdrop-filter: blur(22px) saturate(140%);
            backdrop-filter: blur(22px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 12px 40px rgba(0, 0, 0, 0.5);
        }
        .glass-panel {
            background: rgba(21, 21, 27, 0.55);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            backdrop-filter: blur(16px) saturate(140%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 8px 32px rgba(0, 0, 0, 0.35);
            border-radius: 14px;
        }
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
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .glass, .glass-strong, .glass-panel, .glass-card, .glass-chip, .glass-input { background: #1D0E0E; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
        @media (max-width: 640px) {
            .glass { -webkit-backdrop-filter: blur(12px) saturate(140%); backdrop-filter: blur(12px) saturate(140%); }
            .glass-strong { -webkit-backdrop-filter: blur(14px) saturate(140%); backdrop-filter: blur(14px) saturate(140%); }
            .glass-panel, .glass-card, .glass-input { -webkit-backdrop-filter: blur(10px) saturate(140%); backdrop-filter: blur(10px) saturate(140%); }
            .glass-chip { -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
            .poster-frame::after { display: none; }
        }
    </style>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[var(--bg-base)] bg-background text-on-surface selection:bg-[#93000A] selection:text-[#FFEAE9] overflow-x-hidden font-body-md text-body-md' ); ?>>
<?php wp_body_open(); ?>

<div class="fixed inset-0 scanlines z-40"></div>

<!-- TopNavBar — Premium Cinema (z above mobile overlay so hamburger stays tappable) -->
<nav class="fixed top-0 left-0 right-0 w-full z-[130] transition-all duration-300 ease-in-out">
    <div class="glass rounded-full mx-4 md:mx-8 mt-3 px-4 md:px-6 h-14 md:h-16 flex justify-between items-center border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.45)] max-w-container-max lg:mx-auto">
        <div class="flex items-center gap-2 md:gap-6 lg:gap-8">
            <!-- Mobile Menu Button -->
            <button type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu" class="lg:hidden material-symbols-outlined text-on-surface-variant hover:text-primary-fixed transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center" id="mobileMenuBtn">menu</button>

            <a class="flex items-center gap-2" href="<?php echo esc_url( home_url( '/' ) ); ?>"><svg class="telegram-icon flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="url(#telegram-grad)"/><defs><linearGradient id="telegram-grad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse"><stop stop-color="#E50914" stop-opacity="0.9"/><stop offset="1" stop-color="#F5C518" stop-opacity="0.6"/></linearGradient></defs></svg><span class="logo-text text-xl md:text-2xl font-display-hero">MorimoFlix</span></a>
            <?php
            $morimo_movies_link = get_post_type_archive_link( 'movie' );
            if ( ! $morimo_movies_link ) { $morimo_movies_link = home_url( '/' ); }
            $morimo_genres_link = home_url( '/#explore-genres' );
            $morimo_is_home = is_front_page() || is_home();
            $morimo_is_movies = is_post_type_archive( 'movie' );
            ?>
            <div class="hidden lg:flex items-center gap-1 bg-white/5 border border-white/10 rounded-full px-1.5 py-1">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors <?php echo $morimo_is_home ? 'bg-[#E50914] text-white' : 'text-[#D8C2C2] hover:text-white'; ?>"<?php echo $morimo_is_home ? ' aria-current="page"' : ''; ?>><span class="material-symbols-outlined text-sm" aria-hidden="true">home</span>Home</a>
                <a href="<?php echo esc_url( $morimo_movies_link ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors <?php echo $morimo_is_movies ? 'bg-[#E50914] text-white' : 'text-[#D8C2C2] hover:text-white'; ?>"<?php echo $morimo_is_movies ? ' aria-current="page"' : ''; ?>><span class="material-symbols-outlined text-sm" aria-hidden="true">movie</span>Movies</a>
                <a href="<?php echo esc_url( $morimo_genres_link ); ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-label-caps text-label-caps transition-colors text-[#D8C2C2] hover:text-white"><span class="material-symbols-outlined text-sm" aria-hidden="true">grid_view</span>Genres</a>
            </div>
              <!-- Movie Request Button (Desktop) — Premium Cinema -->
            <a href="#" id="movieRequestBtn" class="hidden lg:flex items-center gap-2 px-4 py-2 rounded-lg font-label-caps text-label-caps bg-[#271212]/50 border border-[#E50914]/50 text-[#FFB4AB] hover:bg-[#3A3939] hover:border-[#E50914] transition-all shadow-[0_0_15px_rgba(255,83,87,0.4)]">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">add_circle</span>
                REQUEST
            </a>
            <!-- A-Z List Button (Desktop) -->
            <a href="<?php echo esc_url(home_url('/list/')); ?>" class="hidden lg:flex items-center gap-2 px-4 py-2 rounded-lg font-label-caps text-label-caps bg-[#271212]/40 border border-[#534343]/30 text-[#D8C2C2] hover:text-[#FFEAE9] hover:bg-[#3A3939] transition-all">
                <span class="material-symbols-outlined text-sm">sort_by_alpha</span>
                LIST
            </a>
        </div>

        <div class="flex items-center gap-3 md:gap-4">
            <div class="hidden lg:flex items-center bg-[#1D0E0E] rounded-full px-4 py-2 border border-[#534343]/30 focus-within:border-[#E50914] transition-colors">
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex;align-items:center;flex:1;gap:8px;">
                    <button type="submit" aria-label="Search" class="material-symbols-outlined text-[#4CD6FF] scale-75 cursor-pointer hover:text-[#FFB4AB] transition-colors">search</button>
                    <input type="search" name="s" class="glass-input bg-transparent border-none focus:ring-0 font-label-caps text-label-caps placeholder:text-[#6B6B75] w-48 text-[#FFEAE9] px-3 py-1.5" placeholder="Search movies, shows, actors..." value="<?php echo get_search_query(); ?>">
                </form>
            </div>
            <!-- Mobile Search Button -->
            <button type="button" aria-label="Search" class="lg:hidden material-symbols-outlined text-on-surface-variant hover:text-primary-fixed transition-colors text-xl p-2 -m-1 min-w-[44px] min-h-[44px] flex items-center justify-center" id="mobileSearchBtn">search</button>
            <button type="button" aria-label="Account" class="material-symbols-outlined text-on-surface-variant hover:text-primary-fixed transition-colors text-xl md:text-base rounded-full border border-white/20 w-10 h-10 flex items-center justify-center shrink-0">account_circle</button>
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
<div class="mobile-search-overlay glass-strong" id="mobileSearchOverlay">
    <div class="mobile-search-container">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-search-form glass-input">
            <button type="button" aria-label="Close search" class="material-symbols-outlined text-on-surface-variant hover:text-primary-fixed transition-colors" id="mobileSearchClose">arrow_back</button>
            <input type="search" name="s" class="mobile-search-input glass-input" placeholder="Search movies, shows, actors..." value="<?php echo get_search_query(); ?>" autofocus>
            <button type="submit" class="material-symbols-outlined text-primary-fixed-dim hover:text-primary transition-colors">search</button>
        </form>
    </div>
</div>

<!-- Movie Request Modal -->
<div id="movieRequestModal" class="fixed inset-0 z-[300] hidden">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" id="movieRequestOverlay"></div>
    
    <!-- Modal Content -->
    <div class="relative flex items-center justify-center min-h-screen p-4 overflow-y-auto">
        <div class="glass-strong relative w-full max-w-md mx-2 bg-surface border border-primary/20 rounded-lg shadow-[0_0_30px_rgba(0,220,230,0.1)] my-4">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 md:p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-xl md:text-2xl" style="font-variation-settings: 'FILL' 1;">movie</span>
                    <h3 class="text-base md:text-lg font-bold uppercase tracking-wider text-white">Request a Movie</h3>
                </div>
                <button type="button" id="movieRequestClose" aria-label="Close dialog" class="text-neutral-400 hover:text-white transition-colors p-1">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            
            <!-- Form -->
            <form id="movieRequestForm" class="p-4 md:p-6 space-y-4 md:space-y-5">
                <!-- Movie Name -->
                <div>
                    <label for="request_movie_name" class="block text-xs md:text-[10px] tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">Movie Name *</label>
                    <input type="text" id="request_movie_name" name="movie_name" required
                        class="w-full bg-surface-container border border-white/10 text-white font-sans text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder-neutral-500"
                        placeholder="Enter movie name">
                </div>
                
                <!-- Release Year -->
                <div>
                    <label for="request_movie_year" class="block text-xs md:text-[10px] tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">Release Year *</label>
                    <input type="text" id="request_movie_year" name="movie_year" required
                        class="w-full bg-surface-container border border-white/10 text-white font-sans text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder-neutral-500"
                        placeholder="e.g., 2024">
                </div>
                
                <!-- IMDb/TMDB Link -->
                <div>
                    <label for="request_imdb_link" class="block text-xs md:text-[10px] tracking-normal md:tracking-[0.2em] uppercase font-bold text-primary mb-1 md:mb-2">IMDb/TMDB Link <span class="text-neutral-400">(optional)</span></label>
                    <input type="url" id="request_imdb_link" name="imdb_link"
                        class="w-full bg-surface-container border border-white/10 text-white font-sans text-base md:text-sm px-4 py-3 focus:border-primary focus:ring-0 focus:outline-none rounded placeholder-neutral-500"
                        placeholder="https://www.imdb.com/title/...">
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="movieRequestSubmit"
                    class="glass-chip glass-chip-red w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white font-bold uppercase tracking-wider py-3 px-6 rounded hover:opacity-90 transition-all flex items-center justify-center gap-2 text-sm md:text-base mt-6">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">send</span>
                    Submit Request
                </button>
                
                <!-- Message -->
                <div id="movieRequestMessage" class="hidden text-center text-sm font-sans"></div>
            </form>
        </div>
    </div>
</div>

<!-- Movie Request Modal Styles -->
<style>
    .movie-request-modal-active {
        overflow: hidden;
    }
</style>
<style>
    html,body{overflow-x:hidden!important}body{position:relative}.scanlines,.noise{overflow:hidden}
</style>

<!-- Movie Request JavaScript -->
<script>
jQuery(document).ready(function($) {
    // Open modal
    $('#movieRequestBtn, #mobileMovieRequestBtn, #seoRequestLink').on('click', function(e) {
        e.preventDefault();
        $('#movieRequestModal').removeClass('hidden');
        $('body').addClass('movie-request-modal-active');
        // Close mobile menu if open
        $('#mobileMenu').removeClass('active');
        $('#mobileMenuOverlay').removeClass('active');
    });
    
    // Close modal
    $('#movieRequestClose, #movieRequestOverlay').on('click', function() {
        $('#movieRequestModal').addClass('hidden');
        $('body').removeClass('movie-request-modal-active');
        $('#movieRequestForm')[0].reset();
        $('#movieRequestMessage').addClass('hidden');
    });
    
    // Form submission
    $('#movieRequestForm').on('submit', function(e) {
        e.preventDefault();
        
        var $submit = $('#movieRequestSubmit');
        var $message = $('#movieRequestMessage');
        
        // Disable button
        $submit.prop('disabled', true).html('<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Submitting...');
        
        $.ajax({
            url: morimoflixRequest.ajaxUrl,
            type: 'POST',
            data: {
                action: 'morimoflix_submit_movie_request',
                nonce: morimoflixRequest.nonce,
                movie_name: $('#request_movie_name').val(),
                movie_year: $('#request_movie_year').val(),
                imdb_link: $('#request_imdb_link').val()
            },
            success: function(response) {
                if (response.success) {
                    $message.removeClass('hidden text-red-400').addClass('text-green-400').text(response.data.message);
                    setTimeout(function() {
                        $('#movieRequestModal').addClass('hidden');
                        $('body').removeClass('movie-request-modal-active');
                        $('#movieRequestForm')[0].reset();
                        $message.addClass('hidden');
                    }, 2000);
                } else {
                    $message.removeClass('hidden text-green-400').addClass('text-red-400').text(response.data.message);
                }
            },
            error: function() {
                $message.removeClass('hidden text-green-400').addClass('text-red-400').text('An error occurred. Please try again.');
            },
            complete: function() {
                $submit.prop('disabled', false).html('<span class="material-symbols-outlined text-sm" style="font-variation-settings: \'FILL\' 1;">send</span> Submit Request');
            }
        });
    });
});
</script>

<!-- Hero Carousel JavaScript (Motion) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var heroCarousel = document.getElementById('heroCarousel');
        if (!heroCarousel || typeof window.MotionAnimate === 'undefined') return;

        var slides = heroCarousel.querySelectorAll('.hero-slide');
        var slideCount = slides.length;
        if (slideCount <= 1) return;

        var currentHeroSlide = 0;
        var heroAutoInterval;
        var animating = false;

        function showHeroSlide(index) {
            if (animating) return;
            animating = true;
            slides.forEach(function(slide, i) {
                if (i === index) {
                    slide.style.zIndex = '10';
                    window.MotionAnimate(slide, { opacity: 1, scale: [0.98, 1] }, { duration: 0.7, ease: [0.25, 0.46, 0.45, 0.94] });
                } else {
                    window.MotionAnimate(slide, { opacity: 0 }, { duration: 0.5, ease: 'ease-out' });
                    setTimeout(function() { slide.style.zIndex = '0'; }, 500);
                }
            });
            setTimeout(function() { animating = false; }, 700);
        }

        function nextHeroSlide() {
            currentHeroSlide = (currentHeroSlide + 1) % slideCount;
            showHeroSlide(currentHeroSlide);
        }

        function prevHeroSlide() {
            currentHeroSlide = (currentHeroSlide - 1 + slideCount) % slideCount;
            showHeroSlide(currentHeroSlide);
        }

        function startHeroAutoSlide() {
            stopHeroAutoSlide();
            heroAutoInterval = setInterval(nextHeroSlide, 2000);
        }

        function stopHeroAutoSlide() {
            if (heroAutoInterval) clearInterval(heroAutoInterval);
        }

        showHeroSlide(0);
        startHeroAutoSlide();

        var prevBtn = document.getElementById('heroPrevBtn');
        var nextBtn = document.getElementById('heroNextBtn');

        if (prevBtn) prevBtn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            stopHeroAutoSlide(); prevHeroSlide(); startHeroAutoSlide();
        });
        if (nextBtn) nextBtn.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            stopHeroAutoSlide(); nextHeroSlide(); startHeroAutoSlide();
        });

        heroCarousel.addEventListener('mouseenter', stopHeroAutoSlide);
        heroCarousel.addEventListener('mouseleave', startHeroAutoSlide);
    });
</script>
