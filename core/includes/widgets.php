<?php 

// register all buddypress widgets if buddypress is activated 

if(defined('BP_VERSION')){ 
	if ( function_exists('cc_login_widget') )
	    wp_register_sidebar_widget( 'cc_login_widget', 'BP Sidebar Login', 'cc_login_widget', '' );

	if ( function_exists('widget_community_nav') )
    	wp_register_sidebar_widget( 'widget_community_nav', 'Community Navigation', 'widget_community_nav', '' );

    if ( function_exists('groups_header_widget') )
        wp_register_sidebar_widget( 'groups_header_widget', 'Groups Header Widget', 'groups_header_widget');
        wp_register_widget_control( 'groups_header_widget', 'Groups Header Control', 'groups_header_control', '' );
    
    if ( function_exists('profiles_header_widget') )
        wp_register_sidebar_widget( 'profiles_header_widget','Profiles Header Widget', 'profiles_header_widget');
        wp_register_widget_control( 'profiles_header_widget', 'Profiles Header Control', 'profiles_header_control', '' );
        

    if ( function_exists('forum_tags_widget') )
	    wp_register_sidebar_widget( 'forum_tags_widget', 'Forum Tags', 'forum_tags_widget', '' );
}

   if ( function_exists('featured_posts') )
        wp_register_sidebar_widget( 'featured_posts','featured posts control', 'featured_posts');
        wp_register_widget_control( 'featured_posts', 'featured posts control', 'featured_posts_control', '' );
 

/**
 *  widget for the community navigation
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function widget_community_nav() { ?>
  		<div id="community-nav" class="widget widget-title" >
  		<ul class="item-list">
         	<h3 class="widgettitle"><?php _e( 'Community', 'cc' ) ?></h3>
					
	  		<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
				<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'cc' ) ?>"><?php _e( 'Activity', 'cc' ) ?></a>
				</li>
			<?php endif; ?>
	
			<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user() ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'cc' ) ?>"><?php _e( 'Members', 'cc' ) ?></a>
			</li>
	
			<?php if ( bp_is_active( 'groups' ) ) : ?>
				<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'cc' ) ?>"><?php _e( 'Groups', 'cc' ) ?></a>
				</li>
	
				<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
					<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'cc' ) ?>"><?php _e( 'Forums', 'cc' ) ?></a>
					</li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( bp_is_active( 'blogs' ) && is_multisite() ) : ?>
				<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'cc' ) ?>"><?php _e( 'Blogs', 'cc' ) ?></a>
				</li>
			<?php endif; ?>
  		</ul>
				
   <?php if( bp_is_single_item() || bp_is_user() ) { ?>
	   	<?php if(bp_is_group()){ ?>
		<div id="community-single-nav" class="widget-title" >
		  <ul class="item-list">
		  <h3 class="widgettitle"><?php _e( '@ Group', 'cc' ) ?></h3>
				<?php bp_get_options_nav() ?>
				<?php do_action( 'bp_group_options_nav' ) ?>
			</ul>
		
		</div>	
		<?php } ?>

		<?php if(bp_is_user()){ ?>
		<div id="community-single-nav" class="widget-title" >
		  <ul class="item-list">
		  <h3 class="widgettitle"><?php _e( '@ Member', 'cc' ) ?></h3>
		  <?php bp_get_displayed_user_nav() ?>
				<?php do_action( 'bp_group_options_nav' ) ?>
			</ul>
		
		</div>	
		<?php } ?>
  	<?php } ?>
  	</div>
<?php } ?>
<?php

/**
 *  buddypress login widget
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function cc_login_widget(){?>
	<?php global $tkf;?>
		<?php do_action( 'bp_inside_before_sidebar' ) ?>
	
		<?php if ( is_user_logged_in() ) : ?>
	
			<?php do_action( 'bp_before_sidebar_me' ) ?>
			<div class="widget">
			<div id="sidebar-me">
				<a href="<?php echo bp_loggedin_user_domain() ?>">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
				</a>
	
				<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
				<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'cc' ) ?></a>
	
				<?php do_action( 'bp_sidebar_me' ) ?>
			</div>
			</div>
			<?php do_action( 'bp_after_sidebar_me' ) ?>
	
			<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
				<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
			<?php endif; ?>
	
		<?php else : ?>
	
			<?php do_action( 'bp_before_sidebar_login_form' ) ?>
			<div class="widget">
			<p id="login-text">
			<?php if(!$tkf->bp_login_sidebar_text) { ?>
				<?php _e( 'To start connecting please log in first.', 'cc' ) ?>
			<?php } else { ?>
				<?php echo $tkf->bp_login_sidebar_text; ?>
			<?php } ?>
				<?php if ( bp_get_signup_allowed() ) : ?>
					<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'cc' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
				<?php endif; ?>
			</p>
	
			<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
				<label><?php _e( 'Username', 'cc' ) ?><br />
				<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>
	
				<label><?php _e( 'Password', 'cc' ) ?><br />
				<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label>
	
				<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', 'cc' ) ?></label></p>
	
				<?php do_action( 'bp_sidebar_login_form' ) ?>
				<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In','cc'); ?>" tabindex="100" />
				<input type="hidden" name="testcookie" value="1" />
			</form>
			</div>
			<?php do_action( 'bp_after_sidebar_login_form' ) ?>
		<?php endif; ?>
<?php } 

/**
 *  buddypress default forum topic tags widget to show forum tags on the forums directory
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function forum_tags_widget(){
 /* Show forum tags on the forums directory */
	if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags">

			<h3 class="widgettitle"><?php _e( 'Forum Topic Tags', 'cc' ) ?></h3>
			<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
				<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
			<?php endif; ?>
		</div>
<?php
	endif; 
}

/**
 *  groups sidebar header widget
 *
 * @package Custom Community
 * @since 1.8.3
 */	
function groups_header_widget($args) {
  extract($args);

  $options = get_option("groups_header_position");
  if (!is_array( $options )) {
    $options = array(
      'groups_header_position' => 'horizontal'
    );
  }

  if($options[groups_header_position] != 'horizontal') {
  		locate_template( array( 'groups/single/group-header-sidebar.php' ), true, false );
    } else {
    if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
  		locate_template( array( 'groups/single/group-header.php' ), true, false );
    endwhile; endif;
  }
}

function groups_header_control() {
  $options = get_option("groups_header_position");
  if (!is_array( $options )) {
    $options = array(
      'groups_header_position' => 'horizontal'
     );
  }

  if (isset($_POST['groups_header_submit'])){
    $options['groups_header_position'] = htmlspecialchars($_POST['groups_header_position']);
    update_option("groups_header_position", $options);
  }?>
  <p>
    <label for="groups_header_position">Widget Position: </label><br />
    Horizontal: <input type="radio" name="groups_header_position" value="horizontal" <?php if($options['groups_header_position'] == 'horizontal'){ ?> checked="checked" <?php } ?> /><br />
    Vertical: <input type="radio" name="groups_header_position" value="vertical" <?php if($options['groups_header_position'] == 'vertical'){ ?> checked="checked" <?php } ?> /><br />
    <input type="hidden" id="groups_header_submit" name="groups_header_submit" value="1" />
  </p>
<?php
}

/**
 *  members sidebar header widget
 *
 * @package Custom Community
 * @since 1.8.3
 */	

function profiles_header_widget($args) {
  extract($args);

  $options = get_option("profiles_header_position");
  if (!is_array( $options )) {
    $options = array(
      'profiles_header_position' => 'horizontal'
    );
  }

  if($options[profiles_header_position] != 'horizontal') {
  		locate_template( array( 'members/single/member-header-sidebar.php' ), true, false );
    } else {
    if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
  		locate_template( array( 'members/single/member-header.php' ), true, false );
    endwhile; endif;
  }
}

function profiles_header_control() {
  $options = get_option("profiles_header_position");
  if (!is_array( $options )) {
    $options = array(
      'profiles_header_position' => 'horizontal'
     );
  }

  if (isset($_POST['profiles_header_submit'])){
    $options['profiles_header_position'] = htmlspecialchars($_POST['profiles_header_position']);
    update_option("profiles_header_position", $options);
  }?>
  <p>
    <label for="profiles_header_position">Widget Position: </label><br />
    Horizontal: <input type="radio" name="profiles_header_position" value="horizontal" <?php if($options['profiles_header_position'] == 'horizontal'){ ?> checked="checked" <?php } ?> /><br />
    Vertical: <input type="radio" name="profiles_header_position" value="vertical" <?php if($options['profiles_header_position'] == 'vertical'){ ?> checked="checked" <?php } ?> /><br />
    <input type="hidden" id="profiles_header_submit" name="profiles_header_submit" value="1" />
  </p>	
<?php } 

/**
 *  featured posts widget
 *
 * @package Custom Community
 * @since 2.0
 */	

class featured_posts_widget extends WP_Widget {
	function featured_posts_widget() {
		  //Constructor
	        parent::WP_Widget(false, $name = 'Custom Community -> List Post', array(
	            'description' => 'List Post'
	        ));
	}
	
	function widget($args, $instance) {
		global $post;
 		extract( $args );	
	
		$selected_category = esc_attr($instance['category']);
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
        
	    $listing_style = empty($instance['featured_posts_listing_style']) ? ' ' : apply_filters('widget_title', $instance['featured_posts_listing_style']);
    
		$selected_post_type = esc_attr($instance['featured_posts_post_type']);
        
		$featured_posts_order_by = $instance['featured_posts_order_by'];
		$featured_posts_order = $instance['featured_posts_order'];
		$featured_posts_background_color = $instance['featured_posts_background_color'];
		$featured_posts_show_sticky = $instance['featured_posts_show_sticky'];
		$featured_posts_show_pages_by_id = $instance['featured_posts_show_pages_by_id'];
		$featured_posts_amount = $instance['featured_posts_amount'];
		$featured_posts_posts_per_page = $instance['featured_posts_posts_per_page'];
		$featured_posts_show_pagination = $instance['featured_posts_show_pagination'];
		$featured_posts_pagination_ajax_effect = $instance['featured_posts_pagination_ajax_effect'];
		
     	
		$tmp .= $before_widget;
		
		
		if ( ! empty( $title ) )
			$tmp .=  $before_title . $title . $after_title;
		
		
		
		$tmp .= '<ul>';
		$tmp .= '<div class="border"></div>';
		
		$atts = array(
			'amount' => $featured_posts_amount,
			'category_name' => $selected_category,
			'img_position' => $listing_style,
			'height' => 'auto',
			'page_id' => $featured_posts_show_pages_by_id,
			'post_type' => $selected_post_type,
			'show_sticky' => $featured_posts_show_sticky,
			'show_pagination' => $featured_posts_show_pagination,
			'show_pagination_wp_pagenavi' => 'hide',
			'posts_per_page' => $featured_posts_posts_per_page,
			'featured_id' => $widget_id,
			'orderby' => $featured_posts_order_by,
			'order' => $featured_posts_order
		);
		$tmp .=  cc_list_posts($atts,$content = null);
	
//	echo '<pre>';
//		print_r($atts);
//	echo '</pre>';
	
		$tmp .= '</ul>';
		$tmp .= $after_widget;
		
				
		
		echo $tmp;
		wp_reset_query();
	}
    function update($new_instance, $old_instance) {
        //update and save the widget
        return $new_instance;
    }
    function form($instance) {
    	global $tkf;
        //widgetform in backend
        $selected_category = esc_attr($instance['category']);
        $selected_post_type = esc_attr($instance['featured_posts_post_type']);
        $title = strip_tags($instance['title']);
		$listing_style = esc_attr($instance['featured_posts_listing_style']);
		
		
		$featured_posts_order_by = $instance['featured_posts_order_by'];
		$featured_posts_order = $instance['featured_posts_order'];
		$featured_posts_background_color = $instance['featured_posts_background_color'];
		$featured_posts_show_sticky = $instance['featured_posts_show_sticky'];
		$featured_posts_show_pages_by_id = $instance['featured_posts_show_pages_by_id'];
		$featured_posts_amount = $instance['featured_posts_amount'];
		$featured_posts_posts_per_page = $instance['featured_posts_posts_per_page'];
		$featured_posts_show_pagination = $instance['featured_posts_show_pagination'];
		$featured_posts_pagination_ajax_effect = $instance['featured_posts_pagination_ajax_effect'];
       // Get the existing categories and build a simple select dropdown for the user.

		$args = array('echo' => '0','hide_empty' => '0');
		$categories = get_categories($args);
		$cat_options[] = '<option value="all-categories">All categories</option>';	
		foreach($categories as $category) {
		    $selected = $selected_category === $category->slug ? ' selected="selected"' : '';
            $cat_options[] = '<option value="' . $category->slug .'"' . $selected . '>' . $category->name . '</option>';	
		}
		
		$args=array(
		  'public'   => true,
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types=get_post_types($args,$output,$operator); 
		foreach ($post_types  as $post_type ) {
		
		    $selected = $selected_post_type === $post_type ? ' selected="selected"' : '';
            $post_type_options[] = '<option value="' . $post_type .'"' . $selected . '>' . $post_type . '</option>';	
		
		}?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">List posts listing style: </label>
		 	<select name="<?php echo $this->get_field_name('featured_posts_listing_style'); ?>" id="<?php echo $this->get_field_id('featured_posts_listing_style'); ?>">
			   	<option <?php if($listing_style == 'img-mouse-over'){ ?> selected <?php } ?> value="img-mouse-over">image mouse over</option>
			   	<option <?php if($listing_style == 'posts-img-left-content-right'){ ?> selected <?php } ?> value="posts-img-left-content-right">posts-img-left-content-right</option>
			   	<option <?php if($listing_style == 'posts-img-right-content-left'){ ?> selected <?php } ?> value="posts-img-right-content-left">posts-img-right-content-left</option>
			   	<option <?php if($listing_style == 'posts-img-over-content'){ ?> selected <?php } ?> value="posts-img-over-content">posts-img-over-content</option>
			   	<option <?php if($listing_style == 'posts-img-under-content'){ ?> selected <?php } ?> value="posts-img-under-content">posts-img-under-content</option>
			   	<option <?php if($listing_style == 'bubbles'){ ?> selected <?php } ?> value="bubbles">bubbles</option><option value="default">default</option>
			   	<option <?php if($listing_style == 'default'){ ?> selected <?php } ?> value="pro">more options in the pro version</option>
		
		<?php if(is_array($tkf->list_post_template_name)){	foreach ($tkf->list_post_template_name as $key => $value) { ?>
			   	<option <?php if($listing_style == $key){ ?> selected <?php } ?> value="<?php echo $key ?>"><?php echo $value ?></option>
		<?php } } ?>
		
			 </select>
		</p>		
		
		<p>
		    <label for="<?php echo $this->get_field_id('category'); ?>">
		        <?php _e('Include category (optional):'); ?>
		    </label>
		    <select id="<?php echo $this->get_field_id('category'); ?>" class="widefat" name="<?php echo $this->get_field_name('category'); ?>">
		        <?php echo implode('', $cat_options); ?>
		    </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_order_by'); ?>">Order by: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('featured_posts_order_by'); ?>" name="<?php echo $this->get_field_name('featured_posts_order_by'); ?>" type="text" value="<?php echo attribute_escape($featured_posts_order_by); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_order'); ?>">Order: </label><br />
		 	<select name="<?php echo $this->get_field_name('featured_posts_order'); ?>" id="<?php echo $this->get_field_id('featured_posts_order'); ?>">
			   	<option <?php if($featured_posts_order == 'ASC'){ ?> selected <?php } ?> value="ASC">Ascending</option>
			   	<option <?php if($featured_posts_order == 'DESC'){ ?> selected <?php } ?> value="DESC">Descending</option>
			 </select>
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_show_sticky'); ?>">Show only sticky posts: </label><br />
   			<input type="checkbox" id="<?php echo $this->get_field_id('featured_posts_show_sticky'); ?>" name="<?php echo $this->get_field_name('featured_posts_show_sticky'); ?>" value="on" <?php if(attribute_escape($featured_posts_show_sticky) == 'on'){ ?> checked="checked" <?php } ?> /><br />
   		</p>
		
		<p>
		    <label for="<?php echo $this->get_field_id('featured_posts_post_type'); ?>">
		        <?php _e('Include post type (optional):'); ?>
		    </label>
		    <select id="<?php echo $this->get_field_id('featured_posts_post_type'); ?>" class="widefat" name="<?php echo $this->get_field_name('featured_posts_post_type'); ?>">
		        <?php echo implode('', $post_type_options); ?>
		    </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_show_pages_by_id'); ?>">Page IDs: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('featured_posts_show_pages_by_id'); ?>" name="<?php echo $this->get_field_name('featured_posts_show_pages_by_id'); ?>" type="text" value="<?php echo attribute_escape($featured_posts_show_pages_by_id); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_amount'); ?>">Amount of posts: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('featured_posts_amount'); ?>" name="<?php echo $this->get_field_name('featured_posts_amount'); ?>" type="text" value="<?php echo attribute_escape($featured_posts_amount); ?>" />
		</p>
	
		<p>
			<label for="<?php echo $this->get_field_id('featured_posts_posts_per_page'); ?>">Posts per page: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('featured_posts_posts_per_page'); ?>" name="<?php echo $this->get_field_name('featured_posts_posts_per_page'); ?>" type="text" value="<?php echo attribute_escape($featured_posts_posts_per_page); ?>" />
		</p>
		
		<p>
		    <label for="<?php echo $this->get_field_id('featured_posts_show_pagination'); ?>">
		        <?php _e('Show pagination:'); ?>
		    </label>
		    <select id="<?php echo $this->get_field_id('featured_posts_show_pagination'); ?>" class="widefat" name="<?php echo $this->get_field_name('featured_posts_show_pagination'); ?>">
			   	<option value="show">show</option>
				<option value="hide">hide</option>
			</select>
		</p>
		
		<p>
		    <label for="<?php echo $this->get_field_id('featured_posts_pagination_ajax_effect'); ?>">
		        <?php _e('Choose jQery effect:'); ?>
		    </label>
		    <select id="<?php echo $this->get_field_id('featured_posts_pagination_ajax_effect'); ?>" class="widefat" name="<?php echo $this->get_field_name('featured_posts_pagination_ajax_effect'); ?>">
	   			<option value="none">none</option>
				<option value="hide_show">hide - show</option>
				<option value="fadeOut_fadeIn">fadeOut - fadeIn</option>
				<option value="slideUp-slidedown">slideUp - slidedown</option>
				<option value="default">More in the Pro Version</option>
			</select>
		</p>
		
        <?php
    }
}
register_widget('featured_posts_widget');

