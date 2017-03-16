<?php

add_action('wp_head', 'cc_hide_breadcrumb_home');

function cc_hide_breadcrumb_home() {
	if(is_home()) {
		add_filter( 'convertica_show_breadcrumb', '__return_false' );
	}
}

add_action( 'wp_head', 'cc_disable_comments', 30 );

function cc_disable_comments() {
	remove_theme_support( 'convertica-comments' );
}

// Enqueue creatika scripts

add_action( 'wp_enqueue_scripts', 'creatika_scripts' );

function creatika_scripts() {
	wp_enqueue_script( 'creatika-scripts', CONVERTICA_URL . 'dev/convertica/design/scripts/creatika_scripts.js', array(), false, false );
	
	// Enqueue dashicons for front end
	wp_enqueue_style( 'dashicons' );
}


// adding facebook icon, twitter icon and setting up animated search icon 

add_theme_support( 'html5', array( 'search-form' ) );

add_filter( 'wp_nav_menu_items', 'cc_search_menu_item', 10, 2 );

function cc_search_menu_item( $menu, $args ) {
	if ( 'before_header' !== $args->theme_location )
		return $menu;
	$menu .= '<li class="right cc-fb"><a href="https://www.facebook.com/creatikacommerce"><span class="dashicons dashicons-facebook-alt"></span></a></li><li class="right cc-twitter"><a href="https://twitter.com/creatikacomerce"><span class="dashicons dashicons-twitter"></span></a></li><li class="right search"><a id="main-nav-search-link" class="icon-search"></a><div class="search-div">' . get_search_form( false ) . '</div></li>';
	return $menu;
}

// Changing excerpt more - only works where excerpt IS hand-crafted

add_filter( 'get_the_excerpt', 'cc_manual_excerpt_more' );

function cc_manual_excerpt_more( $excerpt ) {
	$excerpt_more = '';
	if( has_excerpt() ) {
    	$excerpt_more = '&nbsp;<a class="cc-read-more" href="' . get_permalink() . '" rel="nofollow">Continue Reading »</a>';
	}
	return $excerpt . $excerpt_more;
}


// Changing excerpt more - for automatic excerpts

add_filter('excerpt_more', 'new_excerpt_more');

function new_excerpt_more($more) {
	return '&nbsp;<a class="cc-read-more" href="' . get_permalink() . '" rel="nofollow">Continue Reading »</a>';
}


add_filter( 'convertica_footer_text', 'cc_footer_text' );

function cc_footer_text($footer_creds) {
	$footer_creds .= '<p class="cc-sm"><a class="cc-twitter" href="https://twitter.com/creatikacomerce"><span class="dashicons dashicons-twitter"></span></a><a class="cc-fb" href="https://www.facebook.com/creatikacommerce"><span class="dashicons dashicons-facebook"></span></a></p>';
	return $footer_creds;	
}

add_action('convertica_atn_before_html', 'cc_fb_script');

function cc_fb_script() {
	?>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=842393802544162";
		fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
	<?php
}

add_action('convertica_after_entry', 'cc_fb_comments', 11);

function cc_fb_comments() {
	if(is_single()) {
		echo '<div class="fb-comments" data-href="'.get_the_permalink().'" data-numposts="5" data-width="100%" order_by="reverse_time"></div>';
	}
}
