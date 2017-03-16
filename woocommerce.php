<?php get_header(); // Loads the header.php template. ?>
<div class="column-container">
<main <?php hybrid_attr( 'content' ); ?>>

	<?php if ( ! is_front_page() && hybrid_is_plural() ) : // If viewing a multi-post page 

	//locate_template( array( 'misc/archive-header.php' ), true ); // Loads the misc/archive-header.php template. 

	endif; // End check for multi-post page. 

	//if ( have_posts() ) : // Checks if any posts were found. 

		//hybrid_get_content_template(); // Loads the content/*.php template. 
		//add_filter( 'woocommerce_show_page_title', '__return_false' );
		//remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
		//remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
		//remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

		woocommerce_content();
		

		convertica_loop_nav(); 

	//else : // If no posts were found. 

	//locate_template( array( 'content/error.php' ), true ); // Loads the content/error.php template. 

	//endif; // End check for posts. 

	?>

</main><!-- #content -->

<?php get_footer(); // Loads the footer.php template. ?>