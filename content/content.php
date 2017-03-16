<article <?php hybrid_attr( 'post' ); ?>>
	<?php if ( is_singular( get_post_type() ) ) : // If viewing a single post. ?>
		<?php do_action('convertica_before_entry_header'); ?>
		<header class="entry-header">
			<h1 <?php hybrid_attr( 'entry-title' ); ?>><?php single_post_title(); ?></h1>
			<div class="entry-byline">
				<?php _e('by ','convertica-td');?><span <?php hybrid_attr( 'entry-author' ); ?>><?php the_author_posts_link(); ?></span>
				<?php _e('on ','convertica-td');?><time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
				<?php 
				if (get_comments_number()) {
					echo '&bull;&nbsp;';
				}
				comments_popup_link( '', __( "1 Comment" ), __('% Comments'), 'comments-link', '' ); ?>
				<?php edit_post_link(__('Edit', 'convertica-td')); ?>
			</div><!-- .entry-byline -->
		</header><!-- .entry-header -->
		<?php do_action('convertica_after_entry_header'); ?>
		<div <?php hybrid_attr( 'entry-content' ); ?>>
			<?php the_content(); ?>
			<?php do_action('convertica_after_entry_content'); ?>
			<?php wp_link_pages(); ?>
		</div><!-- .entry-content -->
		<footer class="entry-footer">
			<?php hybrid_post_terms( array( 'taxonomy' => 'category', 'text' => '<span class="categories">Categories:</span> %s' ) ); ?>
			<?php hybrid_post_terms( array( 'taxonomy' => 'post_tag', 'text' => '<span class="tags">Tags:</span> %s' , 'before' => '<br />' ) ); ?>
		</footer><!-- .entry-footer -->
		<?php do_action('convertica_after_entry'); ?>
	<?php else : // If not viewing a single post. ?>
		<?php do_action('convertica_before_entry_header'); ?>
		<header class="entry-header">
			<?php the_title( '<h2 ' . hybrid_get_attr( 'entry-title' ) . '><a href="' . get_permalink() . '" rel="bookmark" itemprop="url">', '</a></h2>' ); ?>
			<div class="entry-byline">
				<?php _e('by ','convertica-td');?><span <?php hybrid_attr( 'entry-author' ); ?>><?php the_author_posts_link(); ?></span>
				<?php _e('on ','convertica-td');?><time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
				<?php
				if (get_comments_number()) {
					echo '&bull;&nbsp;';
				}
				comments_popup_link( '', __( "1 Comment" ), __('% Comments'), 'comments-link', '' ); ?>
				<?php edit_post_link(__('Edit', 'convertica-td')); ?>
			</div><!-- .entry-byline -->
		</header><!-- .entry-header -->
		<?php do_action('convertica_after_entry_header'); ?>
		
			<?php
			
			if(convertica_get_mod('archive_style') == 'excerpts') {
					if(function_exists('is_bbPress') && is_bbPress()) {
						?><div <?php hybrid_attr( 'entry-content' ); ?>>
						<?php
						the_content();
						//the_excerpt();
					}
					else {
						?><div <?php hybrid_attr( 'entry-summary' ); ?>>
						<?php
						the_excerpt();
					}
				}
			if(convertica_get_mod('archive_style') == 'content') {
					?><div <?php hybrid_attr( 'entry-content' ); ?>>
						<?php
						the_content();
				}
			?>
			<?php do_action('convertica_after_entry_content'); ?>
		</div><!-- .entry-summary -->
		<?php do_action('convertica_after_entry'); ?>
	<?php endif; // End single post check. ?>
</article><!-- .entry -->