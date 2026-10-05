<?php get_header(); ?>
<main class="pt-20">
    <div class="max-w-container-max mx-auto px-5 md:px-margin-desktop py-20 text-center">
        <span class="material-symbols-outlined text-6xl text-dim mb-6 block">error</span>
        <h1 class="font-headline-md text-headline-md text-white mb-4"><?php esc_html_e( 'Page not found', 'morimoflix-flicker' ); ?></h1>
        <p class="font-label-mono-sm text-on-surface-variant/60 tracking-widest uppercase mb-8"><?php esc_html_e( 'The page you requested may have moved or been removed.', 'morimoflix-flicker' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-gradient btn-primary inline-block px-8 py-3 text-xs tracking-[0.4em] font-bold uppercase text-white rounded-full"><?php esc_html_e( 'Return Home', 'morimoflix-flicker' ); ?></a>
    </div>
</main>
<?php get_footer(); ?>
