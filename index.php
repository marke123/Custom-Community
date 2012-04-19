<?php get_header() ?>

	<div id="content">
		<div class="padder">
		<?php global $tkf; ?>
		
			<?php do_action( 'cc_first_inside_padder' ); ?>
		
			<?php do_action( 'bp_before_blog_home' ) ?>
		
			<?php if( $tkf->use_widgetized_home != 'on') { ?>
			
			<?php if ( $tkf->home_show_latest_posts == "show" ) { 
				// check if you want to show your latest posts - see in APPEARANCE > THEME SETTINGS > GENERAL > DEFAULT HOMEPAGE ?>
				
			<?php locate_template( array( 'loop.php' ), true );  ?>		
				
			<?php } ?>
		
			<?php do_action( 'bp_after_blog_home' ) ?>
			
		<?php } else {
			echo '<div class="home_widgets_container">';
				if(is_array($tkf->home_widgets_line_amount)){
	 				foreach( $tkf->home_widgets_line_amount as $line){
					echo $tkf->home_widgets_lines_amount;
					echo '<div id="widget_line_' . $line . '" class="home_widget_line widgetarea line_' . $line . ' widgets_amount_'.count($tkf->home_widgets_line_widgets_amount[$line]).'">';
	 				
	 				foreach( $tkf->home_widgets_line_widgets_amount[$line] as $widget){
									
						if( ! dynamic_sidebar( 'home_widget_line_'.$line.'_widget_'.$widget )) : ?>
								<div id="<?php echo 'line_'.$line.'_widget_'.$widget ?>" class="widget">
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
			}
			echo '</div>';
			
		}?>
		</div><!-- .padder -->
	</div><!-- #content -->
<?php get_footer() ?>
