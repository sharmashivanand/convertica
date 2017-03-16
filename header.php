<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>
<head <?php hybrid_attr( 'head' ); ?>>
<?php wp_head(); // Hook required for scripts, styles, and other <head> items. ?>
</head>
<body <?php hybrid_attr( 'body' ); ?>><?php do_action('convertica_atn_before_html'); ?>
	<div id="container">
		<div class="skip-link">
			<a href="#content" class="screen-reader-text"><?php esc_html_e( 'Skip to content', 'convertica-td' ); ?></a>
		</div><!-- .skip-link -->
		<?php do_action('convertica_before_header'); ?>
		<?php do_action('convertica_do_header'); ?>
		<?php do_action('convertica_after_header'); ?>
		<div id="main" class="main">
			<div class="wrap">
			<?php 
			convertica_show_breadcrumb();
			//hybrid_get_menu( 'breadcrumbs' ); // Loads the menu/breadcrumbs.php template. 
			?>
