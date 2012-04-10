<?php get_header() ?>

	<div id="content">
		<div class="padder">
		<?php global $tkf; ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		
			<?php if( $tkf->use_widgetized_home != 'on') { ?>
			
			<?php if ( $tkf->home_show_latest_posts == "show" ) { 
				// check if you want to show your latest posts - see in APPEARANCE > THEME SETTINGS > GENERAL > DEFAULT HOMEPAGE ?>
				
			<?php locate_template( array( 'loop.php' ), true );  ?>		
				
			<?php } ?>
		
			<?php do_action( 'bp_after_blog_home' ) ?>
			
		<?php } else {
			echo '<div class="home_widgets_container">';
 			for ($ln = 1; $ln <= $tkf->home_widgets_lines_number; $ln++ ){ 
 				echo '<div id="widget_line_' . $ln . '" class="home_widget_line widgetarea line_' . $ln . ' widgets_amount_'.$tkf->home_widgets_lines_number.'">';
 				
 				for ($wn = 1; $wn <= $tkf->home_widgets_line_widgets_number[$ln]; $wn++ ){
						
					if( ! dynamic_sidebar( 'home_widget_line_'.$ln.'_widget_'.$wn )) : ?>
							<div class="widget">
								<h3 class="widgettitle" ><?php _e('Widgetarea', 'cc'); ?></h3>
								<div>
									<p>Add your widgets</p>
								</div>
							
							</div>
					<?php endif; 
					
					 
				}	
 				$wn = 0;	
 				echo '</div>';
			}
			echo '</div>';
			
		}?>
		</div><!-- .padder -->
	</div><!-- #content -->
<?php get_footer() ?>
