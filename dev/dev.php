<?php

// Code used during development
define('CONVERTICA_DEV_DIR', CONVERTICA_DIR . 'dev/');
define('CONVERTICA_DEV_URL', CONVERTICA_URL . 'dev/');
define('CONVERTICA_EDGE_DIR', CONVERTICA_DEV_DIR . 'edge/');
define( 'CONVERTICA_EDGE_URL', CONVERTICA_URL . 'dev/edge/' );

add_action( 'get_header', 'convertica_scss_compile' );
add_action( 'admin_head', 'convertica_scss_compile' );
//* Enable Convertica killer features
add_action( 'init', 'convertica_init_killer_features' );

/**
 * Build all the theme admin pages on init using theme support
 */
add_theme_support( 'convertica-landing-page-experience' );
add_theme_support( 'convertica-mobile-experience' );
add_theme_support( 'convertica-landing-sections-experience' );
add_theme_support( 'convertica-silo-menus' );

if ( file_exists( CONVERTICA_DEV_DIR . 'convertica/design/functions.php' ) ) {
	require_once CONVERTICA_DEV_DIR . 'convertica/design/functions.php'; 
}
require_once CONVERTICA_DEV_DIR . 'scssphp/scss.inc.php';
require_once CONVERTICA_EDGE_DIR . 'edge.php';
require_once CONVERTICA_EDGE_DIR . 'widgets/footer-widgets.php';
// Include the landing page settings features for desktop and mobile
require_if_theme_supports( 'convertica-landing-page-experience', CONVERTICA_EDGE_DIR . 'landing/landing-page-settings.php' );
require_if_theme_supports( 'convertica-mobile-experience', CONVERTICA_EDGE_DIR . 'mobile/mobile-landing-page-settings.php' );
require_if_theme_supports( 'convertica-landing-sections-experience', CONVERTICA_EDGE_DIR . 'landing/landing-sections.php' );
require_if_theme_supports( 'convertica-silo-menus', CONVERTICA_EDGE_DIR . 'menus/silo-menu.php' );

function convertica_scss_compile() {
	if ( ! current_user_can( 'update_themes' ) ) {
		return;
	}
	
	$scss = new scssc();
	//$scss->setFormatter('scss_formatter_ex');	
	$scss->setFormatter('scss_formatter');
	if ( file_exists( CONVERTICA_DIR . 'dev/style.scss' ) ) {
		if ( filemtime( CONVERTICA_DIR . 'dev/style.scss' ) > filemtime (CONVERTICA_DIR . 'css/style.css') ) {
			$css = "@charset \"UTF-8\"; \n\n";
			$css ='';
			$css .= $scss->compile( '@import "' . CONVERTICA_DIR . 'dev/style.scss' . '"' );
			file_put_contents( CONVERTICA_DIR . 'css/style.css', $css );
		}
	}
	
	if ( file_exists( CONVERTICA_DEV_DIR . 'convertica/design/style.scss' ) ) {
		if ( filemtime( CONVERTICA_DEV_DIR . 'convertica/design/style.scss' ) > filemtime (CONVERTICA_DEV_DIR . 'convertica/design/style.css') ) {
			$css = "@charset \"UTF-8\"; \n\n";
			$css ='';
			$css .= $scss->compile( '@import "' . CONVERTICA_DEV_DIR . 'convertica/design/style.scss' . '"' );
			file_put_contents( CONVERTICA_DEV_DIR . 'convertica/design/style.css', $css );
		}
	}

	if ( file_exists( convertica_get_custom_location() . 'custom-style.css' ) ) {
		if ( filemtime( convertica_get_custom_location() . 'custom-style.scss' ) > filemtime( convertica_get_custom_location() . 'custom-style.css' ) ) {
			$css = "@charset \"UTF-8\"; \n\n";
			$css ='';
			$css .= $scss->compile('@import "'.convertica_get_custom_location() . 'custom-style.scss'.'"');
			file_put_contents( convertica_get_custom_location() . 'custom-style.css', $css );
		}
	}
}

function convertica_init_killer_features() {
	
	/* Add post type supports for Convertica post/page edit screen features */
	if ( current_theme_supports( 'convertica-landing-page-experience' ) ) {
		add_post_type_support( 'page', 'convertica-landing-page-settings' );
		add_post_type_support( 'post', 'convertica-landing-page-settings' );
	}

	if ( current_theme_supports( 'convertica-mobile-experience' ) ) {
		add_post_type_support( 'page', 'convertica-mobile-landing-page-settings' );
		add_post_type_support( 'post', 'convertica-mobile-landing-page-settings' );
	}

	if ( current_theme_supports( 'convertica-landing-sections-experience' ) ) {
		add_post_type_support( 'page', 'convertica-landing-sections' );
		add_post_type_support( 'post', 'convertica-landing-sections' );
	}

	if ( current_theme_supports( 'convertica-silo-menus' ) ) {
		add_post_type_support( 'page', 'convertica-custom-menu-locations' );
		add_post_type_support( 'post', 'convertica-custom-menu-locations' );
	}
	
}

//add_action( 'pre_get_posts', 'wpse_show_posts_by_format' );
function wpse_show_posts_by_format($query){
	//We are not in admin panel and this is the main query
	if( !$query->is_admin && $query->is_main_query() ){
		//We are in an archive page
		if( $query->is_archive() ){
			$taxquery = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => array( 'post-format-quote', 'post-format-image' )
				)
			);
			//Now adding | updating only to main query 'tax_query' var
			$query->set( 'tax_query', $taxquery );
		}
	}
}


/******** Convertica Dev Features Helper Functions start ********/

/**
 * Helper function for Hide Page Title settings
 * Adds post class to single posts/pages to allow hiding the post/page title
 */
 
function convertica_single_post_classes( $classes ) {
	if ( in_the_loop() ) {
		$new_class = 'hide-title';
		$classes[] = esc_attr( sanitize_html_class( $new_class ) );
	}
	return $classes;
}

/**
 * Helper function for Hide Sidebars for Mobile template options
 * Adds body class to allow hiding the sidebars on mobile devices
 */
 
function convertica_hide_sidebars_class( $classes ) {
	$new_class = 'hide-sidebars';
	$classes[] = esc_attr( sanitize_html_class( $new_class ) );
	return $classes;
}

/**
 * Helper function to detect if the user is visiting the site on mobile
 * Allows developers to add custom viewport detection functions available in Covertica
 * @filter convertica_is_mobile_viewport
 * @see /lib/convertica_mobile_functions.php for additional viewport detection functions that can be used
 * @return bool
 */
 
function convertica_is_mobile_viewport() {
	return apply_filters( 'convertica_is_mobile_viewport', wp_is_mobile() );
}

if ( file_exists( convertica_get_custom_location() . 'custom-functions.php' ) ) {
	require_once convertica_get_custom_location() . 'custom-functions.php';
}

/**
 * Wrapper function for woocommerce 'is_shop' function
 * @return none
 * @since 1.0
 */

function convertica_is_woo_shop() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		if( is_shop() ) {
			return true;
		}
	}
}