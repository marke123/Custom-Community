			<?php /* this search loop shows your blog posts in the unified search 
			you may modify it as you want, It is a copy from my theme 
			
			*/
			
			
			?>
			<?php do_action( 'bp_before_blog_search' ) ?>
			<?php global $wp_query;
				$wp_query->is_search=true;
				
				if(!empty($_REQUEST['search-terms'])){
					$search_term=$_REQUEST['search-terms'];
				} elseif(!empty($_REQUEST['s'])) {
					$search_term=$_REQUEST['s'];
				}
				
			if(!empty($search_term)){
				$wp_query->query("s=".$search_term);?>

					<?php locate_template( array( 'loop.php' ), true );  ?>		
			
			<?php } ?>
			<?php do_action( 'bp_after_blog_search' ) ?>        