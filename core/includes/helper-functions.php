<?php 

function list_posts_loop_featured_image($args){
	return $tmp;
}

		



function list_posts_loop_default(){
	 ob_start(); ?>

<div id="post-<?php the_ID(); ?>"  class="listposts post <?php global $tkf; echo $tkf->home_featured_posts_style ?>">

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
<?php ob_end_clean(); 
return $tmp;	
}



/**
 * check if it's a child theme or parent theme and return the correct path
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_require_path($path){
	Custom_Community::require_path($path);
}
	
/**
 * get the right img for the slideshow shadow
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_slider_shadow() {
	global $tkf;
	if ($tkf->slideshow_shadow == "shadow") { 
		return "slider-shadow.png"; 
	} else { 
		return "slider-shadow-sharp.png"; 
	}
}  

/**
 *  define new excerpt length
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_excerpt_length() {
	global $tkf;
	$excerpt_length = 30;
	if($tkf->excerpt_length){
		$excerpt_length = $tkf->excerpt_length;
	}
	return $excerpt_length;
}

/**
 * change the profile tab order
 *
 * @package Custom Community
 * @since 1.8.3
 */
add_action( 'bp_init', 'cc_change_profile_tab_order' );
function cc_change_profile_tab_order() {
	global $bp, $tkf;
	
	if($tkf->bp_profiles_nav_order == '')
		return;
	
	$order = $tkf->bp_profiles_nav_order;
	$order = str_replace(' ','',$order); 
	$order = explode(",", $order);
	$i = 1;
	
	foreach($order as $item) {
		$bp->bp_nav[$item]['position'] = $i;
		$i ++;
	}
	
}

/**
 * change the groups tab order
 *
 * @package Custom Community
 * @since 1.8.3
 */
add_action('bp_init', 'cc_change_groups_tab_order');
function cc_change_groups_tab_order() {
	global $bp, $tkf;

	
	// In BP 1.3, bp_options_nav for groups is keyed by group slug instead of by 'groups', to
	// differentiate it from the top-level groups directories and the groups subtab of member
	// profiles
	$group_slug = isset( $bp->groups->current_group->slug ) ? $bp->groups->current_group->slug : false;
	
	
	if($tkf->bp_groups_nav_order == '')
		return;

		
	$order = $tkf->bp_groups_nav_order;
	$order = str_replace(' ','',$order); 
	$order = explode(",", $order);
	$i = 1;
	foreach($order as $item) {
		$bp->bp_options_nav[$group_slug][$item]['position'] = $i;
		$i ++;
	}
}


/**
 * find out the right color scheme and create the array of css elements with the hex codes
 *
 * @package Custom Community
 * @since 1.8.3
 */
	
function cc_switch_css(){
	global $tkf;
		
	$switch_css =  array(
	'body_bg_color' => 'ffffff',
	'container_bg_color' => 'ffffff',
	'container_alt_bg_color' => 'ededed',
	'details_bg_color' => 'ededed', 
	'details_hover_bg_color' => 'f9f9f9',
	'font_color' => '888888',
	'font_alt_color' => 'afafaf',
	'link_color' => '489ed5',
	);

	if ($tkf->style_css != false):;
	switch ($tkf->style_css){
        case 'dark':
			$switch_css =  array(
			'body_bg_color' => '333333',
			'container_bg_color' => '181818',
			'container_alt_bg_color' => '333333',
			'details_bg_color' => '181818', 
			'details_hover_bg_color' => '252525',
			'font_color' => '888888',
			'font_alt_color' => '555555',
			'link_color' => 'ffffff',
			);
        break;
        case 'natural':
			$switch_css =  array(
			'body_bg_color' => 'F5E5B3',
			'container_bg_color' => 'FFF9DB',
			'container_alt_bg_color' => 'F5E5B3',
			'details_bg_color' => 'FFF9DB', 
			'details_hover_bg_color' => 'FFE5B3',
			'font_color' => '888888',
			'font_alt_color' => 'aaaaaa',
			'link_color' => 'ff7400',
			);
        	
        break;
        case 'white':
			$switch_css =  array(
			'body_bg_color' => 'ffffff',
			'container_bg_color' => 'ffffff',
			'container_alt_bg_color' => 'ededed',
			'details_bg_color' => 'ededed', 
			'details_hover_bg_color' => 'f9f9f9',
			'font_color' => '888888',
			'font_alt_color' => 'afafaf',
			'link_color' => '489ed5',
			);
        break;
        case 'light':
			$switch_css =  array(
			'body_bg_color' => 'ededed',
			'container_bg_color' => 'ffffff',
			'container_alt_bg_color' => 'ededed',
			'details_bg_color' => 'ffffff', 
			'details_hover_bg_color' => 'f9f9f9',
			'font_color' => '888888',
			'font_alt_color' => 'afafaf',
			'link_color' => '529e81',
			);
        break;
        case 'grey':
			$switch_css =  array(
			'body_bg_color' => 'f1f1f1',
			'container_bg_color' => 'dddddd',
			'container_alt_bg_color' => 'f1f1f1',
			'details_bg_color' => 'dddddd', 
			'details_hover_bg_color' => 'ededed', 
			'font_color' => '555555',
			'font_alt_color' => 'aaaaaa',
			'link_color' => '1f8787',
			);
        break;
        case 'black':
			$switch_css =  array(
			'body_bg_color' => '000000',
			'container_bg_color' => '000000',
			'container_alt_bg_color' => '333333',
			'details_bg_color' => '333333', 
			'details_hover_bg_color' => '181818',
			'font_color' => '888888',
			'font_alt_color' => '555555',
			'link_color' => 'ffffff',
			);
        break;
	    }
	endif;
	return $switch_css;
}
	
/**
 * find out the right color scheme and create the array of css elements with the hex codes
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_color_scheme(){
	echo cc_get_color_scheme();
}
	function cc_get_color_scheme(){
		global $tkf;
		if(isset( $_GET['show_style']))
			$tkf->style_css = $_GET['show_style']; 
			
		switch ($tkf->style_css)
	        {
	        case 'dark':
			$color = 'dark';
	        break;
	        case 'natural':
			$color = 'natural';
	        break;
	        case 'white':
			$color = 'white';
	        break;
	        case 'light':
			$color = 'light';
	        break;
	        case 'grey':
			$color = 'grey';
	        break;
	        case 'black':
			$color = 'black';
	        break;
	        default:
			$color = 'grey';
	        break;
	        }
	        return $color; 
	}
	
/**
 * load the array for the top slider depending on the page settings or theme settings
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function cc_slidertop(){
	global $cc_page_post_settings, $tkf;

	$cc_page_post_settings = get_post_meta($post->ID,"cc_page_post_settings", false);
	$cc_page_post_settings = $cc_page_post_settings[0][cc_page_post_settings];
		
	$slidercat = '0' ;
	$slider_style = 'default';
	$caption = 'on';
	$slideshow_amount = '4';
	$slideshow_time = '5000';
	$slideshow_orderby = 'DESC';
	$slideshow_post_type = 'post';
	$slideshow_show_page = '';
	$slideshow_sticky = '';
	
//	echo '<pre>';
//	print_r( $cc_page_options );
//	echo '<pre>';
		
	if($cc_page_post_settings["cc_page_slider_on"] == 1 ){
				
		if( $cc_page_post_settings["slideshow_cat"] != '' && $cc_page_post_settings["slideshow_show_page"] == '' ){
			$slidercat = $cc_page_post_settings["slideshow_cat"];
		}
		if( $cc_page_post_settings["slideshow_style"] != '' ){
			$slider_style = $cc_page_post_settings["slideshow_style"];
		}
		if( $cc_page_post_settings["slideshow_caption"] != '' ){
			$tkftion = $cc_page_post_settings["slideshow_caption"];
		}
		if( $cc_page_post_settings["slideshow_amount"]  != '' ){
			$slideshow_amount = $cc_page_post_settings["slideshow_amount"];
		}
		if( $cc_page_post_settings["slideshow_time"] != '' ){
			$slideshow_time = $cc_page_post_settings["slideshow_time"];
		}
		if( $cc_page_post_settings["slideshow_orderby"] != '' ){
			$slideshow_orderby = $cc_page_post_settings["slideshow_orderby"];
		}
		if( $cc_page_post_settings["slideshow_post_type"] != '' ){
			$slideshow_post_type = $cc_page_post_settings["slideshow_post_type"];
		}
		if( $cc_page_post_settings["slideshow_show_page"] != '' ){
			$slideshow_show_page = $cc_page_post_settings["slideshow_show_page"];
		}

	}else{

		if( $tkf->slideshow_sticky != '' ){
			$slideshow_sticky = $tkf->slideshow_sticky;
		}
		if( $tkf->slideshow_cat != '' ){
			$slidercat = $tkf->slideshow_cat;
		}
		if( $tkf->slideshow_style != '' ){
			$slider_style = $tkf->slideshow_style;
		}
		if( $tkf->slideshow_caption != '' ){
			$tkftion = $tkf->slideshow_caption;
		}
		if( $tkf->slideshow_amount != '' ){
			$slideshow_amount = $tkf->slideshow_amount;
		}
		if( $tkf->slideshow_time != '' ){
			$slideshow_time = $tkf->slideshow_time;
		}
		if( $tkf->slideshow_orderby != '' ){
			$slideshow_orderby = $tkf->slideshow_orderby;
		}
		if( $tkf->slideshow_post_type != '' ){
			$slideshow_post_type = $tkf->slideshow_post_type;
		}
		if( $tkf->slideshow_show_page != '' ){
			$slideshow_show_page = $tkf->slideshow_show_page;
		}
		
	}
	
	if($slider_style == 'full width' || $slider_style == 'full-width-image' ){ ?>
		<style type="text/css">
			div#cc_slider-top div.cc_slider .featured .ui-tabs-panel{
			width: 100%;
			}
		</style>
	<?php }
	if($slider_style == 'full width' || $slider_style == 'full-width-image' ){
		$atts = array(
			'amount' => $slideshow_amount,
			'slideshow_sticky' => $slideshow_sticky,
			'category_name' => $slidercat,
			'slider_nav' => 'off',
			'caption' => $tkftion,
			'caption_width' => '1000',
			'width' => '1000',
			'height' => '250',
			'id' => 'slidertop',
			'time_in_ms' => $slideshow_time,
			'orderby' => $slideshow_orderby,
			'page_id' => $slideshow_show_page,
			'post_type' =>$slideshow_post_type
		);
	} else {
		$atts = array(
			'amount' => '4',
			'slideshow_sticky' => $slideshow_sticky,
			'category_name' => $slidercat,
			'slider_nav' => 'on',
			'caption' => $tkftion,
			'id' => 'slidertop',
			'time_in_ms' => $slideshow_time,
			'orderby' => $slideshow_orderby,
			'page_id' => $slideshow_show_page,
			'post_type' =>$slideshow_post_type
 			);					
	}

	$tmp = '<div id="cc_slider-top">';
	$tmp .= slider($atts,$content = null);
	$tmp .= '</div>';
	if($tkf->slideshow_shadow != "no shadow"){
		$tmp .= '<div class="slidershadow"><img src="'.get_template_directory_uri().'/images/slideshow/'.cc_slider_shadow().'"></img></div>';
	}
		
	return $tmp;

}

if (!function_exists('bp_core_get_userlink')){
	function bp_core_get_userlink($post_author_id){
		return '<a title="admin" href="'.get_author_posts_url($post_author_id).'">'.get_the_author_link($post_author_id).'</a>';	
	}
}
if (!function_exists('bp_core_get_user_domain')){
	function bp_core_get_user_domain($post_author_id){
		return get_author_posts_url($post_author_id);	
	}
}



?>