<?php
/**
 * 
 * THIS FILE IS A PART OF THE OLD HYBRID CORE 2.0 LIBRARY AND HAS BEEN MODIFIED AS PER THE LICENCE INCLUDED IN HYBRID CORE 2.0 DISTRIBUTION TO 
 * HELP US IMPLEMENT A SETTINGS PAGE.
 * 
 * 
 * 
 * 
 * 
 * Functions for dealing with theme settings on both the front end of the site and the admin. This allows us 
 * to set some default settings and make it easy for theme developers to quickly grab theme settings from 
 * the database. This file is only loaded if the theme adds support for the 'hybrid-core-theme-settings' 
 * feature.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Loads the Hybrid theme settings once and allows the input of the specific field the user would 
 * like to show.  Hybrid theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since  0.7.0
 * @access public
 * @global object  $hybrid  The global Hybrid object.
 * @param  string  $option  The specific theme setting the user wants.
 * @return mixed            Specific setting asked for.
 */

function convertica_get_setting( $option = '', $options_field = CONVERTICA_SETTINGS ) {
	global $hybrid;

	/* If no specific option was requested, return false. */
	if ( !$option ) {
		return false;
		
		}

	/* Get the default settings. */
	//$defaults = convertica_get_default_theme_settings();

	/*
	// If the settings array hasn't been set, call get_option() to get an array of theme settings.
	if ( !isset( $hybrid->settings ) || !is_array( $hybrid->settings ) )
		$hybrid->settings = get_option( $option, $defaults );

	// If the option isn't set but the default is, set the option to the default.
	if ( !isset( $hybrid->settings[ $option ] ) && isset( $defaults[ $option ] ) )
		$hybrid->settings[ $option ] = $defaults[ $option ];
*/
	$hybrid->settings = get_option( $options_field );

	/* If no option is found at this point, return false. */
	if ( !isset( $hybrid->settings[ $option ] ) )
		return false;

	/* If the specific option is an array, return it. */
	if ( is_array( $hybrid->settings[ $option ] ) )
		return $hybrid->settings[ $option ];

	/* Strip slashes from the setting and return. */
	else
		return wp_kses_stripslashes( $hybrid->settings[ $option ] );
}