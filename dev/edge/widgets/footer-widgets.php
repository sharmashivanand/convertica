<?php

/* Convertica Footer Widgets */

/* Output footer widgets */

add_action('wp_head','convertica_footer_widget_support');

function convertica_footer_widget_support() {
	$show_footer_widgets = convertica_get_setting('convertica_footer_widgets');
	if($show_footer_widgets) {
		add_action('convertica_before_footer', 'convertica_footer_widgets', 11);
	}
}

function convertica_footer_widgets() {
	$count = convertica_get_setting('convertica_footer_widgets_count');
	$show = apply_filters( 'convertica_show_footer_widgets', true );
	if($show) {
		?>
		<aside <?php hybrid_attr( 'sidebar', 'footer_widgets' ); ?>><?php
		echo '<div class="wrap">';
			convertica_do_dynamic_footer_widgets($count);
		echo '</div>';
		echo '</aside>';
	}
}

/* Dynamically Register Footer Widgets */

add_action('widgets_init', 'convertica_dynamic_footer_widgets');

function convertica_dynamic_footer_widgets() {
	$show_footer_widgets = convertica_get_setting('convertica_footer_widgets');
	if($show_footer_widgets) {
		$convertica_footer_widgets_count =  convertica_get_setting('convertica_footer_widgets_count');
		register_sidebars($convertica_footer_widgets_count, array(
			'name'          => ( $convertica_footer_widgets_count == 1 ) ? __('Footer Widget 1') : __( 'Footer Widget %d' ),
			'id'            => 'footer-widget',          
			'description'   => __('Footer Widget Area'),
			'class'         => 'footer-widget',
			'before_widget' => '<section class="widget footer-widget">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'

		));
	}	
}

/* Output footer widget area(s) on front end */

function convertica_do_dynamic_footer_widgets($num) {
	$widget_content = '';
	$counter = 1;
	if ($num == 1) {
	?>
		<aside <?php hybrid_attr( 'sidebar', 'footer-widget' ); ?>>
			<?php if ( is_active_sidebar( 'footer-widget' ) ) { // If the sidebar has widgets. ?>
				<?php dynamic_sidebar( 'footer-widget' ); } // Displays the footer widget. ?> 
		</aside>
		<?php
	} else {
		if ( ! is_active_sidebar( 'footer-widget' ) ) return;
		
		while ( $counter <= $num ) {
			ob_start();
			
			?>
			<section <?php hybrid_attr( 'sidebar', 'footer-widget-' . $counter ); ?>>
				<?php dynamic_sidebar( 'Footer Widget '. $counter );  // Displays the widget area. ?> 
			</section>
			<?php
			$widgets = ob_get_clean();
			$widget_content .= $widgets;
			$counter++;
		}
		
		echo $widget_content;
	}
}

/* Add body class on the basis of number of footer widgets */

add_filter( 'body_class', 'convertica_footer_add_body_class' );

function convertica_footer_add_body_class($classes) {
	$count =  convertica_get_setting('convertica_footer_widgets_count');
	$classes[] = 'footer-' . $count;
	return $classes;
}