<?php

add_action( 'add_meta_boxes', 'convertica_add_menu_settings_box', 8 );

/**
 * Add a meta box for SILO menus
 * @return none
 * @since 1.0
 */
function convertica_add_menu_settings_box() {
	foreach ( (array) get_post_types( array(
				'public' => true
			) ) as $type ) {
		if ( post_type_supports( $type, 'convertica-custom-menu-locations' ) ) {
			add_meta_box( 'converticamenu', sprintf( __( '%s Custom Menu Locations', 'convertica-td' ), convertica_get_theme_name() ), 'convertica_menu_meta_box', $type, 'side', 'default' );
		}
	}
}


/**
 * Builds the actual markup for the metabox
 * @param type $post 
 * @return none
 * @since 1.0
 */
function convertica_menu_meta_box( $post ) {
	global $post, $typenow;
	$convertica_custom_menu_options = get_post_meta( $post->ID, '_convertica_custom_menus', 1 );
	$locations                  = get_registered_nav_menus(); //Returns an array of all registered navigation menus in a theme (locations)
	$assigned_menus             = get_nav_menu_locations(); //Returns an array with the registered navigation menu locations + the menu assigned to it
	$all_menus                  = wp_get_nav_menus(); //Returns all navigation menu objects

	//insert additional options as fake menu options
	array_unshift( $all_menus, (object) array(
			'name' => '&mdash;Theme Default&mdash;',
			'term_id' => '-2'
		), (object) array(
			'name' => '&mdash;None&mdash;',
			'term_id' => '-1'
		) );

	echo '<p>' . sprintf( 'Select which custom menu appears in each location for this %s.', $typenow ) . '</p><p><em>' . sprintf( 'Only the menu locations for which the menus have been assigned on <a href="%s">Menus</a> screen will appear here.', admin_url( 'nav-menus.php' ) ) . '</em></p>';

?>
    <table class="widefat fixed" id="menu-locations-table">
		<tbody class="menu-locations">
			<?php
	foreach ( $locations as $loc_slug => $loc_name ) {
		if ( !has_nav_menu( $loc_slug ) )
			continue;
?>
			<tr>
		    <td class="menu-location-title" colspan="2">
				<label for ="locations-<?php
		echo $loc_slug;
		?>"><strong><?php
		echo $loc_name;
		?></strong></label>
			</td>
			</tr>

			<tr>
			<td class="menu-location-menus" colspan="2">
				<select name="convertica-menu-locations[<?php
		echo $loc_slug;
		?>]" id="locations-<?php
		echo $loc_slug;
		?>">
					<?php
		foreach ( $all_menus as $menu ) {
?>
					<?php
			$selected = ( is_array( $convertica_custom_menu_options ) ) ? ( $convertica_custom_menu_options[$loc_slug] == $menu->term_id ) : '';
?>
					<option <?php
			if ( $selected )
				echo 'data-orig="true"';
			?> <?php
			selected( $selected );
			?> value="<?php
			echo $menu->term_id;
			?>">
					<?php
			echo wp_html_excerpt( $menu->name, 40, '&hellip;' );
?>
					</option>
					<?php
		}
?>
				 </select>
			</td>
			</tr>
		   <?php
	}
?>
		</tbody>
   </table>

   <?php
	wp_nonce_field( 'convertica_custom_menu_save', 'convertica_save_menu' );
}

add_action( 'save_post', 'convertica_custom_menu_save_settings' );

/**
 * Save menu metabox settings when saving the post
 * @param type $post_id 
 * @return none
 * @uses update_post_meta
 * @since 1.0
 */
function convertica_custom_menu_save_settings( $post_id ) {
	// Check if our nonce is set.
	if ( !isset( $_POST['convertica_save_menu'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['convertica_save_menu'], 'convertica_custom_menu_save' ) ) {
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
	// Make sure that it is set.
	if ( !isset( $_POST['convertica-menu-locations'] ) ) {
		return;
	}

	$convertica_custom_menus = array();

	$locations = $_POST['convertica-menu-locations'];
	foreach ( $locations as $loc => $menu_id ) {
		$convertica_custom_menus[$loc] = $menu_id;
	}

	update_post_meta( $post_id, '_convertica_custom_menus', $convertica_custom_menus );

}

add_action( 'admin_init', 'convertica_add_tax_menu_settings_box' );

/**
 * Adds SILO menu meta box on taxonomies admin view
 * @return none
 * @since 1.0
 * @uses add_action
 */
function convertica_add_tax_menu_settings_box() {
	$taxonomies = array(
		'category'
	);
	$tax_names  = apply_filters( 'convertica_tax_custom_menu_locations', $taxonomies );
	$all_tax    = get_taxonomies( array(
			'public' => true
		) );
	foreach ( $tax_names as $tax_name ) {
		if ( in_array( $tax_name, $all_tax ) ) {
			add_action( $tax_name . '_edit_form', 'convertica_tax_menu_meta_box', 10, 2 );
		}
	}
}


/**
 * Outputs the actual SILO menu meta box on taxonomy edit view
 * @param type $tag 
 * @param type $taxonomy 
 * @return none
 * @since 1.0
 */
function convertica_tax_menu_meta_box( $tag, $taxonomy ) {
	$tax            = get_taxonomy( $taxonomy );
	$term_meta      = (array) get_option( 'convertica-tax-meta' );
	$selected       = false;
	$locations      = get_registered_nav_menus(); //Returns an array of all registered navigation menus in a theme (locations)
	$assigned_menus = get_nav_menu_locations(); //Returns an array with the registered navigation menu locations + the menu assigned to it
	$all_menus      = wp_get_nav_menus(); //Returns all navigation menu objects
	//insert additional options as fake menu options
	array_unshift( $all_menus, (object) array(
			'name' => '&mdash;Theme Default&mdash;',
			'term_id' => '-2'
		), (object) array(
			'name' => '&mdash;None&mdash;',
			'term_id' => '-1'
		) );

?>
	<h3><?php
	echo esc_html( $tax->labels->singular_name ) . ' ' . __( 'Custom Menu Settings', 'convertica-td' );
	?></h3>
	<?php
	echo '<p>' . sprintf( 'Select which custom menu appears in each location for this %s.', strtolower( $tax->labels->singular_name ) ) . '</p>';
?>
	<table class="widefat fixed" id="menu-locations-table">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-locations"><?php
	_e( 'Theme Location', 'convertica-td' );
	?></th>
			<th scope="col" class="manage-column column-menus"><?php
	_e( 'Assigned Menu', 'convertica-td' );
	?></th>
		</tr>
		</thead>
		<?php
	?>
		<tbody class="menu-locations">
		   <tr>
				<td colspan = "2">
					<p><?php
	echo sprintf( '<em>Only the menu locations for which the menus have been assigned on <a href="%s">Menus</a> screen will appear here.</em>', admin_url( 'nav-menus.php' ) );
	?></p>
				</td>
		   </tr>
		   <?php
	foreach ( $locations as $loc_slug => $loc_name ) {
		if ( !has_nav_menu( $loc_slug ) )
			continue;
?>
				   <tr>
						<td class="menu-location-title"><label for = "convertica-tax-meta[<?php
		echo $loc_slug;
		?>]"><strong><?php
		echo $loc_name;
		?></strong></label></td>
						<td class="menu-location-menus">
							<select name="convertica-tax-meta[<?php
		echo $loc_slug;
		?>]" id="convertica-tax-meta[<?php
		echo $loc_slug;
		?>]">
								<?php
		foreach ( $all_menus as $menu ) {
?>
								<?php
			if ( isset( $term_meta[$tag->term_id] ) ) {
				$selected = $term_meta[$tag->term_id][$loc_slug] == $menu->term_id;
			}
?>
								<option <?php
			if ( $selected )
				echo 'data-orig="true"';
			?> <?php
			selected( $selected );
			?> value="<?php
			echo $menu->term_id;
			?>">
								<?php
			echo wp_html_excerpt( $menu->name, 40, '&hellip;' );
?>
								</option>
								<?php
		}
?>
							 </select>
						</td>
					</tr>
				<?php
	}
?>
		</tbody>
   </table>
   <?php
}

add_action( 'edited_term', 'convertica_save_term_meta', 10, 3 );

/**
 * Saves Taxonomy specific SILO Menu Settings when saving the taxonomy
 * @param type $term_id 
 * @param type $tt_id 
 * @param type $taxonomy 
 * @return type
 */
function convertica_save_term_meta( $term_id, $tt_id, $taxonomy ) {
	if ( !isset( $_POST['convertica-tax-meta'] ) ) {
		return;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		return;

	$term_meta = (array) get_option( 'convertica-tax-meta' );

	$term_meta[$term_id] = isset( $_POST['convertica-tax-meta'] ) ? $_POST['convertica-tax-meta'] : array();

	/* It's safe for us to save the data now. */
	// Make sure that it is set.
	$convertica_tax_custom_menus = array();

	$locations = $_POST['convertica-tax-meta'];
	foreach ( $locations as $loc => $menu_id ) {
		$convertica_tax_custom_menus[$loc] = $menu_id;
	}

	// Save the option array.
	update_option( 'convertica-tax-meta', $term_meta );

}


/**
 * Checks if custom menu is set
 * If custom menu is set, modify the wp_nav_menu_args
 * And assign selected menu to the defined theme locations
 */

add_filter( 'wp_nav_menu_args', 'convertica_get_menus' );

function convertica_get_menus( $args ) {
	
	if ( is_category() || is_tag() || is_tax() ) {
		$convertica_tax_menus = get_option( 'convertica-tax-meta' );
		$convertica_tax_menus = is_array( $convertica_tax_menus ) ? array_filter( $convertica_tax_menus ) : false; // Sometimes empty array elements will throw a warning in the foreach loop, so remove all empty elements from the array
		
		if( !$convertica_tax_menus ) {
			return $args;
		}
		if ( $convertica_tax_menus ) {
			foreach ( $convertica_tax_menus as $term_id => $menu_locations ) {
				if ( is_category( $term_id ) || is_tag( $term_id ) || is_tax( $term_id ) ) {
					foreach ( $menu_locations as $menu_location => $menu_id ) {
						if ( $args['theme_location'] == $menu_location ) { //if
							if ( $menu_id != '-2' ) { //if something custom is set
								$args['menu']           = $menu_id;
								$args['fallback_cb']    = -1;
								$args['theme_location'] = -1;
							}
						}
					}
				}
			}
		}
	}

	if ( is_single() || is_page() || is_home() || convertica_is_woo_shop() ) {
		if ( is_home() ) {
			$page_id = get_option( 'page_for_posts' );
		} else {
			if( convertica_is_woo_shop() ) {
				$page_id = wc_get_page_id( 'shop' );
			} else {
				$page_id = get_the_ID();
			}
		}
		$convertica_menu_value = get_post_meta( $page_id, '_convertica_custom_menus', true );
		$convertica_menu_value = maybe_unserialize( $convertica_menu_value );
		if ( $convertica_menu_value ) {
			foreach ( $convertica_menu_value as $menu_location => $menu_id ) {
				if ( $args['theme_location'] == $menu_location ) { // convertica_get_menus is called on every menu. Check if wp is calling us for the same location as the menu location set in post meta
					if ( $menu_id != '-2' ) { //if something custom is set
						$args['menu']           = $menu_id;
						$args['fallback_cb']    = -1;
						$args['theme_location'] = -1;
					}
				}
			}
		}
	}
	
	return $args;
	
}
