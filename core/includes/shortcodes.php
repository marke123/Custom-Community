<?php 
// shortcode horizontal line
function h_line($atts) { 

	global $tkf;
	
	switch ($tkf->style_css)
        {
        case 'dark':
	    	$color = '333333';
        break;
        case 'natural':
        	$color = 'f5e5b3';
        break;
        case 'white':
        	$color = 'dddddd';
        break;
        case 'light':
        	$color = 'ededed';
        break;
        case 'grey':
        	$color = 'f1f1f1';
        break;
        case 'black':
        	$color = '333333';
        break;
        default:   
			$color = 'f1f1f1';
        break;
        }

	extract(shortcode_atts(array(
		'color' => $color,
		'css' => ''
	), $atts));

	$tmp = '<div style="'.$css.'width:100%; border-top:1px solid #'.$color.'; margin:0; padding:0; height:1px;"></div>';
	return $tmp;
}
add_shortcode('cc_h_line', 'h_line');

// shortcode facebook like button
function facebook_like() { 

 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 
$tmp = '<iframe src="http://www.facebook.com/plugins/like.php?href='.$pageURL.'&layout=standard&show_faces=true&width=450&action=like&colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:auto; height:60px"></iframe>';
return $tmp;
}
add_shortcode('cc_facebook_like', 'facebook_like');

// blockquote_left = add a quotation, left floated
function blockquote_left($atts,$content = null) { 
	return '<span class="cc_blockquote cc_blockquote_left">"'.$content.'"</span>';
}
add_shortcode('cc_blockquote_left', 'blockquote_left');

// blockquote_right = add a quotation, right floated
function blockquote_right($atts,$content = null) { 
	return '<span class="cc_blockquote cc_blockquote_right">"'.$content.'"</span>';
}
add_shortcode('cc_blockquote_right', 'blockquote_right');

// button = add a button with custom text and link
function button($atts,$content = null) { 
	extract(shortcode_atts(array(
		'link' => '',
		'target' => ''
	), $atts)); 
	return '<a href="'.$link.'" target="'.$target.'" class="button">'.$content.'</a>';
}
add_shortcode('cc_button', 'button');

// break = horizontal line / enter
function horline($atts,$content = null) { 
	return '<br />';
}
add_shortcode('cc_break', 'horline');

// clear = reset all css from the elements before
function clear($atts,$content = null) { 
	return '<div class="clear"></div>';
}
add_shortcode('cc_clear', 'clear');

// col_end = end of a column shortcode for advanced use (hierarchical mode)
function col_end(){
return '</div>';
}
add_shortcode('cc_col_end', 'col_end');

// full_width_col = full width column
function full_width_col($atts,$content = null) { 
	extract(shortcode_atts(array(
		'background_color' => 'none',
		'border_color' => 'transparent', 
		'radius' => '0', 
		'shadow_color' => 'transparent',
		'height' => 'auto', 
		'background_image' => 'none',
		'hierarchical' => 'off', 
		
	), $atts)); 
	
	if($height != 'auto'){ $height = $height.'px'; }
	if($background_color != 'none'){ $background_color = '#'.$background_color; }
	if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
	if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
	
	$add=''; 
	if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:95.6%;'; }
	$add_bg='';
	if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
	$tmp = '<div class="full_width_col" style="background-color:'.$background_color.'; 
											  border: 1px solid; border-color:'.$border_color.';
											  -moz-border-radius:'.$radius.'px; 
											  -webkit-border-radius:'.$radius.'px; 
											  border-radius:'.$radius.'px;
											  -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
											  -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
											  box-shadow: 2px 2px 2px '.$shadow_color.';
											  '.$add_bg.'height:'.$height.';'.$add.'
											  ">';
	if($hierarchical == 'off'){
		$tmp .= $content;
		$tmp .= '</div><div class="clear"></div>';
	}
	return $tmp;
}
add_shortcode('cc_full_width_col', 'full_width_col');

// half_col_left = half column, left floated
function half_col_left($atts,$content = null) { 
	extract(shortcode_atts(array(
		'background_color' => 'none',
		'border_color' => 'transparent', 
		'radius' => '0', 
		'shadow_color' => 'transparent', 
		'height' => 'auto',
		'background_image' => 'none',
		'hierarchical' => 'off',
	), $atts)); 
	
	if($height != 'auto'){ $height = $height.'px'; }
	if($background_color != 'none'){ $background_color = '#'.$background_color; }
	if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
	if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }	
	
	$add=''; 
	if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:44%;'; }
	$add_bg='';
	if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
	$tmp = '<div class="half_col_left" style="background:'.$background_color.'; 
											  border: 1px solid; border-color:'.$border_color.';
											  -moz-border-radius:'.$radius.'px; 
											  -webkit-border-radius:'.$radius.'px; 
											  border-radius:'.$radius.'px;
											  -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
											  -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
											  box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
											  height:'.$height.';'.$add.'
											  ">';
	if($hierarchical == 'off'){
		$tmp .= $content;
		$tmp .= '</div>';
	}
	return $tmp;	
}
add_shortcode('cc_half_col_left', 'half_col_left');

// half_col_right = half column, right floated
function half_col_right($atts,$content = null) { 
	extract(shortcode_atts(array(
		'background_color' => 'none',
		'border_color' => 'transparent', 
		'radius' => '0', 
		'shadow_color' => 'transparent', 
		'height' => 'auto',
		'background_image' => 'none',
		'hierarchical' => 'off',
	), $atts)); 
	
	if($height != 'auto'){ $height = $height.'px'; }
	if($background_color != 'none'){ $background_color = '#'.$background_color; }
	if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
	if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }	
	
	$add=''; 
	if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:44%;'; }
	$add_bg='';
	if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
	$tmp = '<div class="half_col_right" style="background:'.$background_color.'; 
											  border: 1px solid; border-color:'.$border_color.';
											  -moz-border-radius:'.$radius.'px; 
											  -webkit-border-radius:'.$radius.'px; 
											  border-radius:'.$radius.'px;
											  -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
											  -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
											  box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
											  height:'.$height.';'.$add.'
											  ">';
	if($hierarchical == 'off'){
		$tmp .= $content;
		$tmp .= '</div><div class="clear"></div>';
	}
	return $tmp;
	
}
add_shortcode('cc_half_col_right', 'half_col_right');

// third_col = one third column, left floated
function third_col($atts,$content = null) { 
	extract(shortcode_atts(array(
		'background_color' => 'none',
		'border_color' => 'transparent', 
		'radius' => '0', 
		'shadow_color' => 'transparent', 
		'height' => 'auto',
		'background_image' => 'none',
		'hierarchical' => 'off',
	), $atts)); 
	
	if($height != 'auto'){ $height = $height.'px'; }
	if($background_color != 'none'){ $background_color = '#'.$background_color; }
	if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
	if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
	
	$add=''; 
	if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:27%;'; } 
	$addborder='';
	if($border_color !='transparent') { $addborder ='border:1px solid '.$border_color.'; margin-right:2.7%;'; }
	$add_bg='';
	if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
	$tmp = '<div class="third_col" style="background:'.$background_color.';'.$addborder.' 
										  -moz-border-radius:'.$radius.'px; 
										  -webkit-border-radius:'.$radius.'px; 
										  border-radius:'.$radius.'px;
										  -moz-box-shadow: 2px 2px 2px '.$shadow_color.';
										  -webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
										  box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
										  height:'.$height.';'.$add.'
										  ">';
	if($hierarchical == 'off'){
		$tmp .= $content;
		$tmp .= '</div>';
	}
	return $tmp;
}
add_shortcode('cc_third_col', 'third_col');

// third_col_right = one third column, right floated
function third_col_right($atts,$content = null) { 
	extract(shortcode_atts(array(
		'background_color' => 'none',
		'border_color' => 'transparent', 
		'radius' => '0', 
		'shadow_color' => 'transparent', 
		'height' => 'auto',
		'background_image' => 'none',
		'hierarchical' => 'off',
	), $atts)); 
	
	if($height != 'auto'){ $height = $height.'px'; }
	if($background_color != 'none'){ $background_color = '#'.$background_color; }
	if($border_color != 'transparent'){ $border_color = '#'.$border_color; }
	if($shadow_color != 'transparent'){ $shadow_color = '#'.$shadow_color; }
	
	$add=''; 
	if($background_color !='none' || $border_color !='transparent' || $shadow_color !='transparent' || $background_image !='none') { $add='padding:2%; width:27%;'; }
	$addborder='';
	if($border_color !='transparent') { $addborder ='border:1px solid '.$border_color.';'; }
	$add_bg='';
	if($background_image !='none') { $add_bg='background-image:url('.$background_image.');'; }
	$tmp = '<div class="third_col_right" style="background:'.$background_color.';'.$addborder.' 
										  		-moz-border-radius:'.$radius.'px; 
										  		-webkit-border-radius:'.$radius.'px; 
										  		border-radius:'.$radius.'px;
										  		-moz-box-shadow: 2px 2px 2px '.$shadow_color.';
										  		-webkit-box-shadow: 2px 2px 2px '.$shadow_color.';
										  		box-shadow: 2px 2px 2px '.$shadow_color.';'.$add_bg.'
										  		height:'.$height.';'.$add.'
										  		">';
	if($hierarchical == 'off'){
		$tmp .= $content;
		$tmp .= '</div><div class="clear"></div>';
	}
	return $tmp;
}
add_shortcode('cc_third_col_right', 'third_col_right');

// list posts
function cc_list_posts($atts,$content = null) {
	global $tkf, $cc_page_options, $post, $more;	
	$tmp = '';
	
	extract(shortcode_atts(array(
		'amount' => '12',
		'category_name' => '0',
		'img_position' => 'mouse_over',
		'height' => 'auto',
		'page_id' => '',
		'post_type' => 'post',
		'orderby' => '',
		'order' => '',
		'last_posts_sticky' => '',
		'last_posts_pagination' => 'show',
		'posts_per_page' => '3'
	), $atts));

	$img_position = 'boxgrid';
    	
	if($category_name == 'all-categories'){
		$category_name = '0';
	}
		
	if($page_id != ''){
		$page_id = explode(',',$page_id);
	}
		
	if($last_posts_sticky == 'on') {

		$args = array(
			'amount' => $last_posts_sticky,
			'post__in'  => get_option( 'sticky_posts' ),
			'ignore_sticky_posts' => 1,
			'posts_per_page' => $amount,
		
		);
	
	} else {
			
		$args = array(
		'amount' => $amount,
		'orderby' => $orderby,
		'order' => $order,
		'post_type' => $post_type,
		'post__in' => $page_id,
		'category_name' => $category_name,
		'posts_per_page' => $posts_per_page,
		'paged' => get_query_var('paged'),
		'ignore_sticky_posts' => 1
		
	);

			}			
				
	remove_all_filters('posts_orderby');
	
	$list_post_query = new WP_Query( $args );
	

	 

	
	$more = 0;
	if ($list_post_query->have_posts()) : while ($list_post_query->have_posts()) : $list_post_query->the_post();

		if($img_position == 'boxgrid'){
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
		} else {
			$tmp .= '<div class="listposts '.$img_position.'">';
			if($img_position != 'posts-img-under-content') $tmp .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail().'</a>';
			$tmp .= '<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>';
			if($height != 'auto'){ $height = $height.'px'; }
			$tmp .= '<p style="height:'.$height.';">'. get_the_excerpt().'<a href="'.get_permalink().'"><br />'.__('read more','cc').'</a></p>';
			if($img_position == 'posts-img-under-content') $tmp .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail().'</a>';
			$tmp .= '</div>';
			if($img_position == 'posts-img-left-content-right' || $img_position == 'posts-img-right-content-left') $tmp .= '<div class="clear"></div>';	
		}
		
	endwhile; endif;
	
	$tmp .='<div class="clear"></div>';
	if($tkf->last_posts_pagination == 'show'){
		$tmp .='<div id="navigation">';
		$tmp .='<div class="alignleft">'. get_next_posts_link('&laquo; Older Entries') .'</div>';
		$tmp .='<div class="alignright">' . get_previous_posts_link('Newer Entries &raquo;') .'</div>';
		$tmp .='</div><!-- End navigation -->';
			
		if(function_exists('wp_pagenavi')){
			ob_start();
				wp_pagenavi( array( 'query' => $list_post_query ) );
				$tmp .= ob_get_contents();
			ob_end_clean();
		}
	}
	

	wp_reset_postdata();
	
	return '<div id="featured_posts" ><div id="list_posts" class="list-posts-all">'.$tmp.'</div></div>';	
}
add_shortcode('cc_list_posts', 'cc_list_posts');

// slideshow
function slider($atts,$content = null) {
	global $post;
	extract(shortcode_atts(array(
		'amount' => '4',
		'category_name' => '0',
		'page_id' => '',
		'post_type' => 'post',
		'orderby' => 'DESC',
		'slider_nav' => 'on',
		'caption' => 'on',
		'caption_height' => '',
		'caption_top' => '',
		'caption_width' => '',
		'reflect'	=> '',
		'width' => '',
		'height' => '',
		'id' => '',
		'background' => '',
		'slider_nav_color' => '',
		'slider_nav_hover_color' => '',
		'slider_nav_selected_color' => '',
		'slider_nav_font_color' => '',
		'time_in_ms' => '5000',
		'slideshow_sticky' => ''
		
	), $atts));


	if($category_name == 'all-categories' || $page_id != '' || $post_type != ''){
		$category_name = '0';
	}
	
	
	if($page_id != '' && $post_type == 'post'){
		 $post_type = 'page';
	}

	
	if($page_id != ''){
		$page_id = explode(',',$page_id);
	}

	// Allow multiple post types to be comma-separated
	if(strpos($post_type, ',') !== false){
		$post_type = explode(',',$post_type);
	}
	
	$tmp = '<script type="text/javascript">'. chr(13);
	$tmp .= '		jQuery.noConflict();'. chr(13);
	$tmp .= '		jQuery(document).ready(function(){'. chr(13);
	
	$tmp .= 'jQuery("#featured'.$id.'").tabs({fx:{opacity: "toggle"}}).tabs("rotate", '.$time_in_ms.', true);
			jQuery("#featured'.$id.'").hover(
			function() {
			jQuery("#featured'.$id.'").tabs("rotate",0,true);
			},
			function() {
			jQuery("#featured'.$id.'").tabs("rotate",'.$time_in_ms.',true);
			}
			);'. chr(13);
	$tmp .= '		});'. chr(13);
	$tmp .= '</script>'. chr(13);

	$tmp .= '<style type="text/css">'. chr(13);
    $tmp .= 'div.post img {'. chr(13);
	$tmp .= 'margin: 0 0 1px 0;'. chr(13);
	$tmp .= '}'. chr(13);
	
	if($slider_nav == 'off'){
	    $tmp .= '#featured'.$id.' ul.ui-tabs-nav {'. chr(13);
		$tmp .= 'visibility: hidden;'. chr(13);
		$tmp .= '}'. chr(13);
		$tmp .= '#featured'.$id.' {'. chr(13);
	    $tmp .= '	background: none;'. chr(13);
	    $tmp .= 'padding:0;';
		$tmp .= '}'. chr(13);
	
	}
	
	if($width != ""){
	    $tmp .= '#featured'.$id.' ul.ui-tabs-nav {'. chr(13);
		$tmp .= 'left:'.$width.'px;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($tkftion_height != ""){
	    $tmp .= '#featured'.$id.' .ui-tabs-panel .info{'. chr(13);
		$tmp .= 'height:'.$caption_height.'px;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($tkftion_width != ""){
	    $tmp .= '#featured'.$id.' .ui-tabs-panel .info{'. chr(13);
		$tmp .= 'width:'.$caption_width.'px;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($tkftion_top != ""){
	    $tmp .= '#featured'.$id.' .ui-tabs-panel .info{'. chr(13);
		$tmp .= 'top:'.$caption_top.'px;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($background != ''){
		$tmp .= '#featured'.$id.'{'. chr(13);
	    $tmp .= 'background: #'.$background.';'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($width != '' || $height != '' || $slider_nav == 'off'){
		$tmp .= '#featured'.$id.'{'. chr(13);
	    $tmp .= 'width:'.$width.'px;'. chr(13);
	    $tmp .= 'height:'.$height.'px;'. chr(13);
	    $tmp .= '}'. chr(13);	
		$tmp .= '#featured'.$id.' .ui-tabs-panel{'. chr(13);
	    $tmp .= 'width:'.$width.'px; height:'.$height.'px;'. chr(13);
	    $tmp .= 'background:none; position:relative;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($slider_nav_color != '') {
		$tmp .= '#featured'.$id.' li.ui-tabs-nav-item a{'. chr(13);
		$tmp .= '    background: none repeat scroll 0 0 #'.$slider_nav_color.';'. chr(13);
		$tmp .= '}'. chr(13);
	}
	if($slider_nav_hover_color != '') {
		$tmp .= '#featured'.$id.' li.ui-tabs-nav-item a:hover{'. chr(13);
		$tmp .= '    background: none repeat scroll 0 0 #'.$slider_nav_hover_color.';'. chr(13);
		$tmp .= '}'. chr(13);
	}

	if($slider_nav_selected_color != '') {
		$tmp .= '#featured'.$id.' .ui-tabs-selected {'. chr(13);
		$tmp .= 'padding-left:0;'. chr(13);
		$tmp .= '}'. chr(13);
		$tmp .= '#featured'.$id.' .ui-tabs-selected a{'. chr(13);
		$tmp .= '    background: none repeat scroll 0 0 #'.$slider_nav_selected_color.' !important;'. chr(13);
		$tmp .= 'padding-left:0;'. chr(13);
		$tmp .= '}'. chr(13);
	}
	
	if($slider_nav_font_color != ''){
		$tmp .= '#featured'.$id.' ul.ui-tabs-nav li span{'. chr(13);
		$tmp .= 'color:#'.$slider_nav_font_color. chr(13);
		$tmp .= '}'. chr(13);
	}
	$tmp .= '</style>'. chr(13);	
	
	if($slideshow_sticky == 'on') {
	
		$args = array(
			'posts_per_page' => $amount,
			'post__in'  => get_option( 'sticky_posts' ),
			'ignore_sticky_posts' => 1
		);
	
	} else {
		
		$args = array(
			'orderby' => $orderby,
			'post_type' => $post_type,
			'post__in' => $page_id,
			'category_name' => $category_name,
			'posts_per_page' => $amount
		);
	
	}
	remove_all_filters('posts_orderby');
	query_posts($args);
	
	if (have_posts()) :

		$shortcodeclass = '';
		if ( $id == "top" ) $shortcodeclass = "cc_slider_shortcode"; 
		
		$tmp .='<div id="cc_slider'.$id.'" class="cc_slider '.$shortcodeclass.'">'. chr(13);
		$tmp .='<div id="featured'.$id.'" class="featured">'. chr(13);
		
		$i = 1; 
		while (have_posts()) : the_post();
		
			$url = get_permalink();
			$theme_fields = get_post_custom_values('my_url');
			if(isset($theme_fields[0])){
			 	$url = $theme_fields[0];
			}
			   
			$tmp .='<div id="fragment-'.$id.'-'.$i.'" class="ui-tabs-panel">'. chr(13);
		    
		    if($width != '' || $height != ''){
		    	if (get_the_post_thumbnail( $post->ID, array($width,$height),""  ) == '') { $ftrdimg = '<img src="'.get_template_directory_uri().'/images/slideshow/noftrdimg-1006x250.jpg" />'; } else { $ftrdimg = get_the_post_thumbnail( $post->ID, array($width,$height),"class={$reflect}" ); }
		    } else {
		    	if (get_the_post_thumbnail( $post->ID, 'slider-middle',""  ) == '') { $ftrdimg = '<img src="'.get_template_directory_uri().'/images/slideshow/noftrdimg.jpg" />'; } else { $ftrdimg = get_the_post_thumbnail( $post->ID, 'slider-middle',"class={$reflect}"  ); }
		    }
		    
			$tmp .='	<a class="reflect" href="'.$url.'">'.$ftrdimg.'</a>'. chr(13);

			if($caption == 'on'){
				$tmp .=' <div class="info" >'. chr(13);
				$tmp .='	<h2><a href="'.$url.'" >'.get_the_title().'</a></h2>'. chr(13);
				$tmp .='	<p>'.get_the_excerpt().'</p>'. chr(13);
				$tmp .=' </div>'. chr(13);
			}
			$tmp .='</div>'. chr(13);
			$i++;
		endwhile;
		
		$tmp .='<ul class="ui-tabs-nav">'. chr(13);
		$i = 1; 
		while (have_posts()) : the_post();
			if (get_the_post_thumbnail( $post->ID, 'slider-thumbnail' ) == '') { $ftrdimgs = '<img src="'.get_template_directory_uri().'/images/slideshow/noftrdimg-80x50.jpg" />'; } else { $ftrdimgs = get_the_post_thumbnail( $post->ID, 'slider-thumbnail' ); } 
	    	$tmp .='<li class="ui-tabs-nav-item ui-tabs-selected" id="nav-fragment-'.$id.'-'.$i.'"><a href="#fragment-'.$id.'-'.$i.'">'.$ftrdimgs.'<span>'.get_the_title().'</span></a></li>'. chr(13);
			$i++;
		endwhile;
	    $tmp .='</ul>'. chr(13);
	    
		$tmp .= '</div></div>'. chr(13);

		else :
			$tmp .='<div id="cc_slider_prev" class="cc_slider" style="background: #ededed;">'. chr(13);
			$tmp .='<div id="featured_prev" class="featured" style="background: #ededed;">'. chr(13);
			$tmp .='<h2 class="center" style="margin-top:50px; margin-left: 20px;">'.__( 'Ooops..', 'cc' ).'</h2>'. chr(13);
			$tmp .='<p class="center" style="margin-top:20px; margin-left: 20px;">'.__( 'Something went wrong with the slideshow! 
				<br />Check your theme settings for the slideshow or the page settings for a single page slideshow... 
				<br />Check the <a href="http://themekraft.com/faq/slideshow/" target="_blank">FAQ</a> for more.', 'cc' ).'</p>'. chr(13);
			$tmp .='</div></div>'. chr(13);
			
		endif;
		   
		wp_reset_query();
		return $tmp . chr(13);
}
add_shortcode('cc_slideshow', 'cc_slideshow');

function cc_slideshow($atts,$content = null) {
	return slider($atts);
}

// nothing
// empty = just to display shortcodes without execution - needed for demos.
function nothing($atts,$content = null) {  
	return $content;
}
add_shortcode('cc_empty', 'nothing');
?>