<?php

// returns theme name
function convertica_get_theme_name(){
	$theme = wp_get_theme( get_template() );
	return $theme->get( 'Name' );
}

// returns theme slug
function convertica_get_theme_slug(){
	return get_option( 'stylesheet' );
}

function convertica_get_prefix() {
	global $hybrid;

	/* If the global prefix isn't set, define it. Plugin/theme authors may also define a custom prefix. */
	if ( empty( $hybrid->prefix ) )
		$hybrid->prefix = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );

	return $hybrid->prefix;
}

// initializes custom directory
function convertica_init_custom_files(){
	global $pagenow;
	if( is_admin() && (	( isset( $_GET['activated'] ) && $pagenow == "themes.php" ))) {	// Theme activation
		$stylesheet_path = convertica_get_custom_location( 'path' );
		if ( ! is_dir( $stylesheet_path ) ) {
			wp_mkdir_p( $stylesheet_path );
		}
		if ( ! is_writable( $stylesheet_path ) ){
			@chmod( $stylesheet_path, 0777 );
		}
		if ( ! is_writable( $stylesheet_path ) ){
			return false;
		}

		$css = "/** For those times when you don't need a full-blown child-theme. This is SASSified so style smart! */\n";
		if ( ! file_exists( convertica_get_custom_location().'custom-style.scss' ) ) {
			$file = @fopen( convertica_get_custom_location().'custom-style.scss', 'w+' );
			@fwrite( $file, $css );
			@fclose( $file );
			@chmod( convertica_get_custom_location().'custom-style.scss', 0644 );
		}

		$custom_code = "<?php\n\n/** For those times when you don't need a full-blown child-theme. Place your WordPress PHP tweaks here and code smart! */\n";
		if ( ! file_exists( convertica_get_custom_location().'custom-functions.php' ) ) {
			$file = @fopen( convertica_get_custom_location().'custom-functions.php', 'w+' );
			@fwrite( $file, $custom_code );
			@fclose( $file );
			@chmod( convertica_get_custom_location().'custom-functions.php', 0644 );
		}
		return true;
	}
}

// returns path/url of "custom" directory
function convertica_get_custom_location( $type = 'dir' ) {
	$uploads = wp_upload_dir();
	$type = ( 'url' == $type ) ? $uploads['baseurl'] : $uploads['basedir'];
	$theme_slug = convertica_get_theme_slug();
	return apply_filters( 'convertica_get_custom_location', $type . "/$theme_slug-custom/" );
}

/* Handles redirect on admin pages; also appends the supplied query args to set the relevant flags */
function convertica_admin_redirect( $page, array $query_args = array() ) {

	if ( ! $page )
		return;

	$url = html_entity_decode( menu_page_url( $page, 0 ) );

	foreach ( (array) $query_args as $key => $value ) {
		if ( empty( $key ) && empty( $value ) ) {
			unset( $query_args[$key] );
		}
	}

	$url = add_query_arg( $query_args, $url );

	wp_redirect( esc_url_raw( $url ) );

}

/* Checks and compares the current page to requested page in admin area */

function is_convertica_admin_page( $pagehook = '' ) {

	global $page_hook;

	if ( isset( $page_hook ) && $page_hook === $pagehook )
		return true;

	//* May be too early for $page_hook
	if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === $pagehook )
		return true;

	return false;

}

/**
 * Helper function that validates a CSS usable color
 *
 * @return string
 */
function convertica_validate_color_settings( $new_val, $old_val ) {
	$clrnames = array(
		'transparent',
		'aliceblue',
		'antiquewhite',
		'aqua',
		'aquamarine',
		'azure',
		'beige',
		'bisque',
		'black',
		'blanchedalmond',
		'blue',
		'blueviolet',
		'brown',
		'burlywood',
		'cadetblue',
		'chartreuse',
		'chocolate',
		'coral',
		'cornflowerblue',
		'cornsilk',
		'crimson',
		'cyan',
		'darkblue',
		'darkcyan',
		'darkgoldenrod',
		'darkgray',
		'darkgreen',
		'darkkhaki',
		'darkmagenta',
		'darkolivegreen',
		'darkorange',
		'darkorchid',
		'darkred',
		'darksalmon',
		'darkseagreen',
		'darkslateblue',
		'darkslategray',
		'darkturquoise',
		'darkviolet',
		'deeppink',
		'deepskyblue',
		'dimgray',
		'dodgerblue',
		'firebrick',
		'floralwhite',
		'forestgreen',
		'fuchsia',
		'gainsboro',
		'ghostwhite',
		'gold',
		'goldenrod',
		'gray',
		'green',
		'greenyellow',
		'honeydew',
		'hotpink',
		'indianred',
		'indigo',
		'ivory',
		'khaki',
		'lavender',
		'lavenderblush',
		'lawngreen',
		'lemonchiffon',
		'lightblue',
		'lightcoral',
		'lightcyan',
		'lightgoldenrodyellow',
		'lightgray',
		'lightgreen',
		'lightpink',
		'lightsalmon',
		'lightseagreen',
		'lightskyblue',
		'lightslategray',
		'lightsteelblue',
		'lightyellow',
		'lime',
		'limegreen',
		'linen',
		'magenta',
		'maroon',
		'mediumaquamarine',
		'mediumblue',
		'mediumorchid',
		'mediumpurple',
		'mediumseagreen',
		'mediumslateblue',
		'mediumspringgreen',
		'mediumturquoise',
		'mediumvioletred',
		'midnightblue',
		'mintcream',
		'mistyrose',
		'moccasin',
		'navajowhite',
		'navy',
		'oldlace',
		'olive',
		'olivedrab',
		'orange',
		'orangered',
		'orchid',
		'palegoldenrod',
		'palegreen',
		'paleturquoise',
		'palevioletred',
		'papayawhip',
		'peachpuff',
		'peru',
		'pink',
		'plum',
		'powderblue',
		'purple',
		'red',
		'rosybrown',
		'royalblue',
		'saddlebrown',
		'salmon',
		'sandybrown',
		'seagreen',
		'seashell',
		'sienna',
		'silver',
		'skyblue',
		'slateblue',
		'slategray',
		'snow',
		'springgreen',
		'steelblue',
		'tan',
		'teal',
		'thistle',
		'tomato',
		'turquoise',
		'violet',
		'wheat',
		'white',
		'whitesmoke',
		'yellow',
		'yellowgreen'
	);

	$new_val = strtolower( $new_val );
	if ( preg_match( '/rgba?\((\s+)?\d+(\s+)?,(\s+)?\d+(\s+)?,(\s+)?\d+(\s+)?,\d*(?:\.\d+)?\)/i', $new_val ) ) {
		return $new_val;
	}

	// Try validating hex
	//can also replace /(#.*?)(([0-9a-f]{3}){1,2})/ with $2???
	preg_match( '/(([0-9a-f]{3}){1,2})/i', $new_val, $matches );

	if ( $matches ) {
		return '#' . $matches[0];
	}

	if ( in_array( $new_val, $clrnames ) ) {
		return $new_val;
	}

	return $old_val;
}