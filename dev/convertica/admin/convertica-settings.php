<?php

//define('CONVERTICA_SETTINGS', convertica_get_prefix().'-theme-settings');

class Convertica_Settings_Edge extends Convertica_Settings_Base {
	
	function __construct() {
		
		$this->page_type		= 'settings';
		
		$this->options_field	= CONVERTICA_SETTINGS;
		$this->page_title		= __( 'Convertica Settings', 'convertica-td' );
		$this->menu_title		= __( 'Convertica Settings', 'convertica-td' );
		$this->slug				= 'convertica-theme-settings';
		
		$this->default_options	= apply_filters( 'convertica_settings_defaults', array(
			'convertica_mob_hide_breadcrumbs'			=> 1,
			'convertica_mob_hide_widgets_above_header'	=> 0,
			'convertica_mob_hide_widgets_below_header'	=> 0,
			'convertica_mob_hide_widgets_above_footer'	=> 0,
			'convertica_mob_hide_sidebars'				=> 0,
			'convertica_mob_hide_fwidgets'				=> 0,
			
			'convertica_footer_widgets'					=> 1,
			'convertica_footer_widgets_count'			=> 3,
			
			'convertica_gfonts_key'						=> $this->check_google_fonts_key(),
		) );
		
		$this->init();
		
	}
	
	protected function check_google_fonts_key() {
		$gfonts_key = convertica_get_setting( 'convertica_gfonts_key' );
		$gfonts_key = empty( $gfonts_key ) ? '' : $gfonts_key;
		
		return $gfonts_key;
	}
	
	function scripts() {
		wp_enqueue_script( 'convertica-settings-chosen-script', CONVERTICA_EDGE_URL . 'scripts/chosen/chosen.jquery' . hybrid_get_min_suffix() . '.js', array( 'jquery' ), false, true );
	}
	
	function styles() {
		wp_enqueue_style( 'convertica-settings-chosen-style', CONVERTICA_EDGE_URL . 'scripts/chosen/chosen' . hybrid_get_min_suffix() . '.css' );
	}
	
	/* Adds custom meta boxes to the theme settings page. */
	function meta_boxes() {
		
		/* Add meta box to display theme information */
		add_meta_box( 'convertica-info-meta-box', __( 'Convertica Information', 'convertica-td' ), array( $this, 'convertica_info_meta_box' ), 'appearance_page_' . $this->slug, 'normal', 'high' );

		/* Add meta box for global mobile viewport settings */
		if( current_theme_supports( 'convertica-mobile-experience' ) ) {
			add_meta_box( 'convertica-mobile-meta-box', __( 'Convertica Mobile ViewPort Settings', 'convertica-td' ), array( $this, 'convertica_mobile_meta_box' ), 'appearance_page_' . $this->slug, 'normal', 'high' );
		}

		/* Add meta box for footer widgets settings */
		add_meta_box( 'convertica-footer-meta-box', __( 'Convertica Footer Widgets', 'convertica-td' ), array( $this, 'convertica_footer_meta_box' ), 'appearance_page_' . $this->slug, 'normal', 'high' );

		/* Add meta box for additional google fonts settings */
		//add_meta_box( 'convertica-additional-gfonts', __( 'Enqueue Google Fonts to Frontend', 'convertica-td' ), array( $this, 'convertica_additional_gfonts' ), 'appearance_page_' . $this->slug, 'normal', 'high' );
		
		/* Add meta box for Google fonts fetcher settings */
		add_meta_box( 'convertica-googleapi-meta-box', __( 'Google Fonts Fetcher', 'convertica-td' ), array( $this, 'convertica_googleapi_meta_box' ), 'appearance_page_' . $this->slug, 'normal', 'high' );
		
		/* Add meta box for exta settings */
		add_meta_box( 'convertica-goodies-meta-box', __( 'Convertica Edge', 'convertica-td' ), array( $this, 'convertica_goodies_meta_box' ), 'appearance_page_' . $this->slug, 'side', 'high' );
		
	}
	
	
	/* Validates theme settings. */
	function validate_settings( $input ) {
		
		/* Validate and/or sanitize the textarea. */
		//$input['convertica_textarea'] = wp_filter_nohtml_kses($input['convertica_textarea']);
		
		/* Validate and/or sanitize the text input. */
		//$input['convertica_text_input'] = wp_filter_nohtml_kses($input['convertica_text_input']);
		
		/* Return the array of theme settings. */
		//clog($input);
		//die();
		
		$old_value = get_option( $this->options_field );
		
		/*
		clog($_POST);
		clog("<hr>");
		clog( $input );
		clog("<hr>");
		*/
		//clog("<hr>");
		/*
		foreach( $input as $key => $val ) {
			echo $key . " = (" . gettype($val) . ") " . $val . "<br />";
		}
		die();
		*/
		
		$bool_options = array(
			'convertica_mob_hide_breadcrumbs',
			'convertica_mob_hide_widgets_above_header',
			'convertica_mob_hide_widgets_below_header',
			'convertica_mob_hide_widgets_above_footer',
			'convertica_mob_hide_sidebars',
			'convertica_mob_hide_fwidgets',
			'convertica_footer_widgets'
		);
		
		$integer_options = array(
			'convertica_footer_widgets_count',
			'before_header_nav_submenu_width',
			'before_header_nav_border_width',
			'after_header_nav_border_width',
			'after_header_nav_submenu_width'
		);
		
		$font_size_options = array(
			'body_font_size',
			'form_field_font_size',
			'site_title_font_size',
			'site_tagline_font_size',
			'before_header_nav_font_size',
			'after_header_nav_font_size',
			'entry_title_font_size',
			'byline_font_size',
			'sb_widget_title_font_size',
			'sb_widget_body_font_size',
			'extra_widgets_title_font_size',
			'extra_widgets_body_font_size',
			'footer_font_size'
		);
		
		$color_options = array(
			'body_font_color',
			'body_link_color',
			'body_link_color_hover',
			'form_field_font_color',
			'site_title_color',
			'site_tagline_color',
			'before_header_nav_text_color',
			'before_header_nav_hover_text_color',
			'before_header_nav_current_link_text_color',
			'before_header_nav_parent_nav_link_text_color',
			'before_header_nav_link_background_color',
			'before_header_nav_link_hover_background_color',
			'before_header_nav_current_link_background_color',
			'before_header_nav_parent_nav_link_background_color',
			'before_header_nav_border_color',
			'after_header_nav_text_color',
			'after_header_nav_hover_text_color',
			'after_header_nav_current_link_text_color',
			'after_header_nav_parent_nav_link_text_color',
			'after_header_nav_link_background_color',
			'after_header_nav_link_hover_background_color',
			'after_header_nav_current_link_background_color',
			'after_header_nav_parent_nav_link_background_color',
			'after_header_nav_border_color',
			'entry_title_color',
			'content_headlines_color',
			'byline_color',
			'sb_widget_title_color',
			'sb_widget_body_color',
			'extra_widgets_title_color',
			'extra_widgets_body_color',
			'footer_color'
		);
		
		$string_options = array(
			'convertica_gfonts_key',
			'body_font_family',
			'body_font_variant',
			'form_field_font_family',
			'site_title_font_family',
			'site_title_font_variant',
			'site_tagline_font_family',
			'site_tagline_font_variant',
			'before_header_nav_font_family',
			'before_header_nav_font_variant',
			'after_header_nav_font_family',
			'after_header_nav_font_variant',
			'entry_title_font_family',
			'entry_title_font_variant',
			'content_headlines_font_family',
			'content_headlines_font_variant',
			'byline_font_family',
			'byline_font_variant',
			'sb_widget_title_font_family',
			'sb_widget_title_font_variant',
			'sb_widget_body_font_family',
			'sb_widget_body_font_variant',
			'extra_widgets_title_font_family',
			'extra_widgets_title_font_variant',
			'extra_widgets_body_font_family',
			'extra_widgets_body_font_variant',
			'footer_font_family',
			'footer_font_variant'
		);
		
		$array_options = array(
			//'convertica_frontend_gfonts',
		);
		
		foreach( $bool_options as $option ) {
			$input[$option] = isset( $input[$option] ) ? (int) (bool) $input[$option] : '';
		}
		
		foreach( $integer_options as $option ) {
			$input[$option] = isset( $input[$option] ) ? absint( $input[$option] ) : '';
		}
		
		foreach( $font_size_options as $option ) {
			$input[$option] = isset( $input[$option] ) ? max( 8, $input[$option] ) : '';
		}
		
		foreach( $color_options as $option ) {
			$input[$option] = isset( $input[$option] ) ? convertica_validate_color_settings( $input[$option], $input[$option] ) : '';
		}
		
		foreach( $string_options as $option ) {
			$input[$option] = isset( $input[$option] ) ? strip_tags( $input[$option] ) : '';
		}
		
		foreach( $array_options as $option ) {
			$input[$option] = ! empty( $input[$option] ) ? $input[$option] : '';
		}
		
		//clog($input); die();
		
		return $input;
		
	}
	
	function save_settings( $new_value, $old_value ) {
		
		//clog("Save");
		//clog($new_value); die();
		
		$new_value['convertica_gfonts_key'] = empty( $new_value['convertica_gfonts_key'] ) ? '' : trim( $new_value['convertica_gfonts_key'] );
		$old_value['convertica_gfonts_key'] = empty( $old_value['convertica_gfonts_key'] ) ? '' : $old_value['convertica_gfonts_key'];
		
		
		
		if ( $new_value['convertica_gfonts_key'] !== $old_value['convertica_gfonts_key'] ) {
			delete_transient( 'convertica_edge_fonts_list' );
		}
		
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}
		
		return $new_value;
		
	}
	
	/* Metabox for theme info */
	
	function convertica_info_meta_box(){
		?>
		<p><em><?php _e( 'This setting screen is for options that aren\'t truly relevant to the customizer.', 'convertica-td' ); ?></em></p>
		<?php
	}
	
	/* Metabox for Mobile Viewport settings */
	
	function convertica_mobile_meta_box() {
		
		?>
		<p><?php _e( 'Use these settings to globally enable or disable the following elements on the site. These settings will only take effect when user is viewing the site on a mobile viewport.', 'convertica-td' ); ?></p>
		<p><em><?php _e( 'Note: You can also change these settings on a per page / post basis using the <strong>Mobile Experience</strong> metabox on the page / post edit screen.', 'convertica-td' ); ?></em></p>
		
		<div class="csbox-section">
		<table>
		<?php
		/* Let's check if breadcrumbs are being displayed on the website. */
		ob_start(); // So that the breacrumbs do not output here if they're being displayed
			convertica_show_breadcrumb();
		$is_breadcrumbs_on = ob_get_clean();
		$is_breadcrumbs_on = empty( $is_breadcrumbs_on ) ? 0 : 1;
		
		if( $is_breadcrumbs_on == 1 ) {
			?>
			<tr>
				<td class="csbox-label">
				<p><label for="<?php echo $this->field_id( 'convertica_mob_hide_breadcrumbs' ); ?>"><?php _e( 'Hide Breadcrumbs', 'convertica-td' ); ?></label></p>
				</td>
				
				<td>
				<p><input type="checkbox" id="<?php echo $this->field_id( 'convertica_mob_hide_breadcrumbs' ); ?>" name="<?php echo $this->field_name( 'convertica_mob_hide_breadcrumbs' ); ?>" value="1" <?php checked( $this->get_field_value( 'convertica_mob_hide_breadcrumbs' ), true ); ?> /></p>
				</td>
			</tr>
			<?php
		}
		
		/* Let's check if Before Header widget is enabled and active. *
		ob_start(); // So that the widget output does not show up
			convertica_sb_before_header();
		$is_before_header_widget = ob_get_clean();
		$is_before_header_widget = empty( $is_before_header_widget ) ? 0 : 1;
		
		if( $is_before_header_widget == 1 ) {*/
			?>
			<tr>
				<td class="csbox-label">
				<p><label for="<?php echo $this->field_id( 'convertica_mob_hide_widgets_above_header' ); ?>"><?php _e( 'Hide Widgets Above Header', 'convertica-td' ); ?></label></p>
				</td>
				<td>
				<p><input type="checkbox" id="<?php echo $this->field_id( 'convertica_mob_hide_widgets_above_header' ); ?>" name="<?php echo $this->field_name( 'convertica_mob_hide_widgets_above_header' ); ?>" value="1" <?php checked( $this->get_field_value('convertica_mob_hide_widgets_above_header'), true ); ?> /></p>
				</td>
			</tr>
		<!-- } -->
		
		<tr>
			<td class="csbox-label">
			<p><label for="<?php echo $this->field_id( 'convertica_mob_hide_widgets_below_header' ); ?>"><?php _e( 'Hide Widgets Below Header', 'convertica-td' ); ?></label></p>
			</td>
			<td>
			<p><input type="checkbox" id="<?php echo $this->field_id( 'convertica_mob_hide_widgets_below_header' ); ?>" name="<?php echo $this->field_name( 'convertica_mob_hide_widgets_below_header' ); ?>" value="1" <?php checked( $this->get_field_value( 'convertica_mob_hide_widgets_below_header' ), true ); ?> /></p>
			</td>
		</tr>
				
		<tr>
			<td class="csbox-label">
			<p><label for="<?php echo $this->field_id( 'convertica_mob_hide_widgets_above_footer' ); ?>"><?php _e( 'Hide Widgets Before Footer', 'convertica-td' ); ?></label></p>
			</td>
			<td>
			<p><input type="checkbox" id="<?php echo $this->field_id( 'convertica_mob_hide_widgets_above_footer' ); ?>" name="<?php echo $this->field_name( 'convertica_mob_hide_widgets_above_footer' ); ?>" value="1" <?php checked( $this->get_field_value( 'convertica_mob_hide_widgets_above_footer' ), true ); ?> /></p>
			</td>
		</tr>
		
		<?php
		$current_layout = hybrid_get_theme_layout();
		if( $current_layout != '1c' ) {
			?>
			<tr>
				<td class="csbox-label">
				<p><label for="<?php echo $this->field_id('convertica_mob_hide_sidebars'); ?>"><?php _e('Hide Sidebar(s)', 'convertica-td'); ?></label></p>
				</td>
				<td>
				<p><input type="checkbox" id="<?php echo $this->field_id('convertica_mob_hide_sidebars'); ?>" name="<?php echo $this->field_name('convertica_mob_hide_sidebars'); ?>" value="1" <?php checked($this->get_field_value('convertica_mob_hide_sidebars'), true ); ?> /></p>
				</td>
			</tr>
			<?php
		}
		
		
		$global_fwidgets = convertica_get_setting( 'convertica_footer_widgets' );
		if ( $global_fwidgets ) {
			?>
			<tr>
				<td class="csbox-label">
				<p><label for="<?php echo $this->field_id( 'convertica_mob_hide_fwidgets' ); ?>"><?php _e( 'Hide Footer Widgets', 'convertica-td' ); ?></label></p>
				</td>
				<td>
				<p><input type="checkbox" id="<?php echo $this->field_id( 'convertica_mob_hide_fwidgets' ); ?>" name="<?php echo $this->field_name( 'convertica_mob_hide_fwidgets' ); ?>" value="1" <?php checked( $this->get_field_value( 'convertica_mob_hide_fwidgets' ), true ); ?> /></p>
				</td>
			</tr>
			<?php
		}
		
		?>
		</table>
		</div>
		<?php
	}

	/* Metabox for Footer Widgets settings */
	
	function convertica_footer_meta_box(){
		?>
		<p><?php _e( 'Use these settings to configure the footer widgets for your theme. You can choose to show or hide the footer widgets and select the number of footer widgets you want to include in your theme.', 'convertica-td' ); ?></p>

		<div class="csbox-section">
		<table>
		<!-- Build the dropdown to enable or disable the footer widgets -->
			<tr>
				<td class="csbox-label">
					<p><label for="<?php echo $this->field_id( 'convertica_footer_widgets' ); ?>"><?php _e( 'Footer Widgets Support', 'convertica-td' ); ?></label></p>
				</td>
				
				<td>
					<?php
					echo '<select id="' . $this->field_id('convertica_footer_widgets'). '" name="' . $this->field_name('convertica_footer_widgets').'">';
					
					$cfwshow = array(
						'Enabled' => 1,
						'Disabled' => 0
					);
					
					$cfw = $this->get_field_value( 'convertica_footer_widgets' );
					
					foreach ( $cfwshow as $fwshow => $val ) {	
						echo "<option " . selected( $val, $cfw, false ) . " value=\"$val\">" . $fwshow . "</option>\n";
					}
					
					echo '</select>';
					?>
				</td>
			</tr>
		</table>	
		
		<!-- Build the dropdown for selecting the number of footer widgets -->
		<div id="convertica-conditional-hide">
		<table>
			<tr>
				<td class="csbox-label">
					<p><label for="<?php echo $this->field_id( 'convertica_footer_widgets_count' ); ?>"><?php _e( 'No. of Footer Widgets', 'convertica-td' ); ?></label></p>
				</td>
				
				<td>
					<?php
					echo '<select id="' . $this->field_id('convertica_footer_widgets_count'). '" name="' . $this->field_name('convertica_footer_widgets_count').'">';
					
					$cfwnum = array(
						'One' => 1,
						'Two' => 2,
						'Three' => 3,
						'Four' => 4
					);
					
					$cfwn = $this->get_field_value( 'convertica_footer_widgets_count' );
					
					foreach ($cfwnum as $wnum => $val) {
						
						echo "<option " . selected($val, $cfwn, false) . " value=\"$val\">" . $wnum . "</option>\n";
						
					}
					echo '</select>';
					?>
				</td>
			</tr>	
		</table>
		</div>
		
		</div>
		<?php

	}
	
	/* Metabox for Additional Google fonts settings */
	
	function convertica_additional_gfonts() {
		?>
		<p><?php printf( __( 'Select the Google fonts that you wish to enqueue and use on your site using the select box below. All Google fonts shipped with %s are available to you to choose from. %sNo more tinkering with the code to use Google fonts%s.', 'convertica-td' ), convertica_get_theme_name(), '<em>', '</em>' ); ?></p>
		
		<p>
			<label for="<?php echo $this->field_id( 'convertica_frontend_gfonts' ); ?>" class="screen-reader-text"><?php _e( 'Select the Google fonts to enqueue' ); ?></label>
			
			<?php
			$all_fonts = convertica_edge_get_super_fonts();
			$google_fonts = array();
			
			foreach( $all_fonts as $font => $font_data ) {
				if ( $font_data['font_type'] === 'google' ) {
					$google_fonts[$font] = $all_fonts[$font];
				}
			}
			$additional_fonts = convertica_get_setting( 'convertica_frontend_gfonts' );
			?>
			
			<select id="<?php echo $this->field_id( 'convertica_frontend_gfonts' ); ?>" class="convertica-frontend-fonts" data-placeholder="<?php _e( 'Choose the fonts to use..', 'convertica-td' ); ?>" name="<?php echo $this->field_name( 'convertica_frontend_gfonts' ); ?>[]" multiple>
				<option value=""></option>
				<?php
				foreach( $google_fonts as $key => $font_data ) {
					/*
					if ( empty( $additional_fonts ) ) {
						$selected = '';
					} else {
						$selected = in_array( $key, $additional_fonts );
					}
					*/
					?>
					<option value="<?php echo $key; ?>" <?php selected( $key, true ); ?>><?php echo $font_data['name'] ?></option>
					<?php
				}
				?>
			</select>
		</p>
		<?php
	}
	
	/* Metabox for Google font API key settings */
	
	function convertica_googleapi_meta_box() {
		?>
		<p><?php printf( __( 'Key in your Google Fonts API key here to allow %1$s to get the latest Google fonts. You can keep using the defualt Google fonts shipped with %1$s if you don\'t have the API key.', 'convertica-td' ), convertica_get_theme_name() ); ?></p>
		
		<p><input class="convertica-gfonts-key" placeholder="<?php _e( 'Google Fonts API Key', CHILD_DOMAIN ); ?>" type="text" id="<?php echo $this->field_id( 'convertica_gfonts_key' ); ?>" name="<?php echo $this->field_name( 'convertica_gfonts_key' ); ?>" value="<?php echo $this->get_field_value( 'convertica_gfonts_key' ); ?>" /></p>
		
		<p><em><strong><?php _e( 'Note: ', 'convertica-td' ) ?></strong><?php printf( __( 'You can get the %sGoogle Fonts API key here%s' ), '<a href="https://developers.google.com/fonts/docs/developer_api#Auth" target="_blank">', '</a>' ); ?></em></p>
		<?php
	}

	function convertica_goodies_meta_box() {	
		
		?>
		<div id="convertica_edge" style="min-height: 200px;background: #0af; color: #fff; padding: 1.618em; font-weight:bolder;">
			<p><?php _e( 'Try Convertica Edge. We promise you\'ll never look back!', 'convertica-td' ); ?></p>
		</div>
		<?php
		
	}
	
}