<?php


function convertica_archive_header_markup(){
	?>
	<header <?php hybrid_attr( 'archive-header' ); ?>>

	<h1 <?php hybrid_attr( 'archive-title' ); ?>><?php the_archive_title(); ?></h1>

	<?php if ( ! is_paged() && $desc = get_the_archive_description() ) : // Check if we're on page/1. ?>

		<div <?php hybrid_attr( 'archive-description' ); ?>>
			<?php echo $desc; ?>
		</div><!-- .archive-description -->

	<?php endif; // End paged check. ?>

</header><!-- .archive-header -->
<?php
}

function convertica_loop_nav(){
	if ( is_singular( 'post' ) ) : // If viewing a single post page. ?>

			<div class="loop-nav">
				<?php previous_post_link( '<div class="prev">' . esc_html__( '&larr;&nbsp;Previous Post: %link', 'convertica-td' ) . '</div>', '%title' ); ?>
				<?php next_post_link(     '<div class="next">' . esc_html__( 'Next Post: %link&nbsp;&rarr;',     'convertica-td' ) . '</div>', '%title' ); ?>
			</div><!-- .loop-nav -->

		<?php elseif ( is_home() || is_archive() || is_search() ) : // If viewing the blog, an archive, or search results. ?>

			<?php the_posts_pagination(
				array( 
					'prev_text' => esc_html_x( '&larr; Previous', 'posts navigation', 'convertica-td' ), 
					'next_text' => esc_html_x( 'Next &rarr;',     'posts navigation', 'convertica-td' )
				) 
			); ?>

		<?php endif; // End check for type of page being viewed.
}

function convertica_comments(){
	// If a post password is required or no comments are given and comments/pings are closed, return.
	if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
		return;
	?>

	<section id="comments-template">

		<?php if ( have_comments() ) : // Check if there are any comments. ?>

			<div id="comments">

				<h3 id="comments-number"><?php comments_number(); ?></h3>

				<ol class="comment-list">
					<?php wp_list_comments(
						array(
							'style'        => 'ol',
							'callback'     => 'hybrid_comments_callback',
							'end-callback' => 'hybrid_comments_end_callback'
						)
					); ?>
				</ol><!-- .comment-list -->

				<?php
				// Loads the misc/comments-nav.php template.
				if ( get_option( 'page_comments' ) && 1 < get_comment_pages_count() ) : // Check for paged comments. ?>

					<nav class="comments-nav" role="navigation" aria-labelledby="comments-nav-title">

						<h3 id="comments-nav-title" class="screen-reader-text"><?php esc_html_e( 'Comments Navigation', 'convertica-td' ); ?></h3>

						<?php previous_comments_link( esc_html_x( '&larr; Previous', 'comments navigation', 'convertica-td' ) ); ?>

						<span class="page-numbers"><?php 
							// Translators: Comments page numbers. 1 is current page and 2 is total pages.
							printf( esc_html__( 'Page %1$s of %2$s', 'convertica-td' ), get_query_var( 'cpage' ) ? absint( get_query_var( 'cpage' ) ) : 1, get_comment_pages_count() ); 
						?></span>

						<?php next_comments_link( esc_html_x( 'Next &rarr;', 'comments navigation', 'convertica-td' ) ); ?>

					</nav><!-- .comments-nav -->

				<?php endif; // End check for paged comments.
				// Loads the misc/comments-nav.php template.
				?>
			</div><!-- #comments-->

		<?php endif; // End check for comments. ?>

		<?php 
		// <!-- Loads the misc/comments-error.php template. 
		if ( pings_open() && ! comments_open() ) : ?>

			<p class="comments-closed pings-open">
				<?php
					// Translators: The two %s are placeholders for HTML. The order can't be changed.
					printf( esc_html__( 'Comments are closed, but %strackbacks%s and pingbacks are open.', 'convertica-td' ), '<a href="' . esc_url( get_trackback_url() ) . '">', '</a>' );
				?>
			</p><!-- .comments-closed .pings-open -->

		<?php elseif ( ! comments_open() ) : ?>

			<p class="comments-closed">
				<?php esc_html_e( 'Comments are closed.', 'convertica-td' ); ?>
			</p><!-- .comments-closed -->

		<?php
		endif;
		// --> Loads the misc/comments-error.php template. 
		?>

		<?php comment_form(); // Loads the comment form. ?>

	</section><!-- #comments-template -->
<?php
}

function convertica_sb_before_header(){
	$show = apply_filters('convertica_show_sb_before_header',true);
	if($show) {
		hybrid_get_sidebar('before_header');
	}
}

function convertica_sb_after_header(){
	$show = apply_filters('convertica_show_sb_after_header',true);
	if($show) {
		hybrid_get_sidebar('after_header');
	}
}

function convertica_sb_before_footer(){
	$show = apply_filters('convertica_show_sb_before_footer',true);
	if($show) {
		hybrid_get_sidebar('before_footer');
	}
}

function convertica_header(){
	$show = apply_filters('convertica_show_header',true);
	if($show) {
	?>
	<header <?php hybrid_attr( 'header' ); ?>>
		<div class="wrap">
		<?php do_action('convertica_before_branding'); ?>
			<?php if ( display_header_text() ) : // If user chooses to display header text. ?>
				<div <?php hybrid_attr( 'branding' ); ?>>
					<?php hybrid_site_title(); ?>
					<?php hybrid_site_description(); ?>
				</div><!-- #branding -->
			<?php endif; // End check for header text. ?>
			<?php do_action('convertica_after_branding'); ?>
		</div><!-- .wrap -->
	</header><!-- #header -->
		<?php if ( get_header_image() && ! display_header_text() ) : // If there's a header image but no header text. ?>
			<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home"><img class="header-image" src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
		<?php elseif ( get_header_image() ) : // If there's a header image. ?>
			<img class="header-image" src="<?php header_image(); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" alt="" />
		<?php endif; // End check for header image.
	}
}

function convertica_footer(){
	?>
	<footer <?php hybrid_attr( 'footer' ); ?>>
		<div class="wrap">
		<?php hybrid_get_menu( 'footer' ); // Loads the menu/subsidiary.php template.
		$footer_creds = '
			<p class="credit">' . sprintf( esc_html__( 'Copyright &#169; %1$s %2$s. Powered by %3$s. %4$s is built on %5$s.', 'convertica-td' ), date_i18n( 'Y' ), hybrid_get_site_link(), hybrid_get_wp_link(), hybrid_get_theme_link(), '<a href="http://themehybrid.com/hybrid-core">Hybrid Core</a>' ) . '</p>';
		$footer_creds = apply_filters( 'convertica_footer_text', $footer_creds );
		echo $footer_creds;
		?>
		<!-- .credit --></div><!-- .wrap -->
		</footer><!-- #footer -->
		<?php
}

function convertica_sidebar_primary() {
	$show = apply_filters('convertica_show_sidebar_primary', true);
	if($show) {
		hybrid_get_sidebar( 'primary' ); // Loads the sidebar/primary.php template. 
	}
}

function convertica_sidebar_subsidiary() {
	$show = apply_filters('convertica_show_sidebar_subsidiary', true);
	if($show) {
		hybrid_get_sidebar( 'subsidiary' ); // Loads the sidebar/primary.php template. 
	}
}

function convertica_after_entry_widget( ) {
	$show = apply_filters('convertica_show_sb_after_entry',true);
	if($show) {
		hybrid_get_sidebar('after_entry');
	}
}


// show breadcrumbs conditionally
function convertica_show_breadcrumb( ) {
	// Allow filtering this till we handle custom posts and taxonomies
	$show = apply_filters( 'convertica_show_breadcrumb', convertica_get_mod('archive_breadcrumbs_setting') );

	if ( $show ) {

		if(yoast_bc_enabled()){
			yoast_breadcrumb('<nav id="breadcrumbs" class="breadcrumbs">','</nav>');
		}
		elseif( function_exists('bcn_display') ) {
			echo '<nav class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . bcn_display(1) . ' </nav>';
		}
		else {

			$args = array(
				'container' => 'nav',
				'separator' => '>',
				'show_on_front' => false,
				'labels' => array(
					 'browse' => esc_html__( 'You are here:', 'convertica-td' ) 
					) 
			);

			if(is_page()) {
				$args['post_taxonomy'] = array( 'page' => 'category' );
			}
			$args = apply_filters('breadcrumb_trail_args',$args);
			breadcrumb_trail( $args );
		}
	}
}


function yoast_bc_enabled(){
	
	if(!function_exists('yoast_breadcrumb')) {
		return false;
	}
	
	$yoast_bc = get_option('wpseo_internallinks');
	
	if($yoast_bc && $yoast_bc['breadcrumbs-enable'] === true) {
		return true;
	}
	
	return false;

}

function convertica_before_header_menu(){
	hybrid_get_menu( 'before_header' ); // Loads the menu/primary.php template.
}


function convertica_after_header_menu(){
	hybrid_get_menu( 'after_header' ); // Loads the menu/primary.php template.
}


function convertica_after_entry_header(){
	if(convertica_get_mod('archive_featured_image_setting') == '1') {
		$float = 'align'.convertica_get_mod('archive_featured_image_float_setting');
		get_the_image(array('size' => convertica_get_mod('archive_featured_image_size_setting'), 'image_class' => "featured after $float"));
	}
}