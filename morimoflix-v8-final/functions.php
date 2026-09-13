<?php
/**
 * MorimoFlix Theme Functions
 * 
 * @package MorimoFlix
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MORIMOFLIX_VERSION', '5.2.0' );
define( 'MORIMOFLIX_DIR', get_template_directory() );
define( 'MORIMOFLIX_URI', get_template_directory_uri() );

/**
 * TGM Plugin Activation — bundled plugins (TGMovies SEO + Rank Math prompt).
 * NOTE: Intentional exception to the flat-structure rule — required by the
 * TGMovies SEO theme-bundling spec (PROMPT-build-tgmovies-seo-plugin.md §5).
 */
$morimoflix_tgmpa = MORIMOFLIX_DIR . '/inc/tgm/class-tgm-plugin-activation.php';
if ( file_exists( $morimoflix_tgmpa ) ) {
    require_once $morimoflix_tgmpa;
    add_action( 'tgmpa_register', 'morimoflix_register_required_plugins' );
}
function morimoflix_register_required_plugins() {
    if ( ! function_exists( 'tgmpa' ) ) {
        return;
    }
    $plugins = array(
        array(
            'name'     => 'TGMovies SEO',
            'slug'     => 'tgmovies-seo',
            'source'   => get_template_directory() . '/inc/plugins/tgmovies-seo.zip',
            'required' => true,
            'version'  => '0.1.0',
        ),
        array(
            'name'     => 'Rank Math SEO',
            'slug'     => 'seo-by-rank-math',
            'required' => true,
        ),
    );
    $config = array(
        'id'           => 'morimoflix',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => false,
        'is_automatic' => true,
    );
    tgmpa( $plugins, $config );
}

/**
 * Theme Setup
 */
function morimoflix_setup() {
    load_theme_textdomain( 'morimoflix-flicker', MORIMOFLIX_DIR . '/languages' );
    
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_image_size( 'morimoflix-hero', 1920, 585, true );
    add_image_size( 'morimoflix-poster', 400, 600, true );
    add_image_size( 'morimoflix-poster-large', 800, 1200, true );
    add_image_size( 'morimoflix-thumbnail', 300, 170, true );

    register_nav_menus( array(
        'primary'   => __( 'Primary Menu', 'morimoflix-flicker' ),
        'mobile'    => __( 'Mobile Menu', 'morimoflix-flicker' ),
        'footer'    => __( 'Footer Menu', 'morimoflix-flicker' ),
    ) );
}
add_action( 'after_setup_theme', 'morimoflix_setup' );

/**
 * Register Movie Custom Post Type
 */
function morimoflix_register_post_types() {
    // Movie Post Type
    register_post_type( 'movie', array(
        'labels' => array(
            'name'               => __( 'Movies', 'morimoflix-flicker' ),
            'singular_name'      => __( 'Movie', 'morimoflix-flicker' ),
            'add_new_item'       => __( 'Add New Movie', 'morimoflix-flicker' ),
            'edit_item'          => __( 'Edit Movie', 'morimoflix-flicker' ),
            'new_item'           => __( 'New Movie', 'morimoflix-flicker' ),
            'view_item'          => __( 'View Movie', 'morimoflix-flicker' ),
            'search_items'       => __( 'Search Movies', 'morimoflix-flicker' ),
            'not_found'          => __( 'No movies found', 'morimoflix-flicker' ),
            'not_found_in_trash' => __( 'No movies found in trash', 'morimoflix-flicker' ),
            'menu_name'          => __( 'Movies', 'morimoflix-flicker' ),
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'movie' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-video-alt3',
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    ) );

    // TV Show Post Type
    register_post_type( 'tv', array(
        'labels' => array(
            'name'               => __( 'TV Shows', 'morimoflix-flicker' ),
            'singular_name'      => __( 'TV Show', 'morimoflix-flicker' ),
            'add_new_item'       => __( 'Add New TV Show', 'morimoflix-flicker' ),
            'edit_item'          => __( 'Edit TV Show', 'morimoflix-flicker' ),
            'new_item'           => __( 'New TV Show', 'morimoflix-flicker' ),
            'view_item'          => __( 'View TV Show', 'morimoflix-flicker' ),
            'search_items'       => __( 'Search TV Shows', 'morimoflix-flicker' ),
            'not_found'          => __( 'No TV shows found', 'morimoflix-flicker' ),
            'not_found_in_trash' => __( 'No TV shows found in trash', 'morimoflix-flicker' ),
            'menu_name'          => __( 'TV Shows', 'morimoflix-flicker' ),
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'tv' ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'          => 'dashicons-desktop',
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'hierarchical'       => false,
    ) );
}
add_action( 'init', 'morimoflix_register_post_types' );

/**
 * Register Taxonomies
 */
function morimoflix_register_taxonomies() {
    // Genre Taxonomy (for movies and TV)
    register_taxonomy( 'genre', array( 'movie', 'tv' ), array(
        'labels' => array(
            'name'              => __( 'Genres', 'morimoflix-flicker' ),
            'singular_name'     => __( 'Genre', 'morimoflix-flicker' ),
            'search_items'      => __( 'Search Genres', 'morimoflix-flicker' ),
            'all_items'         => __( 'All Genres', 'morimoflix-flicker' ),
            'parent_item'       => __( 'Parent Genre', 'morimoflix-flicker' ),
            'edit_item'         => __( 'Edit Genre', 'morimoflix-flicker' ),
            'add_new_item'      => __( 'Add New Genre', 'morimoflix-flicker' ),
            'new_item_name'     => __( 'New Genre Name', 'morimoflix-flicker' ),
            'menu_name'         => __( 'Genres', 'morimoflix-flicker' ),
        ),
        'hierarchical'      => true,
        'public'            => true,
        'rewrite'           => array( 'slug' => 'genre' ),
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );

    // Actor Taxonomy (for movies and TV)
    register_taxonomy( 'actor', array( 'movie', 'tv' ), array(
        'labels' => array(
            'name'              => __( 'Actors', 'morimoflix-flicker' ),
            'singular_name'     => __( 'Actor', 'morimoflix-flicker' ),
            'search_items'      => __( 'Search Actors', 'morimoflix-flicker' ),
            'all_items'         => __( 'All Actors', 'morimoflix-flicker' ),
            'edit_item'         => __( 'Edit Actor', 'morimoflix-flicker' ),
            'add_new_item'      => __( 'Add New Actor', 'morimoflix-flicker' ),
            'new_item_name'     => __( 'New Actor Name', 'morimoflix-flicker' ),
            'menu_name'         => __( 'Actors', 'morimoflix-flicker' ),
        ),
        'hierarchical'      => false,
        'public'            => true,
        'rewrite'           => array( 'slug' => 'actor' ),
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'morimoflix_register_taxonomies' );

/**
 * Flush rewrite rules on theme activation
 */
function morimoflix_rewrite_flush() {
    morimoflix_register_post_types();
    morimoflix_register_taxonomies();

    // Auto-create the List page if it doesn't exist
    $list_page = get_page_by_path('list');
    if (!$list_page) {
        wp_insert_post(array(
            'post_title'   => 'List',
            'post_name'    => 'list',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'page_template' => 'page-list.php',
        ));
    } else {
        // Update existing page to use the correct template
        update_post_meta($list_page->ID, '_wp_page_template', 'page-list.php');
    }

    // Set default TMDB API key
    if (!get_option('morimoflix_tmdb_api_key')) {
        update_option('morimoflix_tmdb_api_key', '');
    }

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'morimoflix_rewrite_flush');

/**
 * Enqueue Scripts and Styles
 */
function morimoflix_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'morimoflix-fonts',
        'https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Space+Mono:wght@400;700&family=Geist:wght@400;500;600&display=swap',
        array(),
        null
    );

    // Material Symbols
    wp_enqueue_style(
        'morimoflix-icons',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
        array(),
        null
    );

    // Main Stylesheet
    wp_enqueue_style(
        'morimoflix-style',
        get_stylesheet_uri(),
        array(),
        MORIMOFLIX_VERSION
    );

    // jQuery (required by movie-request modal script in header.php)
    wp_enqueue_script( 'jquery' );

    // Main Script
    wp_enqueue_script(
        'morimoflix-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        MORIMOFLIX_VERSION,
        true
    );

    wp_localize_script( 'morimoflix-main', 'morimoflixData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'morimoflix-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'morimoflix_enqueue_assets' );

/**
 * Add Movie Meta Box
 */
function morimoflix_add_meta_boxes() {
    $post_types = array( 'movie', 'tv' );
    
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'morimoflix_movie_meta',
            __( 'Movie Information', 'morimoflix-flicker' ),
            'morimoflix_movie_meta_callback',
            $post_type,
            'normal',
            'high'
        );
        
        add_meta_box(
            'morimoflix_download_links',
            __( 'Download Links', 'morimoflix-flicker' ),
            'morimoflix_download_meta_box_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'morimoflix_add_meta_boxes' );

/**
 * Movie Meta Box Callback
 */
function morimoflix_movie_meta_callback( $post ) {
    wp_nonce_field( 'morimoflix_movie_meta', 'morimoflix_movie_meta_nonce' );

    $rating    = get_post_meta( $post->ID, '_movie_rating', true );
    $duration  = get_post_meta( $post->ID, '_movie_duration', true );
    $quality   = get_post_meta( $post->ID, '_movie_quality', true ) ?: 'HD';
    $language  = get_post_meta( $post->ID, '_movie_language', true );
    $year      = get_post_meta( $post->ID, '_movie_year', true );
    $trailer   = get_post_meta( $post->ID, '_movie_trailer', true );
    $is_live   = get_post_meta( $post->ID, '_movie_live', true );
    $posted_by = get_post_meta( $post->ID, '_movie_posted_by', true );
    
    // Auto-capture admin name if empty
    if ( empty( $posted_by ) ) {
        $posted_by = get_the_author_meta( 'display_name', $post->post_author );
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="movie_posted_by"><?php _e( 'Posted By', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_posted_by" name="movie_posted_by" value="<?php echo esc_attr( $posted_by ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Admin name', 'morimoflix-flicker' ); ?>"></td>
        </tr>
        <tr>
            <th><label for="movie_rating"><?php _e( 'IMDb Rating', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_rating" name="movie_rating" value="<?php echo esc_attr( $rating ); ?>" class="regular-text" placeholder="8.0"></td>
        </tr>
        <tr>
            <th><label for="movie_duration"><?php _e( 'Duration', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_duration" name="movie_duration" value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="120M"></td>
        </tr>
        <tr>
            <th><label for="movie_quality"><?php _e( 'Quality', 'morimoflix-flicker' ); ?></label></th>
            <td>
                <select id="movie_quality" name="movie_quality">
                    <option value="HD" <?php selected( $quality, 'HD' ); ?>>HD</option>
                    <option value="4K" <?php selected( $quality, '4K' ); ?>>4K</option>
                    <option value="SD" <?php selected( $quality, 'SD' ); ?>>SD</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="movie_language"><?php _e( 'Language', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_language" name="movie_language" value="<?php echo esc_attr( $language ); ?>" class="regular-text" placeholder="English, Hindi"></td>
        </tr>
        <tr>
            <th><label for="movie_year"><?php _e( 'Year', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="text" id="movie_year" name="movie_year" value="<?php echo esc_attr( $year ); ?>" class="regular-text" placeholder="2024"></td>
        </tr>
        <tr>
            <th><label for="movie_trailer"><?php _e( 'Trailer URL', 'morimoflix-flicker' ); ?></label></th>
            <td><input type="url" id="movie_trailer" name="movie_trailer" value="<?php echo esc_attr( $trailer ); ?>" class="regular-text" placeholder="https://youtube.com/watch?v=..."></td>
        </tr>
        <tr>
            <th><label for="movie_live"><?php _e( 'Live Status', 'morimoflix-flicker' ); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="movie_live" name="movie_live" value="yes" <?php checked( $is_live, 'yes' ); ?>>
                    <?php _e( 'Mark as Live', 'morimoflix-flicker' ); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Download Links Meta Box Callback
 */
function morimoflix_download_meta_box_callback( $post ) {
    $download_links = get_post_meta( $post->ID, '_download_links', true );
    ?>
    <p style="margin-bottom: 10px;">
        <label style="display: block; margin-bottom: 5px; font-weight: 600;"><?php _e( 'Download Links:', 'morimoflix-flicker' ); ?></label>
        <textarea id="download_links" name="download_links" rows="8" style="width: 100%; padding: 8px; background: #2a2a2a; border: 1px solid #3a494b; color: #e5e2e1; font-family: monospace; font-size: 13px;" placeholder="Format: Quality|URL|Size (one per line)
Example:
4K ULTRA|https://example.com/movie-4k.mp4|2.1 GB
1080p HD|https://example.com/movie-1080p.mp4|1.4 GB"><?php echo esc_textarea( $download_links ); ?></textarea>
    </p>
    <p class="description">
        <?php _e( 'Format: Quality|URL|Size (one per line)', 'morimoflix-flicker' ); ?><br>
        <?php _e( 'Example: 4K ULTRA|https://example.com/movie-4k.mp4|2.1 GB', 'morimoflix-flicker' ); ?>
    </p>
    <?php
}

/**
 * Save Movie Meta
 */
function morimoflix_save_meta_boxes( $post_id ) {
    if ( ! isset( $_POST['morimoflix_movie_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['morimoflix_movie_meta_nonce'], 'morimoflix_movie_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'movie_rating'    => '_movie_rating',
        'movie_duration'  => '_movie_duration',
        'movie_quality'   => '_movie_quality',
        'movie_language'  => '_movie_language',
        'movie_year'      => '_movie_year',
        'movie_trailer'   => '_movie_trailer',
        'movie_posted_by' => '_movie_posted_by',
    );

    foreach ( $fields as $field => $meta_key ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $field ] ) );
        }
    }

    // Save trailer URL with esc_url_raw
    if ( isset( $_POST['movie_trailer'] ) ) {
        update_post_meta( $post_id, '_movie_trailer', esc_url_raw( $_POST['movie_trailer'] ) );
    }

    // Save live status
    $live = isset( $_POST['movie_live'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_movie_live', $live );

    // Save download links
    if ( isset( $_POST['download_links'] ) ) {
        update_post_meta( $post_id, '_download_links', sanitize_textarea_field( $_POST['download_links'] ) );
    }
}
add_action( 'save_post', 'morimoflix_save_meta_boxes' );

/**
 * Get movie meta data
 */
function morimoflix_get_movie_meta( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $posted_by = get_post_meta( $post_id, '_movie_posted_by', true );
    if ( empty( $posted_by ) ) {
        $posted_by = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
    }

    return array(
        'rating'    => get_post_meta( $post_id, '_movie_rating', true ) ?: '8.0',
        'duration'  => get_post_meta( $post_id, '_movie_duration', true ) ?: '120M',
        'quality'   => get_post_meta( $post_id, '_movie_quality', true ) ?: 'HD',
        'language'  => get_post_meta( $post_id, '_movie_language', true ) ?: '',
        'is_live'   => get_post_meta( $post_id, '_movie_live', true ),
        'year'      => get_post_meta( $post_id, '_movie_year', true ) ?: date( 'Y' ),
        'trailer'   => get_post_meta( $post_id, '_movie_trailer', true ) ?: '',
        'download'  => get_post_meta( $post_id, '_download_links', true ) ?: '',
        'cast'      => get_post_meta( $post_id, '_tmdb_cast', true ) ?: array(),
        'backdrop'  => get_post_meta( $post_id, '_tmdb_backdrop', true ) ?: '',
        'tagline'   => get_post_meta( $post_id, '_movie_tagline', true ) ?: '',
        'posted_by' => $posted_by,
    );
}

/**
 * Get backdrop URL
 */
function morimoflix_get_backdrop( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $backdrop = get_post_meta( $post_id, '_tmdb_backdrop', true );
    if ( $backdrop ) {
        return $backdrop;
    }
    
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, 'large' );
    }
    
    return '';
}

/**
 * Get actor archive link by name
 */
function morimoflix_get_actor_link( $actor_name ) {
    $term = get_term_by( 'name', $actor_name, 'actor' );
    if ( $term && ! is_wp_error( $term ) ) {
        return get_term_link( $term );
    }
    return '#';
}

/**
 * Include movie post type in search results
 */
function morimoflix_search_filter( $query ) {
    if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'post', 'movie', 'tv' ) );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'morimoflix_search_filter' );

/**
 * Fix homepage pagination - reset paged on main query for front page / home.
 *
 * Problem: When ?paged=2 is on the front page, WordPress main query tries to
 * paginate the front page itself and 404s because there's only 1 page there.
 *
 * Solution: Use pre_get_posts (not the 'request' filter) and let WordPress
 * tell us via is_home() / is_front_page() whether we are actually on the
 * front page. The previous request-filter check was matching every empty
 * query var, which incorrectly tripped on taxonomy / category / author
 * archives and broke their pagination.
 *
 * Our custom WP_Query in front-page.php / index.php reads $_GET['paged']
 * directly, so resetting the main query is safe.
 */
function morimoflix_fix_front_page_request( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_front_page() || $query->is_home() ) {
        $query->set( 'paged', 0 );
        $query->is_paged = false;
    }
}
add_action( 'pre_get_posts', 'morimoflix_fix_front_page_request' );

/**
 * Custom excerpt length
 */
function morimoflix_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'morimoflix_excerpt_length' );

/**
 * Custom excerpt more
 */
function morimoflix_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'morimoflix_excerpt_more' );

/**
 * Movie Request Feature
 *
 * @package MorimoFlix
 */

/**
 * Create movie requests table on theme activation
 */
function morimoflix_create_requests_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        movie_name varchar(255) NOT NULL,
        movie_year varchar(4) NOT NULL,
        imdb_link varchar(500) DEFAULT '',
        user_ip varchar(45) NOT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'morimoflix_create_requests_table' );

/**
 * Auto-create movie requests table if it doesn't exist
 * Runs on every admin page load
 */
function morimoflix_check_requests_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';
    
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
        morimoflix_create_requests_table();
    }
}
add_action( 'admin_init', 'morimoflix_check_requests_table' );

/**
 * AJAX handler for movie request submission
 */
function morimoflix_submit_movie_request() {
    check_ajax_referer( 'morimoflix-request-nonce', 'nonce' );

    $movie_name = isset( $_POST['movie_name'] ) ? sanitize_text_field( $_POST['movie_name'] ) : '';
    $movie_year = isset( $_POST['movie_year'] ) ? sanitize_text_field( $_POST['movie_year'] ) : '';
    $imdb_link  = isset( $_POST['imdb_link'] ) ? esc_url_raw( $_POST['imdb_link'] ) : '';

    if ( empty( $movie_name ) || empty( $movie_year ) ) {
        wp_send_json_error( array( 'message' => 'Movie name and year are required.' ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';
    $user_ip = $_SERVER['REMOTE_ADDR'];

    $result = $wpdb->insert(
        $table_name,
        array(
            'movie_name' => $movie_name,
            'movie_year' => $movie_year,
            'imdb_link'  => $imdb_link,
            'user_ip'    => $user_ip,
            'status'     => 'pending',
            'created_at' => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( $result ) {
        wp_send_json_success( array( 'message' => 'Movie request submitted successfully!' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to submit request. Please try again.' ) );
    }
}
add_action( 'wp_ajax_morimoflix_submit_movie_request', 'morimoflix_submit_movie_request' );
add_action( 'wp_ajax_nopriv_morimoflix_submit_movie_request', 'morimoflix_submit_movie_request' );

/**
 * Add Movie Requests admin menu
 */
function morimoflix_add_requests_menu() {
    add_menu_page(
        'Movie Requests',
        'Movie Requests',
        'manage_options',
        'movie-requests',
        'morimoflix_requests_page',
        'dashicons-video-alt3',
        30
    );
}
add_action( 'admin_menu', 'morimoflix_add_requests_menu' );

/**
 * Movie Requests admin page
 */
function morimoflix_requests_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'movie_requests';

    // Handle bulk actions
    if ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' ) {
        $action = sanitize_text_field( $_POST['action'] );
        $ids = isset( $_POST['request_ids'] ) ? array_map( 'intval', $_POST['request_ids'] ) : array();

        if ( ! empty( $ids ) ) {
            $ids_string = implode( ',', $ids );
            if ( $action === 'mark_fulfilled' ) {
                $wpdb->query( "UPDATE $table_name SET status = 'fulfilled' WHERE id IN ($ids_string)" );
            } elseif ( $action === 'mark_pending' ) {
                $wpdb->query( "UPDATE $table_name SET status = 'pending' WHERE id IN ($ids_string)" );
            } elseif ( $action === 'delete' ) {
                $wpdb->query( "DELETE FROM $table_name WHERE id IN ($ids_string)" );
            }
        }
    }

    // Get filter
    $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
    $where = '';
    if ( $status_filter !== 'all' ) {
        $where = $wpdb->prepare( "WHERE status = %s", $status_filter );
    }

    // Get counts
    $all_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $pending_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'pending'" );
    $fulfilled_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'fulfilled'" );

    // Get requests
    $requests = $wpdb->get_results( "SELECT * FROM $table_name $where ORDER BY created_at DESC" );

    ?>
    <style>
        /* Mobile Responsive Styles for Movie Requests */
        @media screen and (max-width: 782px) {
            .mfr-wrap {
                padding: 0 10px;
            }
            
            .mfr-wrap h1 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
            }
            
            .mfr-filters {
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 15px;
            }
            
            .mfr-filters li {
                display: inline-block;
                margin-right: 0 !important;
            }
            
            .mfr-filters a {
                display: inline-block;
                padding: 6px 12px;
                font-size: 13px;
                background: #2271b1;
                color: #fff !important;
                border-radius: 4px;
                text-decoration: none;
            }
            
            .mfr-filters .current {
                background: #135e96;
            }
            
            .mfr-bulk-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 15px;
            }
            
            .mfr-bulk-actions select,
            .mfr-bulk-actions input {
                font-size: 14px !important;
                padding: 8px 12px !important;
            }
            
            .mfr-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -10px;
                padding: 0 10px;
            }
            
            .mfr-table {
                min-width: 600px;
                font-size: 13px;
            }
            
            .mfr-table thead th {
                font-size: 12px;
                padding: 10px 8px !important;
                white-space: nowrap;
            }
            
            .mfr-table tbody td {
                padding: 10px 8px !important;
                font-size: 13px;
                vertical-align: middle;
            }
            
            .mfr-table tbody tr {
                border-bottom: 1px solid #3c434a;
            }
            
            .mfr-movie-name {
                font-size: 14px;
                font-weight: 600;
                display: block;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .mfr-status {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
            }
            
            .mfr-status-pending {
                background: #fff3e0;
                color: #e65100;
            }
            
            .mfr-status-fulfilled {
                background: #e8f5e9;
                color: #2e7d32;
            }
            
            .mfr-link {
                display: inline-block;
                padding: 4px 10px;
                background: #2271b1;
                color: #fff;
                border-radius: 3px;
                text-decoration: none;
                font-size: 12px;
                white-space: nowrap;
            }
            
            .mfr-link:hover {
                background: #135e96;
            }
            
            .mfr-ip {
                font-family: monospace;
                font-size: 11px;
                background: #f0f0f1;
                padding: 3px 6px;
                border-radius: 3px;
                display: inline-block;
                color: #333;
            }
            
            .mfr-date {
                white-space: nowrap;
                font-size: 12px;
            }
            
            .mfr-empty {
                text-align: center;
                padding: 30px 10px !important;
                font-size: 14px;
                color: #666;
            }
            
            .mfr-checkbox {
                width: 30px;
                height: 30px;
            }
        }
        
        /* Desktop Styles */
        @media screen and (min-width: 783px) {
            .mfr-movie-name {
                font-weight: 600;
            }
            
            .mfr-status {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 3px;
                font-size: 13px;
                font-weight: 600;
            }
            
            .mfr-status-pending {
                background: #fff3e0;
                color: #e65100;
            }
            
            .mfr-status-fulfilled {
                background: #e8f5e9;
                color: #2e7d32;
            }
            
            .mfr-link {
                display: inline-block;
                padding: 4px 12px;
                background: #2271b1;
                color: #fff;
                border-radius: 3px;
                text-decoration: none;
                font-size: 13px;
            }
            
            .mfr-link:hover {
                background: #135e96;
                color: #fff;
            }
            
            .mfr-ip {
                font-family: monospace;
                font-size: 12px;
                background: #f0f0f1;
                padding: 3px 8px;
                border-radius: 3px;
                color: #333;
            }
        }
    </style>

    <div class="wrap mfr-wrap">
        <h1 class="wp-heading-inline">Movie Requests</h1>
        <hr class="wp-header-end">

        <!-- Status Filters -->
        <ul class="subsubsub mfr-filters">
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests' ); ?>" class="<?php echo $status_filter === 'all' ? 'current' : ''; ?>">All <span class="count">(<?php echo $all_count; ?>)</span></a></li>
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests&status=pending' ); ?>" class="<?php echo $status_filter === 'pending' ? 'current' : ''; ?>">Pending <span class="count">(<?php echo $pending_count; ?>)</span></a></li>
            <li><a href="<?php echo admin_url( 'admin.php?page=movie-requests&status=fulfilled' ); ?>" class="<?php echo $status_filter === 'fulfilled' ? 'current' : ''; ?>">Fulfilled <span class="count">(<?php echo $fulfilled_count; ?>)</span></a></li>
        </ul>

        <!-- Bulk Actions Form -->
        <form method="post">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions mfr-bulk-actions">
                    <select name="action">
                        <option value="-1">Bulk Actions</option>
                        <option value="mark_fulfilled">Mark as Fulfilled</option>
                        <option value="mark_pending">Mark as Pending</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
            </div>

            <!-- Table -->
            <div class="mfr-table-container">
                <table class="wp-list-table widefat fixed striped mfr-table">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all" class="mfr-checkbox"></td>
                            <th class="manage-column">Movie Name</th>
                            <th class="manage-column" style="width: 70px;">Year</th>
                            <th class="manage-column" style="width: 80px;">Link</th>
                            <th class="manage-column" style="width: 120px;">IP Address</th>
                            <th class="manage-column" style="width: 100px;">Date</th>
                            <th class="manage-column" style="width: 90px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $requests ) ) : ?>
                            <tr><td colspan="7" class="mfr-empty">No movie requests found.</td></tr>
                        <?php else : ?>
                            <?php foreach ( $requests as $request ) : ?>
                                <tr>
                                    <th class="check-column"><input type="checkbox" name="request_ids[]" value="<?php echo $request->id; ?>" class="mfr-checkbox"></th>
                                    <td><span class="mfr-movie-name"><?php echo esc_html( $request->movie_name ); ?></span></td>
                                    <td><?php echo esc_html( $request->movie_year ); ?></td>
                                    <td>
                                        <?php if ( ! empty( $request->imdb_link ) ) : ?>
                                            <a href="<?php echo esc_url( $request->imdb_link ); ?>" target="_blank" class="mfr-link">View</a>
                                        <?php else : ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="mfr-ip"><?php echo esc_html( $request->user_ip ); ?></span></td>
                                    <td><span class="mfr-date"><?php echo date( 'Y-m-d', strtotime( $request->created_at ) ); ?></span></td>
                                    <td>
                                        <?php if ( $request->status === 'pending' ) : ?>
                                            <span class="mfr-status mfr-status-pending">⏳ Pending</span>
                                        <?php else : ?>
                                            <span class="mfr-status mfr-status-fulfilled">✓ Fulfilled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Localize script for movie request
 */
function morimoflix_localize_request_script() {
    wp_localize_script( 'morimoflix-main', 'morimoflixRequest', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'morimoflix-request-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'morimoflix_localize_request_script' );

function morimoflix_track_download() {
    if (!isset($_POST['post_id'])) wp_send_json_error('No post ID');
    $post_id = absint($_POST['post_id']);
    $count = get_post_meta($post_id, '_total_downloads', true) ?: 0;
    $count++;
    update_post_meta($post_id, '_total_downloads', $count);
    wp_send_json_success(array('count' => $count));
}
add_action('wp_ajax_morimoflix_track_download', 'morimoflix_track_download');
add_action('wp_ajax_nopriv_morimoflix_track_download', 'morimoflix_track_download');

function morimoflix_get_download_count($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    return get_post_meta($post_id, '_total_downloads', true) ?: 0;
}

/**
 * SEO poster alt text: "{Title} ({Year}) {Language} {Genre} poster, IMDb {rating}"
 * (Audit Tier 2.2 anchor-text fix + image SEO — explicit alt overrides
 * attachment titles so card links are never announced as generic text.)
 */
function morimoflix_poster_alt( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $meta  = morimoflix_get_movie_meta( $post_id );
    $parts = array( get_the_title( $post_id ) . ' (' . $meta['year'] . ')' );
    if ( ! empty( $meta['language'] ) ) $parts[] = $meta['language'];
    $terms = get_the_terms( $post_id, 'genre' );
    if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) $parts[] = $terms[0]->name;
    return implode( ' ', $parts ) . ' poster, IMDb ' . $meta['rating'];
}

/**
 * SEO card link label: title-only anchor ("Title (Year)")
 * (Audit Tier 2.2 — badges/meta must live outside the <a>.)
 */
function morimoflix_card_label( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $meta = morimoflix_get_movie_meta( $post_id );
    return get_the_title( $post_id ) . ' (' . $meta['year'] . ')';
}

/**
 * Trim text to a word count and guarantee a clean sentence ending
 * (no mid-sentence ellipsis). Used for meta/OG descriptions.
 *
 * @param string $text Input text.
 * @param int    $words Max words.
 * @return string
 */
function morimoflix_clean_desc( $text, $words = 30 ) {
    $text = wp_trim_words( wp_strip_all_tags( $text, true ), $words, '' );
    $text = preg_replace( '/\s*(\[&hellip;\]|&hellip;|…|\.\.\.|\[\.\.\.\])\s*$/u', '', $text );
    $text = trim( $text );
    if ( '' === $text ) {
        return '';
    }
    if ( ! preg_match( '/[.!?…]$/u', $text ) ) {
        if ( preg_match( '/^(.*[.!?])[^.!?]*$/us', $text, $m ) && mb_strlen( $m[1] ) > 40 ) {
            $text = trim( $m[1] );
        } else {
            $text .= '.';
        }
    }
    return $text;
}

/**
 * SEO: Meta description, canonical, Open Graph, Twitter Card
 * Critical fixes from FULL-AUDIT-REPORT.md §1.1, §1.3, §1.4
 */
function morimoflix_seo_head_tags() {
    $site_name = get_bloginfo('name') ?: 'TGMovies MORIMOFLIX';
    $site_url  = home_url('/');
    $og_image  = home_url('/wp-content/uploads/og-default.jpg');

    // --- Meta description ---
    $description = '';
    if (is_front_page() || is_home()) {
        $description = 'Watch and download the latest movies in HD — Telugu, Hindi & English. 1,500+ curated titles, daily Telegram updates, request-a-movie.';
    } elseif (is_singular(array('movie', 'tv'))) {
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content('', false), true), 25);
        $excerpt = $excerpt ?: 'Watch and download ' . get_the_title() . ' in HD on TGMovies MORIMOFLIX.';
        $description = morimoflix_clean_desc( $excerpt, 25 );
    } elseif (is_tax('genre') || is_tax('actor') || is_category() || is_tag()) {
        $term_desc = term_description();
        $description = $term_desc ? wp_trim_words(wp_strip_all_tags($term_desc, true), 25) : sprintf('Browse %s movies on %s.', single_term_title('', false), $site_name);
    } elseif (is_search()) {
        $description = sprintf('Search results for "%s" on %s.', get_search_query(), $site_name);
    }
    if (!empty($description)) {
        if (is_front_page() || is_home()) {
            // Homepage uses the exact approved string — never trim it.
        } else {
            $description = wp_trim_words($description, 26, '');
            if (mb_strlen($description) > 160) {
                $description = morimoflix_clean_desc(mb_substr($description, 0, 157), 60);
            }
        }
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // --- Canonical (homepage only; movie pages already have WP canonical) ---
    if (is_front_page() || is_home()) {
        echo '<link rel="canonical" href="' . esc_url($site_url) . '">' . "\n";
    }

    // --- Open Graph ---
    $og_title = $site_name . ' — Watch & Download Movies in HD';
    $og_desc  = $description ?: 'Browse 1,500+ HD movies. Newly released, A–Z list, request-a-movie. Telegram-powered updates.';
    $og_url   = $site_url;
    $og_type  = 'website';
    $og_image_w = 0;
    $og_image_h = 0;
    if (is_singular(array('movie', 'tv'))) {
        $og_title = get_the_title() . ' — ' . $site_name;
        $og_url   = get_permalink();
        $thumb_id = get_post_thumbnail_id(get_the_ID());
        if ($thumb_id) {
            $src = wp_get_attachment_image_src($thumb_id, 'large');
            if ($src) {
                $og_image   = $src[0];
                $og_image_w = absint($src[1]);
                $og_image_h = absint($src[2]);
            }
        }
        // website (not video.movie): pages embed no video player.
        $og_type = 'website';
    } elseif (is_tax() || is_category() || is_tag()) {
        $og_title = single_term_title('', false) . ' — ' . $site_name;
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $term_link = get_term_link($term);
            if (!is_wp_error($term_link)) $og_url = $term_link;
        }
    } elseif (is_search()) {
        $og_title = 'Search: ' . get_search_query() . ' — ' . $site_name;
        $og_url   = home_url('/?s=' . urlencode(get_search_query()));
    }
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr(morimoflix_clean_desc($og_desc, 30)) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    if (!empty($og_image_w) && !empty($og_image_h)) {
        echo '<meta property="og:image:width" content="' . absint($og_image_w) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . absint($og_image_h) . '">' . "\n";
    }
    echo '<meta property="og:locale" content="en_US">' . "\n";

    // --- Twitter Card ---
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr(morimoflix_clean_desc($og_desc, 30)) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
}
add_action('wp_head', 'morimoflix_seo_head_tags', 1);

/**
 * SEO: JSON-LD Structured Data
 * Fixes §1.5 No JSON-LD. Adds WebSite+SearchAction, Organization, Movie, BreadcrumbList.
 * Uses only allowed schemas (no FAQPage, no HowTo per audit).
 */
function morimoflix_jsonld_output() {
    $site_url  = home_url('/');
    $site_name = get_bloginfo('name') ?: 'TGMovies MORIMOFLIX';
    $logo_url  = home_url('/wp-content/uploads/logo.png');

    // --- WebSite + Organization on homepage ---
    if (is_front_page() || is_home()) {
        $website_org = array(
            '@context' => 'https://schema.org',
            '@graph' => array(
                array(
                    '@type' => 'WebSite',
                    '@id'   => $site_url . '#website',
                    'url'   => $site_url,
                    'name'  => $site_name,
                    'description' => 'Watch and download the latest movies in HD — Telugu, Hindi & English. Browse 1,500+ titles, request new releases, and join the TGMovies Telegram for daily updates.',
                    'inLanguage' => 'en-US',
                    'potentialAction' => array(
                        '@type' => 'SearchAction',
                        'target' => $site_url . '?s={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ),
                ),
                array(
                    '@type' => 'Organization',
                    '@id'   => $site_url . '#org',
                    'name'  => $site_name,
                    'url'   => $site_url,
                    'logo'  => array(
                        '@type' => 'ImageObject',
                        'url'   => $logo_url,
                    ),
                    'sameAs' => array('https://t.me/MorimoFlix_Zone'),
                ),
            ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode($website_org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    // --- Movie JSON-LD on singular movie/tv ---
    if (is_singular(array('movie', 'tv'))) {
        $post_id = get_the_ID();
        $meta    = morimoflix_get_movie_meta($post_id);
        $title   = get_the_title($post_id);
        $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags(get_the_content('', false, $post_id), true), 30);
        $image   = get_the_post_thumbnail_url($post_id, 'large') ?: $logo_url;
        $date_published = get_the_date('c', $post_id);

        // Genres
        $genres = array();
        $genre_terms = get_the_terms($post_id, 'genre');
        if ($genre_terms && !is_wp_error($genre_terms)) {
            foreach ($genre_terms as $t) $genres[] = $t->name;
        }

        // Actors
        $actors = array();
        $actor_terms = get_the_terms($post_id, 'actor');
        if ($actor_terms && !is_wp_error($actor_terms)) {
            foreach ($actor_terms as $t) $actors[] = array('@type' => 'Person', 'name' => $t->name);
        }
        // Fallback to _tmdb_cast meta if no actor taxonomy
        if (empty($actors)) {
            $cast_meta = get_post_meta($post_id, '_tmdb_cast', true);
            if (!empty($cast_meta) && is_array($cast_meta)) {
                foreach (array_slice($cast_meta, 0, 5) as $c) {
                    $name = is_array($c) ? ($c['name'] ?? $c) : $c;
                    if (!empty($name)) $actors[] = array('@type' => 'Person', 'name' => $name);
                }
            }
        }

        // Duration to ISO 8601: "120M" => "PT120M", "2H 30M" => "PT2H30M"
        $duration_iso = '';
        if (!empty($meta['duration'])) {
            $d = strtoupper(trim($meta['duration']));
            $d = preg_replace('/\s+/', '', $d);
            if (preg_match('/^(\d+H)?(\d+M)?$/', $d)) {
                $duration_iso = 'PT' . $d;
            } elseif (preg_match('/^\d+$/', $d)) {
                $duration_iso = 'PT' . $d . 'M';
            }
        }

        $movie_data = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Movie',
            'name'     => $title,
            'alternateName' => $title . ' (' . $meta['year'] . ')',
            'url'      => get_permalink($post_id),
            'image'    => $image,
            'description' => wp_strip_all_tags($excerpt, true),
            'datePublished' => $date_published,
            'dateModified'  => get_the_modified_date('c', $post_id),
            'genre'    => $genres,
        );
        if (!empty($duration_iso)) $movie_data['duration'] = $duration_iso;
        if (!empty($actors)) $movie_data['actor'] = $actors;
        // inLanguage mapped from the real _movie_language meta (omit if unmappable).
        $lang_map = array('english' => 'en', 'hindi' => 'hi', 'tamil' => 'ta', 'telugu' => 'te', 'malayalam' => 'ml', 'kannada' => 'kn', 'bengali' => 'bn', 'marathi' => 'mr', 'punjabi' => 'pa', 'gujarati' => 'gu', 'urdu' => 'ur', 'spanish' => 'es', 'french' => 'fr', 'german' => 'de', 'japanese' => 'ja', 'korean' => 'ko', 'chinese' => 'zh');
        if (!empty($meta['language'])) {
            $first_lang = strtolower(trim(explode(',', $meta['language'])[0]));
            if (isset($lang_map[$first_lang])) $movie_data['inLanguage'] = $lang_map[$first_lang];
        }
        // author: real post-author byline (same name/URL as the visible byline).
        $movie_author_id = get_post_field('post_author', $post_id);
        $movie_author_name = !empty($meta['posted_by']) ? $meta['posted_by'] : get_the_author_meta('display_name', $movie_author_id);
        if (!empty($movie_author_name)) {
            $movie_data['author'] = array('@type' => 'Person', 'name' => $movie_author_name, 'url' => get_author_posts_url($movie_author_id));
        }
        // NOTE: director / contentRating / countryOfOrigin / productionCompany are
        // intentionally omitted — the theme stores no such meta and hardcoding them
        // would repeat the fake-aggregateRating spam risk. Add _movie_director meta
        // (or TMDB enrich) to unlock the director rich result.
        // NOTE (Audit Tier 3.2): aggregateRating removed — ratingCount was hardcoded
        // (rich-snippet spam risk). Re-add only with real user-submitted ratings.
        // Director not stored in theme; omit if empty (avoid empty field)
        echo '<script type="application/ld+json">' . wp_json_encode($movie_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }

    // --- BreadcrumbList on interior pages (singular, taxonomy, search, page-list) ---
    if (!is_front_page() || is_paged()) {
        $crumbs = array();
        $pos = 1;
        $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => 'Home', 'item' => $site_url);
        if (is_singular(array('movie', 'tv'))) {
            $post_type = get_post_type();
            $archive_link = get_post_type_archive_link($post_type);
            if ($archive_link) {
                $label = $post_type === 'tv' ? 'TV Shows' : 'Movies';
                $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $label, 'item' => $archive_link);
            }
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title(), 'item' => get_permalink());
        } elseif (is_tax('genre') || is_tax('actor')) {
            $term = get_queried_object();
            $tax_label = is_tax('genre') ? 'Genres' : 'Actors';
            // Link to first genre/actor archive? Use home + tax slug for hierarchy
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $tax_label, 'item' => $site_url);
            if ($term && !is_wp_error($term)) {
                $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $term->name, 'item' => get_term_link($term));
            }
        } elseif (is_search()) {
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => 'Search: ' . get_search_query(), 'item' => home_url('/?s=' . urlencode(get_search_query())));
        } elseif (is_page()) {
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title(), 'item' => get_permalink());
        } elseif (is_archive() && !is_tax()) {
            $crumbs[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_archive_title(), 'item' => get_permalink());
        }
        // Only output if more than just Home
        if (count($crumbs) > 1) {
            $breadcrumb = array(
                '@context' => 'https://schema.org',
                '@type'    => 'BreadcrumbList',
                'itemListElement' => $crumbs,
            );
            echo '<script type="application/ld+json">' . wp_json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'morimoflix_jsonld_output', 5);

/**
 * Security headers (fallback if Cloudflare Transform Rules not set)
 * Fixes §1.6 Missing security headers. Cloudflare is preferred, this is a PHP fallback.
 * NOTE: No Content-Security-Policy is sent from the theme on purpose — this
 * site runs third-party ad tags, Cloudflare beacon, CanvasJS stats and
 * LiteSpeed-inlined data: fonts, none of which survive a strict theme-level
 * CSP (it rendered icons as tofu boxes and killed ad/beacon scripts on live).
 * Manage CSP only at Cloudflare (Report-Only first) if ever needed.
 */
function morimoflix_security_headers() {
    if (is_admin() || headers_sent()) return;
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
add_action('send_headers', 'morimoflix_security_headers');

/**
 * robots.txt: Explicitly allow AI crawlers for GEO (fixes §2.6 / Tier1 #1.10)
 * Preserves existing output + ai-train signals, adds ChatGPT-User etc.
 */
function morimoflix_robots_txt($output, $public) {
    $extra  = "\n# TGMovies AI crawler policy - allowed for citation/GEO\n";
    $extra .= "User-agent: ChatGPT-User\nAllow: /\n\n";
    $extra .= "User-agent: PerplexityBot\nAllow: /\n\n";
    $extra .= "User-agent: anthropic-ai\nAllow: /\n\n";
    $extra .= "User-agent: FacebookBot\nAllow: /\n";
    return $output . $extra;
}
add_filter('robots_txt', 'morimoflix_robots_txt', 10, 2);

/**
 * llms.txt virtual file (fixes §2.7)
 * Serves /llms.txt via rewrite so no physical file needed at WP root.
 */
function morimoflix_llms_rewrite() {
    add_rewrite_rule('^llms\\.txt$', 'index.php?morimoflix_llms=1', 'top');
}
add_action('init', 'morimoflix_llms_rewrite');
function morimoflix_llms_flush_rewrite() {
    morimoflix_llms_rewrite();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'morimoflix_llms_flush_rewrite');
function morimoflix_llms_query_vars($vars) {
    $vars[] = 'morimoflix_llms';
    return $vars;
}
add_filter('query_vars', 'morimoflix_llms_query_vars');
function morimoflix_llms_template() {
    if (get_query_var('morimoflix_llms')) {
        header('Content-Type: text/plain; charset=utf-8');
        $site_url = home_url('/');
        echo "# TGMovies MORIMOFLIX\n";
        echo "> Watch and download the latest movies in HD — Telugu, Hindi & English.\n\n";
        echo "## Home\n- [Homepage](" . esc_url($site_url) . ")\n\n";
        echo "## Browse\n- [A-Z Library](" . esc_url(home_url('/list/')) . ")\n";
        echo "- [Search](" . esc_url(home_url('/?s=')) . ")\n";
        echo "- [Request a Movie](" . esc_url($site_url) . ")\n\n";
        echo "## Genres\n";
        $genres = get_terms(array('taxonomy' => 'genre', 'hide_empty' => false, 'number' => 8));
        if (!is_wp_error($genres) && !empty($genres)) {
            foreach ($genres as $g) {
                echo "- [" . esc_html($g->name) . "](" . esc_url(get_term_link($g)) . ")\n";
            }
        }
        echo "\n## Top Picks\n";
        $picks = new WP_Query(array('post_type' => array('movie','tv'), 'posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC'));
        if ($picks->have_posts()) {
            while ($picks->have_posts()) { $picks->the_post();
                echo "- [" . esc_html(get_the_title()) . "](" . esc_url(get_permalink()) . ")\n";
            }
            wp_reset_postdata();
        }
        exit;
    }
}
add_action('template_redirect', 'morimoflix_llms_template');
