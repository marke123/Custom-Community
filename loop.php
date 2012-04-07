<?php ob_start(); ?>

<div id="post-<?php the_ID(); ?>" class="listposts <?php global $tkf; echo $tkf->home_featured_posts_style ?>">

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
		
<?php $tmp .= ob_get_contents(); ?>
<?php ob_end_clean(); ?>