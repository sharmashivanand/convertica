<?php
if ( in_array(hybrid_get_theme_layout(),array('3c-r','3c-l','3c-c')) ) { // If is a three-column layout.
	if ( is_active_sidebar( 'subsidiary' ) ) { // If the sidebar has widgets. ?>
		<aside <?php hybrid_attr( 'sidebar', 'subsidiary' ); ?>>
			<?php dynamic_sidebar( 'subsidiary' ); // Displays the subsidiary sidebar. ?>
		</aside><!-- #sidebar-subsidiary -->
		<?php
	}
	else { // If the sidebar has no widgets. ?>
		<aside <?php hybrid_attr( 'sidebar', 'subsidiary' ); ?>>
		<?php
		the_widget(
			'WP_Widget_Text',
			array(
				'title'  => __( 'Example Widget', 'convertica-td' ),
				// Translators: The %s are placeholders for HTML, so the order can't be changed.
				'text'   => sprintf( __( 'This is an example widget to show how the Secondary sidebar looks by default. You can add custom widgets from the %swidgets screen%s in the admin.', 'convertica-td' ), current_user_can( 'edit_theme_options' ) ? '<a href="' . admin_url( 'widgets.php' ) . '">' : '', current_user_can( 'edit_theme_options' ) ? '</a>' : '' ),
				'filter' => true,
			),
			array(
				'before_widget' => '<section class="widget widget_text">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>'
			)
		); ?>
	</aside><!-- #sidebar-subsidiary -->
	<?php
	} // End widgets check.
} // End layout check.
