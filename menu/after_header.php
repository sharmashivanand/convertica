<?php
$args = array(
	'theme_location'  => 'after_header',
	'container'       => '',
	'menu_id'         => 'menu-after_header-items',
	'menu_class'      => 'menu-items',
	'fallback_cb'     => false,
	'echo'     => 0,
	'items_wrap'      => '<div class="wrap"><ul id="%s" class="%s">%s</ul></div>'
);
if ( has_nav_menu( 'after_header' ) && wp_nav_menu($args) ) { // Check if there's a menu assigned to the 'after_header' location.
	
	$nav = wp_nav_menu( $args );
	?>
	<nav <?php hybrid_attr( 'menu', 'after_header' ); ?>>

		<?php echo $nav; ?>

	</nav><!-- #menu-after_header -->

<?php 
} // End check for menu.
