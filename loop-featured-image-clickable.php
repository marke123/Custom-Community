<?php 
global $list_post_query, $tmp, $list_post_atts;

extract($list_post_atts);

if ($list_post_query->have_posts()) : while ($list_post_query->have_posts()) : $list_post_query->the_post();
	
	$featured_image = '<span class="link">'.get_the_post_thumbnail( $post->ID, array($featured_posts_image_width,$featured_posts_image_height),"class={$reflect}" ).'</span>';
								
	ob_start(); ?>
		<a class="clickable" href="<?php the_permalink() ?>" target="<?php echo $link_target; ?>" title="<?php _e( 'Permanent Link to', 'cc' ) . the_title_attribute(); ?>">			
			<div class="listposts <?php echo $img_position .' '. $template_name ?>">
			<?php 	
			if( $img_position != 'posts-img-under-content' && $img_position != 'posts-img-between-title-content'){
				echo $featured_image;
			}
			?>
			
			<h3><span class="link"><?php the_title() ?></span></h3>
			
			<?php
			if($img_position == 'posts-img-between-title-content') {
				echo $featured_image;
			}
			?>
			
			<?php 
			
			if( $margintop != ''  ){
				$str_style = $margintop . ';';
			}
			if( $height != ''  ){
				$str_style.= 'height: ' . $height . ';';
			}
			
			if( $str_style != '' )
				$str_style = ' style="' . $str_style . '"';
			
			?>
			
			<div<?php echo $str_style; ?>>
				<?php the_excerpt() ?>
				<?php if($hide_more_link == '') { ?>
					<span class="link readmore"><?php _e('read more','cc') ?></span>
				<?php } ?>
			</div>
			
			<?php if($img_position == 'posts-img-under-content') {
					echo $featured_image;
			} ?>
			</div>
		</a>
		<?php if($img_position == 'posts-img-left-content-right' || $img_position == 'posts-img-right-content-left') { ?>
			<div class="clear"></div>
		<?php } ?>
		
	<?php 
	$tmp .= ob_get_contents();
	ob_end_clean();
	$list_post_atts = '';
	
endwhile; endif;
		
?>