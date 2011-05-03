<?php get_header() ?>
<?php global $cap;?>
<?php if($cap->sidebar_position == ""){ $cap->sidebar_position = "left and right"; }?>
<?php if($cap->sidebar_position == "left" || $cap->sidebar_position == "left and right"){?>
	<?php locate_template( array( 'sidebar-left.php' ), true ) ?>
<?php };?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">
				
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="item-options">
					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
				</div>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
						global $cc_post_options;
						$cc_post_options=get_cc_post_options(); 
						$single_class = false;
					    if(isset($cc_post_options) && $cc_post_options['cc_post_template_on'] == 1){
					    		
							switch ($cc_post_options['cc_post_template_type'])
					        {
					        case 'img-left-content-right':
								$single_class = 'single-img-left-content-right'; 
					        break;
					        case 'img-right-content-left':
								$single_class = 'single-img-right-content-left'; 
					        break;
					        case 'img-over-content':
								$single_class = 'single-img-over-content'; 
					        break;
					        case 'img-under-content':
								$single_class = 'single-img-under-content'; 
					        break;
					        default:
					        	$single_class = false; 
					        break;
					        }
						}
						
					?>		
				<?php if($cc_post_options['cc_post_template_avatar'] != '1') {?>
					<div class="author-box">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
						<?php if(defined('BP_VERSION')){ ?>
							<p><?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
						<?php } ?>
					</div>
				<?php } ?>
				<div class="post-content">	
				<?php if ($single_class != false){ ?>
				<div class="<?php echo $single_class ?>">
				<?php } ?>

				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php if($cc_post_options['cc_post_template_date'] != '1') {?>
				<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php if(defined('BP_VERSION')){  printf( __( ' by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ); }?></em></p>
				<?php } ?> 

				<div class="entry">
					<?php if ($single_class == 'single-img-left-content-right' || $single_class == 'single-img-right-content-left' || $single_class == 'single-img-over-content'){ ?>
						<?php the_post_thumbnail()?>
					<?php } ?>
					<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
					<?php if ($single_class == 'single-img-under-content'){ ?>
						<?php the_post_thumbnail()?>
					<?php } ?>		
					<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', 'buddypress' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				</div>

				<?php if($cc_post_options['cc_post_template_tags'] != '1') {?>
					<?php $tags = get_the_tags(); if($tags)	{  ?>
						<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'buddypress' ), ', ', '<br />'); ?></span></p>
					<?php } ?> 
				<?php } ?>	 
				
				<?php if($cc_post_options['cc_post_template_comments_info'] != '1') {?>
					<p class="postmetadata"><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?></span></p>
				<?php } ?>
						
				<?php if ($single_class != false){ ?>
					</div>
				<?php } ?>	

				<div class="clear"></div>

				<?php comments_template(); ?>
	
				<?php endwhile; else: ?>
	
					<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>
	
				<?php endif; ?>
				</div>
			</div>
		</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php if($cap->sidebar_position == "right" || $cap->sidebar_position == "left and right"){?>
	<?php locate_template( array( 'sidebar.php' ), true ) ?>
<?php };?>

<?php get_footer() ?>