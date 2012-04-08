<?php 
class CC_Theme_Generator{

	var $detect;

	/**
	 * PHP 4 constructor
	 *
	 * @package custom community
	 * @since 1.8.3
	 */
	function custom_community() {
		$this->__construct();
	}

	/**
	 * PHP 5 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function __construct() {
		global $bp;
		
		$this->detect = new TK_WP_Detect();
	
		// load predefined constants first
		add_action( 'bp_head', array( $this, 'load_constants' ), 2 );
		add_filter('body_class', array( $this, 'home_body_class'), 10 );
		
	}
	

	function load_constants(){
		global $tkf, $post;
		
		$component = explode('-',$this->detect->get_page_type());
			
		if($tkf->sidebar_position == ''){
			$tkf->sidebar_position = 'right';
			$tkf->menue_disable_home = 'true';
			$tkf->enable_slideshow_home = 'home';
			$tkf->header_text = 'off';
			$tkf->preview = 'true';
		}	
		
		$sidebar_position = $tkf->sidebar_position;
		
		
		if(!empty($component[2])){
			if($component[2] == 'groups' && !empty($component[3]) && $tkf->bp_groups_sidebars != 'default') {
				$sidebar_position = $tkf->bp_groups_sidebars;
			} elseif($component[2] == 'profile' && !empty($component[3]) && $tkf->bp_profile_sidebars != 'default') {
				$sidebar_position = $tkf->bp_profile_sidebars;
			}
		}
		
		if(is_home() && $tkf->hompage_sidebars != 'default')
			$sidebar_position = $tkf->hompage_sidebars;			
		
				
		if($tkf->leftsidebar_width == ''){
			$tkf->leftsidebar_width = '224';
		}
		
		
		if($tkf->rightsidebar_width == ''){
			$tkf->rightsidebar_width = '224';
		}
		
		$tmp = get_post_meta( $post->ID, '_wp_page_template', true );
		
		if($tmp == 'left-sidebar.php' || $tmp == 'right-sidebar.php' || $tmp == 'left-and-right-sidebar.php' || $tmp == 'full-width.php' ){
			switch ($tmp) {
				case 'left-sidebar.php': $tkf->rightsidebar_width = 0; break;
				case 'right-sidebar.php': $tkf->leftsidebar_width = 0; break;
				case 'full-width.php': $tkf->leftsidebar_width = 0; $tkf->rightsidebar_width = 0; break;
			}
		} else {
			switch ($sidebar_position) {
				case 'left': $tkf->rightsidebar_width = 0; break;
				case 'right': $tkf->leftsidebar_width = 0; break;
				case 'none': $tkf->leftsidebar_width = 0; $tkf->rightsidebar_width = 0; break;
				case 'full-width': $tkf->leftsidebar_width = 0; $tkf->rightsidebar_width = 0; break;
			}			
		}		
	}
	
	/**
	 * header: add div 'innerrim' before header if the header is not set to full width
	 * 
	 * located: header.php - do_action( 'bp_before_header' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_before_header(){
		global $tkf;
		
		if ($tkf->header_width != "full-width") {
			echo '<div id="innerrim">'; 
		}
	}
	
	/**
	 * header: add div class 'inner' inside the header if the header is set to full width
	 * 
	 * located: header.php - do_action( 'bp_first_inside_header' )
	 *
	 * @package Custom Community
	 * @since 2.0
	 */	
	function div_inner_start_inside_header(){
		global $tkf;
		
		if ($tkf->header_width == "full-width") {
			echo '<div class="inner">'; 
		}
	}
	
	/**
	 * header: add div end for class 'inner' inside the header if the header is set to full width
	 * 
	 * located: header.php - do_action( 'bp_last_inside_header' )
	 *
	 * @package Custom Community
	 * @since 2.0
	 */	
	function div_inner_end_inside_header(){
		global $tkf;
		
		if ($tkf->header_width == "full-width") {
			echo '</div><!-- .inner -->'; 
		}
	}
	
	/**
	 * header: add div 'innerrim' after header if the header is set to full width
	 * 
	 * located: header.php do_action( 'bp_after_header' ) on line 84
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_after_header(){
		global $tkf;
		
		if ($tkf->header_width == "full-width") {
			echo '<div id="innerrim">';
		}
	}
	
	/**
	 * header: add a search field in the header
	 * 
	 * located: header.php do_action( 'bp_after_header_nav' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	
	function menue_enable_search(){
		global $tkf;

		if(defined('BP_VERSION')){
			if($tkf->menue_enable_search == 'true'){?>
			<div id="search-bar" role="search">
				<div class="padder">
						<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
							<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
							<?php echo bp_search_form_type_select() ?>

							<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'cc' ) ?>" />

							<?php wp_nonce_field( 'bp_search_form' ) ?>

						</form><!-- #search-form -->

				<?php do_action( 'bp_search_login_bar' ) ?>

				</div><!-- .padder -->
			</div><!-- #search-bar -->
			<?php 
			}
		}
	}
	
	/**
	 * header: add a header logo in the header
	 * 
	 * located: header.php do_action( 'bp_before_access' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function header_logo(){
		global $tkf;	
			if(is_home()): ?>
			<div id="logo">
			<h1><a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?></a></h1>
			<div id="blog-description"><?php bloginfo('description'); ?></div>
			
			<?php if($tkf->logo){ ?>
			<a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><img src="<?php echo $tkf->logo?>" alt="<?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?>"></img></a>
			<?php } ?>
			</div>
		<?php else: ?>
			<div id="logo">
			<h4><a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?></a></h4>
			<div id="blog-description"><?php bloginfo('description'); ?></div>
			<?php if($tkf->logo){ ?>
			<a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><img src="<?php echo $tkf->logo?>" alt="<?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?>"></img></a>
			<?php } ?>
			</div>
		<?php endif;
	}
	
	/**
	 * header: add the buddypress dropdown navigation to the menu
	 * 
	 * located: header.php do_action( 'bp_menu' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function bp_menu(){
		global $tkf;	
	
			
			if(!defined('BP_VERSION')) :
				if($tkf->menue_disable_home == 'true'){ ?>
					<ul>
						<li id="nav-home"<?php if ( is_home() ) : ?> class="page_item current-menu-item"<?php endif; ?>>
							<a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php _e( 'Home', 'cc' ) ?></a>
						</li>
					</ul>
				<?php } ?>
			<?php else : ?>
				<ul>
				<?php if($tkf->menue_disable_home == 'true'){ ?>
					<li id="nav-home"<?php if ( is_front_page() ) : ?> class="page_item current-menu-item"<?php endif; ?>>
						<a href="<?php echo home_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php _e( 'Home', 'cc' ) ?></a>
					</li>
				<?php }?>
					<?php if($tkf->menue_enable_community == 'true'){ ?>
					<li id="nav-community"<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) || (bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user()) || (bp_is_page( BP_GROUPS_SLUG ) || bp_is_group()) || bp_is_page( BP_FORUMS_SLUG ) || bp_is_page( BP_BLOGS_SLUG ) )  : ?> class="page_item current-menu-item"<?php endif; ?>>
						<a href="<?php echo home_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Community', 'cc' ) ?>"><?php _e( 'Community', 'cc' ) ?></a>
						<ul class="children">
							<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
								<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
									<a href="<?php echo home_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'cc' ) ?>"><?php _e( 'Activity', 'cc' ) ?></a>
								</li>
							<?php endif; ?>
			
							<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user() ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo home_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'cc' ) ?>"><?php _e( 'Members', 'cc' ) ?></a>
							</li>
			
							<?php if ( bp_is_active( 'groups' ) ) : ?>
								<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
									<a href="<?php echo home_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'cc' ) ?>"><?php _e( 'Groups', 'cc' ) ?></a>
								</li>
								<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
									<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
										<a href="<?php echo home_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'cc' ) ?>"><?php _e( 'Forums', 'cc' ) ?></a>
									</li>
								<?php endif; ?>
							<?php endif; ?>
			
							<?php if ( bp_is_active( 'blogs' ) && is_multisite() ) : ?>
								<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
									<a href="<?php echo home_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'cc' ) ?>"><?php _e( 'Blogs', 'cc' ) ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
	        		<?php do_action( 'bp_nav_items' ); ?>
	        		<?php } ?>
				</ul>
			<?php endif;
			
	}

	
	function remove_home_nav_from_fallback( $args ) {
		$args['show_home'] = false;
		return $args;
	}
	
	/**
	 * header: add the top slider to the homepage, all pages, or just on specific pages
	 * 
	 * !!! this function needs to be rewritten !!!
	 * 
	 * located: header.php do_action( 'bp_after_header' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function slideshow_home(){
		global $tkf;	
		$cc_page_options=cc_get_page_meta();
	
		if(defined('BP_VERSION')){ 
			if($tkf->enable_slideshow_home == 'all' || $tkf->enable_slideshow_home == 'home' && is_home() || $tkf->enable_slideshow_home  == 'home' && is_front_page() || $tkf->enable_slideshow_home == 'home' && bp_is_component_front_page( 'activity' ) || is_page() && isset($cc_page_options) && $cc_page_options['cc_page_slider_on'] == 1 || is_archive() && $tkf->enable_slideshow_home == 'home-archive-categories'){
				echo cc_slidertop(); // located under wp/templatetags
			}
		} elseif($tkf->enable_slideshow_home == 'all' || $tkf->enable_slideshow_home == 'home' && is_home() || $tkf->enable_slideshow_home == 'home' && is_front_page() || is_page() && isset($cc_page_options) && $cc_page_options['cc_page_slider_on'] == 1 || is_archive() && $tkf->enable_slideshow_home == 'home-archive-categories' ){
			echo cc_slidertop(); // located under wp/templatetags
		}
	}
	
	/**
	 * header: add the favicon icon to the site
	 * 
	 * located: header.php do_action( 'favicon' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function favicon(){
		global $tkf;	
		
		if($tkf->favicon != '') {
			echo '<link rel="shortcut icon" href="'.$tkf->favicon.'" />';
		}
	}
	

	/**
	 * footer: add div 'innerrim' before footer if the footer is set to full width
	 * 
	 * located: footer.php do_action( 'bp_before_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_before_footer(){
		global $tkf;
		
		if ($tkf->footer_width == "full-width") {
			echo '</div><!-- #innerrim -->'; 
		}
	}
	
	/**
	 * footer: add div class 'inner' inside the footer if the footer is set to full width
	 * 
	 * located: footer.php - do_action( 'bp_first_inside_footer' )
	 *
	 * @package Custom Community
	 * @since 2.0
	 */	
	function div_inner_start_inside_footer(){
		global $tkf;
		
		if ($tkf->footer_width == "full-width") {
			echo '<div class="inner">'; 
		}
	}
	
	/**
	 * footer: add div end for class 'inner' inside the footer if the footer is set to full width
	 * 
	 * located: header.php - do_action( 'bp_last_inside_footer' )
	 *
	 * @package Custom Community
	 * @since 2.0
	 */	
	function div_inner_end_inside_footer(){
		global $tkf;
		
		if ($tkf->footer_width == "full-width") {
			echo '</div><!-- .inner -->'; 
		}
	}

	/**
	 * footer: add div 'innerrim' after footer if the footer is not set to full width
	 * 
	 * located: footer.php do_action( 'bp_after_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_after_footer(){
		global $tkf;
		
		if ($tkf->footer_width != "full-width") {
			echo '</div><!-- #innerrim -->';
		}
	}

	/**
	 * footer: add the sidebars and their default widgets to the footer
	 * 
	 * located: footer.php do_action( 'bp_after_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function footer_content(){ 
		global $tkf;
		if( ! dynamic_sidebar( 'footerfullwidth' )) :
			if($tkf->preview == 'true'){ ?>
				<div class="widget" style="margin-bottom: 0; padding: 12px; border: 1px solid #dddddd;">
						<h3 class="widgettitle" ><?php _e('20 widget areas all over the site', 'cc'); ?></h3>
						<div><p style="font-size: 16px; line-height:170%;">4 header + 4 footer widget areas (2 full width and 6 columns). <br>
						6 widget areas for members + 6 for groups. 
						</p></div>
				
				</div>
			<?php } ?>	
		<?php endif; ?>
	
		<?php  if (is_active_sidebar('footerleft') || $tkf->preview == 'true' ){ ?>
		<div class="widgetarea cc-widget">
			<?php if( ! dynamic_sidebar( 'footerleft' )){ ?>
				<div class="widget">
					<h3 class="widgettitle" ><?php _e('Links', 'cc'); ?></h3>
					<ul>
						<?php wp_list_bookmarks('title_li=&categorize=0&orderby=id'); ?>
					</ul>
				</div>
			<?php } ?>
	  	</div>
		<?php  } ?>
  	
  		<?php if (is_active_sidebar('footercenter') || $tkf->preview == 'true'){ ?>
		<div <?php if(!is_active_sidebar('footerleft') && $tkf->preview != 'true' ) { echo 'style="margin-left: 34% !important;"'; } ?> class="widgetarea cc-widget">
			<?php if( ! dynamic_sidebar( 'footercenter' )){ ?>
				<div class="widget">
					<h3 class="widgettitle" ><?php _e('Archives', 'cc'); ?></h3>
					<ul>
						<?php wp_get_archives( 'type=monthly' ); ?>
					</ul>
				</div>				
			<?php } ?>
	  	</div>
  		<?php } ?>
  	
  		<?php if (is_active_sidebar('footerright') || $tkf->preview == 'true' ){ ?>
		<div class="widgetarea cc-widget cc-widget-right">
			<?php if( ! dynamic_sidebar( 'footerright' )){ ?>
				<div class="widget">
					<h3 class="widgettitle" ><?php _e('Meta', 'cc'); ?></h3>
					<ul>
						<?php wp_register(); ?>
						<li><?php wp_loginout(); ?></li>
						<?php wp_meta(); ?>
					</ul>
				</div>
			<?php } ?>
	  	</div>
	  	<?php } ?>
  	
  		<div class="clear"></div>
		<br />
		<div class="credits"><?php printf( __( '%s is proudly powered by <a class="credits" href="http://wordpress.org">WordPress</a> and <a class="credits" href="http://buddypress.org">BuddyPress</a>. ', 'cc' ), bloginfo('name') ); ?>
		Just another <a class="credits" href="http://themekraft.com/" target="_blank" title="Wordpress Theme" alt="WordPress Theme">WordPress Theme</a> developed by Themekraft.</div>
	<?php 
	}
	

	/**
	 * header: add the sidebar and their default widgets to the left sidebar
	 * 
	 * located: header.php do_action( 'sidebar_left' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function sidebar_left(){
		global $tkf, $bp, $post;
		
		$tmp = get_post_meta( $post->ID, '_wp_page_template', true );
		if( $tmp == 'full-width.php' || $tmp == 'right-sidebar.php')
			return;
		
		if( $tmp == 'left-and-right-sidebar.php' || $tmp == 'left-sidebar.php'){
			locate_template( array( 'sidebar-left.php' ), true );
			return;		
		}
		
		if( is_home()){
			if($tkf->hompage_sidebars == 'left' || $tkf->hompage_sidebars == 'left and right' || $tkf->hompage_sidebars == 'none'){
					
				if($tkf->hompage_sidebars == 'left' || $tkf->hompage_sidebars == 'none')
					remove_sidebar_right();
				
				if($tkf->hompage_sidebars == 'left' || $tkf->hompage_sidebars == 'left and right')
					locate_template( array( 'home-sidebar-left.php' ), true );
				
				return;		
			}
		}
		
		$component = explode('-',$this->detect->get_page_type());
		if(!empty($component[2])){	
		
			if($component[2] == 'groups' && !empty($component[3])) {
				if($tkf->bp_groups_sidebars == 'left' || $tkf->bp_groups_sidebars == 'left and right' ){
					locate_template( array( 'groups/single/group-sidebar-left.php' ), true );
				} elseif($tkf->bp_groups_sidebars == "default" && $tkf->sidebar_position == "left" || $tkf->sidebar_position == "left and right" && $tkf->bp_groups_sidebars == "default"){
					locate_template( array( 'sidebar-left.php' ), true );
				}
			} elseif($component[2] == 'profile' && !empty($component[3])) {
			
				if($tkf->bp_profile_sidebars == 'left' || $tkf->bp_profile_sidebars == 'left and right' ){
					locate_template( array( 'members/single/member-sidebar-left.php' ), true );
				} elseif( $tkf->bp_profile_sidebars == "default" && $tkf->sidebar_position == "left" || $tkf->sidebar_position == "left and right" && $tkf->bp_profile_sidebars == "default"){
					locate_template( array( 'sidebar-left.php' ), true );
				}
			} else if($tkf->sidebar_position == "left" || $tkf->sidebar_position == "left and right"){
				locate_template( array( 'sidebar-left.php' ), true );
			}  
		} else {
			if($tkf->sidebar_position == "left" || $tkf->sidebar_position == "left and right"){
				locate_template( array( 'sidebar-left.php' ), true );
			}    
	  	}
	}

	/**
	 * footer: add the sidebar and their default widgets to the right sidebar
	 * 
	 * located: footer.php do_action( 'sidebar_left' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function sidebar_right(){
		global $tkf, $bp, $post;
	
		$tmp = get_post_meta( $post->ID, '_wp_page_template', true );
		
		if( $tmp == 'full-width.php' || $tmp == 'left-sidebar.php')
			return;
		
		if( $tmp == 'left-and-right-sidebar.php' || $tmp == 'right-sidebar.php'){
			locate_template( array( 'sidebar.php' ), true );
			return;		
		}
		
		if( is_home()){
			if($tkf->hompage_sidebars == 'right' || $tkf->hompage_sidebars == 'left and right' || $tkf->hompage_sidebars == 'none'){
					
				if($tkf->hompage_sidebars == 'right' || $tkf->hompage_sidebars == 'none')
					remove_sidebar_left();
				
				if($tkf->hompage_sidebars == 'right' || $tkf->hompage_sidebars == 'left and right')
					locate_template( array( 'home-sidebar-right.php' ), true );
				
				return;		
			}
		}
		
		
		$component = explode('-',$this->detect->get_page_type());
		if(!empty($component[2])){	
			if($component[2] == 'groups' && !empty($component[3])) {
				if($tkf->bp_groups_sidebars == 'right' || $tkf->bp_groups_sidebars == 'left and right' ){
					locate_template( array( 'groups/single/group-sidebar-right.php' ), true );
				} elseif($tkf->bp_groups_sidebars == "default" && $tkf->sidebar_position == "right" || $tkf->sidebar_position == "left and right" && $tkf->bp_groups_sidebars == "default"){
					locate_template( array( 'sidebar.php' ), true );
				}
			} elseif($component[2] == 'profile' && !empty($component[3])) {
				if($tkf->bp_profile_sidebars == 'right' || $tkf->bp_profile_sidebars == 'left and right' ){
					locate_template( array( 'members/single/member-sidebar-right.php' ), true );
				} elseif( $tkf->bp_profile_sidebars == "default" && $tkf->sidebar_position == "right" || $tkf->sidebar_position == "left and right" && $tkf->bp_profile_sidebars == "default"){
					locate_template( array( 'sidebar.php' ), true );
				}
			} else if($tkf->sidebar_position == "right" || $tkf->sidebar_position == "left and right"){
				locate_template( array( 'sidebar.php' ), true );
			}     
		} else {
			if(($tkf->sidebar_position == "right" || $tkf->sidebar_position == "left and right" )){
				locate_template( array( 'sidebar.php' ), true );
			}    
  		}
		
	}
	
	/**
	 * footer: add the buddypress default login widget to the right sidebar
	 * 
	 * located: footer.php do_action( 'bp_inside_after_sidebar' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function login_sidebar_widget(){
		global $tkf;
	
		if(defined('BP_VERSION')) { if($tkf->login_sidebar != 'off' || $tkf->login_sidebar == 'false'){ cc_login_widget();}}
	
	}
	

	/**
	 * homepage: add the latest 3 posts to the default homepage in mouse-over magazine style
	 * 
	 * located: index.php do_action( 'bp_before_blog_home' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function home_featured_posts(){
		global $tkf;
		
		if( $tkf->preview == 'true'  || $tkf->home_featured_posts == 'show') {
						
			if($tkf->home_featured_posts_amount == ''){
				$tkf->home_featured_posts_amount = '3';
			}
			if($tkf->home_featured_posts_category == 'all-categories'){
				$tkf->home_featured_posts_category = '0';
			}
			if($tkf->home_featured_posts_post_type == ''){
				$tkf->home_featured_posts_post_type = 'post';
			}
		
			$args = array(
				'amount' => $tkf->home_featured_posts_amount,
				'img_position' => $tkf->home_featured_posts_style,
				'home_featured_posts_show_sticky'  => $tkf->home_featured_posts_show_sticky,
				'orderby' => $orderby,
				'category_name' => $tkf->home_featured_posts_category,
				'post_type' => $tkf->home_featured_posts_post_type,
				'page_id' => $tkf->last_posts_show_page,
				'posts_per_page' => $tkf->home_featured_posts_posts_per_page,
				'home_featured_posts_show_pagination' => $tkf->home_featured_posts_show_pagination
			);
	
				
			echo cc_list_posts($args); 
		}
	}
	

	/**
	 * check if to use content or excerpt and the excerpt length
	 * 
	 * located: multiple places
	 * 
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function excerpt_on(){
		global $tkf;
	
		if($tkf->excerpt_on != 'content'){
			add_filter('excerpt_length', 'cc_excerpt_length');
			the_excerpt( __( 'Read the rest of this entry &rarr;', 'cc' ) );
		} else {
			the_content( __( 'Read the rest of this entry &rarr;', 'cc' ) ); 
		}
		
	}
	

	/**
	 * groups home: add the sidebars and their default widgets to the groups header
	 * 
	 * located: grous/home.php do_action( 'bp_before_group_home_content' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function before_group_home_content(){
		global $tkf;
		if( $tkf->bp_groups_header == 'false' || $tkf->bp_groups_header == 'on'):?>
			<div id="item-header">
				<?php if( ! dynamic_sidebar( 'groupheader' )) : ?>
				 <?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
				<?php endif; ?>
				
				<?php if (is_active_sidebar('groupheaderleft') ){ ?>
					<div class="widgetarea cc-widget">
					<?php dynamic_sidebar( 'groupheaderleft' )?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('groupheadercenter') ){ ?>
					<div <?php if(!is_active_sidebar('groupheaderleft')) { echo 'style="margin-left:30% !important"'; } ?> class="widgetarea cc-widget">
					<?php dynamic_sidebar( 'groupheadercenter' ) ?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('groupheaderright') ){ ?>
					<div class="widgetarea cc-widget cc-widget-right">
					<?php dynamic_sidebar( 'groupheaderright' ) ?>
					</div>
				<?php } ?>
			</div>
		<?php elseif($tkf->bp_groups_header == 'just-title'):?>
			<div id="item-header">
				<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
			</div>
		<?php endif;?>
		<?php if($tkf->bp_default_navigation == 'true'){?>
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>
			
						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->
		<?php }
	}	

	/**
	 * members home: add the sidebars and their default widgets to the members header
	 * 
	 * located: members/home.php do_action( 'bp_before_member_home_content' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function before_member_home_content(){
		global $tkf;

		if($tkf->bp_profile_header == 'false' || $tkf->bp_profile_header == 'on'): ?>
			<div id="item-header">
				<?php if( ! dynamic_sidebar( 'memberheader' )) : ?>
					<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
				<?php endif; ?>
				
				<div class="clear"></div>
				
				<?php if (is_active_sidebar('memberheaderleft') ){ ?>
					<div class="widgetarea cc-widget">
					<?php dynamic_sidebar( 'memberheaderleft' )?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('memberheadercenter') ){ ?>
					<div <?php if(!is_active_sidebar('memberheaderleft')) { echo 'style="margin-left:30% !important"'; } ?> class="widgetarea cc-widget">
					<?php dynamic_sidebar( 'memberheadercenter' ) ?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('memberheaderright') ){ ?>
					<div class="widgetarea cc-widget cc-widget-right">
					<?php dynamic_sidebar( 'memberheaderright' ) ?>
					</div>
				<?php } ?>
			</div>
		<?php elseif($tkf->bp_profile_header == 'just-title'): ?>
			<div id="item-header">
				<h2 class="fn"><a href="<?php bp_user_link() ?>"><?php bp_displayed_user_fullname() ?></a> <span class="highlight">@<?php bp_displayed_user_username() ?> <span>?</span></span></h2>
			</div>
		<?php endif;?>
			
		<?php if($tkf->bp_default_navigation == 'true'){?>
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav">
				<ul>
					<?php bp_get_displayed_user_nav() ?>
		
					<?php do_action( 'bp_member_options_nav' ) ?>
				</ul>
			</div>
		</div><!-- #item-nav -->
		<?php }
	}
	

	/**
	 * login page: overwrite the login css by adding it to the login_head
	 * 
	 * located: login.php do_action( 'login_head' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function custom_login() { 
		global $tkf;?> 
		<style type="text/css">
		
		#login h1 a{
			<?php if($tkf->bg_loginpage_img){ ?>
				background-image: url('<?php echo $tkf->bg_loginpage_img; ?>');
			<?php } ?>
			color:#777;
		}
		#login h1 a {
			<?php if($tkf->bg_loginpage_img){ ?>
				background-image: url('<?php echo $tkf->bg_loginpage_img; ?>');
				height:<?php echo $tkf->login_logo_height; ?>px;
			<?php } ?>
			clear: both;
		}
		
		<?php if($tkf->bg_loginpage_body_img || $tkf->bg_loginpage_body_color){ ?>
			html, body.login {
				<?php if($tkf->bg_loginpage_body_img){ ?>
					background-image: url('<?php echo $tkf->bg_loginpage_body_img; ?>');
				<?php } ?>
				<?php if($tkf->bg_loginpage_body_color){ ?>
					background-color: #<?php echo $tkf->bg_loginpage_body_color; ?>;
				<?php } ?>
				height: 100%;
				padding-top: 0px;
			}
		<?php } ?>
		
		<?php if($tkf->bg_loginpage_body_color){ ?>
		body {
			color:#<?php echo $tkf->bg_loginpage_body_color; ?>;
		}
		<?php } ?>
		<?php if ($tkf->login_page_css){
			print $tkf->login_page_css;		
		} ?>
		#login{
		    margin: auto;
    		padding-top: 30px;
		}
		.login #nav a {
			color:#777 !important;
		}
		.login #nav a:hover {
			color:#777 !important;
		}
		.updated, .login #login_error, .login .message {
			background: none;
			color:#777;
			border-color:#888;
		}
		#lostpasswordform {
			border-color:#999;
		}
		<?php if($tkf->bg_loginpage_backtoblog_fade_1 && $tkf->bg_loginpage_backtoblog_fade_2){ ?>
			#backtoblog {
				background: -moz-linear-gradient(center bottom , #<?php echo $tkf->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $tkf->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;
				background: -webkit-linear-gradient(bottom, #<?php echo $tkf->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $tkf->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;				
				background: -o-linear-gradient(bottom, #<?php echo $tkf->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $tkf->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;
				background: -ms-linear-gradient((bottom, #<?php echo $tkf->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $tkf->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;
				background: linear-gradient(bottom, #<?php echo $tkf->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $tkf->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;				
			}
		<?php } ?>
		</style>
	<?php 
	}
	
	/**
	 * check if the class 'home' exists in the body_class if buddypress is activated.
	 * if not, add class 'home' or 'bubble' if cc is deactivated 
	 * 
	 * do_action( 'body_class' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function home_body_class($classes){
	
		if(defined('BP_VERSION')){
			if( !in_array( 'home', $classes ) ){
				if ( is_front_page() )
				$classes[] = 'home';
			}
		}
		
		return $classes;
	
	}
}

function innerrim_before_header(){
	global $Theme_Generator;
	$Theme_Generator->innerrim_before_header();
}
function div_inner_start_inside_header(){
	global $Theme_Generator;
	$Theme_Generator->div_inner_start_inside_header();
}
function div_inner_end_inside_header(){
	global $Theme_Generator;
	$Theme_Generator->div_inner_end_inside_header();
}
function innerrim_after_header(){
	global $Theme_Generator;
	$Theme_Generator->innerrim_after_header();
}
function menue_enable_search(){
	global $Theme_Generator;
	$Theme_Generator->menue_enable_search();
}
function header_logo(){
	global $Theme_Generator;
	$Theme_Generator->header_logo();
}
function bp_menu(){
	global $Theme_Generator;
	$Theme_Generator->bp_menu();
}
function remove_home_nav_from_fallback(){
	global $Theme_Generator;
	$Theme_Generator->remove_home_nav_from_fallback();
}
function slideshow_home(){
	global $Theme_Generator;
	$Theme_Generator->slideshow_home();
}
function favicon(){
	global $Theme_Generator;
	$Theme_Generator->favicon();
}
function innerrim_before_footer(){
	global $Theme_Generator;
	$Theme_Generator->innerrim_before_footer();
}
function div_inner_start_inside_footer(){
	global $Theme_Generator;
	$Theme_Generator->div_inner_start_inside_footer();
}
function div_inner_end_inside_footer(){
	global $Theme_Generator;
	$Theme_Generator->div_inner_end_inside_footer();
}
function innerrim_after_footer(){
	global $Theme_Generator;
	$Theme_Generator->innerrim_after_footer();
}
function footer_content(){
	global $Theme_Generator;
	$Theme_Generator->footer_content();
}
function sidebar_left(){
	global $Theme_Generator;
	$Theme_Generator->sidebar_left();
}
function sidebar_right(){
	global $Theme_Generator;
	$Theme_Generator->sidebar_right();
}
function login_sidebar_widget(){
	global $Theme_Generator;
	$Theme_Generator->login_sidebar_widget();
}
function home_featured_posts(){
	global $Theme_Generator;
	$Theme_Generator->home_featured_posts();
}
function home_body_class($classes){
	global $Theme_Generator;
	$Theme_Generator->home_body_class($classes);
}
function excerpt_on(){
	global $Theme_Generator;
	$Theme_Generator->excerpt_on();
}
function before_group_home_content(){
	global $Theme_Generator;
	$Theme_Generator->before_group_home_content();
}
function custom_login(){
	global $Theme_Generator;
	$Theme_Generator->custom_login();
}?>