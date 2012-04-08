<?php get_header() ?>

	<div id="content">
		<div class="padder">
		<?php 
		global $tkf; 
		if( $tkf->use_widgetized_home != 'on') { ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
			
			<?php if ( $tkf->home_show_latest_posts == "show" ) { 
				// check if you want to show your latest posts - see in APPEARANCE > THEME SETTINGS > GENERAL > DEFAULT HOMEPAGE ?>
				
			<?php locate_template( array( 'loop.php' ), true );  ?>		
				
			<?php } ?>
		
			<?php do_action( 'bp_after_blog_home' ) ?>
			
		<?php } else {
			
			for ($ln = 1; $ln <= $tkf->home_widgets_lines_number; $ln++ ){ 
 				echo '<div class="home_widget_line line_' . $ln . '">';
 				echo $tkf->home_widgets_line_widgets_number.'_'.$ln;
				for ($wn = 1; $wn <= $tkf->home_widgets_line_widgets_number.'_'.$ln; $wn++ ){
					echo 'asa';
					if( ! dynamic_sidebar( 'home_widget_line_'.$ln.'widget_'.$wn )) : ?>
							<div class="widget">
								<h3 class="widgettitle" ><?php _e('Widgetaria', 'cc'); ?></h3>
								<div>
									<p><?php echo 'home_widget_line_'.$ln.'widget_'.$wn ?></p>
								</div>
							
							</div>
					<?php endif; 
					
					 
				}	
 				$wn = 0;	
 				echo '</div>';
			}
			
			
		}?>
		</div><!-- .padder -->
	</div><!-- #content -->
<?php get_footer() ?>
