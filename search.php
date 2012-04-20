<?php get_header() ?>

	<div id="content">
		<div class="padder">
			
		<?php do_action( 'cc_first_inside_padder' ); ?>

		<?php do_action( 'bp_before_blog_search' ) ?>

		<div class="page" id="blog-search">

			<h2 class="pagetitle"><?php _e( 'Blog', 'cc' ) ?></h2>

			<?php locate_template( array( 'loop.php' ), true );  ?>		


		</div>

		<?php do_action( 'bp_after_blog_search' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer() ?>