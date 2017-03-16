<?php


/**
 * Inserts the Mobile Landing Page Settings meta box on supported post types edit screen
 * @return none
 * @since 1.0
 */
 
add_action( 'add_meta_boxes', 'convertica_mobile_landing_settings_box' );

function convertica_mobile_landing_settings_box() {
	foreach( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'convertica-mobile-landing-page-settings' ) ) {
			add_meta_box( 'convertica-mobile-lp-settings', sprintf( __( '%s Mobile Experience', 'convertica-td' ), convertica_get_theme_name() ), 'convertica_mobile_landing_page_settings_box_cb', $type, 'side', 'default' );
		}
	}
}


/**
 * Displays mobile elements toggle fields for mobile template meta box
 * @return none
 * @since 1.0
 */

function convertica_mobile_landing_page_settings_box_cb( $post ) {
	global $post, $typenow;

	$convertica_mobile_lp_options = get_post_meta( $post->ID, '_convertica_mobile_lp_options', true );

	$use_global = isset( $convertica_mobile_lp_options['mobile_use_global'] ) ? $convertica_mobile_lp_options['mobile_use_global'] : true;

	$hide_header = isset( $convertica_mobile_lp_options['mobile-hide-header'] ) ? $convertica_mobile_lp_options['mobile-hide-header'] : false;

	$hide_breadcrumbs = isset( $convertica_mobile_lp_options['mobile-hide-breadcrumbs'] ) ? $convertica_mobile_lp_options['mobile-hide-breadcrumbs'] : false;

	$hide_page_title = isset( $convertica_mobile_lp_options['mobile-hide-page-title'] ) ? $convertica_mobile_lp_options['mobile-hide-page-title'] : false;

	$hide_widgets_above_header = isset( $convertica_mobile_lp_options['mobile-hide-widgets-above-header'] ) ? $convertica_mobile_lp_options['mobile-hide-widgets-above-header'] : false;

	$hide_widgets_below_header = isset( $convertica_mobile_lp_options['mobile-hide-widgets-below-header'] ) ? $convertica_mobile_lp_options['mobile-hide-widgets-below-header'] : false;

	$hide_widgets_above_footer = isset( $convertica_mobile_lp_options['mobile-hide-widgets-above-footer'] ) ? $convertica_mobile_lp_options['mobile-hide-widgets-above-footer'] : false;

	$hide_after_entry_widget = isset( $convertica_mobile_lp_options['mobile-hide-after-entry-widget'] ) ? $convertica_mobile_lp_options['mobile-hide-after-entry-widget'] : false;

	$hide_sidebars = isset( $convertica_mobile_lp_options['mobile-hide-sidebars'] ) ? $convertica_mobile_lp_options['mobile-hide-sidebars'] : false;

	$hide_footer_widgets = isset( $convertica_mobile_lp_options['mobile-hide-footer-widgets'] ) ? $convertica_mobile_lp_options['mobile-hide-footer-widgets'] : false;

	$hide_footer = isset( $convertica_mobile_lp_options['mobile-hide-footer'] ) ? $convertica_mobile_lp_options['mobile-hide-footer'] : false;

	wp_nonce_field( 'convertica_mobile_lp_settings_nonce_field', 'convertica_mobile_lp_settings_nonce' );

	?>
	<p>
		<?php _e( 'These settings can be used to hide page elements for users visiting your site on mobile. The elements you hide here will be still visible to users visiting your site on viewports other than mobile.', 'convertica-td' ); ?>
	</p>
	
	<p>
		<em><?php printf( __( 'These options will override the global settings as set on %s%s Settings%s screen.', 'convertica-td' ), '<a href="' . menu_page_url( CONVERTICA_SETTINGS, false ) . '">', convertica_get_theme_name(), '</a>' ); ?></em>
	</p>

	<p>
		<input type="checkbox" id="mobile_use_global" value="true" name="mobile_use_global" <?php checked( $use_global, true ); ?> />
		<label for="mobile_use_global"><?php _e( 'Use global settings', 'convertica-td' ); ?>
	</p>

	<div id="mobile-lp-options">
	<!-- Hide Site Header setting -->
	<p>
		<input type="checkbox" id="mobile-hide-header" value="true" name="mobile-hide-header" <?php checked( $hide_header, true ); ?> />
		<label for="mobile-hide-header"><?php _e( 'Hide Header', 'convertica-td' ); ?></label>
	</p>

	<?php
	/* Let's check if breadcrumbs are being displayed on the website. */
	ob_start(); // So that the breacrumbs do not output here if they're being displayed
		convertica_show_breadcrumb();
	$is_breadcrumbs_on = ob_get_clean();
	$is_breadcrumbs_on = empty( $is_breadcrumbs_on ) ? 0 : 1;
	
	if( $is_breadcrumbs_on == 1 ) {
	?>
		<!-- Hide Breadcrumbs setting -->
		<p>
			<input type="checkbox" id="mobile-hide-breadcrumbs" name="mobile-hide-breadcrumbs" <?php checked( $hide_breadcrumbs, true ); ?> value="true" />
			<label for="mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'convertica-td' ); ?></label>
		</p>
	<?php
	}
	?>

	<!-- Hide Page Title setting -->
	<p>
		<input type="checkbox" id="mobile-hide-page-title" name="mobile-hide-page-title" <?php checked( $hide_page_title, true ); ?> value="true" />
		<label for="mobile-hide-page-title"><?php _e( 'Hide Page Title', 'convertica-td' ); ?></label>
	</p>

	<!-- Hide Widgets Above Header setting -->
	<p>
		<input type="checkbox" id="mobile-hide-widgets-above-header" name="mobile-hide-widgets-above-header" <?php checked( $hide_widgets_above_header, true ); ?> value="true" />
		<label for="mobile-hide-widgets-above-header"><?php _e( 'Hide Widgets Above Header' , 'convertica-td' ); ?></label>
	</p>

	<!-- Hide Widgets Below Header setting -->
	<p>
		<input type="checkbox" id="mobile-hide-widgets-below-header" name="mobile-hide-widgets-below-header" <?php checked( $hide_widgets_below_header, true ); ?> value="true" />
		<label for="mobile-hide-widgets-below-header"><?php _e( 'Hide Widgets Below Header', 'convertica-td' ); ?></label>
	</p>

	
	<!-- Hide Widgets Above Footer setting -->
	<p>
		<input type="checkbox" id="mobile-hide-widgets-above-footer" name="mobile-hide-widgets-above-footer" <?php checked( $hide_widgets_above_footer, true ); ?> value="true" />
		<label for="mobile-hide-widgets-above-footer"><?php _e( 'Hide Widgets Above Footer', 'convertica-td' ); ?></label>
	</p>
	
	<?php if( $typenow == 'post' ) { ?>
	
		<!-- Hide After Entry Widgets setting -->
		<p>
			<input type="checkbox" id="mobile-hide-after-entry-widget" name="mobile-hide-after-entry-widget" <?php checked( $hide_after_entry_widget, true ); ?> value="true" />
			<label for="mobile-hide-after-entry-widget"><?php _e( 'Hide After Entry Widgets', 'convertica-td' ); ?></label>
		</p>
		
	<?php } ?>
	
	<?php
	$current_layout = hybrid_get_theme_layout();
	if( $current_layout != '1c' ) {
	?>
		<!-- Hide Sidebars setting -->
		<p>
			<input type="checkbox" id="mobile-hide-sidebars" name="mobile-hide-sidebars" <?php checked( $hide_sidebars, true ); ?> value="true" />
			<label for="mobile-hide-sidebars"><?php _e( 'Hide Sidebar(s)', 'convertica-td' ); ?></label>
		</p>
	<?php
	}
	
	/* Check if footer widgets are enabled */
	
	$show_footer_widgets = convertica_get_setting('convertica_footer_widgets');
	if($show_footer_widgets) {
	?>
		<!-- Hide Footer Widgets setting-->
		<p>
			<input type="checkbox" id="mobile-hide-footer-widgets" name="mobile-hide-footer-widgets" <?php checked( $hide_footer_widgets, true ); ?> value="true" />
			<label for="mobile-hide-footer-widgets"><?php _e( 'Hide Footer Widgets', 'convertica-td' ); ?></label>
		</p>
		<?php
	}
	?>

	<!-- Hide Site Footer setting -->
	<p>
		<input type="checkbox" id="mobile-hide-footer" name="mobile-hide-footer" <?php checked( $hide_footer, true ); ?> value="true" />
		<label for="mobile-hide-footer"><?php _e( 'Hide Footer', 'convertica-td' ); ?></label>
	</p>
	</div>
	<?php
	
}

add_action( 'save_post', 'convertica_save_mobile_landing_page_settings' );

/**
 * Save the mobile landing page settings when saving the post type
 * @param type $post_id 
 * @return none
 * @since 1.0
 */
function convertica_save_mobile_landing_page_settings( $post_id ) {

	// Check if our nonce is set.
	if ( !isset( $_POST['convertica_mobile_lp_settings_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['convertica_mobile_lp_settings_nonce'], 'convertica_mobile_lp_settings_nonce_field' ) ) {
		return;
	}
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* It's safe for us to save the data now. */

	$convertica_mobile_lp_options = array();

	$convertica_mobile_lp_options['mobile_use_global'] = isset( $_POST['mobile_use_global'] ) ? true : false;

	if ( $convertica_mobile_lp_options['mobile_use_global'] ) {

		$global_hide_breadcrumbs = convertica_get_setting( 'convertica_mob_hide_breadcrumbs' );

		$global_hide_widgets_above_header = convertica_get_setting( 'convertica_mob_hide_widgets_above_header' );

		$global_hide_widgets_below_header = convertica_get_setting( 'convertica_mob_hide_widgets_below_header' );

		$global_hide_widgets_above_footer = convertica_get_setting( 'convertica_mob_hide_widgets_above_footer' );

		$global_hide_sidebars = convertica_get_setting( 'convertica_mob_hide_sidebars' );

		$global_hide_fwidgets = convertica_get_setting( 'convertica_mob_hide_fwidgets' );

		$convertica_mobile_lp_options['mobile-hide-breadcrumbs'] = empty( $global_hide_breadcrumbs ) ? false : true;

		$convertica_mobile_lp_options['mobile-hide-widgets-above-header'] = empty( $global_hide_widgets_above_header ) ? false : true;

		$convertica_mobile_lp_options['mobile-hide-widgets-below-header'] = empty( $global_hide_widgets_below_header ) ? false : true;

		$convertica_mobile_lp_options['mobile-hide-widgets-above-footer'] = empty( $global_hide_widgets_above_footer ) ? false : true;

		$convertica_mobile_lp_options['mobile-hide-sidebars'] = empty( $global_hide_sidebars ) ? false : true;

		$convertica_mobile_lp_options['mobile-hide-footer-widgets'] = empty( $global_hide_breadcrumbs ) ? false : true;

		$lander_mobile_options = get_post_meta( $post_id, '_convertica_mobile_lp_options', TRUE );

		$lander_mobile_options['mobile_use_global'] = isset( $_POST['mobile_use_global'] ) ? true : false;

		update_post_meta( $post_id, '_convertica_mobile_lp_options', $lander_mobile_options );

	} else {

		$convertica_mobile_lp_options['mobile_use_global'] = isset( $_POST['mobile_use_global'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-header'] = isset( $_POST['mobile-hide-header'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-breadcrumbs'] = isset( $_POST['mobile-hide-breadcrumbs'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-page-title'] = isset( $_POST['mobile-hide-page-title'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-widgets-above-header'] = isset( $_POST['mobile-hide-widgets-above-header'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-widgets-below-header'] = isset( $_POST['mobile-hide-widgets-below-header'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-widgets-above-footer'] = isset( $_POST['mobile-hide-widgets-above-footer'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-after-entry-widget'] = isset( $_POST['mobile-hide-after-entry-widget'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-sidebars'] = isset( $_POST['mobile-hide-sidebars'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-footer-widgets'] = isset( $_POST['mobile-hide-footer-widgets'] ) ? true : false;

		$convertica_mobile_lp_options['mobile-hide-footer'] = isset( $_POST['mobile-hide-footer'] ) ? true : false;

		update_post_meta( $post_id, '_convertica_mobile_lp_options', $convertica_mobile_lp_options );

	}

}

add_action( 'wp_head', 'convertica_mobile_landing_page_settings' );

/**
 * Used to build/show the mobile template on the front-end
 * @return type
 * @since 1.0
 */
function convertica_mobile_landing_page_settings() {

	// Bail if not visiting on mobile
	if ( !convertica_is_mobile_viewport() )
		return;
	
	if ( is_home() ) {
		$page_id = get_option( 'page_for_posts' );
	} else {
		$page_id = get_the_ID();
	}

	$convertica_mobile_lp_optionset = get_post_meta( $page_id, '_convertica_mobile_lp_options', true );

	$use_global = ( is_array( $convertica_mobile_lp_optionset ) && array_key_exists( 'mobile_use_global', $convertica_mobile_lp_optionset ) ) ? $convertica_mobile_lp_optionset['mobile_use_global'] : true;

	if ( $use_global ) {

		$global_hide_breadcrumbs = convertica_get_setting( 'convertica_mob_hide_breadcrumbs' );

		$global_hide_widgets_above_header = convertica_get_setting( 'convertica_mob_hide_widgets_above_header' );

		$global_hide_widgets_below_header = convertica_get_setting( 'convertica_mob_hide_widgets_below_header' );
		
		$global_hide_widgets_above_footer = convertica_get_setting( 'convertica_mob_hide_widgets_above_footer' );

		$global_hide_fwidgets = convertica_get_setting( 'convertica_mob_hide_fwidgets' );
		
		$global_hide_sidebars = convertica_get_setting( 'convertica_mob_hide_sidebars' );
		
		$hide_breadcrumbs = empty( $global_hide_breadcrumbs ) ? false : true;

		$hide_widgets_above_header = empty( $global_hide_widgets_above_header ) ? false : true;

		$hide_widgets_below_header = empty( $global_hide_widgets_below_header ) ? false : true;

		$hide_widgets_above_footer = empty( $global_hide_widgets_above_footer ) ? false : true;

		$hide_sidebars = empty( $global_hide_sidebars ) ? false : true;

		$hide_footer_widgets = empty( $global_hide_fwidgets ) ? false : true;

	} else {

		if( !is_singular() )
			return;
		
		$hide_header = isset( $convertica_mobile_lp_optionset['mobile-hide-header'] ) ? $convertica_mobile_lp_optionset['mobile-hide-header'] : false;

		$hide_breadcrumbs = isset( $convertica_mobile_lp_optionset['mobile-hide-breadcrumbs'] ) ? $convertica_mobile_lp_optionset['mobile-hide-breadcrumbs'] : false;

		$hide_page_title = isset( $convertica_mobile_lp_optionset['mobile-hide-page-title'] ) ? $convertica_mobile_lp_optionset['mobile-hide-page-title'] : false;

		$hide_widgets_above_header = isset( $convertica_mobile_lp_optionset['mobile-hide-widgets-above-header'] ) ? $convertica_mobile_lp_optionset['mobile-hide-widgets-above-header'] : false;

		$hide_widgets_below_header = isset( $convertica_mobile_lp_optionset['mobile-hide-widgets-below-header'] ) ? $convertica_mobile_lp_optionset['mobile-hide-widgets-below-header'] : false;

		$hide_widgets_above_footer = isset( $convertica_mobile_lp_optionset['mobile-hide-widgets-above-footer'] ) ? $convertica_mobile_lp_optionset['mobile-hide-widgets-above-footer'] : false;

		$hide_after_entry_widget = isset( $convertica_mobile_lp_optionset['mobile-hide-after-entry-widget'] ) ? $convertica_mobile_lp_optionset['mobile-hide-after-entry-widget'] : false;

		$hide_sidebars = isset( $convertica_mobile_lp_optionset['mobile-hide-sidebars'] ) ? $convertica_mobile_lp_optionset['mobile-hide-sidebars'] : false;

		$hide_footer_widgets = isset( $convertica_mobile_lp_optionset['mobile-hide-footer-widgets'] ) ? $convertica_mobile_lp_optionset['mobile-hide-footer-widgets'] : false;

		$hide_footer = isset( $convertica_mobile_lp_optionset['mobile-hide-footer'] ) ? $convertica_mobile_lp_optionset['mobile-hide-footer'] : false;

	}

	/** Disable options if selected in the metabox. Hides the relevant page elements if disabled on a page **/
	if ( isset( $hide_header ) && $hide_header == true ) {
		remove_action( 'convertica_do_header', 'convertica_header' );
	}

	if ( isset( $hide_breadcrumbs ) && $hide_breadcrumbs == true ) {
		add_filter( 'convertica_show_breadcrumb', '__return_false' );
	}

	if ( isset( $hide_page_title ) && $hide_page_title == true ) {
		add_filter( 'post_class', 'convertica_single_post_classes' );
	}

	if ( isset( $hide_widgets_above_header ) && $hide_widgets_above_header == true ) {
		add_filter( 'convertica_show_sb_before_header', '__return_false' );
	}

	if ( isset( $hide_widgets_below_header ) && $hide_widgets_below_header == true ) {
		add_filter( 'convertica_show_sb_after_header', '__return_false' );
	}

	if ( isset( $hide_widgets_above_footer ) && $hide_widgets_above_footer == true ) {
		add_filter( 'convertica_show_sb_before_footer', '__return_false' );
	}

	if ( isset( $hide_after_entry_widget ) && $hide_after_entry_widget == true ) {
		add_filter( 'convertica_show_sb_after_entry', '__return_false' );
	}

	if ( isset( $hide_sidebars ) && $hide_sidebars == true ) {
		// Force full-width-content layout setting
		add_filter('convertica_show_sidebar_primary', '__return_false');
		add_filter('convertica_show_sidebar_subsidiary', '__return_false');
		add_filter( 'body_class', 'convertica_hide_sidebars_class' );
	}

	
	if ( isset( $hide_footer_widgets ) && $hide_footer_widgets == true ) {
		add_filter( 'convertica_show_footer_widgets', '__return_false' );
	}
	

	if ( isset( $hide_footer ) && $hide_footer == true ) {
		remove_action( 'convertica_do_footer', 'convertica_footer' );
	}

}