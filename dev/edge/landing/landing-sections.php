<?php

/* Defines an array for creation of landing sections
 * uses apply_filter() to enable adding additional landing sections
 */

function convertica_get_landing_sections() {
	$convertica_landing_sections = array(
		'after_header_first' => array(
			'context' => 'after-header-first',		//used for id/classname
			'heading' => __( 'Landing Section After Header First', 'convertica-td' ),	//used for heading on the backend
			'hook' => 'convertica_after_header',	//hook where the section will be output
			'priority' => '10'	//priority of the hook
		),
		'after_header_second' => array(
			'context' => 'after-header-second',
			'heading' => __( 'Landing Section After Header Second', 'convertica-td' ),
			'hook' => 'convertica_after_header',
			'priority' => '11'
		),
		'after_header_third' => array(
			'context' => 'after-header-third',
			'heading' => __( 'Landing Section After Header Third', 'convertica-td' ),
			'hook' => 'convertica_after_header',
			'priority' => '12'

		),
		'before_footer_first' => array(
			'context' => 'before-footer-first',
			'heading' => __( 'Landing Section Before Footer First', 'convertica-td' ),
			'hook' => 'convertica_before_footer',
			'priority' => '4'
		),
		'before_footer_second' => array(
			'context' => 'before-footer-second',
			'heading' => __( 'Landing Section Before Footer Second', 'convertica-td' ),
			'hook' => 'convertica_before_footer',
			'priority' => '4'
		),
		'before_footer_third' => array(
			'context' => 'before-footer-third',
			'heading' => __( 'Landing Section Before Footer Third', 'convertica-td' ),
			'hook' => 'convertica_before_footer',
			'priority' => '4'
		)

	);

	return apply_filters( 'convertica_landing_sections', $convertica_landing_sections );
}

/* Helper function to get the context (for use with adding structural wrap) of the landing section
 * used in convertica_landing_section_markup()
 */

function convertica_get_landing_section_context( $landing_section ) {
	$landing_sections = convertica_get_landing_sections();
	if ( array_key_exists( $landing_section, $landing_sections ) ) {
		return $landing_sections[$landing_section]['context'];
	} else {
		return;
	}
}

/* Helper function to get the names of the landing section
 * used in convertica_landing_section_markup()
 */
function convertica_landing_section_names() {
	$convertica_landing_sections = convertica_get_landing_sections();
	$section_names           = array();
	foreach ( $convertica_landing_sections as $key => $value ) {
		$section_names[] = $key;
	}
	return $section_names;
}


add_action( 'add_meta_boxes', 'convertica_add_landing_sections_meta_box', 9 );

/**
 * Register a new meta box to the post or page edit screen, so that the user can add landing section
 * on a per-post or per-page basis.
 */
function convertica_add_landing_sections_meta_box() {
	global $post;
	if(!$post) return;
	if ( get_option( 'show_on_front' ) == 'page' ) {
		$posts_page_id = get_option( 'page_for_posts' );
		if ( $posts_page_id == $post->ID ) {
			add_action( 'edit_form_after_title', 'convertica_landing_section_notice' );
			return;
		}
	}

	$context  = 'normal';
	$priority = 'high';
	foreach ( (array) get_post_types( array(
				'public' => true
			) ) as $type ) {
		if ( post_type_supports( $type, 'convertica-landing-sections' ) ) {
			add_meta_box( 'landing-sections', sprintf( __( '%s Landing Sections', 'convertica-td' ), convertica_get_theme_name() ), 'convertica_landing_sections_box', $type, $context, $priority );
		}
	}
}

// Outputs an info on on the posts page where landing sections are not available.
function convertica_landing_section_notice() {
	echo '<div class="notice notice-warning inline"><p>' . __( 'convertica Landing Pages Sections are not available on the posts page.', 'convertica-td' ) . '</p></div>';
}

/**
 * Callback for landing page sections.
 * Builds the backend UI
 * @param $post
 * @since 1.0
 */

function convertica_landing_sections_box( $post ) {
	global $post;
	wp_nonce_field( 'convertica_landing_sections_save', 'convertica_landing_sections_box' );
	$title_placeholder = __( 'Enter the section title here', 'convertica-td' );
	$convertica_cs_section_options = get_post_meta( $post->ID, '_convertica_cs_section_options', 1 );
	$convertica_landing_sections   = convertica_get_landing_sections();
	
	foreach ( $convertica_landing_sections as $convertica_landing_section => $convertica_landing_section_val ) {
		$section_name         = $convertica_landing_section;
		$section_title        = isset( $convertica_cs_section_options['cs_' . $section_name . '_title'] ) ? $convertica_cs_section_options['cs_' . $convertica_landing_section . '_title'] : '';
		$section_content      = isset( $convertica_cs_section_options['cs_' . $section_name . '_content'] ) ? html_entity_decode( $convertica_cs_section_options['cs_' . $convertica_landing_section . '_content'] ) : '';
		$section_id = empty( $convertica_cs_section_options['cs_' . $section_name . '_id'] ) ? '' : $convertica_cs_section_options['cs_' . $convertica_landing_section . '_id'];
		$section_class = empty( $convertica_cs_section_options['cs_' . $section_name . '_classes'] ) ? '' : $convertica_cs_section_options['cs_' . $convertica_landing_section . '_classes'];
		//clog( $section_id );
		//clog( $section_class );
		$hide_section_desktop = isset( $convertica_cs_section_options['cs_' . $section_name . '_hide_desktop'] ) ? $convertica_cs_section_options['cs_' . $convertica_landing_section . '_hide_desktop'] : false;
		$hide_section_mobile  = isset( $convertica_cs_section_options['cs_' . $section_name . '_hide_mobile'] ) ? $convertica_cs_section_options['cs_' . $convertica_landing_section . '_hide_mobile'] : false;
		
		?>
		<div class="landing-section-stuff">
			<h4><?php echo esc_html( $convertica_landing_section_val['heading'] ); ?></h4>
			<div class="section-title">
				<label class="title-prompt-text" for="<?php echo 'cs_' . $convertica_landing_section . '_title'; ?>"><?php echo esc_html( $title_placeholder ); ?></label>
				<input type="text" name="<?php echo 'cs_' . $convertica_landing_section . '_title'; ?>" value="<?php echo esc_attr( $section_title ); ?>" id="<?php echo 'cs_' . $convertica_landing_section . '_title'; ?>" spellcheck="true" autocomplete="off" />
			</div>
			<div class="section-content">
				<?php
				$settings = array(
					'textarea_name' => 'cs_' . $section_name . '_content',
					'textarea_rows' => 7,
					'dfw' => true,
					'drag_drop_upload' => true
				);
				wp_editor( $section_content, 'cs_' . $section_name . '_content', $settings );
				?>
			</div>
			<div class="landing-section-id-classes">
				<table class="convertica-settings-table">
				<tr>
					<td colspan="2">
					<p>
						<label for="<?php echo 'cs_' . $convertica_landing_section . '_id'; ?>"><?php _e( 'Custom ID attribute', 'convertica-td' ); ?></label>
					</p>
					</td>
					<td>
					<p>
						<input type="text" name="<?php echo 'cs_' . $convertica_landing_section . '_id'; ?>" id="<?php echo 'cs_' . $convertica_landing_section . '_id' ?>" class="section-id" value="<?php echo esc_attr( $section_id ); ?>" maxlength="15" />
					</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<p>
						<label for="<?php echo 'cs_' . $convertica_landing_section . '_classes'; ?>"><?php _e( 'Custom Classes', 'convertica-td' ); ?></label>
					</p>
					</td>
					<td>
					<p>	
						<input type="text" name="<?php echo 'cs_' . $convertica_landing_section . '_classes'; ?>" id="<?php echo 'cs_' . $convertica_landing_section . '_classes' ?>" class="section-classes" value="<?php echo esc_attr( $section_class ); ?>" />
					</p>
					</td>
				</tr>
				</table>
			</div>
			<table class="convertica-settings-table">
			<tr>
				<td>
				<p>
					<label for="<?php echo 'cs_' . $convertica_landing_section . '_hide_desktop'; ?>"><?php _e( 'Hide on Desktop', 'convertica-td' ); ?></label>
				</p>
				</td>
				<td>	
				<p>
					<input type="checkbox" id="<?php echo 'cs_' . $convertica_landing_section . '_hide_desktop'; ?>" name="<?php echo 'cs_' . $convertica_landing_section . '_hide_desktop'; ?>" value="true" <?php checked( $hide_section_desktop, true ); ?> />
				</p>
				</td>
			</tr>
			<tr>
				<td>
				<p>
					<label for="<?php echo 'cs_' . $convertica_landing_section . '_hide_mobile'; ?>"><?php _e( 'Hide on Mobile', 'convertica-td' ); ?></label>
				</p>
				</td>
				<td>
				<p>
					<input type="checkbox" id="<?php echo 'cs_' . $convertica_landing_section . '_hide_mobile'; ?>" name="<?php echo 'cs_' . $convertica_landing_section . '_hide_mobile'; ?>" value="true" <?php checked( $hide_section_mobile, true ); ?> />
				</p>
				</td>
			</tr>
			</table>
		</div>
		<?php
	}
}

add_action( 'save_post', 'convertica_landing_sections_save_settings' );

/**
 * Save the Landing sections when we save a post or page.
 * @param $post_id
 * @since 1.0
 */
function convertica_landing_sections_save_settings( $post_id ) {
	// Check if our nonce is set.
	if ( !isset( $_POST['convertica_landing_sections_box'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['convertica_landing_sections_box'], 'convertica_landing_sections_save' ) ) {
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

	//clog( $_POST ); die();
	
	/* It's safe for us to save the data now. */
	// Make sure that it is set.
	$convertica_cs_section_options = array();
	$convertica_landing_sections   = convertica_get_landing_sections();

	foreach ( $convertica_landing_sections as $convertica_landing_section => $convertica_landing_section_val ) {
		$section_name = $convertica_landing_section;
		$convertica_cs_section_options['cs_' . $section_name . '_title']        = isset( $_POST['cs_' . $section_name . '_title'] ) ? sanitize_text_field( $_POST['cs_' . $section_name . '_title'] ) : '';
		$convertica_cs_section_options['cs_' . $section_name . '_content']      = isset( $_POST['cs_' . $section_name . '_content'] ) ? ( current_user_can( 'unfiltered_html' ) ? htmlentities( $_POST['cs_' . $section_name . '_content'] ) : wp_filter_post_kses( htmlentities( $_POST['cs_' . $section_name . '_content'] ) ) ) : '';
		$convertica_cs_section_options['cs_' . $section_name . '_id']        = isset( $_POST['cs_' . $section_name . '_id'] ) ? convertica_validate_custom_id( sanitize_html_class( $_POST['cs_' . $section_name . '_id'] ) ) : '';
		$convertica_cs_section_options['cs_' . $section_name . '_classes']        = isset( $_POST['cs_' . $section_name . '_classes'] ) ? convertica_validate_custom_classes( $_POST['cs_' . $section_name . '_classes'] ) : '';
		$convertica_cs_section_options['cs_' . $section_name . '_hide_desktop'] = isset( $_POST['cs_' . $section_name . '_hide_desktop'] ) ? true : false;
		$convertica_cs_section_options['cs_' . $section_name . '_hide_mobile']  = isset( $_POST['cs_' . $section_name . '_hide_mobile'] ) ? true : false;
	}

	update_post_meta( $post_id, '_convertica_cs_section_options', $convertica_cs_section_options );
}

function convertica_validate_custom_id( $custom_id ) {
	
	$custom_id = trim( $custom_id );
	$sanitized_custom_id = strstr( $custom_id, ' ', true );
	
	if ( $sanitized_custom_id !== false ) {
		return $sanitized_custom_id;
	}
	
	return $custom_id;
	
}

function convertica_validate_custom_classes( $custom_classes ) {
	
	$sanitized_custom_classes = explode( ' ', $custom_classes );
	$valid_classes = array();
	
	foreach( $sanitized_custom_classes as $sanitized_custom_class ) {
		$sanitized_custom_class = sanitize_html_class( $sanitized_custom_class );
		array_push( $valid_classes, $sanitized_custom_class );
	}
	
	$custom_classes = implode( ' ', $valid_classes );
	
	return $custom_classes;
	
}

/**
 * Checks if landing page sections are populated
 * Defines the markup for the landing sections.
 * Hooks landing sections on front
 * @since 1.0
 */
function convertica_output_landing_sections() {
	if ( is_404() || is_search() ) {
		return;
	}
	global $post;

	$convertica_landing_sections = convertica_get_landing_sections();
	foreach ( $convertica_landing_sections as $convertica_landing_section => $convertica_landing_section_val ) {
		$section_name  = $convertica_landing_section;
		$hook_name     = $convertica_landing_section_val['hook'];
		$hook_priority = $convertica_landing_section_val['priority'];
		//add_action( $hook_name, 'convertica_output_'.$section_name.'_section', $hook_priority );
		$callback = function() use ( $section_name ) {
			convertica_landing_section_markup( $section_name );
		};
		add_action( $hook_name, $callback, $hook_priority ); // to dynamically build the markup for landing-sections to show on the front-end
	}
}

add_action( 'convertica_atn_before_html', 'convertica_output_landing_sections' );

/**
 *  Helper function
 * Used in output functions to build the markup of the landing sections
 * @since 1.0
 */
function convertica_landing_section_markup( $section_name ) {

	if ( !is_singular() ) {
		return;
	}

	global $post;
	$context = convertica_get_landing_section_context( $section_name );
	$convertica_cs_section_options = get_post_meta( $post->ID, '_convertica_cs_section_options', 1 );

	if ( !$convertica_cs_section_options ) {
		return;
	}

	$section_title        = $convertica_cs_section_options['cs_' . $section_name . '_title'];
	$section_title        = apply_filters( 'convertica_landing_section_title', $section_title );
	$section_content      = empty( $convertica_cs_section_options['cs_' . $section_name . '_content'] ) ? '' : html_entity_decode( $convertica_cs_section_options['cs_' . $section_name . '_content'] );
	$section_id        = empty( $convertica_cs_section_options['cs_' . $section_name . '_id'] ) ? '' : 'id="' . $convertica_cs_section_options['cs_' . $section_name . '_id'] . '" ';
	$section_classes        = empty( $convertica_cs_section_options['cs_' . $section_name . '_classes'] ) ? '' : ' ' . $convertica_cs_section_options['cs_' . $section_name . '_classes'] . ' ';
	$hide_section_desktop = isset( $convertica_cs_section_options['cs_' . $section_name . '_hide_desktop'] ) ? $convertica_cs_section_options['cs_' . $section_name . '_hide_desktop'] : false;
	$hide_section_mobile  = isset( $convertica_cs_section_options['cs_' . $section_name . '_hide_mobile'] ) ? $convertica_cs_section_options['cs_' . $section_name . '_hide_mobile'] : false;

	// Hide if hidden and not visiting on mobile
	if ( $hide_section_desktop && !convertica_is_mobile_viewport() )
		return;

	// Hide if hidden and only on mobile
	if ( $hide_section_mobile && convertica_is_mobile_viewport() )
		return;

	if ( $section_title || $section_content ) {
		?>
		<div <?php echo $section_id; ?>class="<?php echo $context . $section_classes; ?> convertica-landing-section">
			<?php convertica_structural_wrap_open( $context ); ?>
				<section class="cs-<?php echo $context; ?> landing-section">
				<?php
				if ( $section_title ) {
					echo '<h2 class="landing-section-title">' . $section_title . '</h2>';
				}

				if ( $section_content ) {
					echo '<div class="landing-section-content">' . apply_filters( 'the_content', $section_content ) . '</div>';
				}
				?>
				</section>
			<?php convertica_structural_wrap_close( $context ); ?>
		</div>
		<?php
	}
}

/**
 * Adding opening markup for wrap for landing section
 * Echos markup: open the markup for wrap 
 * @since 1.0
 */

function convertica_structural_wrap_open( $context ) {
	echo '<div class = "wrap">';
}

/**
 * Adding closing markup for wrap for landing section
 * Echos markup: close the markup for wrap 
 * @since 1.0
 */

function convertica_structural_wrap_close( $context ) {
	echo '</div>';
}