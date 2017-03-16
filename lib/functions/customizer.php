<?php

/* all customizer functionality */

function convertica_customize_register( $wp_customize ) {
	global $convertica_defaults;

	$defaults      = $convertica_defaults;
	$settings_type = 'theme_mod';
	$transport     = 'refresh';

	$wp_customize->remove_control('background_color');
	$wp_customize->remove_control('header_textcolor');
	
	$wp_customize->add_panel( 'convertica_panel', array('priority' => 10,'capability' => 'edit_theme_options','title' => __( 'Convertica Layout', 'convertica-td' ),'description' => __( 'Tune the raw power of Convertica', 'convertica-td' )));
	$wp_customize->remove_section( 'layout', 30 );

	$wp_customize->add_section( 'layout', array('title' => esc_html__( 'Layout', 'hybrid-core' ),'priority' => 10,'panel' => 'convertica_panel' ) );
	$wp_customize->add_setting( 'layout_style_setting',array('default' => $defaults[ 'layout_style_setting' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_layout_style_control', array('label' => __( 'Layout Style', 'convertica-td' ),'section' => 'layout','settings' => 'layout_style_setting','type' => 'radio','priority' => '10','choices' => convertica_choices('layout_style_setting')) );
	$wp_customize->add_setting( 'layout_body_bg_color',array('default' => $defaults[ 'layout_body_bg_color' ],'type' => $settings_type,'transport' => $transport) );
	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'layout_body_bg_color_control', array('label' => __( 'Body Background Color', 'convertica-td' ),'section' => 'layout','settings' => 'layout_body_bg_color', 'priority' => '10') ) );
	$wp_customize->add_setting( 'layout_wrap_bg_color',array('default' => $defaults[ 'layout_wrap_bg_color' ],'type' => $settings_type,'transport' => $transport) );
	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'layout_wrap_bg_color_control', array('label' => __( 'Wrap Background Color', 'convertica-td' ),'section' => 'layout','settings' => 'layout_wrap_bg_color', 'priority' => '10') ) );
	$wp_customize->add_section( 'convertica_archives_section', array('title' => __( 'Archive Options', 'convertica-td' ), 'priority' => 35, 'description' => __( 'Select display options for archives.', 'convertica-td' ), 'panel' => 'convertica_panel' ) );
	$wp_customize->add_setting( 'archive_style', array('default' => $defaults[ 'archive_style' ], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_archive_control', array('label' => __( 'Content Style', 'convertica-td' ),'section' => 'convertica_archives_section','settings' => 'archive_style','type' => 'select','choices' => convertica_choices('archive_style')) );
	$wp_customize->add_setting( 'archive_featured_image_setting', array('default' => $defaults[ 'archive_featured_image_setting' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_archive_featured_image_control', array('label' => __( 'Show Featured Image', 'convertica-td' ),'section' => 'convertica_archives_section','settings' => 'archive_featured_image_setting','type' => 'select','choices' => convertica_choices('archive_featured_image_setting')) );	
	$wp_customize->add_setting( 'archive_featured_image_size_setting', array('default' => $defaults[ 'archive_featured_image_size_setting' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_archive_image_size_control', array('label' => __( 'Featured Image Size', 'convertica-td' ),'section' => 'convertica_archives_section','settings' => 'archive_featured_image_size_setting','type' => 'select','choices' => convertica_choices('archive_featured_image_size_setting')) );	
	$wp_customize->add_setting( 'archive_featured_image_float_setting', array('default' => $defaults[ 'archive_featured_image_float_setting' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_archive_image_float_control', array('label' => __( 'Float', 'convertica-td' ),'section' => 'convertica_archives_section','settings' => 'archive_featured_image_float_setting','type' => 'select','choices' => convertica_choices('archive_featured_image_float_setting')) );	
	$wp_customize->add_setting( 'archive_breadcrumbs_setting', array('default' => $defaults[ 'archive_breadcrumbs_setting' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'convertica_sanitizer') );
	$wp_customize->add_control( 'convertica_archive_breadcrumbs_control', array('label' => __( 'Breadcrumbs', 'convertica-td' ),'section' => 'convertica_archives_section','settings' => 'archive_breadcrumbs_setting','type' => 'radio','choices' => convertica_choices('archive_breadcrumbs_setting')) );
	$wp_customize->add_section( 'convertica_layout_widths_section', array('title' => __( 'Layout Widths', 'convertica-td' ), 'priority' => 30, 'description' => __( 'Select Layout Widths.', 'convertica-td' ), 'panel' => 'convertica_panel' ) );
	$wp_customize->add_setting( 'layout_padding', array('default' => $defaults[ 'layout_padding' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'layout_padding_control', array('label' => __( 'Column Padding', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_padding','type' => 'number' ) );
	$wp_customize->add_setting( 'layout_content_1c', array('default' => $defaults[ 'layout_content_1c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint') );
	$wp_customize->add_control( 'layout_content_1c_control', array('label' => __( 'Content Width–Single Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_content_1c','type' => 'number' ) );
	$wp_customize->add_setting( 'layout_content_2c', array('default' => $defaults[ 'layout_content_2c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'layout_content_2c_control', array('label' => __( 'Content Width–Two Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_content_2c','type' => 'number' ) );
	$wp_customize->add_setting( 'layout_sb_2c', array('default' => $defaults[ 'layout_sb_2c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint') );
	$wp_customize->add_control( 'layout_sb_2c_control', array('label' => __( 'Sidebar Width–Two Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_sb_2c','type' => 'number') );
	$wp_customize->add_setting( 'layout_content_3c', array('default' => $defaults[ 'layout_content_3c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'layout_content_3c_control', array('label' => __( 'Content Width–Three Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_content_3c','type' => 'number') );
	$wp_customize->add_setting( 'layout_sb_3c', array('default' => $defaults[ 'layout_sb_3c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'layout_sb_3c_control', array('label' => __( 'Sidebar Width–Three Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_sb_3c','type' => 'number') );
	$wp_customize->add_setting( 'layout_sb_sub_3c', array('default' => $defaults[ 'layout_sb_sub_3c' ],'type' => $settings_type,'transport' => $transport,'sanitize_callback' => 'absint') );
	$wp_customize->add_control( 'layout_sb_sub_3c_control', array('label' => __( 'Sidebar Subsidiary–Three Column View', 'convertica-td' ),'section' => 'convertica_layout_widths_section','settings' => 'layout_sb_sub_3c','type' => 'number') );
}

function convertica_sanitizer($value, $objsetting) {
	if ( array_key_exists( $value, convertica_choices($objsetting->id) ) ) {
        return $value;
    }
    else {
    	global $convertica_defaults;
        return $convertica_defaults[$objsetting->id];
    }	
}

function convertica_customizer_css( ) {
	

	$padding__page = convertica_get_mod('layout_padding');
	$body_bg_color = convertica_get_mod('layout_body_bg_color');
	$wrap_bg_color = convertica_get_mod('layout_wrap_bg_color');
	$css           = '.breadcrumbs {
		margin-bottom: ' . ( $padding__page / 2 ) . 'px;
	}
	.plural .sticky {
		padding: ' . $padding__page . 'px;
	}
	/*
	.fullwidth #container {
		background-color: '.$body_bg_color.' !important;
	}
	
	.boxed #container {
		border-top: 0.81em solid '.$wrap_bg_color.';
		border-bottom: 0.81em solid '.$wrap_bg_color.';
	}

	.fullwidth #container {
		border-top: 0;
		border-bottom: 0;
		width: auto !important;
	}
	*/
	.wrap {
		margin:auto;
		background-color: '.$wrap_bg_color.';
	}
	body {
		background-color: '.$body_bg_color.';
	}
	';
	$css           = apply_filters( 'convertica_settings_css', $css );
	echo '<style type="text/css">' . $css . '</style>';
}

/*
Returns defaults. Make shure the choices are valid.
*/
function convertica_get_defaults( ) {
	$defaults = array(
		// settings that have choices
		'archive_style' => 'excerpts',
		'layout_style_setting' => 'fullwidth',
		'archive_featured_image_setting' => '1',
		'archive_featured_image_size_setting' => 'full',
		'archive_featured_image_float_setting' => 'none',
		'archive_breadcrumbs_setting' => '1',

		// settings that don't have choices
		'layout_padding' => '35', // We'll add units after some calculations
		'layout_content_1c' => '940', // We'll add units after some calculations
		'layout_content_2c' => '710',
		'layout_sb_2c' => '195',
		'layout_content_3c' => '460',
		'layout_sb_3c' => '195',
		'layout_sb_sub_3c' => '195',
		'layout_body_bg_color' => '#ffffff',
		'layout_wrap_bg_color' => '#ffffff',
	);
	return apply_filters( 'convertica_settings_defaults', $defaults );
}

function convertica_responsive_css( $css ) {
	$padding__page             = convertica_get_mod('layout_padding');
	$size__site_content_1c     = convertica_get_mod('layout_content_1c');
	$size__site_content_2c     = convertica_get_mod('layout_content_2c');
	$size__site_sidebar_2c     = convertica_get_mod('layout_sb_2c');
	$size__site_content_3c     = convertica_get_mod('layout_content_3c');
	$size__site_sidebar_3c     = convertica_get_mod('layout_sb_3c');
	$size__site_sidebar_sub_3c = convertica_get_mod('layout_sb_sub_3c');
	$size__site_wrap_1c        = $padding__page + $size__site_content_1c + $padding__page;
	$size__site_page_1c        = $size__site_wrap_1c;
	$size__site_wrap_2c        = $padding__page + $size__site_content_2c + $padding__page + $size__site_sidebar_2c + $padding__page;
	$size__site_page_2c        = $size__site_wrap_2c;
	$size__site_wrap_3c        = $padding__page + $size__site_sidebar_sub_3c + $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page;
	$size__site_page_3c        = $size__site_wrap_3c;
	$size__column_container    = $size__site_content_3c + $padding__page + $size__site_sidebar_3c;
	$size__column_container    = $size__site_content_3c + $padding__page + $size__site_sidebar_3c;
	$widths[ 'three-two' ]     = $padding__page + $size__site_sidebar_sub_3c + $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page; // From the size of three column content width + sb width to size of three col wrap
	$widths[ 'three-one' ]     = $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_content_3c + $padding__page; // From the size of three column content width to size of three col content + sb width
	$widths[ 'three-zero' ]    = $padding__page + $size__site_content_3c + $padding__page; // From min up to the size of three column content width
	$widths[ 'two-one' ]       = $padding__page + $size__site_content_2c + $padding__page + $size__site_sidebar_2c + $padding__page; // From bigger than bp of 2-col-content up to the size of two column wrap width
	$widths[ 'two-zero' ]      = $padding__page + $size__site_content_2c + $padding__page; // From min up to the size of two column wrap width
	$widths[ 'one-zero' ]      = $padding__page + $size__site_content_1c + $padding__page; // From min up to the size of single column wrap width
	$widths[ 'min-width' ]     = min( ( $size__site_content_1c + ( 2 * $padding__page ) ), ( $size__site_content_2c + ( 2 * $padding__page ) ), ( $size__site_content_3c + ( 2 * $padding__page ) ) );
	$responsive                = '.wrap {
			padding: 17px '.$padding__page.'px;
			width: auto;
		}
	@media only screen and (max-width: ' . $widths[ 'min-width' ] . 'px ) {
		#menu-before_header-items,
		#menu-after_header-items {
			display: none;
		}
	}
	@media only screen and (min-width: ' . $widths[ 'min-width' ] . 'px ) {
		#site-title, #site-description {
			text-align: left;
		}
		.slicknav_menu {
			display: none;
		}
	}
	@media only screen and (min-width: ' . $widths[ 'one-zero' ] . 'px ) {
		.boxed.layout-1c #container {
			width: ' . $size__site_page_1c . 'px;
		}
		.layout-1c .wrap {
			width: ' . $size__site_wrap_1c . 'px;
		}
		.layout-1c #content {
			width: ' . $size__site_content_1c . 'px;
		}
	}
	@media only screen and (min-width: ' . $widths[ 'two-one' ] . 'px ) {
		.boxed.layout-2c-l #container {
			width: ' . $size__site_page_2c . 'px;
		}
		.layout-2c-l .wrap {
			width: ' . $size__site_wrap_2c . 'px;
			
		}
		.layout-2c-l #content {
			width: ' . $size__site_content_2c . 'px;
			float:left;
		}
		.layout-2c-l #sidebar-primary {
			width: ' . $size__site_sidebar_2c . 'px;
			float: right;
		}
		.boxed.layout-2c-r #container {
			width: ' . $size__site_page_2c . 'px;
		  }
		.layout-2c-r .wrap {
			width: ' . $size__site_wrap_2c . 'px;
			
		  }
		.layout-2c-r #content {
			width: ' . $size__site_content_2c . 'px;
			float:right;
		  }
		.layout-2c-r #sidebar-primary {
			width: ' . $size__site_sidebar_2c . 'px;
			float: left;
		  }
	}
	@media only screen and (min-width: ' . $widths[ 'three-one' ] . 'px ) {
		.boxed.layout-3c-l #container {
			width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px;
			}
		.layout-3c-l .wrap {
			width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px ;
			
			}
		.layout-3c-l .column-container {
			width: ' . $size__column_container . 'px;
			}
		.layout-3c-l #content {
			width: ' . $size__site_content_3c . 'px;
			float: left;
			}
		.layout-3c-l #sidebar-primary {
			width: ' . $size__site_sidebar_3c . 'px;
			float: right;
			}
		.layout-3c-l #sidebar-subsidiary {
			width: ' . $size__site_sidebar_sub_3c . 'px;
			}

		.boxed.layout-3c-r #container {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px;
			}
		.layout-3c-r .wrap {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px;
				
			}
		.layout-3c-r .column-container {
				width: ' . $size__column_container . 'px;
			}
		.layout-3c-r #content {
				width: ' . $size__site_content_3c . 'px;
				float: right;
			}

		.layout-3c-r #sidebar-primary {
				width: ' . $size__site_sidebar_3c . 'px;
				float: left;
			}
		.layout-3c-r #sidebar-subsidiary {
				width: ' . $size__site_sidebar_sub_3c . 'px;
			}
		
		.boxed.layout-3c-c #container {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px;
			}
		.layout-3c-c .wrap {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page ) . 'px;
				
			}
		.layout-3c-c .column-container {
				width: ' . $size__column_container . 'px;
			}
		.layout-3c-c #content {
				width: ' . $size__site_content_3c . 'px;
				float: left;
			}
		.layout-3c-c #sidebar-primary {
				width: ' . $size__site_sidebar_3c . 'px;
				float: right;
			}
		.layout-3c-c #sidebar-subsidiary {
				width: ' . $size__site_sidebar_sub_3c . 'px;
			}
	}

	@media only screen and (min-width: ' . $widths[ 'three-two' ] . 'px ) {
		.boxed.layout-3c-l #container {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
			}
		.layout-3c-l .wrap {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
				
			}
		.layout-3c-l .column-container {
				width: ' . $size__column_container . 'px;
				float: left;
			}
		.layout-3c-l #content {
				width: ' . $size__site_content_3c . 'px;
				float: left;
			}

		.layout-3c-l #sidebar-primary {
				width: ' . $size__site_sidebar_3c . 'px;
				float: right;
			}
		.layout-3c-l #sidebar-subsidiary {
				width: ' . $size__site_sidebar_sub_3c . 'px;
				float: right;
			}
		
		.boxed.layout-3c-r #container {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
			}
		.layout-3c-r .wrap {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
				
			}
		.layout-3c-r .column-container {
				width: ' . $size__column_container . 'px;
				float: right;
			}
		.layout-3c-r #content {
				width: ' . $size__site_content_3c . 'px;
				float: right;
			}
		.layout-3c-r #sidebar-primary {
				width: ' . $size__site_sidebar_3c . 'px;
				float: left;
			}
		.layout-3c-r #sidebar-subsidiary {
				width: ' . $size__site_sidebar_sub_3c . 'px;
				float: left;
			}
		
		.boxed.layout-3c-c #container {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
			}
		.layout-3c-c .wrap {
				width: ' . ( $padding__page + $size__site_content_3c + $padding__page + $size__site_sidebar_3c + $padding__page + $size__site_sidebar_sub_3c + $padding__page ) . 'px;
				
			}
		.layout-3c-c .column-container {
				width: ' . $size__column_container . 'px;
				float: right;
			}
		.layout-3c-c #content {
				width: ' . $size__site_content_3c . 'px;
				float: left;
			}
		.layout-3c-c #sidebar-primary {
				width: ' . $size__site_sidebar_3c . 'px;
				float: right;
			}
		.layout-3c-c #sidebar-subsidiary {
				width: ' . $size__site_sidebar_sub_3c . 'px;
				float: left;
			}
	}
';
	return $css . $responsive;
}

function convertica_choices($setting = false){
	$choices = array();

	$choices['archive_style'] = array( 'excerpts' => 'Excerpts', 'content' => 'Full Content');
	$choices['layout_style_setting'] = array('fullwidth'=> 'Full Width', 'boxed' => 'Boxed' );
	$choices['archive_featured_image_setting'] = array('1'=> 'Yes', '0' => 'No' );
	$choices['archive_featured_image_size_setting'] = convertica_get_all_image_sizes();
	$choices['archive_featured_image_float_setting'] = array( 'none' => 'none', 'center' => 'Center', 'left' => 'Left', 'right' => 'Right');
	$choices['archive_breadcrumbs_setting'] = array('1'=> 'Yes', '0' => 'No' );

	$choices = apply_filters( 'convertica_settings_choices', $choices, $setting );

	if($setting) {
		return $choices[$setting];
	}

	return $choices;
}


function convertica_get_mod($mod){
	global $convertica_defaults;
	
	return get_theme_mod($mod,$convertica_defaults[$mod]);
}

function convertica_get_mods(){
	global $convertica_defaults;
	
	//$mods = get_theme_mods();
	$mods = array();
	foreach($convertica_defaults as $key => $value)
		{
			$mods[$key] = convertica_get_mod($key);
			//clog($key);
			//clog(convertica_get_mod($key));
		}
	
	return wp_parse_args($mods, $convertica_defaults);
}