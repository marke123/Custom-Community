<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ) ?>
		
		<?php if ( $tkf->home_show_latest_posts == "show" ) { 
			// check if you want to show your latest posts - see in APPEARANCE > THEME SETTINGS > GENERAL > DEFAULT HOMEPAGE ?>
			
		<?php locate_template( array( 'loop.php' ), true );  ?>		
			
		<?php } ?>

		<?php do_action( 'bp_after_blog_home' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	
<?php get_footer() ?>
