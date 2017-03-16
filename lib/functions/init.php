<?php

if ( ! defined( 'CONVERTICA_DIR' ) ) {
	define( 'CONVERTICA_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'CONVERTICA_URL' ) ) {
	define( 'CONVERTICA_URL', trailingslashit( get_template_directory_uri() ) );
}
define( 'CONVERTICA_LIB', CONVERTICA_DIR . 'lib/' );
define( 'HYBRID_DIR', CONVERTICA_DIR . 'lib/hybrid-core/' );
define( 'HYBRID_URI', CONVERTICA_URL . 'lib/hybrid-core/' );
define( 'PARENT_TD', 'CONVERTICA' );
define( 'PARENT_OPTIONS_FIELD', 'convertica' );
define( 'CHILD_DOMAIN', 'CONVERTICA' );
define( 'SETTINGS_TD', 'CONVERTICA' );
define( 'SETTINGS_FIELD', 'convertica-admin' );
require_once HYBRID_DIR . 'hybrid.php';
require_once CONVERTICA_LIB . 'functions/markup.php';
require_once CONVERTICA_LIB . 'functions/customizer.php';
require_once CONVERTICA_LIB . 'functions/custom-background.php';
require_once CONVERTICA_LIB . 'functions/custom-header.php';
require_once CONVERTICA_LIB . 'classes/mobile-detect.php';
require_once CONVERTICA_LIB . 'functions/convertica_mobile_functions.php';

if ( file_exists( CONVERTICA_DIR . 'dev/dev.php' ) ) {
	require_once CONVERTICA_DIR . 'dev/dev.php';
}

new Hybrid();

add_action( 'after_setup_theme', 'convertica_setup', 5 );

$convertica_defaults = convertica_get_defaults();

function convertica_setup( ) {
	global $convertica_defaults;
	$convertica_defaults = convertica_get_defaults();
	add_theme_support( 'convertica-semantic-ui' );
	add_theme_support( 'convertica-slicknav' );
	add_theme_support( 'hybrid-core-template-hierarchy' );
	add_action( 'admin_enqueue_scripts', 'convertica_admin_styles' );
	add_action( 'wp_enqueue_scripts', 'convertica_enqueue_scripts', 4 );
	add_action( 'customize_register', 'convertica_customize_register' );
	add_action( 'convertica_before_loop', 'convertica_archive_header' );
	add_filter( 'body_class', 'convertica_body_classes' );
	// Theme layouts.
	add_theme_support( 'theme-layouts', array(
		 'default' => is_rtl() ? '2c-r' : '2c-l' 
	) );
	add_theme_support( 'hybrid-core-template-hierarchy' ); // Enable custom template hierarchy.
	add_theme_support( 'get-the-image' ); // The best thumbnail/image script ever.
	add_theme_support( 'breadcrumb-trail' ); // Breadcrumbs. Yay!
	add_theme_support( 'cleaner-gallery' ); // Nicer [gallery] shortcode implementation.
	add_theme_support( 'automatic-feed-links' ); // Automatically add feed links to <head>.
	remove_theme_support( 'post-formats' );
	// Handle content width for embeds and images.
	hybrid_set_content_width( 1280 );
	add_theme_support( 'convertica-comments' );
}

function convertica_archive_header( ) {

	if ( function_exists( 'is_bbPress' ) && is_bbPress() )
		return;

	
	if ( !is_home() && hybrid_is_plural() ) { // If viewing a multi-post page but not the blog page (should be togglable on homepage via setting?)
		
		convertica_archive_header_markup(); // Loads the misc/archive-header.php template.
		
	} // End check for multi-post page. 
}


function clog( $string = '', $debug = false, $echo = true ) {
	echo '<pre>';
	print_r( $string );
	echo '</pre>';
}


function convertica_enqueue_scripts( ) {
	
	wp_register_script( 'modernizr', CONVERTICA_URL . 'lib/foundation-5/js/vendor/modernizr', array( ), '2.8.3', true );
	if ( current_theme_supports( 'convertica-foundation' ) ) {
		wp_enqueue_script( 'convertica-foundation-script', CONVERTICA_URL . 'lib/foundation-5/js/foundation.min.js', array(
			 'jquery',
			'modernizr' 
		), false, true );
		wp_enqueue_style( 'convertica-foundation-style', CONVERTICA_URL . 'lib/foundation-5/css/foundation.min.css' );
	}
	if ( current_theme_supports( 'convertica-semantic-ui' ) ) {
		wp_enqueue_script( 'convertica-semantic-script', CONVERTICA_URL . 'lib/semantic-ui/semantic.min.js', array(
			 'jquery' 
		), false, true );
		wp_enqueue_style( 'convertica-semantic-style', CONVERTICA_URL . 'lib/semantic-ui/semantic.min.css' );
	}
	if ( current_theme_supports( 'convertica-ui-kit' ) ) {
		wp_enqueue_script( 'convertica-ui-kit-script', CONVERTICA_URL . 'lib/uikit/js/uikit.min.js', array(
			 'jquery' 
		), false, true );
		wp_enqueue_style( 'convertica-ui-kit-style', CONVERTICA_URL . 'lib/uikit/css/uikit.min.css' );
	}
	
	if ( current_theme_supports( 'convertica-bootstrap' ) ) {
		wp_enqueue_script( 'convertica-bootstrap-script', CONVERTICA_URL . 'lib/bootstrap/js/bootstrap.min.js', array(
			 'jquery' 
		), false, true );
		wp_enqueue_style( 'convertica-bootstrap-style', CONVERTICA_URL . 'lib/bootstrap/css/bootstrap.min.css' );
		wp_enqueue_style( 'convertica-bootstrap-theme', CONVERTICA_URL . 'lib/bootstrap/css/bootstrap-theme.min.css' );
	}
	if ( current_theme_supports( 'convertica-slicknav' ) ) {
		wp_enqueue_script( 'convertica-slicknav-script', CONVERTICA_URL . 'lib/slicknav/jquery.slicknav.min.js', array(
			 'jquery' 
		), false, true );
		
	}
	if ( current_user_can( 'edit_theme_options' ) ) {
		$style_version = microtime();
	} else {
		$style_version = false;
	}
	wp_enqueue_style( 'convertica-sass-style', CONVERTICA_URL . 'css/style.css', array( ), $style_version );
	
	wp_enqueue_style( 'dashicons' );
}

add_action( 'wp_head', 'convertica_enqueue_base_scripts' );

function convertica_enqueue_base_scripts( ) {
?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#menu-before_header-items').slicknav({ /* responsive menu: primary nav menu  */
			prependTo: "#menu-before_header .wrap",
			label: '<span class="menu-icon">&#8801;</span>&ensp;Menu',
			duration: 400,
			closedSymbol: '<span class="dashicons dashicons-arrow-right"></span>',
			openedSymbol: '<span class="dashicons dashicons-arrow-down"></span>'  
		});
		
		jQuery('#menu-after_header-items').slicknav({ /* responsive menu: secondary nav menu */
			prependTo: "#menu-after_header .wrap",
			label: '<span class="menu-icon">&#8801;</span>&ensp;Menu',
			duration: 400,
			closedSymbol: '<span class="dashicons dashicons-arrow-right"></span>',
			openedSymbol: '<span class="dashicons dashicons-arrow-down"></span>' 
		});
	});
	</script>
	<?php
}


function convertica_admin_styles( ) {
	wp_enqueue_style( 'convertica-admin-style', CONVERTICA_URL . 'css/admin.css', false, '1.0.0' );
}


function convertica_body_classes( $classes ) {
	$classes[ ] = convertica_get_mod( 'layout_style_setting' );
	$classes[ ] = get_option( 'stylesheet' );
	if ( 'page' == get_option( 'show_on_front' ) && is_front_page() ) {
		$classes[ ] = 'front';
		//unset($classes['home']); //doesn't work
	}
	return $classes;
}


function convertica_get_all_image_sizes( $size = '' ) {
	global $_wp_additional_image_sizes;
	$sizes                           = array( );
	$get_intermediate_image_sizes    = get_intermediate_image_sizes();
	$get_intermediate_image_sizes[ ] = 'full';
	
	$image_sizes = array( );
	foreach ( $get_intermediate_image_sizes as $value ) {
		$image_sizes[ $value ] = ucwords( $value );
	}
	
	return $image_sizes;
}

add_action( 'wp_head', 'convertica_customizer_css' );
add_filter( 'convertica_settings_css', 'convertica_responsive_css' );
add_action( 'convertica_after_entry', 'convertica_after_entry_widget' );
add_action( 'convertica_before_header', 'convertica_sb_before_header' );
add_action( 'convertica_after_header', 'convertica_sb_after_header' );
add_action( 'convertica_before_footer', 'convertica_sb_before_footer' );
add_filter( 'convertica_show_sb_after_entry', 'convertica_show_sb_after_single_entry', 10, 2 );

function convertica_show_sb_after_single_entry( $show ) {
	if ( is_single() ) {
		return true;
	}
	return false;
}

add_action( 'convertica_do_header', 'convertica_header' );
add_action( 'convertica_do_footer', 'convertica_footer' );
add_action( 'convertica_do_sidebar', 'convertica_sidebar_primary' );
add_action( 'convertica_do_sidebar_alt', 'convertica_sidebar_subsidiary' );



add_action('convertica_before_header','convertica_before_header_menu','11');
add_action('convertica_after_header','convertica_after_header_menu');





add_action('convertica_after_entry_header','convertica_after_entry_header');

# Register custom menus.
add_action( 'init', 'convertica_register_menus', 5 );

# Register custom layouts.
add_action( 'hybrid_register_layouts', 'convertica_register_layouts' );

# Register sidebars.
add_action( 'widgets_init', 'convertica_register_sidebars', 5 );

# Add custom scripts and styles
add_action( 'wp_enqueue_scripts', 'convertica_enqueue_scripts', 5 );
add_action( 'wp_enqueue_scripts', 'convertica_enqueue_styles',  5 );



/**
 * Registers nav menu locations.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function convertica_register_menus() {
	register_nav_menu( 'before_header',    esc_html_x( 'Before Header',    'nav menu location', 'convertica-td' ) );
	register_nav_menu( 'after_header',  esc_html_x( 'After Header',  'nav menu location', 'convertica-td' ) );
	register_nav_menu( 'footer', esc_html_x( 'Footer', 'nav menu location', 'convertica-td' ) );
}

/**
 * Registers layouts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function convertica_register_layouts() {

	hybrid_register_layout( '1c',   array( 'label' => esc_html__( '1 Column',                     'convertica-td' ), 'image' => '%s/images/layouts/1c.svg'   ) );
	hybrid_register_layout( '2c-l', array( 'label' => esc_html__( '2 Columns: Content / Sidebar', 'convertica-td' ), 'image' => '%s/images/layouts/2c-l.svg' ) );
	hybrid_register_layout( '2c-r', array( 'label' => esc_html__( '2 Columns: Sidebar / Content', 'convertica-td' ), 'image' => '%s/images/layouts/2c-r.svg' ) );
	hybrid_register_layout( '3c-l', array( 'label' => esc_html__( '3 Columns: Content / Sidebar / Sidebar', 'convertica-td' ), 'image' => '%s/images/layouts/3c-l.svg' ) );
	hybrid_register_layout( '3c-r', array( 'label' => esc_html__( '3 Columns: Sidebar / Sidebar / Content', 'convertica-td' ), 'image' => '%s/images/layouts/3c-r.svg' ) );
	hybrid_register_layout( '3c-c', array( 'label' => esc_html__( '3 Columns: Sidebar / Content / Sidebar', 'convertica-td' ), 'image' => '%s/images/layouts/3c-c.svg' ) );
}

/**
 * Registers sidebars.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function convertica_register_sidebars() {

	hybrid_register_sidebar(
		array(
			'id'          => 'primary',
			'name'        => esc_html_x( 'Primary', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is the primary sidebar if you are using a two or three column layout option.', 'convertica-td' )
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'subsidiary',
			'name'        => esc_html_x( 'Subsidiary', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is the subsidiary sidebar if you are using a three column layout option.', 'convertica-td' )
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'after_entry',
			'name'        => esc_html_x( 'After Entry', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is after entry widget area.', 'convertica-td' )
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'before_header',
			'name'        => esc_html_x( 'Before Header', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is before header widget area.', 'convertica-td' )
		)
	);
	hybrid_register_sidebar(
		array(
			'id'          => 'after_header',
			'name'        => esc_html_x( 'After Header', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is after header widget area.', 'convertica-td' )
		)
	);
	hybrid_register_sidebar(
		array(
			'id'          => 'before_footer',
			'name'        => esc_html_x( 'Before Footer', 'sidebar', 'convertica-td' ),
			'description' => esc_html__( 'This is before footer widget area.', 'convertica-td' )
		)
	);	
}


/**
 * Load stylesheets for the front end.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function convertica_enqueue_styles() {

	// Load one-five base style.
	//wp_enqueue_style( 'hybrid-one-five' );

	// Load gallery style if 'cleaner-gallery' is active.
	if ( current_theme_supports( 'cleaner-gallery' ) )
		wp_enqueue_style( 'hybrid-gallery' );

	// Load parent theme stylesheet if child theme is active.
	if ( is_child_theme() )
		wp_enqueue_style( 'hybrid-parent' );

	// Load active theme stylesheet.
	wp_enqueue_style( 'hybrid-style' );
}