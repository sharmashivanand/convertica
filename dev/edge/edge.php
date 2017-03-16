<?php

define( 'CONVERTICA_SETTINGS', 'convertica-theme-settings' );

add_theme_support( 'hybrid-core-theme-settings' );
add_theme_support( 'convertica-settings-page' );
add_theme_support( 'convertica-import-export' );

//* Render theme admin pages
add_action( 'after_setup_theme', 'convertica_build_admin_pages' );
add_action( 'admin_init', 'convertica_init_custom_files' );

add_filter( 'extra_theme_headers', 'convertica_theme_headers' );

require_once CONVERTICA_DEV_DIR . 'convertica/functions.php';
require_once CONVERTICA_EDGE_DIR . 'fonts/fonts.php';
require_if_theme_supports( 'hybrid-core-theme-settings', CONVERTICA_DEV_DIR . 'convertica/settings.php' );
if ( is_admin() ) {
	/* Load the theme settings feature if supported. */
	require_if_theme_supports( 'hybrid-core-theme-settings', CONVERTICA_DEV_DIR . 'convertica/admin/theme-settings.php' );
	require_if_theme_supports( 'convertica-settings-page', CONVERTICA_DEV_DIR . 'convertica/admin/convertica-settings.php' );
	require_if_theme_supports( 'convertica-import-export', CONVERTICA_DEV_DIR . 'convertica/admin/convertica-import-export.php' );
	require_once( CONVERTICA_DEV_DIR . 'convertica/admin/validation.php' );
}

function convertica_theme_headers( $headers ) {

	if ( !in_array( 'Support URI', $headers ) )
		$headers[] = 'Support URI';

	if ( !in_array( 'Documentation URI', $headers ) )
		$headers[] = 'Documentation URI';

	return $headers;
	
}

function convertica_build_admin_pages() {
	
	if ( ! is_admin() )
		return;
	
	global $_convertica_settings_page, $_convertica_import_export_page;
	
	if ( current_theme_supports( 'convertica-settings-page' ) ) {
		$_convertica_settings_page = new Convertica_Settings_Edge;
	}
	
	if ( current_theme_supports( 'convertica-import-export' ) ) {
		$_convertica_import_export_page = new Convertica_Import_Export;
	}
	
}

function convertica_edge_customizer_enqueue() {
	
	wp_enqueue_script( 'convertica-chosen-script', CONVERTICA_EDGE_URL . 'scripts/chosen/chosen.jquery'.hybrid_get_min_suffix().'.js', array( 'jquery', 'customize-controls' ), false, true );
	wp_enqueue_style( 'convertica-chosen-style', CONVERTICA_EDGE_URL . 'scripts/chosen/chosen'.hybrid_get_min_suffix().'.css');
	wp_add_inline_style( 'customize-controls', "
		#accordion-panel-convertica_panel.control-panel > h3,
		#accordion-panel-convertica_panel.control-panel > h3:hover,
		#accordion-panel-convertica_edge_panel.control-panel > h3,
		#accordion-panel-convertica_edge_panel.control-panel > h3:hover {             
			background-image: -moz-linear-gradient(left,  #cc0000 0%, #cc0000 100%) !important;
			background-image: -webkit-gradient(linear, left top, right top, color-stop(0%,#cc0000), color-stop(100%,#cc0000)) !important;
			background-image: -webkit-linear-gradient(left,  #cc0000 0%,#cc0000 100%) !important;
			background-image: -o-linear-gradient(left,  #cc0000 0%,#cc0000 100%) !important;
			background-image: -ms-linear-gradient(left,  #cc0000 0%,#cc0000 100%) !important;
			background-image: linear-gradient(to right,  #cc0000 0%,#cc0000 100%) !important;
			background-size: 5px !important;
			background-repeat: repeat-y !important;
		}	
	" );
	wp_enqueue_script( 'convertica-admin-script', CONVERTICA_EDGE_URL . 'scripts/admin.js', array( 'jquery' ), false, true );
	wp_localize_script( 'convertica-admin-script', 'vars', array(
		'font_family_fields' => json_encode( convertica_get_saved_web_fonts_ids( convertica_get_mods() ) ),
		'all_fonts' => json_encode( convertica_edge_get_super_fonts() )
	) );
	
}

add_action( 'after_setup_theme', 'convertica_edge_setup', 5 );

function convertica_edge_setup(){
	add_action( 'customize_controls_enqueue_scripts', 'convertica_edge_customizer_enqueue' );
	add_action( 'customize_register', 'convertica_edge_customize_register' );
	$theme_slug = get_option( 'stylesheet' );
	add_filter( 'convertica_settings_defaults', 'convertica_edge_settings_defaults' );
	add_filter( 'pre_update_option_theme_mods_' . $theme_slug, 'convertica_configure_fonts', 10, 2 );
	add_action( 'wp_enqueue_scripts', 'convertica_edge_enqueue_web_fonts' );
	add_action( 'wp_enqueue_scripts', 'convertica_edge_custom_scripts' , 9999 );
	add_filter( 'convertica_settings_css','convertica_edge_settings_css' );
	add_action( 'admin_enqueue_scripts', 'convertica_enqueue_admin_edge_scripts' );
}

function convertica_enqueue_admin_edge_scripts() {
	wp_enqueue_script( 'convertica-admin-script', CONVERTICA_EDGE_URL . 'scripts/admin.js', array( 'jquery' ), false, true );
}

function convertica_edge_custom_scripts(){
	//enqueue design specific style.css
	if ( file_exists( CONVERTICA_DEV_DIR . 'convertica/design/style.css' ) ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			$design_style_version = microtime();
		} else {
			$design_style_version = false;
		}
		wp_enqueue_style( 'convertica-design-css', CONVERTICA_DEV_URL . 'convertica/design/style.css', array(), $design_style_version, 'all' );
	}
	
	//enqueue custom style.css
	if ( file_exists( convertica_get_custom_location() . 'custom-style.css' ) ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			$style_version = microtime();
		} else {
			$style_version = false;
		}
		wp_enqueue_style( 'convertica-custom', convertica_get_custom_location( 'url' ) . 'custom-style.css', array(), $style_version, 'all' );
	}

}
function convertica_configure_fonts( $value, $old_value  ) {
	
	$options = wp_parse_args( $value, convertica_get_mods() );
	$fonts = convertica_filter_font_family_settings( $options );
	$value = wp_parse_args( $fonts,$value );
	
	return $value;
	
}

function convertica_edge_customize_register( $wp_customize ) {
	
	global $convertica_defaults;
	$defaults = $convertica_defaults;
	$settings_type = 'theme_mod';
	$transport     = 'refresh';
	
	$wp_customize->add_panel( 'convertica_edge_panel', array('priority' => 15,'title' => __( 'Convertica Design', 'convertica-td' ), 'description' => __( 'Tune the raw power of Convertica', 'convertica-td' )));
	
	//Body
	$wp_customize->add_section( 'convertica_body_section', array('title' => __( 'Body Fonts &amp; Globals', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for body fonts and other global options.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//body font family
	$wp_customize->add_setting('body_font_family', array('default' => $defaults['body_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('body_font_family_control', array('label' => __( 'Font Family', 'convertica-td' ), 'settings' => 'body_font_family', 'choices' => convertica_choices('body_font_family'), 'section' => 'convertica_body_section', 'type' => 'select' ));
	//body_font_size
	$wp_customize->add_setting('body_font_size', array('default' => $defaults['body_font_size'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('body_font_size_control', array('label' => __( 'Font Size', 'convertica-td' ), 'settings' => 'body_font_size', 'section' => 'convertica_body_section', 'type' => 'number'));
	//body_font_variant
	$wp_customize->add_setting('body_font_variant', array('default' => $defaults['body_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('body_font_variant_control', array('label' => __( 'Font Variant', 'convertica-td' ), 'settings' => 'body_font_variant', 'choices' => convertica_choices('body_font_variant'), 'section' => 'convertica_body_section', 'type' => 'select'));
	//body_font_color
	$wp_customize->add_setting('body_font_color', array('default' => $defaults['body_font_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'body_font_color_control', array('label' => __( 'Text Color', 'convertica-td' ), 'settings' => 'body_font_color', 'section' => 'convertica_body_section', 'type' => 'color')));
	//body_link_color
	$wp_customize->add_setting('body_link_color', array('default' => $defaults['body_link_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'body_link_color_control', array('label' => __( 'Link Color', 'convertica-td' ), 'settings' => 'body_link_color', 'section' => 'convertica_body_section', 'type' => 'color')));
	//body_link_color_hover
	$wp_customize->add_setting('body_link_color_hover', array('default' => $defaults['body_link_color_hover'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'body_link_color_hover_control', array('label' => __( 'Link Hover Color', 'convertica-td' ), 'settings' => 'body_link_color_hover', 'section' => 'convertica_body_section', 'type' => 'color')));
	//form_field_font_family
	$wp_customize->add_setting('form_field_font_family', array('default' => $defaults['form_field_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('form_field_font_family_control', array('label' => __( 'Form Fields Font Family', 'convertica-td' ), 'settings' => 'form_field_font_family', 'choices' => convertica_choices('form_field_font_family'), 'section' => 'convertica_body_section', 'type' => 'select' ));
	//form_field_font_size
	$wp_customize->add_setting('form_field_font_size', array('default' => $defaults['form_field_font_size'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('form_field_font_size_control', array('label' => __( 'Form Fields Font Size', 'convertica-td' ), 'settings' => 'form_field_font_size', 'section' => 'convertica_body_section', 'type' => 'number'));
	//form_field_font_color
	$wp_customize->add_setting('form_field_font_color', array('default' => $defaults['form_field_font_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'form_field_font_color_control', array('label' => __( 'Form Fields Text Color', 'convertica-td' ), 'settings' => 'form_field_font_color', 'section' => 'convertica_body_section', 'type' => 'color')));
	
	//Header
	$wp_customize->add_section( 'convertica_header_section', array('title' => __( 'Site Header', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for site Header.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//site title font family
	$wp_customize->add_setting('site_title_font_family', array('default' => $defaults['site_title_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('site_title_font_family_control', array('label' => __( 'Site Title Font Family', 'convertica-td' ), 'settings' => 'site_title_font_family', 'choices' => convertica_choices('site_title_font_family'), 'section' => 'convertica_header_section', 'type' => 'select' ));
	//site_title_font_size
	$wp_customize->add_setting('site_title_font_size', array('default' => $defaults['site_title_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('site_title_font_size_control', array('label' => __( 'Site Title Font Size', 'convertica-td' ), 'settings' => 'site_title_font_size', 'section' => 'convertica_header_section', 'type' => 'number', ));
	//site title font variant
	$wp_customize->add_setting('site_title_font_variant', array('default' => $defaults['site_title_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('site_title_font_variant_control', array('label' => __( 'Site Title Font Variant', 'convertica-td' ), 'settings' => 'site_title_font_variant', 'choices' => convertica_choices('site_title_font_variant'), 'section' => 'convertica_header_section', 'type' => 'select'));
	//site title text color
	$wp_customize->add_setting('site_title_color', array('default' => $defaults['site_title_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_title_color_control', array('label' => __( 'Site Title Color', 'convertica-td' ), 'settings' => 'site_title_color', 'section' => 'convertica_header_section', 'type' => 'color')));
	//site tagline font family
	$wp_customize->add_setting('site_tagline_font_family', array('default' => $defaults['site_tagline_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('site_tagline_font_family_control', array('label' => __( 'Site Tagline Font Family', 'convertica-td' ), 'settings' => 'site_tagline_font_family', 'choices' => convertica_choices('site_tagline_font_family'), 'section' => 'convertica_header_section', 'type' => 'select' ));
	//site_tagline_font_size
	$wp_customize->add_setting('site_tagline_font_size', array('default' => $defaults['site_tagline_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('site_tagline_font_size_control', array('label' => __( 'Site Tagline Font Size', 'convertica-td' ), 'settings' => 'site_tagline_font_size', 'section' => 'convertica_header_section', 'type' => 'number', ));
	//site tagline font variant
	$wp_customize->add_setting('site_tagline_font_variant', array('default' => $defaults['site_tagline_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('site_tagline_font_variant_control', array('label' => __( 'Site Tagline Font Variant', 'convertica-td' ), 'settings' => 'site_tagline_font_variant', 'choices' => convertica_choices('site_tagline_font_variant'), 'section' => 'convertica_header_section', 'type' => 'select'));
	//site tagline text color
	$wp_customize->add_setting('site_tagline_color', array('default' => $defaults['site_tagline_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_tagline_color_control', array('label' => __( 'Site Tagline Color', 'convertica-td' ), 'settings' => 'site_tagline_color', 'section' => 'convertica_header_section', 'type' => 'color')));
	
	//Primary Nav
	$wp_customize->add_section( 'convertica_before_header_nav_section', array('title' => __( 'Before Header Menu', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for Primary Menu.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//primary nav font family
	$wp_customize->add_setting('before_header_nav_font_family', array('default' => $defaults['before_header_nav_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('before_header_nav_font_family_control', array('label' => __( 'Font Family', 'convertica-td' ), 'settings' => 'before_header_nav_font_family', 'choices' => convertica_choices('before_header_nav_font_family'), 'section' => 'convertica_before_header_nav_section', 'type' => 'select' ));
	//primary nav_font_size
	$wp_customize->add_setting('before_header_nav_font_size', array('default' => $defaults['before_header_nav_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('before_header_nav_font_size_control', array('label' => __( 'Font Size', 'convertica-td' ), 'settings' => 'before_header_nav_font_size', 'section' => 'convertica_before_header_nav_section', 'type' => 'number', ));
	//primary nav font variant
	$wp_customize->add_setting('before_header_nav_font_variant', array('default' => $defaults['before_header_nav_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('before_header_nav_font_variant_control', array('label' => __( 'Font Variant', 'convertica-td' ), 'settings' => 'before_header_nav_font_variant', 'choices' => convertica_choices('before_header_nav_font_variant'), 'section' => 'convertica_before_header_nav_section', 'type' => 'select'));
	//primary nav text color
	$wp_customize->add_setting('before_header_nav_text_color', array('default' => $defaults['before_header_nav_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_text_color_control', array('label' => __( 'Text Color', 'convertica-td' ), 'settings' => 'before_header_nav_text_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav hover text color
	$wp_customize->add_setting('before_header_nav_hover_text_color', array('default' => $defaults['before_header_nav_hover_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_hover_text_color_control', array('label' => __( 'Hover Text Color', 'convertica-td' ), 'settings' => 'before_header_nav_hover_text_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav current link color
	$wp_customize->add_setting('before_header_nav_current_link_text_color', array('default' => $defaults['before_header_nav_current_link_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_current_link_text_color_control', array('label' => __( 'Current Link Text Color', 'convertica-td' ), 'settings' => 'before_header_nav_current_link_text_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav parent link color
	$wp_customize->add_setting('before_header_nav_parent_nav_link_text_color', array('default' => $defaults['before_header_nav_parent_nav_link_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_parent_nav_link_text_color_control', array('label' => __( 'Parent Nav Link Text Color', 'convertica-td' ), 'settings' => 'before_header_nav_parent_nav_link_text_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav background color
	$wp_customize->add_setting('before_header_nav_link_background_color', array('default' => $defaults['before_header_nav_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_link_background_color_control', array('label' => __( 'Link Background Color', 'convertica-td' ), 'settings' => 'before_header_nav_link_background_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav hover background color
	$wp_customize->add_setting('before_header_nav_link_hover_background_color', array('default' => $defaults['before_header_nav_link_hover_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_link_hover_background_color_control', array('label' => __( 'Link Hover Background Color', 'convertica-td' ), 'settings' => 'before_header_nav_link_hover_background_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav current link background color
	$wp_customize->add_setting('before_header_nav_current_link_background_color', array('default' => $defaults['before_header_nav_current_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_current_link_background_color_control', array('label' => __( 'Current Link Background Color', 'convertica-td' ), 'settings' => 'before_header_nav_current_link_background_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav parent link background color
	$wp_customize->add_setting('before_header_nav_parent_nav_link_background_color', array('default' => $defaults['before_header_nav_parent_nav_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_parent_nav_link_background_color_control', array('label' => __( 'Parent Link Background Color', 'convertica-td' ), 'settings' => 'before_header_nav_parent_nav_link_background_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav border width
	$wp_customize->add_setting('before_header_nav_border_width', array('default' => $defaults['before_header_nav_border_width'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('before_header_nav_border_width_control', array('label' => __( 'Border Width', 'convertica-td' ), 'settings' => 'before_header_nav_border_width', 'section' => 'convertica_before_header_nav_section', 'type' => 'number'));
	//primary nav border color
	$wp_customize->add_setting('before_header_nav_border_color', array('default' => $defaults['before_header_nav_border_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'before_header_nav_border_color_control', array('label' => __( 'Border Color', 'convertica-td' ), 'settings' => 'before_header_nav_border_color', 'section' => 'convertica_before_header_nav_section', 'type' => 'color')));
	//primary nav border width
	$wp_customize->add_setting('before_header_nav_submenu_width', array('default' => $defaults['before_header_nav_submenu_width'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('before_header_nav_submenu_width_control', array('label' => __( 'Submenu Width', 'convertica-td' ), 'settings' => 'before_header_nav_submenu_width', 'section' => 'convertica_before_header_nav_section', 'type' => 'number'));

	//Secondary Nav
	$wp_customize->add_section( 'convertica_after_header_nav_section', array('title' => __( 'After Header Menu', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for Secondary Menu.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//secondary nav font family
	$wp_customize->add_setting('after_header_nav_font_family', array('default' => $defaults['after_header_nav_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('after_header_nav_font_family_control', array('label' => __( 'Font Family', 'convertica-td' ), 'settings' => 'after_header_nav_font_family', 'choices' => convertica_choices('after_header_nav_font_family'), 'section' => 'convertica_after_header_nav_section', 'type' => 'select' ));
	//secondary nav_font_size
	$wp_customize->add_setting('after_header_nav_font_size', array('default' => $defaults['after_header_nav_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('after_header_nav_font_size_control', array('label' => __( 'Font Size', 'convertica-td' ), 'settings' => 'after_header_nav_font_size', 'section' => 'convertica_after_header_nav_section', 'type' => 'number', ));
	//secondary nav font variant
	$wp_customize->add_setting('after_header_nav_font_variant', array('default' => $defaults['after_header_nav_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('after_header_nav_font_variant_control', array('label' => __( 'Font Variant', 'convertica-td' ), 'settings' => 'after_header_nav_font_variant', 'choices' => convertica_choices('after_header_nav_font_variant'), 'section' => 'convertica_after_header_nav_section', 'type' => 'select'));
	//secondary nav text color
	$wp_customize->add_setting('after_header_nav_text_color', array('default' => $defaults['after_header_nav_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_text_color_control', array('label' => __( 'Text Color', 'convertica-td' ), 'settings' => 'after_header_nav_text_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav hover text color
	$wp_customize->add_setting('after_header_nav_hover_text_color', array('default' => $defaults['after_header_nav_hover_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_hover_text_color_control', array('label' => __( 'Hover Text Color', 'convertica-td' ), 'settings' => 'after_header_nav_hover_text_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav current link color
	$wp_customize->add_setting('after_header_nav_current_link_text_color', array('default' => $defaults['after_header_nav_current_link_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_current_link_text_color_control', array('label' => __( 'Current Link Text Color', 'convertica-td' ), 'settings' => 'after_header_nav_current_link_text_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav parent link color
	$wp_customize->add_setting('after_header_nav_parent_nav_link_text_color', array('default' => $defaults['after_header_nav_parent_nav_link_text_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_parent_nav_link_text_color_control', array('label' => __( 'Parent Link Text Color', 'convertica-td' ), 'settings' => 'after_header_nav_parent_nav_link_text_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav background color
	$wp_customize->add_setting('after_header_nav_link_background_color', array('default' => $defaults['after_header_nav_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_link_background_color_control', array('label' => __( 'Link Background Color', 'convertica-td' ), 'settings' => 'after_header_nav_link_background_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav hover background color
	$wp_customize->add_setting('after_header_nav_link_hover_background_color', array('default' => $defaults['after_header_nav_link_hover_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_link_hover_background_color_control', array('label' => __( 'Link Hover Background Color', 'convertica-td' ), 'settings' => 'after_header_nav_link_hover_background_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav current link background color
	$wp_customize->add_setting('after_header_nav_current_link_background_color', array('default' => $defaults['after_header_nav_current_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_current_link_background_color_control', array('label' => __( 'Current Link Background Color', 'convertica-td' ), 'settings' => 'after_header_nav_current_link_background_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav parent link background color
	$wp_customize->add_setting('after_header_nav_parent_nav_link_background_color', array('default' => $defaults['after_header_nav_parent_nav_link_background_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_parent_nav_link_background_color_control', array('label' => __( 'Parent Link Background Color', 'convertica-td' ), 'settings' => 'after_header_nav_parent_nav_link_background_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav border width
	$wp_customize->add_setting('after_header_nav_border_width', array('default' => $defaults['after_header_nav_border_width'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('after_header_nav_border_width_control', array('label' => __( 'Border Width', 'convertica-td' ), 'settings' => 'after_header_nav_border_width', 'section' => 'convertica_after_header_nav_section', 'type' => 'number'));
	//secondary nav border color
	$wp_customize->add_setting('after_header_nav_border_color', array('default' => $defaults['after_header_nav_border_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'after_header_nav_border_color_control', array('label' => __( 'Border Color', 'convertica-td' ), 'settings' => 'after_header_nav_border_color', 'section' => 'convertica_after_header_nav_section', 'type' => 'color')));
	//secondary nav border width
	$wp_customize->add_setting('after_header_nav_submenu_width', array('default' => $defaults['after_header_nav_submenu_width'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('after_header_nav_submenu_width_control', array('label' => __( 'Submenu Width', 'convertica-td' ), 'settings' => 'after_header_nav_submenu_width', 'section' => 'convertica_after_header_nav_section', 'type' => 'number'));

	//Headlines
	$wp_customize->add_section( 'convertica_headlines_section', array('title' => __( 'Headlines', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for headings.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));	
	//post title font family
	$wp_customize->add_setting('entry_title_font_family', array('default' => $defaults['entry_title_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('entry_title_font_family_control', array('label' => __( 'Post Title Font Family', 'convertica-td' ), 'settings' => 'entry_title_font_family', 'choices' => convertica_choices('entry_title_font_family'), 'section' => 'convertica_headlines_section', 'type' => 'select' ));
	//entry_title_font_size
	$wp_customize->add_setting('entry_title_font_size', array('default' => $defaults['entry_title_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('entry_title_font_size_control', array('label' => __( 'Post Title Font Size', 'convertica-td' ), 'settings' => 'entry_title_font_size', 'section' => 'convertica_headlines_section', 'type' => 'number', ));
	//post title font variant
	$wp_customize->add_setting('entry_title_font_variant', array('default' => $defaults['entry_title_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('entry_title_font_variant_control', array('label' => __( 'Post Title Font Variant', 'convertica-td' ), 'settings' => 'entry_title_font_variant', 'choices' => convertica_choices('entry_title_font_variant'), 'section' => 'convertica_headlines_section', 'type' => 'select'));
	//post title text color
	$wp_customize->add_setting('entry_title_color', array('default' => $defaults['entry_title_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'entry_title_color_control', array('label' => __( 'Post Title Color', 'convertica-td' ), 'settings' => 'entry_title_color', 'section' => 'convertica_headlines_section', 'type' => 'color')));
	//content headlines text color
	$wp_customize->add_setting('content_headlines_font_family', array('default' => $defaults['content_headlines_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('content_headlines_font_family_control', array('label' => __( 'Content Headlines Font Family', 'convertica-td' ), 'settings' => 'content_headlines_font_family', 'choices' => convertica_choices('content_headlines_font_family'), 'section' => 'convertica_headlines_section', 'type' => 'select' ));
	//content headlines font variant
	$wp_customize->add_setting('content_headlines_font_variant', array('default' => $defaults['content_headlines_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('content_headlines_font_variant_control', array('label' => __( 'Content Headlines Font Variant', 'convertica-td' ), 'settings' => 'content_headlines_font_variant', 'choices' => convertica_choices('content_headlines_font_variant'), 'section' => 'convertica_headlines_section', 'type' => 'select'));
	//content headlines text color
	$wp_customize->add_setting('content_headlines_color', array('default' => $defaults['content_headlines_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_headlines_color_control', array('label' => __( 'Content Headlines Color', 'convertica-td' ), 'settings' => 'content_headlines_color', 'section' => 'convertica_headlines_section', 'type' => 'color')));

	//Bylines
	$wp_customize->add_section( 'convertica_byline_section', array('title' => __( 'Bylines', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for bylines.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//post title font family
	$wp_customize->add_setting('byline_font_family', array('default' => $defaults['byline_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('byline_font_family_control', array('label' => __( 'Byline Font Family', 'convertica-td' ), 'settings' => 'byline_font_family', 'choices' => convertica_choices('byline_font_family'), 'section' => 'convertica_byline_section', 'type' => 'select' ));
	//byline_font_size
	$wp_customize->add_setting('byline_font_size', array('default' => $defaults['byline_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('byline_font_size_control', array('label' => __( 'Byline Font Size', 'convertica-td' ), 'settings' => 'byline_font_size', 'section' => 'convertica_byline_section', 'type' => 'number', ));
	//post title font variant
	$wp_customize->add_setting('byline_font_variant', array('default' => $defaults['byline_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('byline_font_variant_control', array('label' => __( 'Byline Font Variant', 'convertica-td' ), 'settings' => 'byline_font_variant', 'choices' => convertica_choices('byline_font_variant'), 'section' => 'convertica_byline_section', 'type' => 'select'));
	//post title text color
	$wp_customize->add_setting('byline_color', array('default' => $defaults['byline_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'byline_color_control', array('label' => __( 'Byline Color', 'convertica-td' ), 'settings' => 'byline_color', 'section' => 'convertica_byline_section', 'type' => 'color')));

	//Sidebar Widgets
	$wp_customize->add_section( 'convertica_sb_widget_section', array('title' => __( 'Sidebar Widgets', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for Widgets inside sidebars.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//widget title font family
	$wp_customize->add_setting('sb_widget_title_font_family', array('default' => $defaults['sb_widget_title_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('sb_widget_title_font_family_control', array('label' => __( 'Widget Title Font Family', 'convertica-td' ), 'settings' => 'sb_widget_title_font_family', 'choices' => convertica_choices('sb_widget_title_font_family'), 'section' => 'convertica_sb_widget_section', 'type' => 'select' ));
	//sb_widget_title_font_size
	$wp_customize->add_setting('sb_widget_title_font_size', array('default' => $defaults['sb_widget_title_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('sb_widget_title_font_size_control', array('label' => __( 'Widget Title Font Size', 'convertica-td' ), 'settings' => 'sb_widget_title_font_size', 'section' => 'convertica_sb_widget_section', 'type' => 'number', ));
	//widget title font variant
	$wp_customize->add_setting('sb_widget_title_font_variant', array('default' => $defaults['sb_widget_title_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('sb_widget_title_font_variant_control', array('label' => __( 'Widget Title Font Variant', 'convertica-td' ), 'settings' => 'sb_widget_title_font_variant', 'choices' => convertica_choices('sb_widget_title_font_variant'), 'section' => 'convertica_sb_widget_section', 'type' => 'select'));
	//widget title text color
	$wp_customize->add_setting('sb_widget_title_color', array('default' => $defaults['sb_widget_title_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sb_widget_title_color_control', array('label' => __( 'Widget Title Color', 'convertica-td' ), 'settings' => 'sb_widget_title_color', 'section' => 'convertica_sb_widget_section', 'type' => 'color')));
	//widget body font family
	$wp_customize->add_setting('sb_widget_body_font_family', array('default' => $defaults['sb_widget_body_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('sb_widget_body_font_family_control', array('label' => __( 'Widget Body Font Family', 'convertica-td' ), 'settings' => 'sb_widget_body_font_family', 'choices' => convertica_choices('sb_widget_body_font_family'), 'section' => 'convertica_sb_widget_section', 'type' => 'select' ));
	//sb_widget_body_font_size
	$wp_customize->add_setting('sb_widget_body_font_size', array('default' => $defaults['sb_widget_body_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('sb_widget_body_font_size_control', array('label' => __( 'Widget Body Font Size', 'convertica-td' ), 'settings' => 'sb_widget_body_font_size', 'section' => 'convertica_sb_widget_section', 'type' => 'number', ));
	//widget body font variant
	$wp_customize->add_setting('sb_widget_body_font_variant', array('default' => $defaults['sb_widget_body_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('sb_widget_body_font_variant_control', array('label' => __( 'Widget Body Font Variant', 'convertica-td' ), 'settings' => 'sb_widget_body_font_variant', 'choices' => convertica_choices('sb_widget_body_font_variant'), 'section' => 'convertica_sb_widget_section', 'type' => 'select'));
	//widget body text color
	$wp_customize->add_setting('sb_widget_body_color', array('default' => $defaults['sb_widget_body_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sb_widget_body_color_control', array('label' => __( 'Widget Body Color', 'convertica-td' ), 'settings' => 'sb_widget_body_color', 'section' => 'convertica_sb_widget_section', 'type' => 'color')));

	//Extra Widgets
	$wp_customize->add_section( 'convertica_extra_widgets_section', array('title' => __( 'Convertica CTA Widgets', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for Widgets inside CTA areas.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//widget title font family
	$wp_customize->add_setting('extra_widgets_title_font_family', array('default' => $defaults['extra_widgets_title_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('extra_widgets_title_font_family_control', array('label' => __( 'Widget Title Font Family', 'convertica-td' ), 'settings' => 'extra_widgets_title_font_family', 'choices' => convertica_choices('extra_widgets_title_font_family'), 'section' => 'convertica_extra_widgets_section', 'type' => 'select' ));
	//extra_widgets_title_font_size
	$wp_customize->add_setting('extra_widgets_title_font_size', array('default' => $defaults['extra_widgets_title_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('extra_widgets_title_font_size_control', array('label' => __( 'Widget Title Font Size', 'convertica-td' ), 'settings' => 'extra_widgets_title_font_size', 'section' => 'convertica_extra_widgets_section', 'type' => 'number', ));
	//widget title font variant
	$wp_customize->add_setting('extra_widgets_title_font_variant', array('default' => $defaults['extra_widgets_title_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('extra_widgets_title_font_variant_control', array('label' => __( 'Widget Title Font Variant', 'convertica-td' ), 'settings' => 'extra_widgets_title_font_variant', 'choices' => convertica_choices('extra_widgets_title_font_variant'), 'section' => 'convertica_extra_widgets_section', 'type' => 'select'));
	//widget title text color
	$wp_customize->add_setting('extra_widgets_title_color', array('default' => $defaults['extra_widgets_title_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'extra_widgets_title_color_control', array('label' => __( 'Widget Title Color', 'convertica-td' ), 'settings' => 'extra_widgets_title_color', 'section' => 'convertica_extra_widgets_section', 'type' => 'color')));
	//widget body font family
	$wp_customize->add_setting('extra_widgets_body_font_family', array('default' => $defaults['extra_widgets_body_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('extra_widgets_body_font_family_control', array('label' => __( 'Widget Body Font Family', 'convertica-td' ), 'settings' => 'extra_widgets_body_font_family', 'choices' => convertica_choices('extra_widgets_body_font_family'), 'section' => 'convertica_extra_widgets_section', 'type' => 'select' ));
	//extra_widgets_body_font_size
	$wp_customize->add_setting('extra_widgets_body_font_size', array('default' => $defaults['extra_widgets_body_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('extra_widgets_body_font_size_control', array('label' => __( 'Widget Body Font Size', 'convertica-td' ), 'settings' => 'extra_widgets_body_font_size', 'section' => 'convertica_extra_widgets_section', 'type' => 'number', ));
	//widget body font variant
	$wp_customize->add_setting('extra_widgets_body_font_variant', array('default' => $defaults['extra_widgets_body_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('extra_widgets_body_font_variant_control', array('label' => __( 'Widget Body Font Variant', 'convertica-td' ), 'settings' => 'extra_widgets_body_font_variant', 'choices' => convertica_choices('extra_widgets_body_font_variant'), 'section' => 'convertica_extra_widgets_section', 'type' => 'select'));
	//widget body text color
	$wp_customize->add_setting('extra_widgets_body_color', array('default' => $defaults['extra_widgets_body_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'extra_widgets_body_color_control', array('label' => __( 'Widget Body Color', 'convertica-td' ), 'settings' => 'extra_widgets_body_color', 'section' => 'convertica_extra_widgets_section', 'type' => 'color')));

	//Footer
	$wp_customize->add_section( 'convertica_footer_section', array('title' => __( 'Footer', 'convertica-td' ),'priority' => 35,'description' => __( 'Settings for footer.', 'convertica-td' ),'panel' => 'convertica_edge_panel'));
	//widget title font family
	$wp_customize->add_setting('footer_font_family', array('default' => $defaults['footer_font_family'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('footer_font_family_control', array('label' => __( 'Footer Font Family', 'convertica-td' ), 'settings' => 'footer_font_family', 'choices' => convertica_choices('footer_font_family'), 'section' => 'convertica_footer_section', 'type' => 'select' ));
	//footer_font_size
	$wp_customize->add_setting('footer_font_size', array('default' => $defaults['footer_font_size'], 'type' => $settings_type, 'transport' => $transport, 'sanitize_callback' => 'absint'));
	$wp_customize->add_control('footer_font_size_control', array('label' => __( 'Footer Font Size', 'convertica-td' ), 'settings' => 'footer_font_size', 'section' => 'convertica_footer_section', 'type' => 'number', ));
	//widget title font variant
	$wp_customize->add_setting('footer_font_variant', array('default' => $defaults['footer_font_variant'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control('footer_font_variant_control', array('label' => __( 'Footer Font Variant', 'convertica-td' ), 'settings' => 'footer_font_variant', 'choices' => convertica_choices('footer_font_variant'), 'section' => 'convertica_footer_section', 'type' => 'select'));
	//widget title text color
	$wp_customize->add_setting('footer_color', array('default' => $defaults['footer_color'], 'type' => $settings_type, 'transport' => $transport));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_color_control', array('label' => __( 'Footer Color', 'convertica-td' ), 'settings' => 'footer_color', 'section' => 'convertica_footer_section', 'type' => 'color')));
}

add_filter( 'convertica_settings_choices', 'convertica_edge_choices' );

function convertica_edge_choices( $choices ){
	
	$font_choices = convertica_edge_get_fonts_choices();
	$choices['body_font_family'] = $font_choices;
	$choices['body_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('body_font_family'));
	$choices['form_field_font_family'] = $font_choices;
	$choices['site_title_font_family'] = $font_choices;
	$choices['site_title_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('site_title_font_family'));
	$choices['site_tagline_font_family'] = $font_choices;
	$choices['site_tagline_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('site_tagline_font_family'));
	$choices['before_header_nav_font_family'] = $font_choices;
	$choices['before_header_nav_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('before_header_nav_font_family'));
	$choices['after_header_nav_font_family'] = $font_choices;
	$choices['after_header_nav_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('after_header_nav_font_family'));
	$choices['entry_title_font_family'] = $font_choices;
	$choices['entry_title_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('entry_title_font_family'));
	$choices['content_headlines_font_family'] = $font_choices;
	$choices['content_headlines_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('content_headlines_font_family'));
	$choices['byline_font_family'] = $font_choices;
	$choices['byline_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('byline_font_family'));
	$choices['sb_widget_title_font_family'] = $font_choices;
	$choices['sb_widget_title_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('sb_widget_title_font_family'));
	$choices['sb_widget_body_font_family'] = $font_choices;
	$choices['sb_widget_body_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('sb_widget_body_font_family'));
	$choices['extra_widgets_title_font_family'] = $font_choices;
	$choices['extra_widgets_title_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('extra_widgets_title_font_family'));
	$choices['extra_widgets_body_font_family'] = $font_choices;
	$choices['extra_widgets_body_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('extra_widgets_body_font_family'));
	$choices['footer_font_family'] = $font_choices;
	$choices['footer_font_variant'] = convertica_edge_get_font_variants(convertica_get_mod('footer_font_family'));
	
	return apply_filters( 'convertica_edge_choices', $choices );
	
}

function convertica_edge_settings_defaults( $defaults ){
	
	//$time_start = microtime(true); 
	$defaults['body_font_family'] = 'helvetica';
	//$defaults['body_font_specs'] = convertica_edge_get_font($defaults['body_font_family']);
	$defaults['body_font_size'] = '14';
	//$variants = convertica_edge_get_font_variants($defaults['body_font_family']);
	//$defaults['body_font_variant'] = array_shift($variants);
	$defaults['body_font_variant'] = '400';
	$defaults['body_font_color'] = '#333333';
	$defaults['body_link_color'] = '#cf4944';
	$defaults['body_link_color_hover'] = '#2361a1';
	$defaults['form_field_font_family'] = 'helvetica';
	//$defaults['form_field_font_specs'] = convertica_edge_get_font($defaults['form_field_font_family']);
	$defaults['form_field_font_size'] = '14';
	$defaults['form_field_font_color'] = '#000';
	$defaults['site_title_font_family'] = 'inherit';
	//$defaults['site_title_font_specs'] = convertica_edge_get_font($defaults['site_title_font_family']);
	$defaults['site_title_font_size'] = '32';
	//$variants = convertica_edge_get_font_variants($defaults['site_title_font_family']);
	//$defaults['site_title_font_variant'] = array_shift($variants);
	$defaults['site_title_font_variant'] = '700';
	$defaults['site_title_color'] = '#333333';
	$defaults['site_tagline_font_family'] = 'inherit';
	//$defaults['site_tagline_font_specs'] = convertica_edge_get_font($defaults['site_tagline_font_family']);
	$defaults['site_tagline_font_size'] = '16';
	//$variants = convertica_edge_get_font_variants($defaults['site_tagline_font_family']);
	//$defaults['site_tagline_font_variant'] = array_shift($variants);
	$defaults['site_tagline_font_variant'] = '400';
	$defaults['site_tagline_color'] = '#333333';
	$defaults['before_header_nav_font_family'] = 'inherit';
	//$defaults['before_header_nav_font_specs'] = convertica_edge_get_font($defaults['before_header_nav_font_family']);
	$defaults['before_header_nav_font_size'] = '13';
	//$variants = convertica_edge_get_font_variants($defaults['before_header_nav_font_family']);
	//$defaults['before_header_nav_font_variant'] = array_shift($variants);
	$defaults['before_header_nav_font_variant'] = '400';
	$defaults['before_header_nav_text_color'] = '#555555';
	$defaults['before_header_nav_hover_text_color'] = '#333333';
	$defaults['before_header_nav_current_link_text_color'] = '#333333';
	$defaults['before_header_nav_parent_nav_link_text_color'] = '#333333';
	$defaults['before_header_nav_link_background_color'] = '#e0e7ea';
	$defaults['before_header_nav_link_hover_background_color'] = '#d5dbde';
	$defaults['before_header_nav_current_link_background_color'] = '#fff';
	$defaults['before_header_nav_parent_nav_link_background_color'] = '#d5dbde';
	$defaults['before_header_nav_border_width'] = '1';
	$defaults['before_header_nav_border_color'] = '#dddddd';
	$defaults['before_header_nav_submenu_width'] = '200';
	$defaults['after_header_nav_font_family'] = 'inherit';
	//$defaults['after_header_nav_font_specs'] = convertica_edge_get_font($defaults['after_header_nav_font_family']);
	$defaults['after_header_nav_font_size'] = '13';
	//$variants = convertica_edge_get_font_variants($defaults['after_header_nav_font_family']);
	//$defaults['after_header_nav_font_variant'] = array_shift($variants);
	$defaults['after_header_nav_font_variant'] = '400';
	$defaults['after_header_nav_text_color'] = '#555555';
	$defaults['after_header_nav_hover_text_color'] = '#333333';
	$defaults['after_header_nav_current_link_text_color'] = '#333333';
	$defaults['after_header_nav_parent_nav_link_text_color'] = '#333333';
	$defaults['after_header_nav_link_background_color'] = '#e0e7ea';
	$defaults['after_header_nav_link_hover_background_color'] = '#d5dbde';
	$defaults['after_header_nav_current_link_background_color'] = '#fff';
	$defaults['after_header_nav_parent_nav_link_background_color'] = '#d5dbde';
	$defaults['after_header_nav_border_width'] = '1';
	$defaults['after_header_nav_border_color'] = '#dddddd';
	$defaults['after_header_nav_submenu_width'] = '200';
	$defaults['entry_title_font_family'] = 'inherit';
	//$defaults['entry_title_font_specs'] = convertica_edge_get_font($defaults['entry_title_font_family']);
	$defaults['entry_title_font_size'] = '26';
	//$variants = convertica_edge_get_font_variants($defaults['entry_title_font_family']);
	//$defaults['entry_title_font_variant'] = array_shift($variants);
	$defaults['entry_title_font_variant'] = '700';
	$defaults['entry_title_color'] = '#333';
	$defaults['content_headlines_font_family'] = 'inherit';
	//$defaults['content_headlines_font_specs'] = convertica_edge_get_font($defaults['content_headlines_font_family']);
	//$variants = convertica_edge_get_font_variants($defaults['content_headlines_font_family']);
	//$defaults['content_headlines_font_variant'] = array_shift($variants);
	$defaults['content_headlines_font_variant'] = '400';
	$defaults['content_headlines_color'] = '#333';
	$defaults['byline_font_family'] = 'helvetica';
	//$defaults['byline_font_specs'] = convertica_edge_get_font($defaults['byline_font_family']);
	$defaults['byline_font_size'] = '12';
	//$variants = convertica_edge_get_font_variants($defaults['byline_font_family']);
	//$defaults['byline_font_variant'] = array_shift($variants);
	$defaults['byline_font_variant'] = '400';
	$defaults['byline_color'] = '#888';
	$defaults['sb_widget_title_font_family'] = 'inherit';
	//$defaults['sb_widget_title_font_specs'] = convertica_edge_get_font($defaults['sb_widget_title_font_family']);
	$defaults['sb_widget_title_font_size'] = '16';
	//$variants = convertica_edge_get_font_variants($defaults['sb_widget_title_font_family']);
	//$defaults['sb_widget_title_font_variant'] = array_shift($variants);
	$defaults['sb_widget_title_font_variant'] = '700';
	$defaults['sb_widget_title_color'] = '#333';
	$defaults['sb_widget_body_font_family'] = 'inherit';
	//$defaults['sb_widget_body_font_specs'] = convertica_edge_get_font($defaults['sb_widget_body_font_family']);
	$defaults['sb_widget_body_font_size'] = '14';
	//$variants = convertica_edge_get_font_variants($defaults['sb_widget_body_font_family']);
	//$defaults['sb_widget_body_font_variant'] = array_shift($variants);
	$defaults['sb_widget_body_font_variant'] = '400';
	$defaults['sb_widget_body_color'] = '#333';
	$defaults['extra_widgets_title_font_family'] = 'inherit';
	//$defaults['extra_widgets_title_font_specs'] = convertica_edge_get_font($defaults['extra_widgets_title_font_family']);
	$defaults['extra_widgets_title_font_size'] = '16';
	//$variants = convertica_edge_get_font_variants($defaults['extra_widgets_title_font_family']);
	//$defaults['extra_widgets_title_font_variant'] = array_shift($variants);
	$defaults['extra_widgets_title_font_variant'] = '700';
	$defaults['extra_widgets_title_color'] = '#333';
	$defaults['extra_widgets_body_font_family'] = 'inherit';
	//$defaults['extra_widgets_body_font_specs'] = convertica_edge_get_font($defaults['extra_widgets_body_font_family']);
	$defaults['extra_widgets_body_font_size'] = '14';
	//$variants = convertica_edge_get_font_variants($defaults['extra_widgets_body_font_family']);
	//$defaults['extra_widgets_body_font_variant'] = array_shift($variants);
	$defaults['extra_widgets_body_font_variant'] = '400';
	$defaults['extra_widgets_body_color'] = '#333';
	$defaults['footer_font_family'] = 'inherit';
	//$defaults['footer_font_specs'] = convertica_edge_get_font($defaults['footer_font_family']);
	$defaults['footer_font_size'] = '13';
	//$variants = convertica_edge_get_font_variants($defaults['footer_font_family']);
	//$defaults['footer_font_variant'] = array_shift($variants);
	$defaults['footer_font_variant'] = '400';
	$defaults['footer_color'] = '#888';
	//$time_end = microtime(true);
	//$execution_time = ($time_end - $time_start)/60;
	//echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
	return $defaults;
	
}

function convertica_line_height( $width, $font_size ) {	
	return 1.618 - (1 / (2 * 1.618) ) * ( 1 - ( $width / pow( ( $font_size * 1.618 ) , 2 ) ) );
}

function convertica_edge_settings_css( $css ) {
	$body_class = get_option( 'stylesheet' );

	$size__site_content_1c     = convertica_get_mod('layout_content_1c');
	$size__site_content_2c     = convertica_get_mod('layout_content_2c');
	$size__site_content_3c     = convertica_get_mod('layout_content_3c');

	$settings_css = '
	
	/*line-heights*/
	.layout-1c-l .entry,
	.layout-1c-l .comment-content,
	.layout-1c-l .archive-description {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_1c'), convertica_get_mod('body_font_size') ).';
	}
	
	.layout-2c-l .entry,
	.layout-2c-l .comment-content,
	.layout-2c-r .entry,
	.layout-2c-r .comment-content,
	.layout-2c-l .archive-description,
	.layout-2c-r .archive-description {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_2c'), convertica_get_mod('body_font_size') ).';
	}

	.layout-3c-l .entry,
	.layout-3c-r .entry,
	.layout-3c-c .entry,
	.layout-3c-l .comment-content,
	.layout-3c-r .comment-content,
	.layout-3c-c .comment-content,
	.layout-3c-l .archive-description,
	.layout-3c-r .archive-description,
	.layout-3c-c .archive-description {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_3c'), convertica_get_mod('body_font_size') ).';
	}

	.layout-1c-l .entry-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_1c'),convertica_get_mod('entry_title_font_size')).';
	}
	
	.layout-2c-l .entry-title,
	.layout-2c-r .entry-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_2c'),convertica_get_mod('entry_title_font_size')).';
	}

	.layout-3c-l .entry-title,
	.layout-3c-r .entry-title,
	.layout-3c-c .entry-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_3c'),convertica_get_mod('entry_title_font_size')).';
	}

	/* SB Primary 2 cols */
	.layout-2c-l .sidebar-primary .widget,
	.layout-2c-r .sidebar-primary .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_2c') ,convertica_get_mod('sb_widget_body_font_size')).';
	}

	.layout-2c-l .sidebar-primary .widget-title,
	.layout-2c-r .sidebar-primary .widget-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_2c') ,convertica_get_mod('sb_widget_body_font_size')).';
	}

	/* SB Primary 3 cols */
	.layout-3c-l .sidebar-primary .widget,
	.layout-3c-r .sidebar-primary .widget,
	.layout-3c-c .sidebar-primary .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_3c') ,convertica_get_mod('sb_widget_body_font_size')).';
	}

	.layout-3c-l .sidebar-primary .widget-title,
	.layout-3c-r .sidebar-primary .widget-title,
	.layout-3c-c .sidebar-primary .widget-title  {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_3c') ,convertica_get_mod('sb_widget_title_font_size')).';
	}
	
	/* SB Subsidiary 3 cols */
	.layout-3c-l .sidebar-subsidiary .widget,
	.layout-3c-r .sidebar-subsidiary .widget,
	.layout-3c-c .sidebar-subsidiary .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_sub_3c') ,convertica_get_mod('sb_widget_body_font_size')).';
	}

	.layout-3c-l .sidebar-subsidiary .widget-title,
	.layout-3c-r .sidebar-subsidiary .widget-title,
	.layout-3c-c .sidebar-subsidiary .widget-title  {
		line-height: '.convertica_line_height(convertica_get_mod('layout_sb_sub_3c') ,convertica_get_mod('sb_widget_title_font_size')).';
	}

	.layout-1c-l #sidebar-before_header .widget,
	.layout-1c-l #sidebar-after_header .widget,
	.layout-1c-l #sidebar-before_footer .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_1c'),convertica_get_mod('extra_widgets_body_font_size')).';
	}

	.layout-1c-l #sidebar-before_header .widget-title,
	.layout-1c-l #sidebar-after_header .widget-title,
	.layout-1c-l #sidebar-before_footer .widget-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_1c'),convertica_get_mod('extra_widgets_title_font_size')).';
	}	


	.layout-2c-l #sidebar-before_header .widget,
	.layout-2c-l #sidebar-after_header .widget,
	.layout-2c-l #sidebar-before_footer .widget,
	.layout-2c-r #sidebar-before_header .widget,
	.layout-2c-r #sidebar-after_header .widget,
	.layout-2c-r #sidebar-before_footer .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_2c'),convertica_get_mod('extra_widgets_body_font_size')).';
	}

	.layout-2c-l #sidebar-before_header .widget-title,
	.layout-2c-l #sidebar-after_header .widget-title,
	.layout-2c-l #sidebar-before_footer .widget-title,
	.layout-2c-r #sidebar-before_header .widget-title,
	.layout-2c-r #sidebar-after_header .widget-title,
	.layout-2c-r #sidebar-before_footer .widget-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_2c'),convertica_get_mod('extra_widgets_title_font_size')).';
	}

	.layout-3c-l #sidebar-before_header .widget,
	.layout-3c-l #sidebar-after_header .widget,
	.layout-3c-l #sidebar-before_footer .widget,
	.layout-3c-r #sidebar-before_header .widget,
	.layout-3c-r #sidebar-after_header .widget,
	.layout-3c-r #sidebar-before_footer .widget,
	.layout-3c-c #sidebar-before_header .widget,
	.layout-3c-c #sidebar-after_header .widget,
	.layout-3c-c #sidebar-before_footer .widget {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_2c'),convertica_get_mod('extra_widgets_body_font_size')).';
	}

	.layout-3c-l #sidebar-before_header .widget-title,
	.layout-3c-l #sidebar-after_header .widget-title,
	.layout-3c-l #sidebar-before_footer .widget-title,
	.layout-3c-r #sidebar-before_header .widget-title,
	.layout-3c-r #sidebar-after_header .widget-title,
	.layout-3c-r #sidebar-before_footer .widget-title,
	.layout-3c-c #sidebar-before_header .widget-title,
	.layout-3c-c #sidebar-after_header .widget-title,
	.layout-3c-c #sidebar-before_footer .widget-title {
		line-height: '.convertica_line_height(convertica_get_mod('layout_content_3c'),convertica_get_mod('extra_widgets_title_font_size')).';
	}

	/*
	todo: what happens to line-height on smaller viewports?
	todo: implement line-height: for CTA widgets
	*/
	/*body*/
	body.'.$body_class.' {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('body_font_family')).';
		font-size: '.convertica_get_mod('body_font_size').'px;
		font-weight: '.convertica_get_font_weight(convertica_get_mod('body_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('body_font_variant')).';
		color: '.convertica_get_mod('body_font_color').';
	}
	a {
		color: '.convertica_get_mod('body_link_color').';
	}	
	a:hover,
	a:focus,
	a:active {
			color: '.convertica_get_mod('body_link_color_hover').';
	}
	body.'.$body_class.' #respond input[type="text"],
	body.'.$body_class.' #respond input[type="url"],
	body.'.$body_class.' #respond input[type="email"],
	body.'.$body_class.' #respond textarea,
	body.'.$body_class.' input[type="search"] {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('form_field_font_family')).';
		font-size: '.convertica_get_mod('form_field_font_size').'px;
		color: '.convertica_get_mod('form_field_font_color').';
	}
	/*header*/
	body.'.$body_class.' #site-title > a {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('site_title_font_family')).';
		font-size: '.convertica_get_mod('site_title_font_size').'px;
		font-weight: '.convertica_get_font_weight(convertica_get_mod('site_title_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('site_title_font_variant')).';
		color: '.convertica_get_mod('site_title_color').' !important;
	}
	body.'.$body_class.' #site-description {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('site_tagline_font_family')).';
		font-size: '.convertica_get_mod('site_tagline_font_size').'px;
		font-weight: '.convertica_get_font_weight(convertica_get_mod('site_tagline_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('site_tagline_font_variant')).';
		color: '.convertica_get_mod('site_tagline_color').';
	}
	/*primary nav*/
	body.'.$body_class.' #menu-before_header {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('before_header_nav_font_family')).';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('before_header_nav_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('before_header_nav_font_variant')).';
	}
	body.'.$body_class.' #menu-before_header li{
		margin-bottom: -'.convertica_get_mod('before_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-before_header ul {
		border-left: '.convertica_get_mod('before_header_nav_border_width').'px solid '.convertica_get_mod('before_header_nav_border_color').';
		border-bottom: '.convertica_get_mod('before_header_nav_border_width').'px solid '.convertica_get_mod('before_header_nav_border_color').';
	}
	body.'.$body_class.' #menu-before_header ul ul {
		mmargin-top: -'.convertica_get_mod('before_header_nav_border_width').'px;
		width: '.convertica_get_mod('before_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-before_header ul ul li:hover > ul,
	body.'.$body_class.' #menu-before_header ul ul li.focus > ul {
		margin-left: -'.convertica_get_mod('before_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-before_header ul ul a {
	  width: '.convertica_get_mod('before_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-before_header ul li:hover > ul,
	body.'.$body_class.' #menu-before_header ul li.focus > ul {
		left: -'.convertica_get_mod('before_header_nav_border_width').'px;
		
	}
	body.'.$body_class.' #menu-before_header ul li:hover > ul li:hover > ul,
	body.'.$body_class.' #menu-before_header ul li.focus > ul li:hover > ul {
		left: '.convertica_get_mod('before_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-before_header .sub-menu .sub-menu
		margin-top: '.convertica_get_mod('before_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-before_header li {
		margin-bottom: -'.convertica_get_mod('before_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-before_header li:hover > a,
	body.'.$body_class.' #menu-before_header li.focus > a {
		color: '.convertica_get_mod('before_header_nav_hover_text_color').';
		background-color: '.convertica_get_mod('before_header_nav_link_hover_background_color').';
	}
	body.'.$body_class.' #menu-before_header a {
		color: '.convertica_get_mod('before_header_nav_text_color').';
		background-color:'.convertica_get_mod('before_header_nav_link_background_color').';
		border: '.convertica_get_mod('before_header_nav_border_width').'px solid '.convertica_get_mod('before_header_nav_border_color').';
		border-left: 0;
		font-size: '.convertica_get_mod('before_header_nav_font_size').'px;
	}
	body.'.$body_class.' #menu-before_header .current_page_item > a,
	body.'.$body_class.' #menu-before_header .current-menu-item > a {
		color: '.convertica_get_mod('before_header_nav_current_link_text_color').';
		background-color: '.convertica_get_mod('before_header_nav_current_link_background_color').';
		border-bottom-color: transparent;
	}
	body.'.$body_class.' #menu-before_header .current_page_ancestor > a {
		color: '.convertica_get_mod('before_header_nav_parent_nav_link_text_color').';
		background-color: '.convertica_get_mod('before_header_nav_parent_nav_link_background_color').';
	}
	body.'.$body_class.' #menu-before_header .slicknav_btn {
		border-left: '.convertica_get_mod('before_header_nav_border_width').'px solid '.convertica_get_mod('before_header_nav_border_color').';
	}
	/*secondary nav*/
	body.'.$body_class.' #menu-after_header {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('after_header_nav_font_family')).';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('after_header_nav_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('after_header_nav_font_variant')).';
	}
	body.'.$body_class.' #menu-after_header li{
		margin-bottom: -'.convertica_get_mod('after_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-after_header ul {
		border-left: '.convertica_get_mod('after_header_nav_border_width').'px solid '.convertica_get_mod('after_header_nav_border_color').';
		border-bottom: '.convertica_get_mod('after_header_nav_border_width').'px solid '.convertica_get_mod('after_header_nav_border_color').';
	}
	body.'.$body_class.' #menu-after_header ul ul {
		mmargin-top: -'.convertica_get_mod('after_header_nav_border_width').'px;
		width: '.convertica_get_mod('after_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-after_header ul ul li:hover > ul,
	body.'.$body_class.' #menu-after_header ul ul li.focus > ul {
		margin-left: -'.convertica_get_mod('after_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-after_header ul ul a {
	  width: '.convertica_get_mod('after_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-after_header ul li:hover > ul,
	body.'.$body_class.' #menu-after_header ul li.focus > ul {
		left: -'.convertica_get_mod('after_header_nav_border_width').'px;
		
	}
	body.'.$body_class.' #menu-after_header ul li:hover > ul li:hover > ul,
	body.'.$body_class.' #menu-after_header ul li.focus > ul li:hover > ul {
		left: '.convertica_get_mod('after_header_nav_submenu_width').'px;
	}
	body.'.$body_class.' #menu-after_header .sub-menu .sub-menu
		margin-top: '.convertica_get_mod('after_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-after_header li {
		margin-bottom: -'.convertica_get_mod('after_header_nav_border_width').'px;
	}
	body.'.$body_class.' #menu-after_header li:hover > a,
	body.'.$body_class.' #menu-after_header li.focus > a {
		color: '.convertica_get_mod('after_header_nav_hover_text_color').';
		background-color: '.convertica_get_mod('after_header_nav_link_hover_background_color').';
	}
	body.'.$body_class.' #menu-after_header a {
		color: '.convertica_get_mod('after_header_nav_text_color').';
		background-color:'.convertica_get_mod('after_header_nav_link_background_color').';
		border: '.convertica_get_mod('after_header_nav_border_width').'px solid '.convertica_get_mod('after_header_nav_border_color').';
		border-left: 0;
		font-size: '.convertica_get_mod('after_header_nav_font_size').'px;
	}
	body.'.$body_class.' #menu-after_header .current_page_item > a,
	body.'.$body_class.' #menu-after_header .current-menu-item > a {
		color: '.convertica_get_mod('after_header_nav_current_link_text_color').';
		background-color: '.convertica_get_mod('after_header_nav_current_link_background_color').';
		border-bottom-color: transparent;
	}
	body.'.$body_class.' #menu-after_header .current_page_ancestor > a {
		color: '.convertica_get_mod('after_header_nav_parent_nav_link_text_color').';
		background-color: '.convertica_get_mod('after_header_nav_parent_nav_link_background_color').';
	}
	body.'.$body_class.' #menu-after_header .slicknav_btn {
		border-left: '.convertica_get_mod('after_header_nav_border_width').'px solid '.convertica_get_mod('after_header_nav_border_color').';
	}
	/* entry-titles */
	body.'.$body_class.' .entry-title, body.'.$body_class.' .archive-title {
		font-family:'.convertica_edge_get_font_family(convertica_get_mod('entry_title_font_family')).';
		font-size: '.convertica_get_mod('entry_title_font_size').'px;
		font-weight: '.convertica_get_font_weight(convertica_get_mod('entry_title_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('entry_title_font_variant')).';
		color: '.convertica_get_mod('entry_title_color').';
	}
	body.'.$body_class.' .entry-title a,
	body.'.$body_class.' .entry-title a:visited {
		color: '.convertica_get_mod('entry_title_color').';
	}
	/*Headlines*/
	body.'.$body_class.' .entry-content h1, body.'.$body_class.' .comment-content h1,
	body.'.$body_class.' .entry-content h2, body.'.$body_class.' .comment-content h2,
	body.'.$body_class.' .entry-content h3, body.'.$body_class.' .comment-content h3,
	body.'.$body_class.' .entry-content h4, body.'.$body_class.' .comment-content h4,
	body.'.$body_class.' .entry-content h5, body.'.$body_class.' .comment-content h5,
	body.'.$body_class.' .entry-content h6, body.'.$body_class.' .comment-content h6,
	body.'.$body_class.' .landing-section-title {
			font-family: '.convertica_edge_get_font_family(convertica_get_mod('content_headlines_font_family')).';
			font-weight: '.convertica_get_font_weight(convertica_get_mod('content_headlines_font_variant')).';
			font-style: '.convertica_get_font_style(convertica_get_mod('content_headlines_font_variant')).';
			color: '.convertica_get_mod('content_headlines_color').';
		}
	/*bylines*/
	body.'.$body_class.' .entry-byline {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('byline_font_family')).';
		font-size: '.convertica_get_mod('byline_font_size').'px;
		color: '.convertica_get_mod('byline_color').';
		font-style: '.convertica_get_font_style(convertica_get_mod('byline_font_variant')).';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('byline_font_variant')).';
	}
	body.'.$body_class.' .entry-byline a{
		color: '.convertica_get_mod('byline_color').';
	}
	/*sidebars*/
	body.'.$body_class.' .sidebar-primary .widget,
	body.'.$body_class.' .sidebar-subsidiary .widget {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('sb_widget_body_font_family')).';
		font-size: '.convertica_get_mod('sb_widget_body_font_size').'px;
		color: '.convertica_get_mod('sb_widget_body_color').';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('sb_widget_body_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('sb_widget_body_font_variant')).';
	}
	body.'.$body_class.' .sidebar-primary .widget-title,
	body.'.$body_class.' .sidebar-subsidiary .widget-title {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('sb_widget_title_font_family')).';
		font-size: '.convertica_get_mod('sb_widget_title_font_size').'px;
		color: '.convertica_get_mod('sb_widget_title_color').';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('sb_widget_title_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('sb_widget_title_font_variant')).';
	}
	/*CTA Widget Areas*/
	body.'.$body_class.' .sidebar-before_header .widget,
	body.'.$body_class.' .sidebar-after_header .widget,
	body.'.$body_class.' .sidebar-before_footer .widget {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('extra_widgets_body_font_family')).';
		font-size: '.convertica_get_mod('extra_widgets_body_font_size').'px;
		color: '.convertica_get_mod('extra_widgets_body_color').';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('extra_widgets_body_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('extra_widgets_body_font_variant')).';
	}
	body.'.$body_class.' .sidebar-before_header .widget-title,
	body.'.$body_class.' .sidebar-after_header .widget-title,
	body.'.$body_class.' .sidebar-before_footer .widget-title {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('extra_widgets_title_font_family')).';
		font-size: '.convertica_get_mod('extra_widgets_title_font_size').'px;
		color: '.convertica_get_mod('extra_widgets_title_color').';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('extra_widgets_title_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('extra_widgets_title_font_variant')).';
	}
	/*footer*/
	body.'.$body_class.' .site-footer {
		font-family: '.convertica_edge_get_font_family(convertica_get_mod('footer_font_family')).';
		font-size: '.convertica_get_mod('footer_font_size').'px;
		color: '.convertica_get_mod('footer_color').';
		font-weight: '.convertica_get_font_weight(convertica_get_mod('footer_font_variant')).';
		font-style: '.convertica_get_font_style(convertica_get_mod('footer_font_variant')).';
	}
	';
	
	return $css . $settings_css;
}
