<?php

function convertica_edge_get_super_fonts( ) {
	if ( !get_transient( 'convertica_edge_fonts_list' ) ) {
		$json       = array( );
		$gfonts_key = 0;
		if ( $gfonts_key ) {
			$gfont_parm = '&key=' . $gfonts_key;
			$json       = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha' . $gfont_parm, array(
				 'sslverify' => false 
			) );
			if ( !is_wp_error( $json ) ) {
				$web_fonts = json_decode( $json[ 'body' ], true );
				if ( !isset( $web_fonts[ 'error' ] ) ) {
					$json = $web_fonts;
				} else {
					unset( $json );
					$json = array( );
				}
			} else {
				unset( $json );
				$json = array( );
			}
		}
		if ( ( !$gfonts_key || !$json ) && file_exists( CONVERTICA_EDGE_DIR . 'fonts/fonts.json' ) ) {
			$json = file_get_contents( CONVERTICA_EDGE_DIR . 'fonts/fonts.json' );
			$json = json_decode( $json, 1 );
		}
		$web_fonts = array( );
		foreach ( $json[ 'items' ] as $item ) {
			$urls = array( );
			foreach ( $item[ 'variants' ] as $variant ) {
				$name             = str_replace( ' ', '+', $item[ 'family' ] );
				$urls[ $variant ] = "https://fonts.googleapis.com/css?family={$name}:{$variant}";
			}
			$atts             = array(
				 'name' => $item[ 'family' ],
				'category' => $item[ 'category' ],
				'font_type' => 'google',
				'font_weights' => $item[ 'variants' ],
				'subsets' => $item[ 'subsets' ],
				'files' => $item[ 'files' ],
				'urls' => $urls 
			);
			$id               = strtolower( str_replace( ' ', '_', $item[ 'family' ] ) );
			$web_fonts[ $id ] = $atts;
		}
		$websafe_list  = array(
			 'inherit' => array(
				 'weights' => array(
					 'inherit',
					 '400',
					 '700'
				),
				'category' => 'inherit' 
			),
			'Arial' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Century Gothic' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Courier New' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'monospace' 
			),
			'Georgia' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'serif' 
			),
			'Helvetica' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Impact' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Lucida Console' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'monospace' 
			),
			'Lucida Sans Unicode' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Palatino Linotype' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'serif' 
			),
			'Tahoma' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Times New Roman' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'serif' 
			),
			'Trebuchet MS' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			),
			'Verdana' => array(
				 'weights' => array(
					 '400',
					'400italic',
					'700',
					'700italic' 
				),
				'category' => 'sans-serif' 
			) 
		);
		$websafe_fonts = array( );
		foreach ( $websafe_list as $font => $attributes ) {
			$urls = array( );
			foreach ( $attributes[ 'weights' ] as $variant ) {
			}
			$atts                 = array(
				'name' => $font,
				'font_type' => 'default',
				'category' => $attributes[ 'category' ],
				'font_weights' => $attributes[ 'weights' ],
				'subsets' => array( ),
				'files' => array( ) 
			);
			$id                   = strtolower( str_replace( ' ', '_', $font ) );
			$websafe_fonts[ $id ] = $atts;
		}
		$fontslist = array_merge( $websafe_fonts, $web_fonts );
		set_transient( 'convertica_edge_fonts_list', $fontslist, 60 * 60 * 24 * 7);
		return $fontslist;
	} else {
		return get_transient( 'convertica_edge_fonts_list' );
	}
}

function convertica_edge_get_fonts_choices( ) {
	$all_fonts = convertica_edge_get_super_fonts();
	$fonts     = array( );
	foreach ( $all_fonts as $font_key => $font ) {
		$fonts[ $font_key ] = $font[ 'name' ];
	}
	return $fonts;
}

function convertica_edge_get_font_variants( $font_id ) {
	$choices = array( );
	if ( $font_id ) {
		$font_specs = convertica_edge_get_font( $font_id );
		$font_specs = $font_specs[ 'font_weights' ];
		foreach ( $font_specs as $variant ) {
			$choices[ $variant ] = $variant;
		}
	} else {
		$choices = array(
			 'normal' => 'normal',
			'bold' => 'bold' 
		);
	}
	return $choices;
}

function convertica_edge_get_font( $font ) {
	$all_fonts = convertica_edge_get_super_fonts();
	$font      = $all_fonts[ $font ];
	return $font;
}

function convertica_edge_get_font_family( $font_id ) {
	$font        = convertica_edge_get_font( $font_id );
	$font_family = $font[ 'name' ];
	if ( $font_family == 'inherit' )
		return 'inherit';
	$font_family = '"' . $font_family . '"';
	if ( $font[ 'category' ] == 'handwriting' || $font[ 'category' ] == 'display' ) {
		$font_family .= ', cursive';
	} else {
		$font_family .= ', ' . $font[ 'category' ];
	}
	return $font_family;
}

function convertica_get_saved_web_fonts_ids( $settings ) {
	$font_settings = array( );
	foreach ( $settings as $key => $value ) {
		if ( preg_match( '/font\_family/', $key ) ) {
			$font_settings[ $key ] = $value;
		}
	}
	return $font_settings;
}

function convertica_filter_font_family_settings( $settings ) {
	$font_settings = array( );
	$variant;
	foreach ( $settings as $key => $value ) {
		if ( preg_match( '/font\_family/', $key ) ) {
			$font_settings[ $key ]                                      = $value;
			//$font_settings[ str_ireplace( '_family', '_specs', $key ) ] = convertica_edge_get_font( $value );
			$variant                                                    = str_ireplace( '_family', '_variant', $key );
			if ( array_key_exists( $variant, $settings ) ) {
				//if ( in_array( $settings[ $variant ], $font_settings[ str_ireplace( '_family', '_specs', $key ) ][ 'font_weights' ] ) ) {
					$font_settings[ str_ireplace( '_family', '_variant', $key ) ] = $settings[ $variant ];
				//} else {
				//	$font_settings[ str_ireplace( '_family', '_variant', $key ) ] = array_shift( $font_settings[ str_ireplace( '_family', '_specs', $key ) ][ 'font_weights' ] );
				//}
			}
		}
	}
	return $font_settings;
}

//add_action('convertica_before_header','convertica_font_debug');

function convertica_font_debug(){
	$opt = array(
		'body_font_family' => 'inherit',
		'body_font_variant' => '700',
		);
	clog(convertica_filter_font_family_settings($opt));
}

function convertica_get_font_weight( $variant ) {
	if ( stristr( $variant, 'inherit' ) ) {
		return 'inherit';
	}
	$weight = intval( $variant, 10 );
	if ( !$weight || stristr( $variant, 'regular' ) ) {
		$weight = '400';
	}
	return $weight;
}

function convertica_get_font_style( $variant ) {
	$style = "normal";
	if ( $variant == 'italic' || stristr( $variant, 'italic' ) ) {
		$style = 'italic';
	}
	return $style;
}

function convertica_edge_enqueue_web_fonts(){
	$settings   = convertica_get_mods();
	$used_fonts = convertica_get_saved_web_fonts_ids( $settings );
	$web_fonts;
	$variant;
	foreach ( $used_fonts as $key => $value ) {
		$spec = convertica_edge_get_font( $value );
		if ( $spec[ 'font_type' ] == 'google' ) {
			$web_fonts[ $key ] = $spec[ 'name' ];
			$variant           = str_ireplace( '_family', '_variant', $key );
			if ( array_key_exists( $variant, $settings ) ) {
				$web_fonts[ $key ] = $web_fonts[ $key ] . ':' . convertica_get_mod( $variant );
			}
		}
	}
	if ( !empty( $web_fonts ) ) {
		$web_fonts = implode( '|', $web_fonts );
		wp_enqueue_style( 'convertica-edge-web-fonts', 'https://fonts.googleapis.com/css?family=' . $web_fonts . '&subset=all' );
	}
}

//add_action( 'wp_head', 'convertica_check_fonts' );

function convertica_check_fonts() {
	$settings   = convertica_get_mods();
	$used_fonts = convertica_get_saved_web_fonts_ids( $settings );
	$additional_fonts = convertica_get_setting( 'convertica_frontend_gfonts' );
	$web_fonts = array();
	foreach ( $used_fonts as $key => $value ) {
		$spec = convertica_edge_get_font( $value );
		if ( $spec[ 'font_type' ] == 'google' ) {
			$web_fonts[ $key ] = str_ireplace( ' ', '+', $spec[ 'name' ] );
			$variant           = str_ireplace( '_family', '_variant', $key );
			if ( array_key_exists( $variant, $settings ) ) {
				$web_fonts[ $key ] = $web_fonts[ $key ] . ':' . convertica_get_mod( $variant );
			}
		}
	}
	clog($additional_fonts);
}


/*
function convertica_edge_enqueue_web_fonts( ) {
	return;
	$settings   = convertica_get_mods();
	$used_fonts = convertica_get_saved_web_fonts_ids( $settings );
	$web_fonts;
	$variant;
	foreach ( $used_fonts as $key => $value ) {
		$spec = str_ireplace( '_family', '_specs', $key );
		$spec = convertica_get_mod( $spec );
		if ( $spec[ 'font_type' ] == 'google' ) {
			$web_fonts[ $key ] = $spec[ 'name' ];
			$variant           = str_ireplace( '_family', '_variant', $key );
			if ( array_key_exists( $variant, $settings ) ) {
				$web_fonts[ $key ] = $web_fonts[ $key ] . ':' . convertica_get_mod( $variant );
			}
		}
	}
	if ( !empty( $web_fonts ) ) {
		$web_fonts = implode( '|', $web_fonts );
		wp_enqueue_style( 'convertica-edge-web-fonts', 'https://fonts.googleapis.com/css?family=' . $web_fonts . '&subset=all' );
	}
}
*/