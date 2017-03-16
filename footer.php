		<?php
		do_action('convertica_do_sidebar');
		?>
		</div> <!-- .layout-container -->
		<?php
		do_action('convertica_do_sidebar_alt');
		?>
		</div><!-- .wrap -->
		</div><!-- #main -->
		<?php do_action('convertica_before_footer'); ?>
		<?php do_action('convertica_do_footer'); ?>
		<?php do_action('convertica_after_footer'); ?>
	</div><!-- #container -->
	<?php wp_footer(); // WordPress hook for loading JavaScript, toolbar, and other things in the footer. ?>
<?php do_action('convertica_atn_after_html'); ?>
</body>
</html>