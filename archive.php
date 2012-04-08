<?php get_header(); ?>
	<div id="content">
		<div class="padder">
		<?php do_action( 'bp_before_archive' ) ?>

		<div class="page" id="blog-archives">

		<?php locate_template( array( 'loop.php' ), true );  ?>		

		</div>

		<?php do_action( 'bp_after_archive' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer(); ?>
