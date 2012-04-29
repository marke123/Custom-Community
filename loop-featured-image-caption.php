<?php
global $list_post_query, $tmp, $list_post_atts;

if ($list_post_query->have_posts()) : while ($list_post_query->have_posts()) : $list_post_query->the_post();
				
	$thumb = get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
	$pattern= "/(?<=src=['|\"])[^'|\"]*?(?=['|\"])/i";
	preg_match($pattern, $thumb, $thePath); 
	if(!isset($thePath[0])){
	$thePath[0] = get_template_directory_uri().'/images/slideshow/noftrdimg-222x160.jpg';
	}
	$tmp .= '<a href="'. get_permalink().'" title="'. get_the_title().'"><div class="boxgrid captionfull" onclick="document.location.href=\''. get_permalink().'\'" style="cursor:pointer;background: transparent url('.$thePath[0].') repeat scroll 0 0; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; " title="'. get_the_title().'">';
	$tmp .= '<div class="cover boxcaption">';
	$tmp .= '<h3 style="padding-left:8px;"><a href="'. get_permalink().'" title="'. get_the_title().'">'. get_the_title().'</a></h3>';
	$tmp .= '<p>'.substr(get_the_excerpt(), 0, 100).'</p>';
	$tmp .= '</div>';		
	$tmp .= '</div></a>';	
	
endwhile; endif;

?>