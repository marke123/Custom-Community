<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ) ?>
		
		<?php if ( $tkf->home_show_latest_posts == "show" ) { 
			// check if you want to show your latest posts - see in APPEARANCE > THEME SETTINGS > GENERAL > DEFAULT HOMEPAGE ?>
					
			<div id="latest_posts">
				<div class="list-posts-all <?php echo $tkf->home_latest_posts_style ?>">
					<?php if ( have_posts() ) : ?>
		
						<?php while (have_posts()) : the_post(); ?>
		
							<?php do_action( 'bp_before_blog_post' ) ?>
		
							<div id="post-<?php the_ID();?> " <?php post_class(); ?>>
		
								<div class="author-box">
									<?php printf( __( '<a href=" %s "> %s </a> <p> by %s </p>', 'cc' ), bp_core_get_user_domain($post->post_author), get_avatar( get_the_author_meta( 'user_email' ), '50' ),bp_core_get_userlink( $post->post_author ) ) ?>
								</div>
		
								<div class="post-content">
		
									<span class="marker"></span>
									
									<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'cc' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									
									<p class="date">
										<?php the_time('F j, Y') ?> <em><?php _e( 'in', 'cc' ) ?> <?php the_category(', ') ?>
										<span><?php printf( __( ' by %s', 'cc' ), bp_core_get_userlink( $post->post_author ) );?></span>
										</em>
									</p>
		
									<div class="entry">
										<?php do_action('blog_post_entry')?>
									</div>
									<?php $tags = get_the_tags(); if($tags)	{  ?>
										<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'cc' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'cc' ), __( '1 Comment &#187;', 'cc' ), __( '% Comments &#187;', 'cc' ) ); ?></span></p>
									<?php } else {?>
										<p class="postmetadata"><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'cc' ), __( '1 Comment &#187;', 'cc' ), __( '% Comments &#187;', 'cc' ) ); ?></span></p>
									<?php } ?>
								</div>
		
							</div>
		
							<?php do_action( 'bp_after_blog_post' ) ?>
		
						<?php endwhile; ?>
		
						<div class="navigation">
		
							<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'cc' ) ) ?></div>
							<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'cc' ) ) ?></div>
		
						</div>
		
					<?php else : ?>
		
						<h2 class="center"><?php _e( 'Not Found', 'cc' ) ?></h2>
						<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'cc' ) ?></p>
		
						<?php locate_template( array( 'searchform.php' ), true ) ?>
		
					<?php endif; ?>
				</div>
			</div>
		<?php } ?>

		<?php do_action( 'bp_after_blog_home' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	
<?php get_footer() ?>
