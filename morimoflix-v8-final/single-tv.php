<?php
/**
 * Single TV Series (CPT: tv)
 *
 * Renders series with a three-level download tree — Season → Quality → Codec — instead
 * of the single quality dropdown used for movies. single-movie.php is untouched; this is
 * a separate template reached automatically by WP's template hierarchy
 * (single-{post_type}.php → single.php) for the `tv` post type only.
 *
 * Design system, CSS class names, type scale and responsive behaviour are copied from
 * single-movie.php so a series page and a movie page read as one site.
 *
 * @package MorimoFlix
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $post_id        = get_the_ID();
        $series         = morimoflix_get_series_meta( $post_id );
        $genres         = get_the_terms( $post_id, 'genre' );
        $actors         = get_the_terms( $post_id, 'actor' );
        $backdrop       = morimoflix_get_backdrop( $post_id );
        $has_trailer    = ! empty( $series['trailer'] );
        $first_genre    = ( $genres && ! is_wp_error( $genres ) && isset( $genres[0] ) ) ? $genres[0] : null;
        $archive_link   = get_post_type_archive_link( 'tv' );
        $series_links   = morimoflix_parse_series_links( get_post_meta( $post_id, '_download_links', true ) );
        $has_series_dl  = ! empty( $series_links );

        $cast_names = array();
        if ( $actors && ! is_wp_error( $actors ) ) {
            foreach ( array_slice( $actors, 0, 10 ) as $a ) {
                $cast_names[] = $a->name;
            }
        } elseif ( ! empty( $series['cast'] ) && is_array( $series['cast'] ) ) {
            foreach ( array_slice( $series['cast'], 0, 10 ) as $c ) {
                $n = is_array( $c ) ? ( $c['name'] ?? '' ) : $c;
                if ( '' !== trim( (string) $n ) ) {
                    $cast_names[] = $n;
                }
            }
        }
    ?>
    <style>
    /* ===== Shared with single-movie.php (kept byte-identical where reusable) ===== */
    .mf-detail{background:#0B0B0F;overflow-x:hidden;max-width:100vw;}
    .mf-detail *,.mf-detail *::before,.mf-detail *::after{box-sizing:border-box;}
    html,body{overflow-x:hidden!important;max-width:100%;}
    .mf-detail img{max-width:100%;}
    .mf-hero,.mf-hero-inner,.mf-body,.mf-card,.mf-rel-grid,.mf-meta{box-sizing:border-box;max-width:100%;}

    /* ---------- HERO ---------- */
    .mf-hero{position:relative;overflow:hidden;background:#0B0B0F;isolation:isolate;width:100%;max-width:100vw;box-sizing:border-box;}
    .mf-hero-bg{position:absolute;inset:0;z-index:0;overflow:hidden;max-width:100%;}
    .mf-hero-bg img{width:100%;height:100%;object-fit:cover;object-position:center 22%;opacity:.82;display:block;max-width:100%;}
    .mf-hero-shade-left{position:absolute;inset:0;z-index:1;background:linear-gradient(90deg,rgba(11,11,15,.94) 0%,rgba(11,11,15,.82) 32%,rgba(11,11,15,.45) 58%,rgba(11,11,15,.12) 82%,rgba(11,11,15,.05) 100%);}
    .mf-hero-shade-bottom{position:absolute;inset:0;z-index:1;background:linear-gradient(to bottom,rgba(11,11,15,.35) 0%,rgba(11,11,15,0) 34%,rgba(11,11,15,.55) 72%,#0B0B0F 100%);}
    .mf-hero-vignette{position:absolute;inset:0;z-index:1;pointer-events:none;box-shadow:inset 0 0 80px rgba(0,0,0,.45);max-width:100%;box-sizing:border-box;}
    .mf-hero-inner{position:relative;z-index:2;display:flex;flex-direction:column;justify-content:flex-end;min-height:88svh;max-width:1200px;width:100%;margin:0 auto;padding:120px 20px 44px;box-sizing:border-box;min-width:0;}

    /* ---------- TITLE ---------- */
    .mf-kicker{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;}
    .mf-pill{display:inline-flex;align-items:center;padding:5px 13px;border-radius:9999px;font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#E8D9D9;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14);text-decoration:none;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
    a.mf-pill:hover{border-color:rgba(229,9,20,.55);color:#FFB4AB;}
    .mf-crumb{margin:0 0 12px;min-width:0;max-width:100%;}
    .mf-crumb ol{display:flex;flex-wrap:wrap;align-items:center;gap:4px 8px;list-style:none;margin:0;padding:0;min-width:0;max-width:100%;}
    .mf-crumb li{display:inline-flex;align-items:center;gap:8px;min-width:0;max-width:100%;}
    .mf-crumb a{display:inline-flex;align-items:center;min-height:26px;color:#B9B3B4;font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;}
    .mf-crumb a:hover{color:#FFB4AB;}
    .mf-crumb .sep{color:#777376;font-size:11px;flex-shrink:0;}
    .mf-crumb [aria-current="page"]{display:inline-flex;align-items:center;min-height:26px;color:#EDE6E7;font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:38ch;min-width:0;}
    .mf-h1{margin:0;max-width:16ch;overflow-wrap:break-word;word-break:break-word;text-wrap:balance;min-width:0;font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:-.025em;line-height:1.02;font-size:clamp(2.6rem,6vw,4.8rem);color:#F7F4F4;text-shadow:0 2px 30px rgba(0,0,0,.6);}
    .mf-h1-year{display:block;margin-top:10px;font-size:clamp(1rem,2.2vw,1.35rem);font-weight:700;letter-spacing:.08em;color:#E50914;}
    .mf-tagline{margin:12px 0 0;font-size:15px;font-style:italic;color:#C9B8B8;}
    .mf-desc{margin:14px 0 0;max-width:38rem;font-size:15.5px;line-height:1.75;color:#CFC9CB;font-weight:400;}

    /* ---------- META ---------- */
    .mf-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:26px;max-width:640px;width:100%;box-sizing:border-box;min-width:0;}
    .mf-meta-item{display:flex;align-items:center;gap:12px;min-height:76px;padding:12px 16px;border-radius:12px;background:rgba(255,255,255,.045);border:1px solid rgba(255,255,255,.1);box-shadow:0 4px 16px rgba(0,0,0,.3);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);min-width:0;max-width:100%;overflow:hidden;box-sizing:border-box;transition:transform .2s ease,border-color .2s ease;}
    .mf-meta-item:hover{transform:translateY(-2px);border-color:rgba(255,255,255,.2);}
    .mf-meta-item .material-symbols-outlined{font-size:24px;flex-shrink:0;color:#E8A33D;}
    .mf-meta-item.plain .material-symbols-outlined{color:#D8D5D6;}
    .mf-meta-v{font-family:'Montserrat',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .mf-meta-l{font-family:'JetBrains Mono',monospace;font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#8E8A8E;}
    .mf-meta-wide{grid-column:span 2;}

    /* ---------- CTA ---------- */
    .mf-cta-row{display:flex;flex-direction:column;gap:10px;margin-top:24px;max-width:640px;width:100%;box-sizing:border-box;min-width:0;}
    .mf-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:52px;padding:14px 26px;border-radius:12px;font-family:'Montserrat',sans-serif;font-weight:800;font-size:13px;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:transform .2s ease,box-shadow .2s ease,background .2s ease,border-color .2s ease;}
    .mf-btn:active{transform:scale(.98);}
    .mf-btn-primary{color:#fff;background:linear-gradient(135deg,#E8111C 0%,#A50D14 100%);border:1px solid rgba(255,255,255,.14);box-shadow:0 8px 24px rgba(229,9,20,.35);}
    .mf-btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(229,9,20,.45);filter:brightness(1.06);}
    .mf-btn-ghost{color:#EDE8E9;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.16);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);}
    .mf-btn-ghost:hover{border-color:rgba(255,255,255,.32);background:rgba(255,255,255,.09);}

    /* ---------- BODY ---------- */
    .mf-body{max-width:1200px;width:100%;margin:0 auto;padding:8px 20px 64px;box-sizing:border-box;overflow-x:hidden;min-width:0;}
    .mf-sec{margin-top:44px;}
    .mf-eyebrow{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.3em;text-transform:uppercase;color:#8E8A8E;margin:0 0 10px;}
    .mf-h2{margin:0 0 14px;font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:-.02em;font-size:clamp(1.4rem,3vw,2rem);line-height:1.15;color:#F5F2F2;text-wrap:balance;}
    .mf-h2 .accent{color:#E50914;}
    .mf-prose{font-size:15px;line-height:1.8;color:#B9B3B4;max-width:46rem;}
    .mf-prose p{margin:0 0 1em;}
    .mf-prose p:last-child{margin-bottom:0;}
    .mf-card{background:rgba(255,255,255,.032);border:1px solid rgba(255,255,255,.09);border-radius:16px;box-shadow:0 10px 32px rgba(0,0,0,.35);padding:22px;box-sizing:border-box;max-width:100%;min-width:0;overflow:hidden;}
    .mf-byline{margin:16px 0 0;font-size:12.5px;color:#777376;}
    .mf-byline a{color:#D99A9C;}
    .mf-byline a:hover{color:#FFB4AB;}
    .mf-cast{display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;}
    .mf-cast a,.mf-cast span{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:9999px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:#DDD6D7;font-size:13px;text-decoration:none;transition:border-color .2s ease;}
    .mf-cast a:hover{border-color:rgba(229,9,20,.55);color:#FFB4AB;}

    /* ============ SERIES DOWNLOAD TREE ============
       Level 1  season   (.sv-season / .sv-season-body)  — accordion: one open at a time
       Level 2  quality  (.sv-quality / .sv-quality-body) — accordion: one open at a time
       Level 3  codec    (.sv-codec / .sv-codec-body)     — independent toggles
       Panels use max-height transitions so expand/collapse animates, with a JS-computed
       px value because the content height is unknown until rendered. Collapsed panels are
       also removed from the a11y tree and made non-focusable (inert when supported). */
    .sv-ctrl{display:flex;align-items:center;gap:10px;width:100%;min-height:48px;padding:12px 16px;margin:0 0 10px;border-radius:12px;background:rgba(255,255,255,.045);border:1px solid rgba(255,255,255,.11);color:#EDE6E7;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;text-align:left;cursor:pointer;box-sizing:border-box;min-width:0;transition:border-color .2s ease,background .2s ease;}
    .sv-ctrl:hover{border-color:rgba(229,9,20,.55);background:rgba(229,9,20,.08);}
    .sv-ctrl:focus-visible{outline:2px solid rgba(229,9,20,.6);outline-offset:2px;}
    .sv-ctrl .material-symbols-outlined{font-size:20px;flex-shrink:0;transition:transform .3s ease;color:#FFB4AB;}
    .sv-ctrl[aria-expanded="true"] .material-symbols-outlined{transform:rotate(180deg);}
    .sv-ctrl .sv-count{margin-left:auto;flex-shrink:0;font-size:11px;color:#8E8A8E;letter-spacing:.08em;}

    /* Quality colour coding for series toggles */
    .sv-ctrl[data-q="2160p"]{background:rgba(168,85,247,.12);border-color:rgba(168,85,247,.45);color:#F3E8FF;}
    .sv-ctrl[data-q="2160p"]:hover{border-color:rgba(168,85,247,.75);background:rgba(168,85,247,.18);}
    .sv-ctrl[data-q="1080p"]{background:rgba(59,130,246,.11);border-color:rgba(59,130,246,.45);color:#DBEAFE;}
    .sv-ctrl[data-q="1080p"]:hover{border-color:rgba(59,130,246,.75);background:rgba(59,130,246,.17);}
    .sv-ctrl[data-q="720p"]{background:rgba(34,197,94,.10);border-color:rgba(34,197,94,.45);color:#DCFCE7;}
    .sv-ctrl[data-q="720p"]:hover{border-color:rgba(34,197,94,.75);background:rgba(34,197,94,.16);}
    .sv-ctrl[data-q="480p"]{background:rgba(245,158,11,.11);border-color:rgba(245,158,11,.45);color:#FEF3C7;}
    .sv-ctrl[data-q="480p"]:hover{border-color:rgba(245,158,11,.75);background:rgba(245,158,11,.17);}
    .sv-ctrl[data-c][data-q="2160p"]{background:rgba(168,85,247,.06);border-color:rgba(168,85,247,.25);color:#E9D5FF;}
    .sv-ctrl[data-c][data-q="2160p"]:hover{border-color:rgba(168,85,247,.45);background:rgba(168,85,247,.10);}
    .sv-ctrl[data-c][data-q="1080p"]{background:rgba(59,130,246,.055);border-color:rgba(59,130,246,.25);color:#BFDBFE;}
    .sv-ctrl[data-c][data-q="1080p"]:hover{border-color:rgba(59,130,246,.45);background:rgba(59,130,246,.10);}
    .sv-ctrl[data-c][data-q="720p"]{background:rgba(34,197,94,.055);border-color:rgba(34,197,94,.25);color:#BBF7D0;}
    .sv-ctrl[data-c][data-q="720p"]:hover{border-color:rgba(34,197,94,.45);background:rgba(34,197,94,.10);}
    .sv-ctrl[data-c][data-q="480p"]{background:rgba(245,158,11,.06);border-color:rgba(245,158,11,.25);color:#FDE68A;}
    .sv-ctrl[data-c][data-q="480p"]:hover{border-color:rgba(245,158,11,.45);background:rgba(245,158,11,.10);}
    .sv-ctrl[data-c]:not([data-q="2160p"]):not([data-q="1080p"]):not([data-q="720p"]):not([data-q="480p"]){background:rgba(14,165,233,.06);border-color:rgba(14,165,233,.25);color:#BAE6FD;}
    .sv-ctrl[data-c]:not([data-q="2160p"]):not([data-q="1080p"]):not([data-q="720p"]):not([data-q="480p"]):hover{border-color:rgba(14,165,233,.45);background:rgba(14,165,233,.10);}
    .sv-ctrl[data-q="2160p"] .material-symbols-outlined{color:#D8B4FE;}
    .sv-ctrl[data-q="1080p"] .material-symbols-outlined{color:#93C5FD;}
    .sv-ctrl[data-q="720p"] .material-symbols-outlined{color:#86EFAC;}
    .sv-ctrl[data-q="480p"] .material-symbols-outlined{color:#FCD34D;}
    .sv-ctrl[data-c][data-q="2160p"] .material-symbols-outlined{color:#C084FC;}
    .sv-ctrl[data-c][data-q="1080p"] .material-symbols-outlined{color:#60A5FA;}
    .sv-ctrl[data-c][data-q="720p"] .material-symbols-outlined{color:#4ADE80;}
    .sv-ctrl[data-c][data-q="480p"] .material-symbols-outlined{color:#FBBF24;}
    .sv-ctrl[data-c]:not([data-q="2160p"]):not([data-q="1080p"]):not([data-q="720p"]):not([data-q="480p"]) .material-symbols-outlined{color:#38BDF8;}

    /* Matching dark tints for the expanded quality/codec panels */
    .sv-ctrl[data-q="2160p"] + .sv-season-body,
    .sv-ctrl[data-c][data-q="2160p"] + .sv-season-body{background:rgba(168,85,247,.045);border-radius:12px;}
    .sv-ctrl[data-q="1080p"] + .sv-season-body,
    .sv-ctrl[data-c][data-q="1080p"] + .sv-season-body{background:rgba(59,130,246,.04);border-radius:12px;}
    .sv-ctrl[data-q="720p"] + .sv-season-body,
    .sv-ctrl[data-c][data-q="720p"] + .sv-season-body{background:rgba(34,197,94,.04);border-radius:12px;}
    .sv-ctrl[data-q="480p"] + .sv-season-body,
    .sv-ctrl[data-c][data-q="480p"] + .sv-season-body{background:rgba(245,158,11,.04);border-radius:12px;}
    .sv-season{position:relative;margin:0 0 14px;border-radius:14px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);padding:12px;box-sizing:border-box;min-width:0;}
    .sv-season:last-child{margin-bottom:0;}
    .sv-season.is-open{border-color:rgba(229,9,20,.28);}
    .sv-season-body{overflow:hidden;max-height:0;transition:max-height .38s cubic-bezier(.4,0,.2,1);will-change:max-height;}
    .sv-season-body.is-open{max-height:none;}
    .sv-pack{padding:6px 2px 4px;display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,260px),1fr));gap:10px;}
    .sv-pack-card{display:flex;flex-direction:column;gap:10px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.09);box-sizing:border-box;min-width:0;}
    .sv-pack-card:hover{border-color:rgba(229,9,20,.4);}
    .sv-pack-title{margin:0;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:500;line-height:1.5;color:#D8D2D3;word-break:break-word;overflow-wrap:anywhere;}
    .sv-dl{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:10px 16px;border-radius:10px;background:linear-gradient(135deg,#E8111C 0%,#A50D14 100%);border:1px solid rgba(255,255,255,.14);color:#fff;font-family:'Montserrat',sans-serif;font-weight:800;font-size:11px;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;box-sizing:border-box;transition:transform .2s ease,box-shadow .2s ease;}
    .sv-dl:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(229,9,20,.35);}
    .sv-dl:active{transform:scale(.98);}
    .sv-dl .material-symbols-outlined{font-size:18px;}
    .sv-size{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.04em;}
    .sv-ep-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,280px),1fr));gap:10px;padding:6px 2px 2px;}
    .sv-ep{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 13px;border-radius:11px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.09);box-sizing:border-box;min-width:0;}
    .sv-ep:hover{border-color:rgba(229,9,20,.4);}
    .sv-ep-l{min-width:0;display:flex;align-items:center;gap:8px;}
    .sv-ep-n{flex-shrink:0;min-width:34px;font-family:'Montserrat',sans-serif;font-weight:800;font-size:13px;color:#FFB4AB;}
    .sv-ep-title{font-size:12px;line-height:1.4;color:#C6C0C1;word-break:break-word;overflow-wrap:anywhere;}
    .sv-badge{flex-shrink:0;padding:3px 7px;border-radius:6px;background:rgba(229,9,20,.16);border:1px solid rgba(229,9,20,.42);font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#FFB4AB;}
    .sv-badge.codec{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.16);color:#C9B8B8;}
    .sv-note{font-size:12.5px;color:#8E8A8E;margin:0 0 10px;}

    /* ---------- Tags (mirrors the reference "Tags Ignore" block) ---------- */
    .sv-tags summary{cursor:pointer;list-style:revert;display:flex;align-items:center;gap:10px;min-height:44px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#DDD6D7;}
    .sv-tags summary::marker{color:#E50914;}
    .sv-tags summary::-webkit-details-marker{color:#E50914;}
    .sv-tags summary:hover{color:#FFB4AB;}
    .sv-tags summary:focus-visible{outline:2px solid rgba(229,9,20,.6);outline-offset:2px;border-radius:6px;}
    .sv-tags p{margin:12px 0 0;font-size:13.5px;line-height:1.75;color:#A09B9C;}
    .sv-tags p:last-child{margin-bottom:0;}

    /* ---------- RELATED ---------- */
    .mf-rel-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;width:100%;max-width:100%;box-sizing:border-box;min-width:0;}
    .mf-rel-card{display:block;text-decoration:none;min-width:0;max-width:100%;box-sizing:border-box;overflow:hidden;}
    .mf-rel-img{overflow:hidden;border-radius:12px;border:1px solid rgba(255,255,255,.09);background:#17171C;aspect-ratio:2/3;transition:border-color .25s ease,transform .25s ease;width:100%;max-width:100%;box-sizing:border-box;min-width:0;}
    .mf-rel-card:hover .mf-rel-img{border-color:rgba(229,9,20,.5);transform:translateY(-3px);}
    .mf-rel-img img{width:100%;height:100%;object-fit:cover;display:block;max-width:100%;}
    .mf-rel-t{margin:10px 0 0;font-size:13px;font-weight:600;color:#D8D2D3;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;box-sizing:border-box;min-width:0;}
    .mf-rel-card:hover .mf-rel-t{color:#fff;}
    .mf-rel-m{margin:4px 0 0;font-size:11.5px;color:#777376;}

    @media (min-width: 640px) {
        .mf-cta-row{flex-direction:row;}
        .mf-btn-primary{flex:1.35;}
        .mf-btn-ghost{flex:1;}
        .mf-rel-grid{grid-template-columns:repeat(4,1fr);gap:20px;}
    }
    @media (min-width: 768px) {
        .mf-meta{grid-template-columns:repeat(4,1fr);max-width:760px;}
        .mf-meta-wide{grid-column:span 4;}
        .mf-hero-inner{padding:150px 32px 56px;}
        .mf-body{padding:8px 32px 80px;}
        .mf-card{padding:30px 32px;}
    }
    @media (max-width: 639px) {
        .mf-hero{overflow:hidden;min-width:0;}
        .mf-hero-bg img{object-position:center 20%;}
        .mf-hero-shade-left{background:linear-gradient(to bottom,rgba(11,11,15,.55) 0%,rgba(11,11,15,.72) 45%,rgba(11,11,15,.9) 75%,#0B0B0F 100%);}
        .mf-hero-vignette{box-shadow:inset 0 0 60px rgba(0,0,0,.42);}
        .mf-hero-inner{min-height:clamp(420px,68svh,560px);padding:84px 20px 32px;justify-content:flex-end;min-width:0;width:100%;}
        .mf-kicker{gap:6px;margin-bottom:10px;}
        .mf-h1{font-size:clamp(1.85rem,7vw,2.45rem);line-height:1.05;max-width:13.5ch;}
        .mf-h1-year{font-size:13px;margin-top:8px;}
        .mf-tagline{font-size:13.5px;max-width:100%;overflow-wrap:break-word;word-break:break-word;}
        .mf-desc{font-size:14px;line-height:1.6;max-width:100%;overflow-wrap:break-word;word-break:break-word;}
        .mf-meta{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:20px;width:100%;max-width:100%;}
        .mf-meta-item{padding:10px 12px;min-height:64px;gap:10px;min-width:0;}
        .mf-meta-v{font-size:13.5px;}
        .mf-meta-l{font-size:11px;}
        .mf-meta-wide{grid-column:span 2;}
        .mf-cta-row{gap:9px;margin-top:18px;width:100%;}
        .mf-btn{min-height:48px;padding:12px 18px;width:100%;box-sizing:border-box;min-width:0;}
        .mf-body{padding:8px 20px 48px;width:100%;max-width:100vw;box-sizing:border-box;overflow-x:hidden;min-width:0;}
        .mf-sec{margin-top:32px;min-width:0;max-width:100%;}
        .mf-card{padding:18px 16px;max-width:100%;min-width:0;}
        .mf-prose{font-size:14px;line-height:1.7;max-width:100%;overflow-wrap:break-word;word-break:break-word;min-width:0;}
        .mf-card,.mf-prose,.mf-cast,.mf-season,.mf-pack,.mf-ep-grid{min-width:0;max-width:100%;box-sizing:border-box;}
        .mf-rel-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;width:100%;max-width:100%;box-sizing:border-box;}
        .mf-rel-card{min-width:0;max-width:100%;overflow:hidden;}
        .mf-rel-img{width:100%;max-width:100%;}
        .mf-crumb{margin-bottom:10px;max-width:100%;overflow:hidden;}
        .mf-crumb a,.mf-crumb [aria-current="page"]{font-size:11px!important;min-height:26px!important;}
        .mf-crumb [aria-current="page"]{max-width:52vw!important;}
        .mf-h2{font-size:clamp(1.25rem,5vw,1.5rem);}
        .sv-ctrl{font-size:11px;letter-spacing:.08em;padding:11px 13px;min-height:46px;gap:8px;}
        .sv-ctrl .material-symbols-outlined{font-size:18px;}
        .sv-season{padding:10px;}
        .sv-pack,.sv-ep-grid{grid-template-columns:1fr;gap:9px;}
        .sv-ep{flex-wrap:wrap;}
        .sv-dl{width:100%;}
    }
    @media (max-width: 375px) {
        .mf-hero-inner{padding-left:14px;padding-right:14px;padding-top:78px;min-height:clamp(400px,66svh,520px);}
        .mf-body{padding-left:14px;padding-right:14px;}
        .mf-card{padding:16px 14px;}
        .mf-rel-grid{gap:12px;}
        .mf-meta{gap:8px;}
        .mf-meta-item{padding:9px 10px;min-height:60px;gap:8px;}
        .mf-h1{font-size:clamp(1.65rem,7.5vw,2.15rem);max-width:12.5ch;}
        .sv-ctrl{font-size:10.5px;letter-spacing:.06em;}
    }
    @media (max-width: 350px) {
        .mf-hero-inner{padding-left:14px;padding-right:14px;}
        .mf-body{padding-left:14px;padding-right:14px;}
        .mf-rel-grid{gap:10px;}
        .mf-rel-t{font-size:12px;}
        .mf-meta{gap:6px;}
    }
    @media (prefers-reduced-motion: reduce) {
        .mf-btn,.mf-meta-item,.mf-rel-img,.mf-cast a,.sv-ctrl,.sv-season-body,.sv-ctrl .material-symbols-outlined,.sv-dl,.mf-syn-toggle,.mf-syn-chev{transition:none;}
        .sv-ctrl[aria-expanded="true"] .material-symbols-outlined{transform:none;}
        .mf-syn-extra{animation:none;}
    }
    /* ---------- Synopsis show-more (reference: hentaidekho.mom) ---------- */
    .mf-synopsis{position:relative;}
    .mf-syn-short{margin:0;max-width:38rem;}
    .mf-syn-extra{display:none;margin-top:1rem;padding-top:1rem;border-top:1px dashed rgba(255,255,255,0.12);}
    .mf-syn-open .mf-syn-extra{display:block;animation:mf-syn-fade .4s ease;}
    @keyframes mf-syn-fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
    .mf-syn-extra img{max-width:100%;height:auto;border-radius:12px;border:1px solid rgba(255,255,255,0.1);margin:0.5rem 0 1rem;display:block;}
    .mf-syn-extra p{margin:0 0 1rem;line-height:1.7;color:#B9B3B4;}
    .mf-syn-extra h2,.mf-syn-extra h3{color:#fff;margin:1.2rem 0 0.5rem;font-size:1.1rem;}
    .mf-syn-toggle{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;margin-top:1rem;padding:0.62rem 1rem;min-height:44px;box-sizing:border-box;background:rgba(229,9,20,0.08);border:1px solid rgba(229,9,20,0.25);border-radius:50px;color:#E50914;font-family:inherit;font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;cursor:pointer;transition:background .3s ease,border-color .3s ease;}
    .mf-syn-toggle:hover{background:rgba(229,9,20,0.16);border-color:#E50914;}
    .mf-syn-toggle .mf-syn-chev{display:inline-block;transition:transform .3s ease;}
    .mf-syn-open .mf-syn-toggle .mf-syn-chev{transform:rotate(180deg);}
    </style>

    <main class="mf-detail">
        <!-- ============ HERO ============ -->
        <section class="mf-hero" aria-labelledby="mf-series-title">
            <?php if ( $backdrop ) : ?>
            <div class="mf-hero-bg" aria-hidden="true">
                <img src="<?php echo esc_url( $backdrop ); ?>" alt="<?php echo esc_attr( get_the_title() . ' backdrop' ); ?>" loading="eager" fetchpriority="high" decoding="async">
            </div>
            <div class="mf-hero-shade-left" aria-hidden="true"></div>
            <div class="mf-hero-shade-bottom" aria-hidden="true"></div>
            <div class="mf-hero-vignette" aria-hidden="true"></div>
            <?php endif; ?>
            <div class="mf-hero-inner">
                <nav class="mf-crumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'morimoflix-flicker' ); ?>">
                    <ol>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'morimoflix-flicker' ); ?></a><span class="sep" aria-hidden="true">/</span></li>
                        <?php if ( $archive_link ) : ?>
                        <li><a href="<?php echo esc_url( $archive_link ); ?>"><?php esc_html_e( 'TV Shows', 'morimoflix-flicker' ); ?></a><span class="sep" aria-hidden="true">/</span></li>
                        <?php endif; ?>
                        <li><span aria-current="page"><?php the_title(); ?></span></li>
                    </ol>
                </nav>

                <?php if ( $genres && ! is_wp_error( $genres ) ) : ?>
                <div class="mf-kicker">
                    <?php foreach ( array_slice( $genres, 0, 3 ) as $g ) : ?>
                    <a class="mf-pill" href="<?php echo esc_url( get_term_link( $g ) ); ?>"><?php echo esc_html( $g->name ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <h1 class="mf-h1" id="mf-series-title"><?php the_title(); ?><span class="mf-h1-year"><?php echo esc_html( $series['year'] ); ?></span></h1>

                <?php if ( ! empty( $series['tagline'] ) ) : ?>
                <p class="mf-tagline"><?php echo esc_html( $series['tagline'] ); ?></p>
                <?php endif; ?>

                <?php
                // Read the excerpt once: the predicate and the rendered text must agree, even if a
                // third party ever attaches a non-idempotent 'get_the_excerpt' filter.
                $mf_excerpt = has_excerpt() ? get_the_excerpt() : '';
                if ( '' !== $mf_excerpt && ! morimoflix_texts_are_duplicate( $mf_excerpt, $series['tagline'] ) ) :
                ?>
                <div class="mf-synopsis" id="mfSynopsis">
                    <p class="mf-desc mf-syn-short"><?php echo esc_html( $mf_excerpt ); ?></p>
                    <div class="mf-syn-extra" id="mf-syn-extra">
                        <?php if ( ! empty( $backdrop ) ) : ?>
                        <img src="<?php echo esc_url( $backdrop ); ?>" alt="<?php echo esc_attr( get_the_title() . ' ' . $series['year'] . ' series' ); ?>" width="1280" height="720" loading="lazy" decoding="async" style="aspect-ratio:16/9;" />
                        <?php endif; ?>
                        <div class="mf-prose">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    <button type="button" class="mf-syn-toggle" onclick="morimoflixSynToggle(this)" aria-expanded="false" aria-controls="mf-syn-extra">
                        <span class="mf-syn-toggle-label">Show More</span>
                        <span class="mf-syn-chev" aria-hidden="true">&#8964;</span>
                    </button>
                </div>
                <?php endif; ?>

                <noscript><style>.mf-syn-extra{display:block!important;}.mf-syn-toggle{display:none!important;}</style></noscript>

                <div class="mf-meta">
                    <?php if ( $series['seasons'] > 0 ) : ?>
                    <div class="mf-meta-item">
                        <span class="material-symbols-outlined" aria-hidden="true">tv</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['seasons'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Seasons', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( $series['episodes'] > 0 ) : ?>
                    <div class="mf-meta-item">
                        <span class="material-symbols-outlined" aria-hidden="true">format_list_numbered</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['episodes'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Episodes', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $series['status'] ) ) : ?>
                    <div class="mf-meta-item plain">
                        <span class="material-symbols-outlined" aria-hidden="true">bolt</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['status'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Status', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <div class="mf-meta-item">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">star</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['rating'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'IMDb', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php if ( $series['episode_run'] > 0 ) : ?>
                    <div class="mf-meta-item plain">
                        <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['episode_run'] ); ?><?php esc_html_e( 'M', 'morimoflix-flicker' ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Per Episode', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( $series['first_air'] ) : ?>
                    <div class="mf-meta-item plain">
                        <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['first_air'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'First Aired', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $series['language'] ) ) : ?>
                    <div class="mf-meta-item plain">
                        <span class="material-symbols-outlined" aria-hidden="true">language</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $series['language'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Language', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $series['networks'] ) ) : ?>
                    <div class="mf-meta-item plain mf-meta-wide">
                        <span class="material-symbols-outlined" aria-hidden="true">live_tv</span>
                        <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( implode( ', ', $series['networks'] ) ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Network', 'morimoflix-flicker' ); ?></div></div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mf-cta-row">
                    <a href="#series-download" class="mf-btn mf-btn-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">download</span>
                        <?php esc_html_e( 'Download Series', 'morimoflix-flicker' ); ?>
                    </a>
                    <?php if ( $has_trailer ) : ?>
                    <a href="<?php echo esc_url( $series['trailer'] ); ?>" target="_blank" rel="noopener noreferrer" class="mf-btn mf-btn-ghost">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">play_arrow</span>
                        <?php esc_html_e( 'Watch Trailer', 'morimoflix-flicker' ); ?>
                    </a>
                    <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/list/' ) ); ?>" class="mf-btn mf-btn-ghost">
                        <span class="material-symbols-outlined" aria-hidden="true">sort_by_alpha</span>
                        <?php esc_html_e( 'A-Z Library', 'morimoflix-flicker' ); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="mf-body">
            <!-- ============ ABOUT ============ -->
            <section class="mf-sec" aria-labelledby="mf-about-h">
                <p class="mf-eyebrow"><?php esc_html_e( 'About the series', 'morimoflix-flicker' ); ?></p>
                <div class="mf-card">
                    <h2 class="mf-h2" id="mf-about-h">
                        <?php esc_html_e( 'About', 'morimoflix-flicker' ); ?> <?php the_title(); ?> <span class="accent">(<?php echo esc_html( $series['year'] ); ?>)</span>
                    </h2>
                    <div class="mf-prose">
                        <?php if ( '' === $mf_excerpt || morimoflix_texts_are_duplicate( $mf_excerpt, $series['tagline'] ) ) : ?>
                            <?php the_content(); ?>
                            <?php echo morimoflix_movie_thin_content( get_the_ID() ); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <?php if ( ! empty( $cast_names ) ) : ?>
            <!-- ============ CAST ============ -->
            <section class="mf-sec" aria-labelledby="mf-cast-h">
                <p class="mf-eyebrow"><?php esc_html_e( 'Top billed cast', 'morimoflix-flicker' ); ?></p>
                <h2 class="mf-h2" id="mf-cast-h"><?php esc_html_e( 'Cast & Crew', 'morimoflix-flicker' ); ?></h2>
                <div class="mf-cast">
                    <?php foreach ( $cast_names as $cname ) :
                        $curl = morimoflix_get_actor_link( $cname );
                    ?>
                        <?php if ( '#' !== $curl ) : ?>
                        <a href="<?php echo esc_url( $curl ); ?>"><?php echo esc_html( $cname ); ?></a>
                        <?php else : ?>
                        <span><?php echo esc_html( $cname ); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- ============ SERIES DOWNLOAD ============ -->
            <section id="series-download" class="mf-sec scroll-mt-24" aria-labelledby="mf-dl-h">
                <p class="mf-eyebrow"><?php esc_html_e( 'Get the show', 'morimoflix-flicker' ); ?></p>
                <h2 class="mf-h2" id="mf-dl-h"><?php esc_html_e( 'Download Links', 'morimoflix-flicker' ); ?></h2>

                <?php if ( $has_series_dl ) : ?>
                <p class="sv-note"><?php esc_html_e( 'Tap a section to open it. Downloads open in a new tab.', 'morimoflix-flicker' ); ?></p>

                <noscript><style>
                    /* Collapsed panels are `hidden`; without JS the toggle cannot run, so
                       reveal everything rather than hiding downloads behind a dead control. */
                    .sv-season-body[hidden]{display:block!important;max-height:none!important;}
                </style></noscript>

                <div class="sv-list" id="sv-list">
                    <?php
                    // Progressively enhanced disclosure: without JS the panels stay
                    // `hidden`, which would make every download unreachable. The <noscript>
                    // below force-reveals them so the links are always reachable.
                    // Only ONE season and ONE quality open at a time (matches the reference),
                    // while codec panels inside a quality stay independently toggleable.
                    $first_season = true;
                    foreach ( $series_links as $skey => $season ) :
                        $s_num    = (int) $season['number'];
                        $s_label  = $s_num > 0
                            ? sprintf( __( 'Season %d', 'morimoflix-flicker' ), $s_num )
                            : __( 'Zip/Pack Files', 'morimoflix-flicker' );
                        $is_open  = $first_season;
                        $first_season = false;

                        // Packs and episodes are grouped per season so the two never mix.
                        $blocks = array();
                        if ( ! empty( $season['packs'] ) ) {
                            $blocks['packs'] = array(
                                'label' => __( 'Packs/Zips', 'morimoflix-flicker' ),
                                'items' => $season['packs'],
                                'open'  => false,
                                'pid'   => 'sv-pack-' . $skey,
                            );
                        }
                        if ( ! empty( $season['episodes'] ) ) {
                            $blocks['episodes'] = array(
                                'label' => __( 'Episodes', 'morimoflix-flicker' ),
                                'items' => $season['episodes'],
                                'open'  => true,
                                'pid'   => 'sv-ep-' . $skey,
                            );
                        }
                    ?>
                    <div class="sv-season<?php echo $is_open ? ' is-open' : ''; ?>">
                        <button type="button" class="sv-ctrl" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( implode( ' ', array_column( $blocks, 'pid' ) ) ); ?>" data-sv-toggle="season">
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                            <span><?php echo esc_html( $s_label ); ?></span>
                            <span class="sv-count"><?php echo esc_html( count( $season['packs'] ) + count( $season['episodes'] ) ); ?></span>
                        </button>

                        <?php foreach ( $blocks as $b_key => $block ) :
                            $is_pack_block = ( 'packs' === $b_key );
                            // Both Packs and Episodes nest by quality then codec. The
                            // Franited Links importer builds labels with no season or
                            // episode token (e.g. "2160P WEB-DL 10-BIT HEVC"), so grouping
                            // packs the same way is what keeps the three-level tree useful
                            // instead of collapsing into one flat list.
                            $by_quality = array();
                            foreach ( $block['items'] as $ep ) {
                                $q = '' !== $ep['quality'] ? $ep['quality'] : __( 'Other', 'morimoflix-flicker' );
                                $by_quality[ $q ][] = $ep;
                            }
                            // 2160 -> 1080 -> 720 -> 480 -> others, then alphabetical.
                            uksort( $by_quality, function ( $a, $b ) {
                                $order = array( '2160p' => 1, '1080p' => 2, '720p' => 3, '480p' => 4 );
                                $oa = isset( $order[ $a ] ) ? $order[ $a ] : 9;
                                $ob = isset( $order[ $b ] ) ? $order[ $b ] : 9;
                                if ( $oa === $ob ) return strcmp( $a, $b );
                                return $oa < $ob ? -1 : 1;
                            } );
                            $q_open = ( 1 === count( $by_quality ) );
                        ?>
                        <div class="sv-season-body<?php echo $block['open'] ? ' is-open' : ''; ?>" id="<?php echo esc_attr( $block['pid'] ); ?>"<?php echo $block['open'] ? '' : ' hidden'; ?>>
                            <?php foreach ( $by_quality as $q_label => $q_items ) :
                                $q_id = 'sv-q-' . $b_key . '-' . $skey . '-' . sanitize_html_class( $q_label );
                                $by_codec = array();
                                foreach ( $q_items as $ep ) {
                                    $c = '' !== $ep['codec'] ? $ep['codec'] : __( 'Other', 'morimoflix-flicker' );
                                    $by_codec[ $c ][] = $ep;
                                }
                                // H.264 before H.265 (smaller files first), then the rest.
                                uksort( $by_codec, function ( $a, $b ) {
                                    $order = array( 'H.264' => 1, 'AV1' => 2, 'H.265' => 3, 'SDR' => 4, 'HDR' => 5 );
                                    $oa = isset( $order[ $a ] ) ? $order[ $a ] : 9;
                                    $ob = isset( $order[ $b ] ) ? $order[ $b ] : 9;
                                    if ( $oa === $ob ) return strcmp( $a, $b );
                                    return $oa < $ob ? -1 : 1;
                                } );
                                $this_q_open = $q_open;
                                $q_open = false;
                            ?>
                            <button type="button" class="sv-ctrl" aria-expanded="<?php echo $this_q_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $q_id ); ?>" data-sv-toggle="quality" data-q="<?php echo esc_attr( strtolower( $q_label ) ); ?>">
                                <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                                <span><?php echo esc_html( $q_label ); ?></span>
                                <span class="sv-count"><?php echo esc_html( count( $q_items ) ); ?></span>
                            </button>
                            <div class="sv-season-body sv-quality-body<?php echo $this_q_open ? ' is-open' : ''; ?>" id="<?php echo esc_attr( $q_id ); ?>"<?php echo $this_q_open ? '' : ' hidden'; ?>>
                                <?php foreach ( $by_codec as $c_label => $c_items ) :
                                    $c_id = 'sv-c-' . $b_key . '-' . $skey . '-' . sanitize_html_class( $q_label ) . '-' . sanitize_html_class( $c_label );
                                    $this_c_open = ( 1 === count( $by_codec ) );
                                ?>
                                <button type="button" class="sv-ctrl" aria-expanded="<?php echo $this_c_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $c_id ); ?>" data-sv-toggle="codec" data-c="<?php echo esc_attr( strtolower( $c_label ) ); ?>" data-q="<?php echo esc_attr( strtolower( $q_label ) ); ?>">
                                    <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                                    <span><?php echo esc_html( $q_label . ' ' . $c_label ); ?></span>
                                    <span class="sv-count"><?php echo esc_html( count( $c_items ) ); ?></span>
                                </button>
                                <div class="sv-season-body sv-codec-body<?php echo $this_c_open ? ' is-open' : ''; ?>" id="<?php echo esc_attr( $c_id ); ?>"<?php echo $this_c_open ? '' : ' hidden'; ?>>
                                    <?php if ( $is_pack_block ) : ?>
                                    <div class="sv-pack">
                                        <?php foreach ( $c_items as $pack ) : ?>
                                        <div class="sv-pack-card">
                                            <p class="sv-pack-title"><?php echo esc_html( $pack['label'] ); ?></p>
                                            <a class="sv-dl" href="<?php echo esc_url( $pack['url'] ); ?>" target="_blank" rel="noopener noreferrer nofollow sponsored">
                                                <span class="material-symbols-outlined" aria-hidden="true">download</span>
                                                <?php echo esc_html( $pack['size'] ? $pack['size'] : __( 'Download', 'morimoflix-flicker' ) ); ?>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else : ?>
                                    <div class="sv-ep-grid">
                                        <?php foreach ( $c_items as $ep ) : ?>
                                        <div class="sv-ep">
                                            <div class="sv-ep-l">
                                                <?php if ( $ep['episode'] > 0 ) : ?>
                                                <span class="sv-ep-n">E<?php echo esc_html( $ep['episode'] ); ?></span>
                                                <?php else : ?>
                                                <span class="sv-ep-n">&#183;</span>
                                                <?php endif; ?>
                                                <span class="sv-ep-title"><?php echo esc_html( $ep['label'] ); ?></span>
                                            </div>
                                            <a class="sv-dl" href="<?php echo esc_url( $ep['url'] ); ?>" target="_blank" rel="noopener noreferrer nofollow sponsored">
                                                <span class="material-symbols-outlined" aria-hidden="true">download</span>
                                                <span class="sv-size"><?php echo esc_html( $ep['size'] ? $ep['size'] : __( 'Download', 'morimoflix-flicker' ) ); ?></span>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php else : ?>
                <div class="mf-card">
                    <p class="mf-prose" style="margin:0;"><?php esc_html_e( 'No download links available yet.', 'morimoflix-flicker' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $series['networks'] ) ) : ?>
                <details class="sv-tags mf-card" style="margin-top:22px;">
                    <summary><?php esc_html_e( 'Where to watch', 'morimoflix-flicker' ); ?></summary>
                    <p><?php
                        /* translators: %s: network names. */
                        printf( esc_html__( 'Airs on %s. Check the listings above for the latest episodes and use the request form for anything missing.', 'morimoflix-flicker' ), esc_html( implode( ', ', $series['networks'] ) ) );
                    ?></p>
                    <?php if ( $series['status'] && $series['last_air'] ) : ?>
                    <p><?php
                        /* translators: %s: status label. */
                        printf( esc_html__( 'Status: %s. Last aired %s.', 'morimoflix-flicker' ), esc_html( $series['status'] ), esc_html( $series['last_air'] ) );
                    ?></p>
                    <?php endif; ?>
                </details>
                <?php endif; ?>
            </section>

            <!-- ============ RELATED ============ -->
            <section class="mf-sec" aria-labelledby="mf-rel-h">
                <p class="mf-eyebrow"><?php esc_html_e( 'Watch next', 'morimoflix-flicker' ); ?></p>
                <h2 class="mf-h2" id="mf-rel-h"><?php echo $first_genre ? esc_html( sprintf( __( 'More %s shows', 'morimoflix-flicker' ), $first_genre->name ) ) : esc_html__( 'You also may like', 'morimoflix-flicker' ); ?></h2>
                <div class="mf-rel-grid">
                    <?php
                    // SAFE perf: no pagination -> no_found_rows; publish + ignore_sticky.
                    $rel_args = array(
                        'post_type'           => 'tv',
                        'posts_per_page'      => 4,
                        'post__not_in'        => array( $post_id ),
                        'orderby'             => 'rand',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => true,
                        'no_found_rows'       => true,
                    );
                    if ( $first_genre ) {
                        $rel_args['tax_query'] = array(
                            array(
                                'taxonomy' => $first_genre->taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $first_genre->term_id,
                            ),
                        );
                    }
                    $related = new WP_Query( $rel_args );
                    if ( $related->have_posts() ) :
                        while ( $related->have_posts() ) :
                            $related->the_post();
                            $rmeta = morimoflix_get_series_meta();
                    ?>
                    <a class="mf-rel-card" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                        <div class="mf-rel-img">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                            <?php else : ?>
                                <div class="w-full h-full" style="background:linear-gradient(135deg,#1B1B21,#0B0B0F);"></div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mf-rel-t"><?php echo esc_html( get_the_title() ); ?></h3>
                        <p class="mf-rel-m"><?php printf( esc_html__( '%s IMDb', 'morimoflix-flicker' ), esc_html( $rmeta['rating'] ) ); ?><?php echo $rmeta['seasons'] > 0 ? ' · ' . esc_html( sprintf( _n( '%s season', '%s seasons', $rmeta['seasons'], 'morimoflix-flicker' ), $rmeta['seasons'] ) ) : ''; ?></p>
                    </a>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </section>
        </div>
    </main>

    <script>
    /**
     * Three-level disclosure for the series download tree.
     *   data-sv-toggle="season" / "quality" — accordion (one open per parent)
     *   data-sv-toggle="codec"              — independent toggles
     *
     * aria-controls holds a space-separated IDREF list because a season owns two panels
     * (Packs/Zips and Episodes) and every level is driven off the same three helpers.
     * Panels animate with max-height, whose px value is measured from scrollHeight since
     * the content height is unknown until rendered; `hidden` keeps a collapsed panel out
     * of the a11y tree and out of the tab order.
     */
    (function () {
        'use strict';

        var COLLAPSE_MS = 400;

        // Every panel this control owns. aria-controls is an IDREF list, so a season
        // legitimately returns two elements.
        function panelsOf(btn) {
            var raw = btn.getAttribute('aria-controls') || '';
            var out = [];
            raw.split(/\s+/).forEach(function (id) {
                if (!id) return;
                var el = document.getElementById(id);
                if (el) out.push(el);
            });
            return out;
        }

        // The element that holds the sibling controls for this level.
        // Seasons live side by side inside #sv-list, so that is the scope; a quality's
        // scope is the episodes block it sits in (which contains only its own season's
        // quality rows, so a Packs sibling is never reached).
        function scopeOf(btn) {
            var kind = btn.getAttribute('data-sv-toggle');
            if (kind === 'season') return document.getElementById('sv-list');
            if (kind === 'quality') {
                var panels = panelsOf(btn);
                return panels.length ? panels[0].parentNode : null;
            }
            return null; // codec panels stay independently toggleable
        }

        function panelHeight(panels) {
            // Panels are siblings stacked vertically, so the group height is their sum.
            var total = 0;
            for (var i = 0; i < panels.length; i++) total += panels[i].scrollHeight;
            return total;
        }

        function setOpen(btn, open, animate) {
            var panels = panelsOf(btn);
            if (!panels.length) return;
            var kind = btn.getAttribute('data-sv-toggle');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');

            // Only the season button owns .sv-season.is-open. A quality or codec toggle
            // must not clear the border accent of a season that is still expanded.
            if (kind === 'season') {
                var season = btn.closest ? btn.closest('.sv-season') : null;
                if (season) season.classList.toggle('is-open', open);
            }

            if (open) {
                for (var i = 0; i < panels.length; i++) {
                    panels[i].hidden = false;
                    panels[i].classList.add('is-open');
                }
                if (animate) {
                    for (var k = 0; k < panels.length; k++) panels[k].style.maxHeight = '0px';
                    window.requestAnimationFrame(function () {
                        for (var j = 0; j < panels.length; j++) {
                            panels[j].style.maxHeight = panelHeight(panels) + 'px';
                        }
                    });
                } else {
                    for (var m2 = 0; m2 < panels.length; m2++) panels[m2].style.maxHeight = '';
                }
                // Release the pinned px value once the transition is done. Without this the
                // panel would stay clipped at its click-time height, so a later reflow
                // (font load, rotation, resize) could hide downloads with no scrollbar.
                window.setTimeout(function () {
                    if (btn.getAttribute('aria-expanded') !== 'true') return;
                    for (var n = 0; n < panels.length; n++) panels[n].style.maxHeight = '';
                }, COLLAPSE_MS + 80);
                return;
            }

            var h = panelHeight(panels);
            for (var k2 = 0; k2 < panels.length; k2++) {
                if (animate) {
                    panels[k2].style.maxHeight = h + 'px';
                    void panels[k2].offsetHeight; // force reflow so the transition has a start
                    panels[k2].style.maxHeight = '0px';
                } else {
                    panels[k2].style.maxHeight = '0px';
                }
                panels[k2].classList.remove('is-open');
            }
            // Keep them rendered while they shrink, then drop them from the a11y tree.
            window.setTimeout(function () {
                if (btn.getAttribute('aria-expanded') === 'false') {
                    for (var m = 0; m < panels.length; m++) panels[m].hidden = true;
                }
            }, COLLAPSE_MS);
        }

        function closeSiblings(scope, btn, animate) {
            if (!scope) return;
            var kind = btn.getAttribute('data-sv-toggle');
            var btns = scope.querySelectorAll('[data-sv-toggle="' + kind + '"]');
            for (var i = 0; i < btns.length; i++) {
                if (btns[i] === btn) continue;
                if (btns[i].getAttribute('aria-expanded') === 'false') continue;
                setOpen(btns[i], false, animate);
            }
        }

        function eachDescendant(btn, fn) {
            var panels = panelsOf(btn);
            for (var i = 0; i < panels.length; i++) {
                var nested = panels[i].querySelectorAll('[data-sv-toggle]');
                for (var j = 0; j < nested.length; j++) fn(nested[j]);
            }
        }

        function openFirstChild(btn) {
            var panels = panelsOf(btn);
            for (var i = 0; i < panels.length; i++) {
                var first = panels[i].querySelector('[data-sv-toggle][aria-expanded="false"]');
                if (first) {
                    setOpen(first, true, true);
                    return;
                }
            }
        }

        function primePanels(root) {
            // Panels the server rendered open must start un-clipped once CSS is live.
            var open = root.querySelectorAll('.sv-season-body.is-open');
            for (var i = 0; i < open.length; i++) {
                open[i].hidden = false;
                open[i].style.maxHeight = '';
            }
            var closed = root.querySelectorAll('.sv-season-body[hidden]');
            for (var j = 0; j < closed.length; j++) closed[j].style.maxHeight = '0px';
        }

        function init() {
            var root = document.getElementById('sv-list');
            if (!root) return;
            primePanels(root);

            var ctrls = root.querySelectorAll('[data-sv-toggle]');
            for (var i = 0; i < ctrls.length; i++) {
                (function (btn) {
                    btn.addEventListener('click', function () {
                        var isOpen = btn.getAttribute('aria-expanded') === 'true';
                        var kind = btn.getAttribute('data-sv-toggle');

                        if (kind === 'season' || kind === 'quality') {
                            closeSiblings(scopeOf(btn), btn, true);
                        }
                        setOpen(btn, !isOpen, true);

                        if (isOpen) {
                            // Collapsing a parent collapses everything nested inside it.
                            eachDescendant(btn, function (child) {
                                if (child.getAttribute('aria-expanded') === 'true') {
                                    setOpen(child, false, true);
                                }
                            });
                        } else if (kind === 'season') {
                            openFirstChild(btn);
                        }
                    });
                }(ctrls[i]));
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    <script>
function morimoflixSynToggle(btn) {
    var box = btn.closest('.mf-synopsis');
    if (!box) return;
    var open = box.classList.toggle('mf-syn-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    var label = btn.querySelector('.mf-syn-toggle-label');
    if (label) {
        label.textContent = open ? 'Show Less' : 'Show More';
    }
}
</script>
<?php
    endwhile;
endif;

get_footer();
