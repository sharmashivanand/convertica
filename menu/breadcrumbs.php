<?php if ( function_exists( 'breadcrumb_trail' ) ) : // Check for breadcrumb support. ?>

	<?php breadcrumb_trail(
		array( 
			'container'     => 'nav', 
			'separator'     => '>', 
			'show_on_front' => true,

			'labels'        => array( 
				'browse' => esc_html__( 'You are here:', 'convertica-td' ) 
			) 
		) 
	); ?>

<?php endif; // End check for breadcrumb support. ?>