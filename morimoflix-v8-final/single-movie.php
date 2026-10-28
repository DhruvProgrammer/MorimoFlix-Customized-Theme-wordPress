<?php
/**
 * Single Movie Template — premium cinematic detail page
 *
 * Presentation-only redesign. All dynamic data, URLs, download/trailer
 * logic, SEO hooks and WordPress integration are preserved.
 *
 * @package MorimoFlix
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $meta           = morimoflix_get_movie_meta();
        $genres         = get_the_terms( get_the_ID(), 'genre' );
        $actors         = get_the_terms( get_the_ID(), 'actor' );
        $download_links = get_post_meta( get_the_ID(), '_download_links', true );
        $backdrop       = morimoflix_get_backdrop( get_the_ID() );
        $has_trailer    = ! empty( $meta['trailer'] );
        $first_genre    = ( $genres && ! is_wp_error( $genres ) && isset( $genres[0] ) ) ? $genres[0] : null;

        // Cast: prefer actor taxonomy, fall back to TMDB cast meta.
        $cast_names = array();
        if ( $actors && ! is_wp_error( $actors ) ) {
            foreach ( array_slice( $actors, 0, 8 ) as $a ) {
                $cast_names[] = $a->name;
            }
        } elseif ( ! empty( $meta['cast'] ) && is_array( $meta['cast'] ) ) {
            foreach ( array_slice( $meta['cast'], 0, 8 ) as $c ) {
                $n = is_array( $c ) ? ( $c['name'] ?? '' ) : $c;
                if ( '' !== trim( (string) $n ) ) {
                    $cast_names[] = $n;
                }
            }
        }
?>
<style>
.mf-detail{background:#0B0B0F;overflow-x:hidden;max-width:100vw;}
.mf-detail *,.mf-detail *::before,.mf-detail *::after{box-sizing:border-box;}
html,body{overflow-x:hidden!important;max-width:100%;}
.mf-detail img{max-width:100%;}
.mf-hero,.mf-hero-inner,.mf-body,.mf-card,.mf-rel-grid,.mf-meta{box-sizing:border-box;max-width:100%;}
/* ---------- HERO ---------- */
.mf-hero{position:relative;overflow-x:hidden;overflow-y:visible;background:#0B0B0F;isolation:isolate;width:100%;max-width:100vw;box-sizing:border-box;}
.mf-hero-bg{position:absolute;inset:0;z-index:0;overflow:hidden;max-width:100%;}
.mf-hero-bg img{width:100%;height:100%;object-fit:cover;object-position:center 22%;opacity:.82;display:block;max-width:100%;}
/* Layer 1: dark LEFT gradient for readability */
.mf-hero-shade-left{position:absolute;inset:0;z-index:1;
background:linear-gradient(90deg,rgba(11,11,15,.94) 0%,rgba(11,11,15,.82) 32%,rgba(11,11,15,.45) 58%,rgba(11,11,15,.12) 82%,rgba(11,11,15,.05) 100%);}
/* Layer 2: subtle BOTTOM fade into page */
.mf-hero-shade-bottom{position:absolute;inset:0;z-index:1;
background:linear-gradient(to bottom,rgba(11,11,15,.35) 0%,rgba(11,11,15,0) 34%,rgba(11,11,15,.55) 72%,#0B0B0F 100%);}
/* Layer 3: very subtle vignette */
.mf-hero-vignette{position:absolute;inset:0;z-index:1;pointer-events:none;
box-shadow:inset 0 0 80px rgba(0,0,0,.45);max-width:100%;box-sizing:border-box;}
.mf-hero-inner{position:relative;z-index:2;display:flex;flex-direction:column;justify-content:flex-end;
min-height:88svh;max-width:1200px;width:100%;margin:0 auto;padding:120px 20px 44px;box-sizing:border-box;min-width:0;}
/* ---------- TITLE ---------- */
.mf-kicker{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;}
.mf-pill{display:inline-flex;align-items:center;padding:5px 13px;border-radius:9999px;
font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;
color:#E8D9D9;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14);text-decoration:none;
backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
a.mf-pill:hover{border-color:rgba(229,9,20,.55);color:#FFB4AB;}
/* ---------- BREADCRUMB (visible trail mirrors BreadcrumbList JSON-LD) ---------- */
.mf-crumb{margin:0 0 12px;min-width:0;max-width:100%;}
.mf-crumb ol{display:flex;flex-wrap:wrap;align-items:center;gap:4px 8px;list-style:none;margin:0;padding:0;min-width:0;max-width:100%;}
.mf-crumb li{display:inline-flex;align-items:center;gap:8px;min-width:0;max-width:100%;}
.mf-crumb a{display:inline-flex;align-items:center;min-height:26px;color:#B9B3B4;
font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;}
.mf-crumb a:hover{color:#FFB4AB;}
.mf-crumb .sep{color:#777376;font-size:11px;flex-shrink:0;}
.mf-crumb [aria-current="page"]{display:inline-flex;align-items:center;min-height:26px;color:#EDE6E7;
font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.14em;text-transform:uppercase;
white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:38ch;min-width:0;}
.mf-h1{margin:0;max-width:16ch;overflow-wrap:break-word;word-break:break-word;text-wrap:balance;min-width:0;
font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:-.025em;line-height:1.02;
font-size:clamp(2.6rem,6vw,4.8rem);color:#F7F4F4;text-shadow:0 2px 30px rgba(0,0,0,.6);}
.mf-h1-year{display:block;margin-top:10px;font-size:clamp(1rem,2.2vw,1.35rem);font-weight:700;
letter-spacing:.08em;color:#E50914;}
.mf-tagline{margin:12px 0 0;font-size:15px;font-style:italic;color:#C9B8B8;}
.mf-desc{margin:14px 0 0;max-width:38rem;font-size:15.5px;line-height:1.75;color:#CFC9CB;font-weight:400;}
/* ---------- META ---------- */
.mf-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:26px;max-width:640px;width:100%;box-sizing:border-box;min-width:0;}
.mf-meta-item{display:flex;align-items:center;gap:12px;min-height:76px;padding:12px 16px;border-radius:12px;
background:rgba(255,255,255,.045);border:1px solid rgba(255,255,255,.1);
box-shadow:0 4px 16px rgba(0,0,0,.3);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
min-width:0;max-width:100%;overflow:hidden;box-sizing:border-box;transition:transform .2s ease,border-color .2s ease;}
.mf-meta-item:hover{transform:translateY(-2px);border-color:rgba(255,255,255,.2);}
.mf-meta-item .material-symbols-outlined{font-size:24px;flex-shrink:0;color:#E8A33D;}
.mf-meta-item.plain .material-symbols-outlined{color:#D8D5D6;}
.mf-meta-v{font-family:'Montserrat',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1.25;
white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mf-meta-l{font-family:'JetBrains Mono',monospace;font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;color:#8E8A8E;}
.mf-meta-lang{grid-column:span 2;}
/* ---------- CTA ---------- */
.mf-cta-row{display:flex;flex-direction:column;gap:10px;margin-top:24px;max-width:640px;width:100%;box-sizing:border-box;min-width:0;}
.mf-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:52px;
padding:14px 26px;border-radius:12px;font-family:'Montserrat',sans-serif;font-weight:800;
font-size:13px;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;
transition:transform .2s ease,box-shadow .2s ease,background .2s ease,border-color .2s ease;}
.mf-btn:active{transform:scale(.98);}
.mf-btn-primary{color:#fff;background:linear-gradient(135deg,#E8111C 0%,#A50D14 100%);
border:1px solid rgba(255,255,255,.14);box-shadow:0 8px 24px rgba(229,9,20,.35);}
.mf-btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(229,9,20,.45);filter:brightness(1.06);}
.mf-btn-ghost{color:#EDE8E9;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.16);
backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);}
.mf-btn-ghost:hover{border-color:rgba(255,255,255,.32);background:rgba(255,255,255,.09);}
/* ---------- SECTIONS ---------- */
.mf-body{max-width:1200px;width:100%;margin:0 auto;padding:8px 20px 64px;box-sizing:border-box;overflow-x:hidden;min-width:0;}
.mf-sec{margin-top:44px;}
.mf-eyebrow{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:600;
letter-spacing:.3em;text-transform:uppercase;color:#8E8A8E;margin:0 0 10px;}
.mf-h2{margin:0 0 14px;font-family:'Montserrat',sans-serif;font-weight:800;letter-spacing:-.02em;
font-size:clamp(1.4rem,3vw,2rem);line-height:1.15;color:#F5F2F2;text-wrap:balance;}
.mf-h2 .accent{color:#E50914;}
.mf-card{background:rgba(255,255,255,.032);border:1px solid rgba(255,255,255,.09);border-radius:16px;
box-shadow:0 10px 32px rgba(0,0,0,.35);padding:22px;box-sizing:border-box;max-width:100%;min-width:0;overflow:hidden;}
.mf-prose{font-size:15px;line-height:1.8;color:#B9B3B4;max-width:46rem;}
.mf-prose p{margin:0 0 1em;}
.mf-prose p:last-child{margin-bottom:0;}
.mf-byline{margin:16px 0 0;font-size:12.5px;color:#777376;}
.mf-byline a{color:#D99A9C;}
.mf-byline a:hover{color:#FFB4AB;}
.mf-browse{display:inline-flex;align-items:center;gap:10px;margin-top:18px;padding:12px 18px;border-radius:12px;
background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.14);color:#EDE6E7;
font-size:14px;font-weight:500;text-decoration:none;transition:border-color .2s ease,background .2s ease;}
.mf-browse:hover{border-color:rgba(229,9,20,.55);background:rgba(229,9,20,.08);}
.mf-browse strong{color:#FFB4AB;font-weight:600;}
.mf-facts{margin-top:18px;border-radius:12px;border:1px solid rgba(255,255,255,.08);
background:rgba(0,0,0,.28);padding:16px 18px;font-size:14px;line-height:1.7;color:#A09B9C;}
.mf-facts h2,.mf-facts h3{color:#E8E2E3;font-size:15px;margin:0 0 8px;}
/* ---------- EXTERNAL REFERENCES (E-E-A-T outbound authority links) ---------- */
.mf-ext{margin-top:18px;border-radius:12px;border:1px solid rgba(255,255,255,.08);
background:rgba(0,0,0,.28);padding:16px 18px;min-width:0;max-width:100%;box-sizing:border-box;}
.mf-ext h3{color:#E8E2E3;font-size:15px;margin:0 0 10px;}
.mf-ext-links{display:flex;flex-wrap:wrap;gap:8px;min-width:0;}
.mf-ext-links a{display:inline-flex;align-items:center;gap:7px;min-height:26px;padding:8px 14px;border-radius:9999px;
background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:#DDD6D7;
font-size:13px;text-decoration:none;}
.mf-ext-links a:hover{border-color:rgba(229,9,20,.55);color:#FFB4AB;}
.mf-ext-note{margin:10px 0 0;font-size:12px;color:#777376;}
/* ---------- VISIBLE SEO DETAILS (collapsed, crawlable, no hidden text) ---------- */
.mf-seo{margin-top:18px;border-radius:12px;border:1px solid rgba(255,255,255,.08);
background:rgba(0,0,0,.28);padding:16px 18px;min-width:0;max-width:100%;box-sizing:border-box;}
.mf-seo summary{cursor:pointer;list-style:revert;display:flex;align-items:center;gap:10px;min-height:44px;
font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#DDD6D7;}
.mf-seo summary::marker{color:#E50914;}
.mf-seo summary::-webkit-details-marker{color:#E50914;}
.mf-seo summary:hover{color:#FFB4AB;}
.mf-seo summary:focus-visible{outline:2px solid rgba(229,9,20,.6);outline-offset:2px;border-radius:6px;}
.mf-seo p{margin:12px 0 0;font-size:13.5px;line-height:1.75;color:#A09B9C;}
.mf-seo p:last-child{margin-bottom:0;}
.mf-cast{display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;}
.mf-cast a,.mf-cast span{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:9999px;
background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:#DDD6D7;
font-size:13px;text-decoration:none;transition:border-color .2s ease;}
.mf-cast a:hover{border-color:rgba(229,9,20,.55);color:#FFB4AB;}
.mf-dl-grid{display:grid;gap:14px;}
.mf-select{position:relative;}
.mf-select select{width:100%;appearance:none;background:#17171C;border:1px solid rgba(255,255,255,.14);
color:#fff;font-size:15px;padding:15px 48px 15px 16px;border-radius:12px;cursor:pointer;}
.mf-select select:focus{border-color:#E50914;outline:none;}
#download-section .mf-select select option{background:#1D0E0E;color:#EDE6E7;}
#download-section .mf-select select option:checked,#download-section .mf-select select option:selected{background:#2A1A1A;color:#fff;}
.mf-select .material-symbols-outlined{position:absolute;right:14px;top:50%;transform:translateY(-50%);
pointer-events:none;color:#8E8A8E;}
.mf-dl-label{display:block;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.2em;
text-transform:uppercase;font-weight:600;color:#8E8A8E;margin-bottom:10px;}
.mf-rel-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;width:100%;max-width:100%;box-sizing:border-box;min-width:0;}
.mf-rel-card{display:block;text-decoration:none;min-width:0;max-width:100%;box-sizing:border-box;overflow:hidden;}
.mf-rel-img{overflow:hidden;border-radius:12px;border:1px solid rgba(255,255,255,.09);
background:#17171C;aspect-ratio:2/3;transition:border-color .25s ease,transform .25s ease;width:100%;max-width:100%;box-sizing:border-box;min-width:0;}
.mf-rel-card:hover .mf-rel-img{border-color:rgba(229,9,20,.5);transform:translateY(-3px);}
.mf-rel-img img{width:100%;height:100%;object-fit:cover;display:block;max-width:100%;}
.mf-rel-t{margin:10px 0 0;font-size:13px;font-weight:600;color:#D8D2D3;text-transform:uppercase;
letter-spacing:.04em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;box-sizing:border-box;min-width:0;}
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
    .mf-meta-lang{grid-column:span 4;}
    .mf-hero-inner{padding:150px 32px 56px;}
    .mf-body{padding:8px 32px 80px;}
    .mf-card{padding:30px 32px;}
    .mf-dl-grid{grid-template-columns:1fr 1fr;align-items:end;}
}
@media (max-width: 639px) {
    .mf-hero{overflow:hidden;min-width:0;}
    .mf-hero-bg img{object-position:center 20%;}
    .mf-hero-shade-left{background:linear-gradient(to bottom,rgba(11,11,15,.55) 0%,rgba(11,11,15,.72) 45%,rgba(11,11,15,.9) 75%,#0B0B0F 100%);}
    .mf-hero-vignette{box-shadow:inset 0 0 60px rgba(0,0,0,.42);}
    /* Hero visible on 350px: clamp height, clear fixed nav (48px+12mt), avoid collapse */
    .mf-hero-inner{min-height:clamp(420px,68svh,560px);padding:84px 20px 32px;justify-content:flex-end;min-width:0;width:100%;}
    .mf-kicker{gap:6px;margin-bottom:10px;}
    .mf-h1{font-size:clamp(1.85rem,7vw,2.45rem);line-height:1.05;max-width:13.5ch;}
    .mf-h1-year{font-size:13px;margin-top:8px;}
    .mf-tagline{font-size:13.5px;max-width:100%;overflow-wrap:break-word;word-break:break-word;}
    .mf-desc{font-size:14px;line-height:1.6;max-width:100%;overflow-wrap:break-word;word-break:break-word;}
    /* 2-col grids must fit 350 viewport: 350-40 pad=310, gap14 => (310-14)/2=148 each — no overflow */
    .mf-meta{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:20px;width:100%;max-width:100%;}
    .mf-meta-item{padding:10px 12px;min-height:64px;gap:10px;min-width:0;}
    .mf-meta-v{font-size:13.5px;}
    .mf-meta-l{font-size:11px;}
    .mf-meta-lang{grid-column:span 2;}
    .mf-cta-row{gap:9px;margin-top:18px;width:100%;}
    .mf-btn{min-height:48px;padding:12px 18px;width:100%;box-sizing:border-box;min-width:0;}
    .mf-body{padding:8px 20px 48px;width:100%;max-width:100vw;box-sizing:border-box;overflow-x:hidden;min-width:0;}
    .mf-sec{margin-top:32px;min-width:0;max-width:100%;}
    .mf-card{padding:18px 16px;max-width:100%;min-width:0;}
    .mf-prose{font-size:14px;line-height:1.7;max-width:100%;overflow-wrap:break-word;word-break:break-word;min-width:0;}
    .mf-card,.mf-prose,.mf-facts,.mf-cast,.mf-dl-grid,.mf-select{min-width:0;max-width:100%;box-sizing:border-box;}
    .mf-rel-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;width:100%;max-width:100%;box-sizing:border-box;}
    .mf-rel-card{min-width:0;max-width:100%;overflow:hidden;}
    .mf-rel-img{width:100%;max-width:100%;}
    .mf-dl-grid{gap:12px;width:100%;}
    .mf-select select{max-width:100%;box-sizing:border-box;}
    .mf-select select option{font-size:13px;}
    .mf-browse{max-width:100%;flex-wrap:wrap;box-sizing:border-box;min-width:0;}
    .mf-crumb{margin-bottom:10px;max-width:100%;overflow:hidden;}
    .mf-crumb a,.mf-crumb [aria-current="page"]{font-size:11px!important;min-height:26px!important;}
    .mf-crumb [aria-current="page"]{max-width:52vw!important;}
    .mf-ext{padding:14px 16px;max-width:100%;overflow-wrap:break-word;}
    .mf-ext-links{gap:6px;}
    .mf-seo{padding:14px 16px;max-width:100%;overflow-wrap:break-word;min-width:0;box-sizing:border-box;}
    .mf-seo summary{font-size:12px;min-height:44px;}
    .mf-seo p{font-size:12px;line-height:1.7;overflow-wrap:break-word;word-break:break-word;}
    .mf-h2{font-size:clamp(1.25rem,5vw,1.5rem);}
    .mf-facts{padding:14px 16px;max-width:100%;overflow-wrap:break-word;}
}
/* Extra narrow: 350px viewport fallback — keeps 14px gap spec: 350-40=310 =>148 each still fits; 14px pad fallback for <375 gives 322-14=308/2=154 */
@media (max-width: 375px) {
    .mf-hero-inner{padding-left:14px;padding-right:14px;padding-top:78px;min-height:clamp(400px,66svh,520px);}
    .mf-body{padding-left:14px;padding-right:14px;}
    .mf-card{padding:16px 14px;}
    .mf-rel-grid{gap:12px;}
    .mf-meta{gap:8px;}
    .mf-meta-item{padding:9px 10px;min-height:60px;gap:8px;}
    .mf-h1{font-size:clamp(1.65rem,7.5vw,2.15rem);max-width:12.5ch;}
}
@media (max-width: 350px) {
    .mf-hero-inner{padding-left:14px;padding-right:14px;}
    .mf-body{padding-left:14px;padding-right:14px;}
    .mf-rel-grid{gap:10px;}
    .mf-rel-t{font-size:12px;}
    .mf-meta{gap:6px;}
}
@media (prefers-reduced-motion: reduce) {
    .mf-btn,.mf-meta-item,.mf-rel-img,.mf-browse,.mf-cast a,.mf-syn-toggle,.mf-syn-chev{transition:none;}
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
    <section class="mf-hero" aria-labelledby="mf-movie-title">
        <?php if ( $backdrop ) : ?>
        <div class="mf-hero-bg" aria-hidden="true">
            <img src="<?php echo esc_url( $backdrop ); ?>" alt="<?php echo esc_attr( get_the_title() . ' backdrop' ); ?>" loading="eager" fetchpriority="high" decoding="async">
        </div>
        <div class="mf-hero-shade-left" aria-hidden="true"></div>
        <div class="mf-hero-shade-bottom" aria-hidden="true"></div>
        <div class="mf-hero-vignette" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="mf-hero-inner">
            <?php
            $mf_ptype = get_post_type();
            $mf_archive = get_post_type_archive_link( $mf_ptype );
            $mf_archive_label = ( 'tv' === $mf_ptype ) ? __( 'TV Shows', 'morimoflix-flicker' ) : __( 'Movies', 'morimoflix-flicker' );
            ?>
            <nav class="mf-crumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'morimoflix-flicker' ); ?></a><span class="sep" aria-hidden="true">/</span></li>
                    <?php if ( $mf_archive ) : ?>
                    <li><a href="<?php echo esc_url( $mf_archive ); ?>"><?php echo esc_html( $mf_archive_label ); ?></a><span class="sep" aria-hidden="true">/</span></li>
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

            <h1 class="mf-h1" id="mf-movie-title"><?php the_title(); ?><span class="mf-h1-year"><?php echo esc_html( $meta['year'] ); ?></span></h1>

            <?php if ( ! empty( $meta['tagline'] ) ) : ?>
            <p class="mf-tagline"><?php echo esc_html( $meta['tagline'] ); ?></p>
            <?php endif; ?>

            <?php
            // Read the excerpt once: the predicate and the rendered text must agree, even if a
            // third party ever attaches a non-idempotent 'get_the_excerpt' filter.
            $mf_excerpt = has_excerpt() ? get_the_excerpt() : '';
            if ( '' !== $mf_excerpt && ! morimoflix_texts_are_duplicate( $mf_excerpt, $meta['tagline'] ) ) :
            ?>
            <div class="mf-synopsis" id="mfSynopsis">
                <p class="mf-desc mf-syn-short"><?php echo esc_html( $mf_excerpt ); ?></p>
                <div class="mf-syn-extra" id="mf-syn-extra">
                    <?php if ( ! empty( $backdrop ) ) : ?>
                    <img src="<?php echo esc_url( $backdrop ); ?>" alt="<?php echo esc_attr( get_the_title() . ' ' . $meta['year'] . ' movie' ); ?>" width="1280" height="720" loading="lazy" decoding="async" style="aspect-ratio:16/9;" />
                    <?php endif; ?>
                    <div class="mf-prose">
                        <?php the_content(); ?>
                        <?php echo morimoflix_movie_thin_content( get_the_ID() ); ?>
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
                <div class="mf-meta-item">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">star</span>
                    <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $meta['rating'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'IMDb', 'morimoflix-flicker' ); ?></div></div>
                </div>
                <div class="mf-meta-item">
                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                    <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $meta['duration'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Duration', 'morimoflix-flicker' ); ?></div></div>
                </div>
                <div class="mf-meta-item">
                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                    <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $meta['year'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Year', 'morimoflix-flicker' ); ?></div></div>
                </div>
                <div class="mf-meta-item plain">
                    <span class="material-symbols-outlined" aria-hidden="true">hd</span>
                    <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $meta['quality'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Quality', 'morimoflix-flicker' ); ?></div></div>
                </div>
                <?php if ( ! empty( $meta['language'] ) ) : ?>
                <div class="mf-meta-item plain mf-meta-lang">
                    <span class="material-symbols-outlined" aria-hidden="true">language</span>
                    <div class="min-w-0"><div class="mf-meta-v"><?php echo esc_html( $meta['language'] ); ?></div><div class="mf-meta-l"><?php esc_html_e( 'Language', 'morimoflix-flicker' ); ?></div></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="mf-cta-row">
                <a href="#download-section" class="mf-btn mf-btn-primary">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">download</span>
                    <?php esc_html_e( 'Download Movie', 'morimoflix-flicker' ); ?>
                </a>
                <?php if ( $has_trailer ) : ?>
                <a href="<?php echo esc_url( $meta['trailer'] ); ?>" target="_blank" rel="noopener noreferrer" class="mf-btn mf-btn-ghost">
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
            <p class="mf-eyebrow"><?php esc_html_e( 'About the movie', 'morimoflix-flicker' ); ?></p>
            <div class="mf-card">
                <h2 class="mf-h2" id="mf-about-h">
                    <?php esc_html_e( 'About', 'morimoflix-flicker' ); ?> <?php the_title(); ?> <span class="accent">(<?php echo esc_html( $meta['year'] ); ?>)</span>
                </h2>
                <div class="mf-prose">
                    <?php if ( '' === $mf_excerpt || morimoflix_texts_are_duplicate( $mf_excerpt, $meta['tagline'] ) ) : ?>
                        <?php the_content(); ?>
                        <?php echo morimoflix_movie_thin_content( get_the_ID() ); ?>
                    <?php endif; ?>
                </div>
                <?php if ( ! empty( $meta['posted_by'] ) || get_the_modified_date() ) : ?>
                <p class="mf-byline">
                    <?php if ( ! empty( $meta['posted_by'] ) ) : ?>
                        <?php esc_html_e( 'Posted by', 'morimoflix-flicker' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_post_field( 'post_author' ) ) ); ?>" rel="author"><?php echo esc_html( $meta['posted_by'] ); ?></a>
                    <?php endif; ?>
                    <?php if ( ! empty( $meta['posted_by'] ) && get_the_modified_date() ) : ?> · <?php endif; ?>
                    <?php esc_html_e( 'Updated', 'morimoflix-flicker' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time>
                </p>
                <?php endif; ?>
                <?php if ( $first_genre ) : ?>
                <div>
                    <a class="mf-browse" href="<?php echo esc_url( get_term_link( $first_genre ) ); ?>">
                        <span><?php esc_html_e( 'Browse more', 'morimoflix-flicker' ); ?> <strong><?php echo esc_html( $first_genre->name ); ?> <?php esc_html_e( 'movies', 'morimoflix-flicker' ); ?></strong></span>
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
                <?php endif; ?>
                <div class="mf-facts">
                    <h3><?php echo esc_html( sprintf( '%s (%s) — Quick Facts', get_the_title(), $meta['year'] ) ); ?></h3>
                    <?php $tg_autoseo_block = get_post_meta( get_the_ID(), '_tg_autoseo_block', true ); ?>
                    <?php if ( ! empty( $tg_autoseo_block ) ) : ?>
                        <?php echo wp_kses_post( $tg_autoseo_block ); ?>
                    <?php else : ?>
                        <?php $tg_qf_genre = $first_genre ? $first_genre->name : __( 'movie', 'morimoflix-flicker' ); ?>
                        <p style="margin:0;"><?php printf( esc_html__( '%1$s (%2$s) is a %3$s %4$s film presented in %5$s. It is rated %6$s IMDb with a runtime of %7$s.', 'morimoflix-flicker' ), esc_html( get_the_title() ), esc_html( $meta['year'] ), esc_html( $meta['language'] ), esc_html( $tg_qf_genre ), esc_html( $meta['quality'] ), esc_html( $meta['rating'] ), esc_html( $meta['duration'] ) ); ?></p>
                    <?php endif; ?>
                </div>
                <?php $mf_ref_q = rawurlencode( trim( get_the_title() . ' ' . $meta['year'] ) ); ?>
                <div class="mf-ext">
                    <h3><?php printf( esc_html__( 'Verify %s (%s) on official sources', 'morimoflix-flicker' ), esc_html( get_the_title() ), esc_html( $meta['year'] ) ); ?></h3>
                    <div class="mf-ext-links">
                        <a href="<?php echo esc_url( 'https://www.imdb.com/find?q=' . $mf_ref_q ); ?>" target="_blank" rel="noopener noreferrer">IMDb</a>
                        <a href="<?php echo esc_url( 'https://en.wikipedia.org/wiki/Special:Search?search=' . $mf_ref_q ); ?>" target="_blank" rel="noopener noreferrer">Wikipedia</a>
                        <a href="<?php echo esc_url( 'https://www.rottentomatoes.com/search?search=' . $mf_ref_q ); ?>" target="_blank" rel="noopener noreferrer">Rotten Tomatoes</a>
                        <a href="<?php echo esc_url( 'https://www.youtube.com/results?search_query=' . rawurlencode( trim( get_the_title() . ' ' . $meta['year'] . ' trailer' ) ) ); ?>" target="_blank" rel="noopener noreferrer">YouTube Trailer</a>
                    </div>
                    <p class="mf-ext-note"><?php esc_html_e( 'External references open in a new tab.', 'morimoflix-flicker' ); ?></p>
                </div>
                <details class="mf-seo">
                    <summary><?php printf( esc_html__( 'About %1$s (%2$s)', 'morimoflix-flicker' ), esc_html( get_the_title() ), esc_html( $meta['year'] ) ); ?></summary>
                    <?php $mf_seo_genre = $first_genre ? $first_genre->name : __( 'movie', 'morimoflix-flicker' ); ?>
                    <p><?php printf( esc_html__( '%1$s (%2$s) is a %3$s title presented in %4$s in %5$s, with cast and genre details listed above for quick reference. Fans of south movie Hindi dubbed versions and Telugu movies can compare Hindi dubbed movies in HD here, then use Where to Watch and OTT listings to check availability.', 'morimoflix-flicker' ), esc_html( get_the_title() ), esc_html( $meta['year'] ), esc_html( $mf_seo_genre ), esc_html( $meta['language'] ), esc_html( $meta['quality'] ) ); ?></p>
                </details>
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

        <!-- ============ DOWNLOAD ============ -->
        <section id="download-section" class="mf-sec scroll-mt-24" aria-labelledby="mf-dl-h">
            <p class="mf-eyebrow"><?php esc_html_e( 'Get the film', 'morimoflix-flicker' ); ?></p>
            <h2 class="mf-h2" id="mf-dl-h"><?php esc_html_e( 'Download Links', 'morimoflix-flicker' ); ?></h2>
            <div class="mf-card" style="max-width:46rem;">
                <?php if ( ! empty( $download_links ) ) :
                    $links = explode( "\n", $download_links );
                    $quality_groups = array();
                    foreach ( $links as $link ) :
                        $link = trim( $link );
                        if ( '' === $link ) continue;
                        $parts   = array_map( 'trim', explode( '|', $link ) );
                        $quality = $parts[0] ?? 'Download';
                        $url     = $parts[1] ?? '';
                        $size    = $parts[2] ?? '';
                        if ( empty( $url ) || ! preg_match( '#^https?://#i', $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) continue;
                        $quality_groups[] = array( 'quality' => $quality, 'url' => $url, 'size' => $size );
                    endforeach;
                    if ( ! empty( $quality_groups ) ) :
                ?>
                    <div class="mf-dl-grid">
                        <div>
                            <label class="mf-dl-label" for="quality-selector"><?php esc_html_e( 'Select Quality', 'morimoflix-flicker' ); ?></label>
                            <div class="mf-select">
                                <select id="quality-selector" onchange="document.getElementById('download-url').href=this.value">
                                    <?php foreach ( $quality_groups as $index => $item ) : ?>
                                        <option value="<?php echo esc_url( $item['url'] ); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                                            <?php echo esc_html( $item['quality'] ); ?><?php echo ! empty( $item['size'] ) ? ' — ' . esc_html( $item['size'] ) : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                            </div>
                        </div>
                        <a id="download-url" href="<?php echo esc_url( $quality_groups[0]['url'] ); ?>" target="_blank" rel="noopener noreferrer nofollow sponsored" class="mf-btn mf-btn-primary" style="width:100%;">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" aria-hidden="true">download</span>
                            <?php esc_html_e( 'Download Now', 'morimoflix-flicker' ); ?>
                        </a>
                    </div>
                    <?php else : ?>
                    <p class="mf-prose" style="margin:0;"><?php esc_html_e( 'No valid download links available yet.', 'morimoflix-flicker' ); ?></p>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="mf-prose" style="margin:0;"><?php esc_html_e( 'No valid download links available yet.', 'morimoflix-flicker' ); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <!-- ============ RELATED ============ -->
        <section class="mf-sec" aria-labelledby="mf-rel-h">
            <p class="mf-eyebrow"><?php esc_html_e( 'Keep watching', 'morimoflix-flicker' ); ?></p>
            <h2 class="mf-h2" id="mf-rel-h"><?php echo $first_genre ? esc_html( sprintf( __( 'More from %s', 'morimoflix-flicker' ), $first_genre->name ) ) : esc_html__( 'You also may like', 'morimoflix-flicker' ); ?></h2>
            <div class="mf-rel-grid">
                <?php
                // SAFE perf: no pagination → no_found_rows; publish + ignore_sticky for correctness.
                // NOTE scale risk: ORDER BY RAND() scans; kept (do not replace logic per SAFE scope).
                $rel_args = array(
                    'post_type'           => get_post_type(),
                    'posts_per_page'      => 4,
                    'post__not_in'        => array( get_the_ID() ),
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
                        $rmeta = morimoflix_get_movie_meta();
                ?>
                <a class="mf-rel-card group" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( morimoflix_card_label() ); ?>">
                    <div class="mf-rel-img">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover', 'alt' => morimoflix_poster_alt(), 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
                        <?php else : ?>
                            <div class="w-full h-full" style="background:linear-gradient(135deg,#1B1B21,#0B0B0F);"></div>
                        <?php endif; ?>
                    </div>
                    <h3 class="mf-rel-t"><?php echo esc_html( get_the_title() ); ?></h3>
                    <p class="mf-rel-m"><?php printf( esc_html__( '%s IMDb', 'morimoflix-flicker' ), esc_html( $rmeta['rating'] ) ); ?> · <?php echo esc_html( $rmeta['year'] ); ?></p>
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
