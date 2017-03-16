<?php
/**
 * 
 * 
 * 
 * THIS FILE IS A PART OF THE OLD HYBRID CORE 2.0 LIBRARY AND HAS BEEN MODIFIED AS PER THE LICENCE INCLUDED IN HYBRID CORE 2.0 DISTRIBUTION TO 
 * HELP US IMPLEMENT A SETTINGS PAGE.
 * 
 * 
 * 
 * Handles the display and functionality of the theme settings page. This provides the needed hooks and
 * meta box calls for developers to create any number of theme settings needed. This file is only loaded if 
 * the theme supports the 'hybrid-core-theme-settings' feature.
 *
 * Provides the ability for developers to add custom meta boxes to the theme settings page by using the 
 * add_meta_box() function.  Developers should register their meta boxes on the 'add_meta_boxes' hook 
 * and register the meta box for 'appearance_page_theme-settings'.  To validate/sanitize data from 
 * custom settings, devs should use the 'sanitize_option_{$prefix}_theme_settings' filter hook.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


class Convertica_Settings_Base {

	public $options_field;

	public $page_title; // = $theme->get( 'Name' );
	public $menu_title; // = $theme->get( 'Name' ).' Settings';
	
	public function init(){

		/* Hook the settings page function to 'admin_menu'. */
		if ( isset( $this->page_type ) && $this->page_type == 'settings' ) {
			add_action( 'admin_menu',  array( $this, 'convertica_settings_page_init' ) );
		}
		
		if ( isset( $this->page_type ) && $this->page_type == 'basic' ) {
			add_action( 'admin_menu',  array( $this, 'convertica_basic_admin_page_init' ) );
		}
		
		/* Register the settings field for theme admin page */
		add_action( 'admin_init',  array( $this, 'convertica_settings_page_register_settings' ) );
		add_action( 'admin_init',  array( $this, 'convertica_basic_admin_page_setup' ) );
		
		/* Create a settings meta box only on the theme settings page. */
		add_action( "load-appearance_page_$this->slug", array( $this, 'meta_boxes' ) ); //appearance_page_convertica-theme-settings
		
		/* Load contextual assets (registered admin page) */
		add_action( 'admin_init', array( $this, 'load_assets' ) );
		
		/* Filter the admin body classes to add a custom class for our settings page */
		add_filter( 'admin_body_class', array( $this, 'body_classes' ) );

		add_filter( "sanitize_option_$this->options_field", array( $this, 'validate_settings' ) );

		//* Add a sanitizer/validator
		add_filter( 'pre_update_option_' . $this->options_field, array( $this, 'save_settings' ), 10, 2 );

	}

	/**
	 * Initializes all the theme settings page functionality. This function is used to create the theme settings 
	 * page, then use that as a launchpad for specific actions that need to be tied to the settings page.
	 *
	 * @since 0.7.0
	 * @global string $hybrid The global theme object.
	 * @return void
	 */
	function convertica_settings_page_init() {
		global $hybrid;

		/* Get theme information. */
		$theme = wp_get_theme( get_template() );
		$prefix = convertica_get_prefix();

		/* Create the theme settings page. */
		$hybrid->settings_page = add_theme_page(
			sprintf( esc_html__( '%s', 'convertica-td' ), $this->page_title ),	// Settings page name.
			sprintf( esc_html__( '%s', 'convertica-td'), $this->menu_title ),				// Menu item name.
			$this->convertica_settings_page_capability(),	// Required capability.
			$this->slug,									// Screen name.
			//todo: should be unique to the class instance
			array( $this, 'convertica_settings_page' )		// Callback function.
		);
		
	}
	
	function convertica_basic_admin_page_init() {
		global $hybrid;

		/* Create the theme settings page. */
		$hybrid->import_export_page = add_theme_page(
			sprintf( esc_html__( '%s', 'convertica-td' ), $this->page_title ),	// Settings page name.
			sprintf( esc_html__( '%s', 'convertica-td'), $this->menu_title ),				// Menu item name.
			$this->convertica_settings_page_capability(),	// Required capability.
			$this->slug,									// Screen name.
			//todo: should be unique to the class instance
			array( $this, 'convertica_import_export_page' )		// Callback function.
		);
		
	}
	
	/**
	 *
	 */
	 
	public function convertica_settings_page_register_settings() {
		
		global $hybrid;
		
		//* If this page doesn't store settings, no need to register them
		if ( ! $this->options_field )
			return;
		
		/* Register theme settings. */
		register_setting(
			$this->options_field,		// Options group.
			$this->options_field,		// Database option.
			array( $this,'convertica_save_theme_settings' )	// Validation callback function.
		);
		add_option( $this->options_field, $this->default_options );
		
		/* Check if the settings page is being shown before running any functions for it. */
		if ( !empty( $hybrid->settings_page ) ) {
			
			/* Filter the settings page capability so that it recognizes the 'edit_theme_options' cap. */
			add_filter( "option_page_capability_$this->options_field", array( $this, 'convertica_settings_page_capability' ));

			/* Add help tabs to the theme settings page. */
			add_action( "load-{$hybrid->settings_page}", array( $this,'convertica_settings_page_help' ) );

			/* Load the theme settings meta boxes. */
			add_action( "load-{$hybrid->settings_page}", array( $this,'convertica_load_settings_page_meta_boxes' ) );

			/* Create a hook for adding meta boxes. */
			add_action( "load-{$hybrid->settings_page}", array( $this,'convertica_settings_page_add_meta_boxes' ) );

			/* Load the JavaScript and stylesheets needed for the theme settings screen. */
			add_action( 'admin_enqueue_scripts', array( $this,'convertica_settings_page_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this,'convertica_settings_page_enqueue_styles' ) );
			add_action( 'admin_head-' . $hybrid->settings_page, array( $this,'convertica_settings_page_load_header_scripts' ) );
			add_action( 'admin_footer-' . $hybrid->settings_page, array( $this,'convertica_settings_page_load_footer_scripts' ) );
		}
		
		/* Reset settings to default if the user resets */
		if( convertica_get_setting( 'reset', $this->options_field ) ) {
			if ( update_option( $this->options_field, $this->default_options ) )
				convertica_admin_redirect( $this->slug, array( 'reset' => 'true' ) );
			else
				convertica_admin_redirect( $this->slug, array( 'error' => 'true' ) );
			exit;
		}
	}
	
	public function convertica_basic_admin_page_setup() {
		
		global $hybrid;
		
		//* Bail, if this page is not a basic admin page
		if ( empty( $hybrid->import_export_page ) )
			return;
		
		/* Load the theme settings meta boxes. */
		add_action( "load-{$hybrid->import_export_page}", array( $this,'convertica_load_settings_page_meta_boxes' ) );
		
		/* Load the JavaScript and stylesheets needed for the theme settings screen. */
		add_action( 'admin_enqueue_scripts', array( $this,'convertica_settings_page_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this,'convertica_settings_page_enqueue_styles' ) );
		
	}
	
	/**
	 * Helps to enqueue scripts and styles on the admin page if the respective methods exist
	 *
	 * @package Convertica
	 * @since 0.28
	 */
	public function load_assets() {
		
		global $hybrid;
		
		//* Hook scripts method
		if ( method_exists( $this, 'scripts' ) ) {
			add_action( "load-{$hybrid->settings_page}", array( $this, 'scripts' ) );
		}

		//* Hook styles method
		if ( method_exists( $this, 'styles' ) ) {
			add_action( "load-{$hybrid->settings_page}", array( $this, 'styles' ) );
		}
		
	}
	
	/**
	 *
	 */
	function body_classes( $classes ) {
		
		$screen = get_current_screen();
		if( 'appearance_page_' . $this->slug !== $screen->id ) {
			return $classes;
		}
		
		$classes .= ' ' . sanitize_html_class( $this->slug ) . ' ';
		
		return $classes;
		
	}

	/**
	 * Returns the required capability for viewing and saving theme settings.
	 *
	 * @since 1.2.0
	 * @return string
	 * todo: the filter name should be unique to the class instance
	 */
	function convertica_settings_page_capability() {
		return apply_filters( convertica_get_prefix() . '_settings_capability', 'edit_theme_options' );
	}

	/**
	 * Returns the theme settings page name/hook as a string.
	 *
	 * @since 1.2.0
	 * @return string
	 * todo: should be unique to the class instance
	 */
	function convertica_get_settings_page_name() {
		global $hybrid;

		//return ( isset( $hybrid->settings_page ) ? $hybrid->settings_page : 'appearance_page_theme-settings' );
		return ( isset( $hybrid->settings_page ) ? $hybrid->settings_page : "appearance_page_$this->slug" );
	}

	/**
	 * Provides a hook for adding meta boxes as seen on the post screen in the WordPress admin.  This addition 
	 * is needed because normal plugin/theme pages don't have this hook by default.  The other goal of this 
	 * function is to provide a way for themes to load and execute meta box code only on the theme settings 
	 * page in the admin.  This way, they're not needlessly loading extra files.
	 *
	 * @since 1.2.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_add_meta_boxes() {
		do_action( 'add_meta_boxes', $this->convertica_get_settings_page_name() );
	}

	/**
	 * Loads the meta boxes packaged with the framework on the theme settings page.  These meta boxes are 
	 * merely loaded with this function.  Meta boxes are only loaded if the feature is supported by the theme.
	 *
	 * @since 1.2.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_load_settings_page_meta_boxes() {

		/* Get theme-supported meta boxes for the settings page. */
		$supports = get_theme_support( 'hybrid-core-theme-settings' );

		/* If there are any supported meta boxes, load them. */
		if ( is_array( $supports[0] ) ) {

			/* Load the 'About' meta box if it is supported. */
			if ( in_array( 'about', $supports[0] ) )
				require_once( trailingslashit( HYBRID_ADMIN ) . 'meta-box-theme-about.php' );

			/* Load the 'Footer' meta box if it is supported. */
			if ( in_array( 'footer', $supports[0] ) )
				require_once( trailingslashit( HYBRID_ADMIN ) . 'meta-box-theme-footer.php' );
		}
	}

	/**
	 * Validation/Sanitization callback function for theme settings.  This just returns the data passed to it.  Theme
	 * developers should validate/sanitize their theme settings on the "sanitize_option_{$prefix}_theme_settings" 
	 * hook.  This function merely exists for backwards compatibility.
	 *
	 * @since 0.7.0
	 * @param array $settings An array of the theme settings passed by the Settings API for validation.
	 * @return array $settings The array of theme settings.
	 * todo: should be unique to the class instance
	 */
	function convertica_save_theme_settings( $settings ) {

		/* @deprecated 1.0.0. Developers should filter "sanitize_option_{$prefix}_theme_settings" instead. */
		return apply_filters( convertica_get_prefix() . '_validate_theme_settings', $settings );
	}

	/**
	 * Displays the theme settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.7.0
	 * @return void
	 */
	function convertica_settings_page() {

		/* Get the theme information. */
		$prefix = convertica_get_prefix();
		$theme = wp_get_theme( get_template() );

		do_action( $this->options_field."_before_settings_page" ); ?>

		<div class="wrap">

			<?php screen_icon(); ?>
			<h2>
				<?php printf( __( '%s', 'convertica-td' ), $this->page_title ); ?>
				<a href="<?php echo admin_url( 'customize.php' ); ?>" class="add-new-h2"><?php esc_html_e( 'Customize', 'convertica-td' ); ?></a>
			</h2>
			<?php settings_errors(); ?>

			<?php do_action( $this->options_field."_open_settings_page" ); ?>

			<div class="hybrid-core-settings-wrap">

				<form method="post" action="options.php">

					<?php settings_fields( $this->options_field ); ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

					<div id="poststuff">

						<div id="post-body" class="metabox-holder columns-2">

							<div id="postbox-container-1" class="postbox-container side">
								<?php do_meta_boxes( $this->convertica_get_settings_page_name(), 'side', null ); ?>
							</div><!-- #postbox-container-1 -->

							<div id="postbox-container-2" class="postbox-container normal advanced">
								<?php do_meta_boxes( $this->convertica_get_settings_page_name(), 'normal', null ); ?>
								<?php do_meta_boxes( $this->convertica_get_settings_page_name(), 'advanced', null ); ?>
							</div><!-- #postbox-container-2 -->

						</div><!-- #post-body -->

						<br class="clear">

					</div><!-- #poststuff -->

					<?php submit_button( esc_attr__( 'Save Settings', 'convertica-td' ), 'primary', 'submit', false  ); ?>
					<?php submit_button( esc_attr__( 'Reset Settings', 'convertica-td' ), 'secondary', $this->field_name( 'reset' ), false  ); ?>

				</form>

			</div><!-- .hybrid-core-settings-wrap -->

			<?php do_action( $this->options_field."_close_settings_page" ); ?>

		</div><!-- .wrap --><?php

		do_action( $this->options_field."_after_settings_page" );
	}
	
	/**
	 * Displays the settings import / export page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.7.0
	 * @return void
	 */
	function convertica_import_export_page() {

		global $hybrid;
		
		?>
		<div class="wrap">

			<h2><?php printf( __( '%s', 'convertica-td' ), $this->page_title ); ?></h2>
			
			<?php settings_errors(); ?>

			<div class="hybrid-core-settings-wrap">

					<div id="poststuff">

						<div id="post-body" class="metabox-holder columns">

							<div class="postbox">
								<?php do_meta_boxes( $hybrid->import_export_page, 'normal', null ); ?>
							</div><!-- #postbox-container-1 -->

						</div><!-- #post-body -->

						<br class="clear">

					</div><!-- #poststuff -->
					
			</div><!-- .hybrid-core-settings-wrap -->

		</div><!-- .wrap -->
		<?php

	}

	/**
	 * Creates a settings field id attribute for use on the theme settings page.  This is a helper function for use
	 * with the WordPress settings API.
	 *
	 * @since 1.0.0
	 * @return string
	 * todo: should be unique to the class instance
	 */
	function field_id( $setting ) {
		return "$this->options_field-" . sanitize_html_class( $setting );
	}

	/**
	 * Creates a settings field name attribute for use on the theme settings page.  This is a helper function for 
	 * use with the WordPress settings API.
	 *
	 * @since 1.0.0
	 * @return string
	 * todo: should be unique to the class instance
	 */
	function field_name( $setting ) {
		return "$this->options_field[{$setting}]";
	}

	function get_field_value($field){
		return convertica_get_setting($field, $this->options_field);

	}
	/**
	 * Adds a help tab to the theme settings screen if the theme has provided a 'Documentation URI' and/or 
	 * 'Support URI'.  Theme developers can add custom help tabs using get_current_screen()->add_help_tab().
	 *
	 * @since 1.3.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_help() {

		/* Get the parent theme data. */
		$theme = wp_get_theme( get_template() );
		$doc_uri = $theme->get( 'Documentation URI' );
		$support_uri = $theme->get( 'Support URI' );

		/* If the theme has provided a documentation or support URI, add them to the help text. */
		if ( !empty( $doc_uri ) || !empty( $support_uri ) ) {

			/* Open an unordered list for the help text. */
			$help = '<ul>';

			/* Add the Documentation URI. */
			if ( !empty( $doc_uri ) )
				$help .= '<li><a href="' . esc_url( $doc_uri ) . '">' . __( 'Documentation', 'convertica-td' ) . '</a></li>';

			/* Add the Support URI. */
			if ( !empty( $support_uri ) )
				$help .= '<li><a href="' . esc_url( $support_uri ) . '">' . __( 'Support', 'convertica-td' ) . '</a></li>';

			/* Close the unordered list for the help text. */
			$help .= '</ul>';

			/* Add a help tab with links for documentation and support. */
			get_current_screen()->add_help_tab(
				array(
					'id' => 'default',
					'title' => esc_attr( $theme->get( 'Name' ) ),
					'content' => $help
				)
			);
		}
	}

	/**
	 * Loads the required stylesheets for displaying the theme settings page in the WordPress admin.
	 *
	 * @since 1.2.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_enqueue_styles( $hook_suffix ) {

		global $hybrid;
		
		/* Load admin stylesheet if on the theme settings screen. */
		if ( $hook_suffix == $this->convertica_get_settings_page_name() || $hook_suffix == $hybrid->import_export_page )
			wp_enqueue_style( 'hybrid-core-admin' );
	}

	/**
	 * Loads the JavaScript files required for managing the meta boxes on the theme settings
	 * page, which allows users to arrange the boxes to their liking.
	 *
	 * @since 1.2.0
	 * @param string $hook_suffix The current page being viewed.
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_enqueue_scripts( $hook_suffix ) {

		global $hybrid;
		
		if ( $hook_suffix == $this->convertica_get_settings_page_name() || $hook_suffix == $hybrid->import_export_page ) {
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
		
		}
	}

	/**
	 * Loads the JavaScript required for using chosen for select fields on the theme settings page.
	 *
	 * @since 0.7.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_load_header_scripts() {
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				$( "body.convertica-theme-settings select" ).chosen({
					width: "95%",
					inherit_select_classes: true,
					disable_search: true,
				});
			});
		</script>
		<?php
	}
	
	/**
	 * Loads the JavaScript required for toggling the meta boxes on the theme settings page.
	 *
	 * @since 0.7.0
	 * @return void
	 * todo: should be unique to the class instance
	 */
	function convertica_settings_page_load_footer_scripts() {
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles( '<?php echo $this->convertica_get_settings_page_name(); ?>' );
			});
			//]]>
		</script>
		<?php
	}

}