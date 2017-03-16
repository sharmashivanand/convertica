<?php get_header(); // Loads the header.php template. ?>
<div class="column-container">
<main <?php hybrid_attr( 'content' ); ?>>

	<?php

	do_action('convertica_before_loop');

	if ( have_posts() ) { // Checks if any posts were found.

		while ( have_posts() ) { // Begins the loop through found posts.

			the_post(); // Loads the post data.

			hybrid_get_content_template(); // Loads the content/*.php template.

			if ( is_singular() ) { // If viewing a single post/page/CPT.

				if( current_theme_supports( 'convertica-comments' ) )
					comments_template( '', true ); // Loads the comments.php template.

			} // End check for single post.

		} // End found posts loop.

		// Loads the misc/loop-nav.php template.
		convertica_loop_nav();
		// Loads the misc/loop-nav.php template.

	}

	else { // If no posts were found.

		locate_template( array( 'content/error.php' ), true ); // Loads the content/error.php template.

	} // End check for posts. 

	do_action('convertica_after_loop');

	?>

</main><!-- #content -->

<?php get_footer(); // Loads the footer.php template. ?>