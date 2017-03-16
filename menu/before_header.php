<?php
$args = array(
		'theme_location'  => 'before_header',
		'container'       => '',
		'menu_id'         => 'menu-before_header-items',
		'menu_class'      => 'menu-items',
		'fallback_cb'     => false,
		'echo'     		  => 0,
		'items_wrap'      => '<div class="wrap"><ul id="%s" class="%s">%s</ul></div>'
	);
if ( has_nav_menu( 'before_header' ) && wp_nav_menu($args) ) { // Check if there's a menu assigned to the 'before_header' location. 
	$nav = wp_nav_menu( $args );
	?>
	<nav <?php hybrid_attr( 'menu', 'before_header' ); ?>>

		<?php echo $nav; ?>

	</nav><!-- #menu-before_header -->

<?php 
} // End check for menu.
