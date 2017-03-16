<?php

/**
 * Add Landing Page Settings meta box to supported post types edit screens
 * @return none
 * @since 1.0
 */
 
add_action( 'add_meta_boxes', 'convertica_landing_page_settings_box' );

function convertica_landing_page_settings_box() {
	foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'convertica-landing-page-settings' ) ) {
			add_meta_box( 'convertica-landing-page-settings', sprintf( __( '%s Landing Page Experience', 'convertica-td' ), convertica_get_theme_name() ), 'convertica_landing_page_settings_box_cb', $type, 'side', 'default' );
		}
	}
}

/**
 * Builds the actual markup for Convertica Landing Page Settings metabox
 * @param type $post 
 * @return none
 * @since 1.0
 */

function convertica_landing_page_settings_box_cb( $post ) {
	global $post, $typenow;
	$convertica_lp_options = get_post_meta( $post->ID, '_convertica_lp_options', true );

	$hide_header = isset( $convertica_lp_options['hide-header'] ) ? $convertica_lp_options['hide-header'] : false;

	$hide_breadcrumbs = isset( $convertica_lp_options['hide-breadcrumbs'] ) ? $convertica_lp_options['hide-breadcrumbs'] : false;

	$hide_page_title = isset( $convertica_lp_options['hide-page-title'] ) ? $convertica_lp_options['hide-page-title'] : false;

	$hide_widgets_above_header = isset( $convertica_lp_options['hide-widgets-above-header'] ) ? $convertica_lp_options['hide-widgets-above-header'] : false;

	$hide_widgets_below_header = isset( $convertica_lp_options['hide-widgets-below-header'] ) ? $convertica_lp_options['hide-widgets-below-header'] : false;

	$hide_widgets_above_footer = isset( $convertica_lp_options['hide-widgets-above-footer'] ) ? $convertica_lp_options['hide-widgets-above-footer'] : false;

	$hide_after_entry_widget = isset( $convertica_lp_options['hide-after-entry-widget'] ) ? $convertica_lp_options['hide-after-entry-widget'] : false;

	$hide_footer_widgets = isset( $convertica_lp_options['hide-footer-widgets'] ) ? $convertica_lp_options['hide-footer-widgets'] : false;

	$hide_footer = isset( $convertica_lp_options['hide-footer'] ) ? $convertica_lp_options['hide-footer'] : false;

	wp_nonce_field( 'convertica_lp_settings_nonce_field', 'convertica_lp_settings_nonce' );
	
	?>
	<!-- Hide Header setting -->
	<p>
		<input type="checkbox" value="true" id="hide-header" name="hide-header" <?php checked( $hide_header, true ); ?> />
		<label for="hide-header"><?php _e( 'Hide Header', 'convertica-td' ); ?></label>
	</p>
	
	<!-- Hide breadrumbs setting -->
	<p>
		<input type="checkbox" value="true" id="hide-breadcrumbs" name="hide-breadcrumbs" <?php checked( $hide_breadcrumbs, true ); ?> />
		<label for="hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'convertica-td' ); ?></label>
	</p>

	<!-- Hide page title setting -->
	<p>
        <input type="checkbox" value="true" id="hide-page-title" name="hide-page-title" <?php checked( $hide_page_title, true ); ?> />
        <label for="hide-page-title"><?php _e( 'Hide Page Title', 'convertica-td' ); ?></label>
	</p>
	
	<!-- Hide sidebar above header setting -->
	<p>
		<input type="checkbox" id="hide-widgets-above-header" name="hide-widgets-above-header" <?php checked( $hide_widgets_above_header, true ); ?> value="true" />
		<label for="hide-widgets-above-header"><?php _e( 'Hide Widgets Above Header', 'convertica-td' ); ?></label>
	</p>
	
	<!-- Hide sidebar below header setting -->
	<p>
		<input type="checkbox" id="hide-widgets-below-header" name="hide-widgets-below-header" <?php checked( $hide_widgets_below_header, true ); ?> value="true" />
		<label for="hide-widgets-below-header"><?php _e( 'Hide Widgets Below Header', 'convertica-td' ); ?></label>
	</p>
	
	<!-- Hide sidebar above footer setting -->
	<p>
		<input type="checkbox" id="hide-widgets-above-footer" name="hide-widgets-above-footer" <?php checked( $hide_widgets_above_footer, true ); ?> value="true" />
		<label for="hide-widgets-above-footer"><?php _e( 'Hide Widgets Above Footer', 'convertica-td' ); ?></label>
	</p>
	
	<?php if( $typenow == 'post' ) { ?>
	
		<!-- Hide after entry sidebar setting -->
		<p>
			<input type="checkbox" id="hide-after-entry-widget" name="hide-after-entry-widget" <?php checked( $hide_after_entry_widget, true ); ?> value="true" />
			<label for="hide-after-entry-widget"><?php _e( 'Hide After Entry Widgets', 'convertica-td' ); ?></label>
		</p>
		
	<?php } 
	
	/* Check if footer widgets support is enables */
	$show_footer_widgets = convertica_get_setting('convertica_footer_widgets');
	if($show_footer_widgets) {
	?>
		<!-- Hide footer widgets setting -->
		<p>
			<input type="checkbox" id="hide-footer-widgets" name="hide-footer-widgets" <?php checked( $hide_footer_widgets, true ); ?> value="true" />
			<label for="hide-footer-widgets"><?php _e( 'Hide Footer Widgets', 'convertica-td' ); ?></label>
		</p>
		<?php
	}
	?>

	<!-- Hide site footer setting -->
	<p>
		<input type="checkbox" id="hide-footer" name="hide-footer" <?php checked( $hide_footer, true ); ?> value="true" />
		<label for="hide-footer"><?php _e( 'Hide Footer', 'convertica-td' ); ?></label>
	</p>
	<?php
}

/**
 * Saves the Convertica Landing Page Settings metabox settings
 * @param type $post_id 
 * @return none
 * @since 1.0
 */
 
add_action( 'save_post', 'convertica_save_landing_page_settings' );

function convertica_save_landing_page_settings( $post_id ) {
	
	// Check if our nonce is set.
	if ( !isset( $_POST['convertica_lp_settings_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['convertica_lp_settings_nonce'], 'convertica_lp_settings_nonce_field' ) ) {
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
	
	$convertica_lp_options                              = array();
	$convertica_lp_options['hide-header']               = isset( $_POST['hide-header'] ) ? true : false;
	$convertica_lp_options['hide-breadcrumbs']          = isset( $_POST['hide-breadcrumbs'] ) ? true : false;
	$convertica_lp_options['hide-page-title']           = isset( $_POST['hide-page-title'] ) ? true : false;
	$convertica_lp_options['hide-widgets-above-header'] = isset( $_POST['hide-widgets-above-header'] ) ? true : false;
	$convertica_lp_options['hide-widgets-below-header'] = isset( $_POST['hide-widgets-below-header'] ) ? true : false;
	$convertica_lp_options['hide-widgets-above-footer'] = isset( $_POST['hide-widgets-above-footer'] ) ? true : false;
	$convertica_lp_options['hide-after-entry-widget']   = isset( $_POST['hide-after-entry-widget'] ) ? true : false;
	$convertica_lp_options['hide-footer-widgets']       = isset( $_POST['hide-footer-widgets'] ) ? true : false;
	$convertica_lp_options['hide-footer']               = isset( $_POST['hide-footer'] ) ? true : false;
	
	//clog($convertica_lp_options); die();
	
	update_post_meta( $post_id, '_convertica_lp_options', $convertica_lp_options );
}


/**
 * Modifies the front-end as per the landing page settings set in the post editor screen.
 * @return none
 * @since 1.0
 */
 
add_action( 'wp_head', 'convertica_landing_page_settings', 20 );

function convertica_landing_page_settings() {
	if( convertica_is_mobile_viewport() || !is_singular() )
		return;

	if( is_home() ) {
		$page_id = get_option( 'page_for_posts' );
	} else {
		$page_id = get_the_ID();
	}
	
	$convertica_lp_options = get_post_meta( $page_id, '_convertica_lp_options', true );
	
	$hide_header             = isset( $convertica_lp_options['hide-header'] ) ? $convertica_lp_options['hide-header'] : false;

	$hide_breadcrumbs = isset( $convertica_lp_options['hide-breadcrumbs'] ) ? $convertica_lp_options['hide-breadcrumbs'] : false;

	$hide_page_title = isset( $convertica_lp_options['hide-page-title'] ) ? $convertica_lp_options['hide-page-title'] : false;

	$hide_widgets_above_header = isset( $convertica_lp_options['hide-widgets-above-header'] ) ? $convertica_lp_options['hide-widgets-above-header'] : false;

	$hide_widgets_below_header = isset( $convertica_lp_options['hide-widgets-below-header'] ) ? $convertica_lp_options['hide-widgets-below-header'] : false;

	$hide_widgets_above_footer = isset( $convertica_lp_options['hide-widgets-above-footer'] ) ? $convertica_lp_options['hide-widgets-above-footer'] : false;

	$hide_after_entry_widget = isset( $convertica_lp_options['hide-after-entry-widget'] ) ? $convertica_lp_options['hide-after-entry-widget'] : false;

	$hide_footer_widgets = isset( $convertica_lp_options['hide-footer-widgets'] ) ? $convertica_lp_options['hide-footer-widgets'] : false;

	$hide_footer = isset( $convertica_lp_options['hide-footer'] ) ? $convertica_lp_options['hide-footer'] : false;

	if ( $hide_header ) {
		remove_action( 'convertica_do_header', 'convertica_header' );
	}

	if ( $hide_breadcrumbs ) {
		add_filter( 'convertica_show_breadcrumb', '__return_false' );
	}

	if ( $hide_page_title ) {
		if ( is_home() ) {
			return;
		}
		add_filter( 'post_class', 'convertica_single_post_classes' );
	}

	if ( $hide_widgets_above_header ) {
		add_filter( 'convertica_show_sb_before_header', '__return_false' );
	}

	if ( $hide_widgets_below_header ) {
		add_filter( 'convertica_show_sb_after_header', '__return_false' );
	}

	if ( $hide_widgets_above_footer ) {
		add_filter( 'convertica_show_sb_before_footer', '__return_false' );
	}

	if ( $hide_after_entry_widget ) {
		add_filter( 'convertica_show_sb_after_entry', '__return_false' );
	}

	
	if ( $hide_footer_widgets ) {
		add_filter( 'convertica_show_footer_widgets', '__return_false' );
	}
	

	if ( $hide_footer ) {
		remove_action( 'convertica_do_footer', 'convertica_footer' );
	}

}