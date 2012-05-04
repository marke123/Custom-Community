<?php 
global $list_post_query, $tmp, $list_post_atts;

extract($list_post_atts);

if ($list_post_query->have_posts()) : while ($list_post_query->have_posts()) : $list_post_query->the_post();
	
	$featured_image = '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail( $post->ID, array($featured_posts_image_width,$featured_posts_image_height),"class={$reflect}" ).'</a>';
								
	ob_start(); ?>
					
		<div class="listposts <?php echo $img_position .' '. $template_name ?>">
		<?php 	
		if( $img_position != 'posts-img-under-content' && $img_position != 'posts-img-between-title-content'){
			echo $featured_image;
		}
		?>
		
		<h3><a href="<?php the_permalink() ?>" title="<?php _e( 'Permanent Link to', 'cc' ) . the_title_attribute(); ?>"><?php the_title() ?></a></h3>
		
		<?php if($img_position == 'posts-img-between-title-content') {
			echo $featured_image;
		} ?>
		
		<?php echo $tkf->list_post_template_read_more_link; ?>
		
		<p style="<?php $margintop ?> height:'<?php $height ?>'">
			<?php the_excerpt() ?>
			<?php if($hide_more_link == '') { ?>
				<a class="readmore" href="'<?php the_permalink() ?>'"><br /><?php _e('read more','cc') ?></a>
			<?php } ?>
		</p>
		
		<?php if($img_position == 'posts-img-under-content') {
				echo $featured_image;
		} ?>
		</div>
		<?php if($img_position == 'posts-img-left-content-right' || $img_position == 'posts-img-right-content-left') { ?>
			<div class="clear"></div>
		<?php } ?>
		
	<?php 
	$tmp .= ob_get_contents();
	ob_end_clean();
	$list_post_atts = '';
	
endwhile; endif;
		
?>