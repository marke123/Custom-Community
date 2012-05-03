<?php
class Custom_Community{
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Custom Community
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
			
		// Load predefined constants first thing
		add_action( 'cc_init', array( $this, 'load_constants' ), 2 );
		
		// Includes necessary files
		// add_action( 'cc_init', array( $this, 'includes' ), 100, 4 );
		
		$this->includes();
		
		// Includes the necessary js
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_script' ), 2 );
		
		// Let plugins know that Custom Community has started loading
		$this->init_hook();

		// Let other plugins know that Custom Community has finished initializing
		$this->loaded();
		
		if ( function_exists( 'bp_is_active' ) )
			BPUnifiedsearch::get_instance(); //that is the beauty of singleton, no proliferation of globals and you can always acess the same instance if you want to :)
		
		$this->framework_init();
		
		add_action( 'init', array( $this, 'generate_theme'), 1 );

		add_action( 'admin_menu',  array( $this, 'init_backend' ) );
		
		add_action( 'after_setup_theme', array( $this, 'set_globals' ), 10 );
		
		add_action( 'after_setup_theme', array( $this, 'cc_setup' ), 20 );
		
		add_action( 'wp_head', array( $this, 'add_header_script' ) );
		
		add_action( 'wp_footer', array( $this, 'add_footer_script' ), 20 );
		
		add_action( 'admin_head', array( $this, 'admin_head' ),1 );
		
		add_action( 'admin_footer', array( $this, 'admin_footer' ),1 );
		
		
	}
	
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * To override cc_setup() in a child theme, add your own cc_setup to your child theme's
	 * functions.php file.
	 *
	 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
	 * @uses register_nav_menus() To add support for navigation menus.
	 * @uses add_custom_background() To add support for a custom background.
	 * @uses add_editor_style() To style the visual editor.
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_custom_image_header() To add support for a custom header.
	 * @uses register_default_headers() To register the default custom header images provided with the theme.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 * @uses $content_width To set a content width according to the sidebars.
	 * @uses BP_DISABLE_ADMIN_BAR To disable the admin bar if set to disabled in the themesettings.
	 *
	 */
	
	function cc_setup() {
		global $tkf, $content_width;
	
	
		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();
	
		// This theme uses post thumbnails
		if ( function_exists( 'add_theme_support' ) ) {
			add_theme_support( 'post-thumbnails' );
			set_post_thumbnail_size( 222, 160, true );
			add_image_size( 'slider-top-large', 1000, 250, true  );
			add_image_size( 'slider-middle', 756, 250, true  );
			add_image_size( 'slider-thumbnail', 80, 50, true );
			add_image_size( 'post-thumbnails', 222, 160, true  );
			add_image_size( 'single-post-thumbnail', 598, 372, true );
		}
	
		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );
	
		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'cc', TEMPLATEPATH . '/languages' );
	
		$locale = get_locale();
		$locale_file = TEMPLATEPATH . "/languages/$locale.php";
		if ( is_readable( $locale_file ) )
			require_once( $locale_file );
	
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu_top' => __( 'Header top menu', 'cc' ),
			'primary' => __( 'Header bottom menu', 'cc' ),
		) );
		
		// This theme allows users to set a custom background
		if($tkf->add_custom_background == 'true'){
			add_custom_background();
		}
		// Your changeable header business starts here
		define( 'HEADER_TEXTCOLOR', '888888' );
		
		// No CSS, just an IMG call. The %s is a placeholder for the theme template directory URI.
		define( 'HEADER_IMAGE', '%s/images/default-header.png' );
	
		// The height and width of your custom header. You can hook into the theme's own filters to change these values.
		// Add a filter to cc_header_image_width and cc_header_image_height to change these values.
		define( 'HEADER_IMAGE_WIDTH', apply_filters( 'cc_header_image_width', 1000 ) );
		define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'cc_header_image_height', 233 ) );
	
	
		// Add a way for the custom header to be styled in the admin panel that controls
		// custom headers. See cc_admin_header_style(), below.
		if($tkf->add_custom_image_header == 'true'){
			add_custom_image_header( 'cc_header_style', 'cc_admin_header_style', 'cc_admin_header_image' );
		}
		
		// Define Content with
		$content_width  = "670";
		if($tkf->sidebar_position == "left and right"){
			$content_width  = "432";
		}
		
		// Define disable the admin bar
		if($tkf->bp_login_bar_top == 'off') {
			define( 'BP_DISABLE_ADMIN_BAR', true );
		}
		
		if($tkf->buddydev_search == 'true' && defined('BP_VERSION') && function_exists('bp_is_active')) {
				
			//* Add these code to your functions.php to allow Single Search page for all buddypress components*/
			//	Remove Buddypress search drowpdown for selecting members etc
			add_filter("bp_search_form_type_select", "cc_remove_search_dropdown"  );
			function cc_remove_search_dropdown($select_html){
			    return '';
			}
			
			remove_action( 'init', 'bp_core_action_search_site', 5 );//force buddypress to not process the search/redirect
			add_action( 'init', 'cc_bp_buddydev_search', 10 );// custom handler for the search
			
			function cc_bp_buddydev_search(){
			global $bp;
				if ( $bp->current_component == BP_SEARCH_SLUG )//if thids is search page
					bp_core_load_template( apply_filters( 'bp_core_template_search_template', 'search-single' ) );//load the single searh template
			}
			add_action("advance-search","cc_show_search_results",1);//highest priority
			
			/* we just need to filter the query and change search_term=The search text*/
			function cc_show_search_results(){
			    //filter the ajaxquerystring
			   	add_filter("bp_ajax_querystring","cc_global_search_qs",100,2);
			}
			
			//show the search results for member*/
			function cc_show_member_search(){
			    ?>
			   <div class="memberss-search-result search-result">
			   <h2 class="content-title"><?php _e("Members Results","cc");?></h2>
			  <?php locate_template( array( 'members/members-loop.php' ), true ) ;  ?>
			  <?php global $members_template;
				if($members_template->total_member_count>1):?>
					<a href="<?php echo bp_get_root_domain().'/'.BP_MEMBERS_SLUG.'/?s='.$_REQUEST['search-terms']?>" ><?php _e(sprintf("View all %d matched Members",$members_template->total_member_count),"cc");?></a>
				<?php 	endif; ?>
			</div>
			<?php	
			 }
			
			//Hook Member results to search page
			add_action("advance-search","cc_show_member_search",10); //the priority defines where in page this result will show up(the order of member search in other searchs)
			function cc_show_groups_search(){
			    ?>
			<div class="groups-search-result search-result">
			 	<h2 class="content-title"><?php _e("Group Search","cc");?></h2>
				<?php locate_template( array('groups/groups-loop.php' ), true ) ;  ?>
				
				<a href="<?php echo bp_get_root_domain().'/'.BP_GROUPS_SLUG.'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Groups","cc");?></a>
			</div>
				<?php
			 //endif;
			  }
			
			//Hook Groups results to search page
			 if(bp_is_active( 'groups' ))
			    add_action("advance-search","cc_show_groups_search",10);
			
			/**
			 *
			 * Show blog posts in search
			 */
			function cc_show_site_blog_search(){
			    ?>
			 <div class="blog-search-result search-result">
			 
			  <h2 class="content-title"><?php _e("Blog Search","cc");?></h2>
			   
			   <?php locate_template( array( 'search-loop.php' ), true ) ;  ?>
			   <a href="<?php echo bp_get_root_domain().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Posts","cc");?></a>
			</div>
			   <?php
			  }
			
			//Hook Blog Post results to search page
			 add_action("advance-search","cc_show_site_blog_search",10);
			
			//show forums search
			function cc_show_forums_search(){
			    ?>
			 <div class="forums-search-result search-result">
			   <h2 class="content-title"><?php _e("Forums Search","cc");?></h2>
			  <?php locate_template( array( 'forums/forums-loop.php' ), true ) ;  ?>
			  <a href="<?php echo bp_get_root_domain().'/'.BP_FORUMS_SLUG.'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched forum posts","cc");?></a>
			</div>
			  <?php
			  }
			
			//Hook Forums results to search page
			if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' )))
				add_action("advance-search","cc_show_forums_search",20);
			
			
			//show blogs search result
			
			function cc_show_blogs_search(){
			
			if(!is_multisite())
				return;
				
			    ?>
			  <div class="blogs-search-result search-result">
			  <h2 class="content-title"><?php _e("Blogs Search","cc");?></h2>
			  <?php locate_template( array( 'blogs/blogs-loop.php' ), true ) ;  ?>
			  <a href="<?php echo bp_get_root_domain().'/'.BP_BLOGS_SLUG.'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Blogs","cc");?></a>
			 </div>
			  <?php
			  }
			
			//Hook Blogs results to search page if blogs comonent is active
			 if(bp_is_active( 'blogs' ))
			    add_action("advance-search","cc_show_blogs_search",10);
			
			
			 //modify the query string with the search term
			function cc_global_search_qs(){
				if(empty($_REQUEST['search-terms']))
					return;
			
				return "search_terms=".$_REQUEST['search-terms'];
			}
			
			function cc_is_advance_search(){
			global $bp;
			if($bp->current_component == BP_SEARCH_SLUG)
				return true;
			return false;
			}
			remove_action( 'bp_init', 'bp_core_action_search_site', 7 );
				
		}
		
	}
	
	function generate_theme(){
		global $Theme_Generator, $tkf;
		$Theme_Generator = new CC_Theme_Generator();
	
		$args = array('echo' => '0','hide_empty' => '0');
		$categories = get_categories($args);
		foreach($categories as $category) {
			tk_select_add_option( 'slideshow_cat', $category->slug , $category->name );
			tk_select_add_option( 'home_featured_posts_category', $category->slug , $category->name );
			
		}
		
		if( is_array( $tkf->list_post_template_name ) ):
			foreach ( $tkf->list_post_template_name as $key => $value) {
				tk_select_add_option( 'home_featured_posts_style', $key , $value );
			}
		endif;
		
		if( is_array( $tkf->post_page_template_name ) ):
			foreach ( $tkf->post_page_template_name as $key => $value) {
				tk_select_add_option( 'post_page_template_style', $key , $value );
			}
		endif;
		
		
		// the image repeat selectbox is defined here! 
		$image_repeat['no-repeat'] = 'no repeat';
		$image_repeat['repeat'] = 'both';
		$image_repeat['repeat-x'] = 'horizontal';
		$image_repeat['repeat-y'] = 'vertical';
		
		foreach($image_repeat as $key => $value) {
			tk_select_add_option( 'bg_body_img_repeat', $key , $value );
			tk_select_add_option( 'bg_body_img_repeat', $key , $value );
		}
		
		
		$args=array(
		  'public'   => true,
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types=get_post_types($args,$output,$operator); 
		foreach ($post_types  as $post_type ) {
		
			tk_select_add_option( 'home_featured_posts_post_type', $post_type , $post_type );
			tk_select_add_option( 'slideshow_post_type', $post_type , $post_type );
		
		}
		
		
		
		if(function_exists('bp_is_active')){
			
			if ( bp_is_active( 'xprofile' ) ) :
				 if ( bp_has_profile() ) : 
					while ( bp_profile_groups() ) : bp_the_profile_group(); 
						tk_select_add_option( 'register_profile_groups',  bp_get_the_profile_group_id() , bp_get_the_profile_group_name() );
					endwhile;
				endif;
			endif; 
		
		}
		
		add_filter( 'tk_jqueryui_accordion_content_section_after_global-hompage-settings', array( $this, 'widgetarea_generator' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_shop', array( $this, 'getting_startet_add_shop' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_child_theme_creator', array( $this, 'child_theme_creator' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_template_generator', array( $this, 'post_page_template_generator' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_template_generator', array( $this, 'list_posts_template_generator' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_template_generator', array( $this, 'widget_template_generator' ) );
		
		/*
		 * Hiding elemts by id 
		 */
		if(!defined('BP_VERSION'))
			tk_hide_element( 'buddypress' );

				
		//if($tkf->use_widgetized_home == 'on' ){
		//	tk_hide_element( 'featured_posts' );	
		//}
		
	
	}
	
	function child_theme_creator( $html ){
		ob_start();
		
		$OneClickChildTheme = new OneClickChildTheme();
		
		$OneClickChildTheme->showThemePage();
		
		$tmp = ob_get_contents();
		ob_end_clean();
		
			return  $html . $tmp ;
	}
	
	function getting_startet_add_shop( $html ){
			$tmp = '<iframe style="width:100%; height:1000px;" src="http://themekraft.com/?post_type=product&fbtab"></iframe>';
			return  $html . $tmp;
	}

	function admin_footer(){ ?>
		
		
		
	<?php }	
	
	function admin_head(){ ?>

<script type="text/javascript">
	function ColorPicker(ColorPickerDiv) {
		
		jQuery(document).ready(function($){
			$(ColorPickerDiv).ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val(hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			})
			.bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
			});
		});
	}
</script>		
	


		<style>
		
		
		#widgetarea_lines_controls div, #widgetarea_lines_controls div input {
		    float:left;    
		    margin-right: 10px;
		}
		
		.controls div, .controls div input {
		    float:left;    
		    margin-right: 10px;
		}

		
	</style>
	
	<?php	
	}


	
	function post_page_template_generator( $html ){
		global $tkf;
		
		$tmp .= '';
			
		ob_start();?>

	<style>
		/* some additional admin page styles */ 
		div#list_post_template_controls {
			padding: 22px 10px 7px 30px;
		}
		div#list_post_template_controls div {
			float: left; 
			margin-right: 10px;
		}
		div#list_post_template_noforms_template {
		    color: #AAAAAA;
		    display: block;
		    font-family: georgia,times,serif;
		    font-size: 16px;
		    font-style: italic;
		    text-shadow: -1px 1px 0 #FFFFFF;
		}
		div#list_post_template div.subcontainer div.tk_field_row a span {
			text-decoration: none !important;
		}
		.ui-widget-content a {
		    text-decoration: none;
		}
	</style>
	
	<script language="javascript"> 
      function togglediv(showHideDiv, switchTextDiv) {
        var ele = document.getElementById(showHideDiv);
        var text = document.getElementById(switchTextDiv);
        if(ele.style.display == "block") {
          ele.style.display = "none";
         }
        else {
           ele.style.display = "block";
        }
      } 
	</script>
      	

	<script type="text/javascript">
		jQuery.noConflict();

		jQuery(document).ready(function() {
		
			var widgetarea_lines = jQuery('#post_page_template').sheepIt({
			
				separator: '',
				allowRemoveLast: false,
				allowRemoveCurrent: true,
				allowRemoveAll: true,
				allowAdd: true,
				allowAddN: false,
				
				maxFormsCount: 10,
				minFormsCount: 0,
				iniFormsCount: 0,	
				<?php if (is_array($tkf->post_page_template_amount)){ ?>
	
			        data: [
					<?php foreach( $tkf->post_page_template_amount as $line){ ?>
					{
					'post_page_template_#index#_cc-config_values[post_page_template_amount]': '<?php echo $tkf->post_page_template_amount[$line]; ?>',
					'post_page_template_#index#_cc-config_values[post_page_template_name]': '<?php echo $tkf->post_page_template_name[$line]; ?>',
					},
					
				<?php } ?>
	
	       		]
			
			<?php } ?>			
			
			});
		
		});
		
		
		</script>

		<div class="hidden_fields" style="display: none;">
			<?php
				// names and amount of created list post templates  					
				echo 'post_page_template_amount'. tk_form_textfield( 'post_page_template_amount', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'post_page_template_name'.tk_form_textfield( 'post_page_template_name', array( 'multi_index' => 0 ) ).'<br>'; 
			?>
		</div>




		<!-- sheepIt Form -->
		<div id="post_page_template">
		 
		  <!-- Form template-->
		  <div id="post_page_template_template">
		  <input type="hidden" id="cc-config_values[post_page_template_amount][#index#]" name="cc-config_values[post_page_template_amount][#index#]" value="#index#">
			
			
		  <div class="subcontainer">
		  	<a class="clickable" href="javascript:togglediv('post_page_template_options_#index#','display_post_page_template_#index#');" id="post_page_template_#index#">
				<div class="tk_field_row">
					<p><span class="tk_row_title">Template  <span id="widgetarea_lines_label">
					
					Name : <input id="post_page_template_#index#_cc-config_values[post_page_template_name]" type="text" value="" name="cc-config_values[post_page_template_name][#index#]">								
					
				<span id="post_page_template_remove_current" style="float: right;">
					<img class="delete" src="<?php echo get_template_directory_uri(); ?>/core/includes/tkf/includes/img/cross.png" width="16" height="16" border="0" title="Remove this template!">
			    </span>
				</p>
				
				</div>
			</a>
				
					
		 	   
				
			<div style="display: none;" class="subcontainer" id="post_page_template_options_#index#">
					
			
		<!-- post entry options (the post container) ///////////////////////////////// -->
		
				<a href="javascript:togglediv('post_page_template_entry_options_#index#','post_page_template_entry#index#');" id="post_page_template_entry#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Post entry (the post container) </span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="post_page_template_entry_options_#index#">
					
					post_page_template_entry_options_
					
				</div>
				
		
		<!-- featured image options ////////////////////////////////////////////////// -->
	
				<a href="javascript:togglediv('post_page_template_image_options_#index#','post_page_template_image#index#');" id="post_page_template_image#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Featured image</span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="post_page_template_image_options_#index#">
					
					Irgentwass
					
				</div>
				
				
		<!-- title options ////////////////////////////////////////////////// -->
				
				<a href="javascript:togglediv('post_page_template_title_options_#index#','post_page_template_title#index#');" id="post_page_template_title#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Title </span></p>
					
					</div>
				</a>
	
				<div style="display: none;" class="subcontainer" id="post_page_template_title_options_#index#">
									
				nochwass
				
				</div>
				
				
		<!-- content options ////////////////////////////////////////////////// -->
	
				<a href="javascript:togglediv('post_page_template_content_options_#index#','post_page_template_content#index#');" id="post_page_template_content#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Content </span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="post_page_template_content_options_#index#">
					
					wass
							
				</div>
			</div>
		</div>
	</div>
		  <!-- /Form template-->
		   
		<div class="tk_field_row">
			  <!-- No forms template -->
			  <div id="post_page_template_noforms_template">No custom templates at the moment. Why not create one?</div>
			  <!-- /No forms template-->
			 
		</div>	 
			   
		  <!-- Controls -->
		  <div id="post_page_template_controls" class="tk_field_row">
		    <div id="post_page_template_add"><a class="button button-secondary"><span>Add a template</span></a></div>
		    <div id="post_page_template_remove_last"><a class="button button-secondary"><span>Remove</span></a></div>
		    <div id="post_page_template_remove_all"><a class="button button-secondary"><span>Remove all</span></a></div>
		    <div id="post_page_template_add_n">
		      <input id="post_page_template_add_n_input" type="text" size="4" />
		      <div id="post_page_template_n_button"><a><span>Add</span></a></div></div>
		  </div>
		  <!-- /Controls -->
		   
		</div>
		<!-- /sheepIt Form -->
	


		<?php
		$tmp = ob_get_contents();
		ob_end_clean();
		
		$element['id'] = 'post_pages_template_generator'; 
		$element['title'] = 'Post and Page Tempaltes'; 
		$element['content'] = $tmp; 
		
		$elements[] = $element;
		
		$widget_config = tk_accordion( 'post_page_template_generator', $elements, FALSE );
		
		return  $html. $widget_config;
	}
	

	function widget_template_generator( $html ){
		global $tkf;
		
		$tmp .= '';
			
		ob_start();?>

	<style>
		/* some additional admin page styles */ 
		div#list_post_template_controls {
			padding: 22px 10px 7px 30px;
		}
		div#list_post_template_controls div {
			float: left; 
			margin-right: 10px;
		}
		div#list_post_template_noforms_template {
		    color: #AAAAAA;
		    display: block;
		    font-family: georgia,times,serif;
		    font-size: 16px;
		    font-style: italic;
		    text-shadow: -1px 1px 0 #FFFFFF;
		}
		div#list_post_template div.subcontainer div.tk_field_row a span {
			text-decoration: none !important;
		}
	</style>
	
	<script language="javascript"> 
      function togglediv(showHideDiv, switchTextDiv) {
        var ele = document.getElementById(showHideDiv);
        var text = document.getElementById(switchTextDiv);
        if(ele.style.display == "block") {
          ele.style.display = "none";
         }
        else {
           ele.style.display = "block";
        }
      } 
	</script>
      	

	<script type="text/javascript">
		jQuery.noConflict();

		jQuery(document).ready(function() {
		
			var widgetarea_lines = jQuery('#widget_template').sheepIt({
			
				separator: '',
				allowRemoveLast: false,
				allowRemoveCurrent: true,
				allowRemoveAll: true,
				allowAdd: true,
				allowAddN: false,
				
				maxFormsCount: 10,
				minFormsCount: 0,
				iniFormsCount: 0,	
				<?php if (is_array($tkf->widget_template_amount)){ ?>
	
			        data: [
					<?php foreach( $tkf->widget_template_amount as $line){ ?>
					{
					'widget_template_#index#_cc-config_values[widget_template_amount]': '<?php echo $tkf->widget_template_amount[$line]; ?>',
					'widget_template_#index#_cc-config_values[widget_template_name]': '<?php echo $tkf->widget_template_name[$line]; ?>',
					},
					
				<?php } ?>
	
	       		]
			
			<?php } ?>			
			
			});
		
		});
		
		
		</script>

		<div class="hidden_fields" style="display: none;">
			<?php
				// names and amount of created list post templates  					
				echo 'widget_template_amount'. tk_form_textfield( 'widget_template_amount', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'widget_template_name'.tk_form_textfield( 'widget_template_name', array( 'multi_index' => 0 ) ).'<br>'; 
			?>
		</div>




		<!-- sheepIt Form -->
		<div id="widget_template">
		 
		  <!-- Form template-->
		  <div id="widget_template_template">
		  <input type="hidden" id="cc-config_values[widget_template_amount][#index#]" name="cc-config_values[widget_template_amount][#index#]" value="#index#">
			
			
		  <div class="subcontainer">
		  	<a class="clickable" href="javascript:togglediv('widget_template_options_#index#','display_widget_template_#index#');" id="widget_template_#index#">
				<div class="tk_field_row">
					<p><span class="tk_row_title">Template  <span id="widgetarea_lines_label">
					
					Name : <input id="widget_template_#index#_cc-config_values[widget_template_name]" type="text" value="" name="cc-config_values[widget_template_name][#index#]">								
					
				<span id="widget_template_remove_current" style="float: right;">
					<img class="delete" src="<?php echo get_template_directory_uri(); ?>/core/includes/tkf/includes/img/cross.png" width="16" height="16" border="0" title="Remove this template!">
			    </span>
				</p>
				
				</div>
			</a>
				
					
		 	   
				
			<div style="display: none;" class="subcontainer" id="widget_template_options_#index#">
					
			
		<!-- post entry options (the post container) ///////////////////////////////// -->
		
				<a href="javascript:togglediv('widget_template_entry_options_#index#','widget_template_entry#index#');" id="widget_template_entry#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Post entry (the post container) </span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="widget_template_entry_options_#index#">
					
					widget_template_entry_options_
					
				</div>
				
		
		<!-- featured image options ////////////////////////////////////////////////// -->
	
				<a href="javascript:togglediv('widget_template_image_options_#index#','widget_template_image#index#');" id="widget_template_image#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Featured image</span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="widget_template_image_options_#index#">
					
					Irgentwass
					
				</div>
				
				
		<!-- title options ////////////////////////////////////////////////// -->
				
				<a href="javascript:togglediv('widget_template_title_options_#index#','widget_template_title#index#');" id="widget_template_title#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Title </span></p>
					
					</div>
				</a>
	
				<div style="display: none;" class="subcontainer" id="widget_template_title_options_#index#">
									
				nochwass
				
				</div>
				
				
		<!-- content options ////////////////////////////////////////////////// -->
	
				<a href="javascript:togglediv('widget_template_content_options_#index#','widget_template_content#index#');" id="widget_template_content#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Content </span></p>
					
					</div>
				</a>
				
				<div style="display: none;" class="subcontainer" id="widget_template_content_options_#index#">
					
					wass
							
				</div>
			</div>
		</div>
	</div>
		  <!-- /Form template-->
		   
		<div class="tk_field_row">
			  <!-- No forms template -->
			  <div id="widget_template_noforms_template">No custom templates at the moment. Why not create one?</div>
			  <!-- /No forms template-->
			 
		</div>	 
			   
		  <!-- Controls -->
		  <div id="widget_template_controls" class="tk_field_row">
		    <div id="widget_template_add"><a class="button button-secondary"><span>Add a template</span></a></div>
		    <div id="widget_template_remove_last"><a class="button button-secondary"><span>Remove</span></a></div>
		    <div id="widget_template_remove_all"><a class="button button-secondary"><span>Remove all</span></a></div>
		    <div id="widget_template_add_n">
		      <input id="widget_template_add_n_input" type="text" size="4" />
		      <div id="widget_template_n_button"><a><span>Add</span></a></div></div>
		  </div>
		  <!-- /Controls -->
		   
		</div>
		<!-- /sheepIt Form -->
	


		<?php
		$tmp = ob_get_contents();
		ob_end_clean();
		
		$element['id'] = 'widgets_template_generator'; 
		$element['title'] = 'Widgets Tempaltes'; 
		$element['content'] = $tmp; 
		
		$elements[] = $element;
		
		$widget_config = tk_accordion( 'widget_template_generator', $elements, FALSE );
		
		return  $html. $widget_config;
	}
	
	
	
	
	function list_posts_template_generator( $html ){
		global $tkf;
		
		$tmp .= '';
			
		ob_start();?>


	<style>
		/* some additional admin page styles */ 
		div#list_post_template_controls {
			padding: 22px 10px 7px 30px;
		}
		div#list_post_template_controls div {
			float: left; 
			margin-right: 10px;
		}
		div#list_post_template_noforms_template {
		    color: #AAAAAA;
		    display: block;
		    font-family: georgia,times,serif;
		    font-size: 16px;
		    font-style: italic;
		    text-shadow: -1px 1px 0 #FFFFFF;
		}
		div#list_post_template div.subcontainer div.tk_field_row a span {
			text-decoration: none !important;
		}
	</style>
	
	<script language="javascript"> 
      function togglediv(showHideDiv, switchTextDiv) {
        var ele = document.getElementById(showHideDiv);
        var text = document.getElementById(switchTextDiv);
        if(ele.style.display == "block") {
          ele.style.display = "none";
         }
        else {
           ele.style.display = "block";
        }
      } 
	</script>
      	

	<script type="text/javascript">
		jQuery.noConflict();

		jQuery(document).ready(function() {
		
			var widgetarea_lines = jQuery('#list_post_template').sheepIt({
			
				separator: '',
				allowRemoveLast: false,
				allowRemoveCurrent: true,
				allowRemoveAll: true,
				allowAdd: true,
				allowAddN: false,
				
				maxFormsCount: 10,
				minFormsCount: 0,
				iniFormsCount: 0,	
				<?php if (is_array($tkf->list_post_template_amount)){ ?>
	
			        data: [
					<?php foreach( $tkf->list_post_template_amount as $line){ ?>
					{
					'list_post_template_#index#_cc-config_values[list_post_template_entry_clickable]': '<?php echo $tkf->list_post_template_entry_clickable[$line]; ?>',
			    	
					'list_post_template_#index#_cc-config_values[list_post_template_background_color]': '<?php echo $tkf->list_post_template_background_color[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_background_color_top]': '<?php echo $tkf->list_post_template_background_color_top[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_background_image]': '<?php echo $tkf->list_post_template_background_image[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_background_image_repeat]': '<?php echo $tkf->list_post_template_background_image_repeat[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_width]': '<?php echo $tkf->list_post_template_width[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_height]': '<?php echo $tkf->list_post_template_height[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_corner_radius]': '<?php echo $tkf->list_post_template_corner_radius[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_border_color]': '<?php echo $tkf->list_post_template_border_color[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_box_shadow_color]': '<?php echo $tkf->list_post_template_box_shadow_color[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_box_shadow_style]': '<?php echo $tkf->list_post_template_box_shadow_style[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_image_show]': '<?php echo $tkf->list_post_template_image_show[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_image_position]': '<?php echo $tkf->list_post_template_image_position[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_image_height]': '<?php echo $tkf->list_post_template_image_height[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_image_width]': '<?php echo $tkf->list_post_template_image_width[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_entry_corner_radius]': '<?php echo $tkf->list_post_template_entry_corner_radius[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_image_border_color]': '<?php echo $tkf->list_post_template_image_border_color[$line]; ?>',
		
					'list_post_template_#index#_cc-config_values[list_post_template_image_box_shadow_color]': '<?php echo $tkf->list_post_template_image_box_shadow_color[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_image_box_shadow_style]': '<?php echo $tkf->list_post_template_image_box_shadow_style[$line]; ?>',
		
					'list_post_template_#index#_cc-config_values[list_post_template_title_show]': '<?php echo $tkf->list_post_template_title_show[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_title_color]': '<?php echo $tkf->list_post_template_title_color[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_title_size]': '<?php echo $tkf->list_post_template_title_size[$line]; ?>',
					
					'list_post_template_#index#_cc-config_values[list_post_template_title_font_family]': '<?php echo $tkf->list_post_template_title_font_family[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_title_font_weight]': '<?php echo $tkf->list_post_template_title_font_weight[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_title_font_style]': '<?php echo $tkf->list_post_template_title_font_style[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_title_text_shadow_color]': '<?php echo $tkf->list_post_template_title_text_shadow_color[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_title_text_shadow_style]': '<?php echo $tkf->list_post_template_title_text_shadow_style[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_content_show]': '<?php echo $tkf->list_post_template_content_show[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_content_font_color]': '<?php echo $tkf->list_post_template_content_font_color[$line]; ?>',
					
					'list_post_template_#index#_cc-config_values[list_post_template_content_link_color]': '<?php echo $tkf->list_post_template_content_link_color[$line]; ?>',
					
					'list_post_template_#index#_cc-config_values[list_post_template_content_font_size]': '<?php echo $tkf->list_post_template_content_font_size[$line]; ?>',
					
					'list_post_template_#index#_cc-config_values[list_post_template_content_font_family]': '<?php echo $tkf->list_post_template_content_font_family[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_content_font_weight]': '<?php echo $tkf->list_post_template_content_font_weight[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_content_font_style]': '<?php echo $tkf->list_post_template_content_font_style[$line]; ?>',
	
					'list_post_template_#index#_cc-config_values[list_post_template_content_text_shadow_color]': '<?php echo $tkf->list_post_template_content_text_shadow_color[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_content_text_shadow_style]': '<?php echo $tkf->list_post_template_content_text_shadow_style[$line]; ?>',
					
					'list_post_template_#index#_cc-config_values[list_post_template_amount]': '<?php echo $tkf->list_post_template_amount[$line]; ?>',
					'list_post_template_#index#_cc-config_values[list_post_template_name]': '<?php echo $tkf->list_post_template_name[$line]; ?>',
					},
					
				<?php } ?>
	
	       		]
			
			<?php } ?>			
			
			});
		
		});
		
		</script>


		<div class="hidden_fields" style="display: none;">
			
			
			<?php
			
			// init selectboxes for image position 
			$img_position['value'] = 'posts-img-left-content-right';
			$img_position['option_name'] = 'left from title and content';
			$img_position_options[] = $img_position;
			
			$img_position['value'] = 'posts-img-right-content-left';
			$img_position['option_name'] = 'right from title and content';
			$img_position_options[] = $img_position;
			
			$img_position['value'] = 'posts-img-over-content';
			$img_position['option_name'] = 'over title and content';
			$img_position_options[] = $img_position;
			
			$img_position['value'] = 'posts-img-under-content';
			$img_position['option_name'] = 'under title and content';
			$img_position_options[] = $img_position;

			$img_position['value'] = 'posts-img-between-title-content';
			$img_position['option_name'] = 'between title and content';
			$img_position_options[] = $img_position;
			
			
			// init selectboxes for font family  
			$font_family['value'] = 'arial, sans-serif';
			$font_family['option_name'] = 'Arial';
			$font_family_options[] = $font_family;

			$font_family['value'] = 'arial black, arial, sans-serif';
			$font_family['option_name'] = 'Arial Black';
			$font_family_options[] = $font_family;
			
			$font_family['value'] = 'helvetica, arial, sans-serif';
			$font_family['option_name'] = 'Helvetica';
			$font_family_options[] = $font_family;
			
			$font_family['value'] = 'century gothic, avant garde, arial, sans-serif';
			$font_family['option_name'] = 'Century Gothic';
			$font_family_options[] = $font_family;
			
			$font_family['value'] = 'impact, arial, sans-serif';
			$font_family['option_name'] = 'Impact';
			$font_family_options[] = $font_family;
			
			$font_family['value'] = 'times new roman, times';
			$font_family['option_name'] = 'Times New Roman';
			$font_family_options[] = $font_family;

			$font_family['value'] = 'garamond, times new roman, times, serif';
			$font_family['option_name'] = 'Garamond';
			$font_family_options[] = $font_family;
			
			$font_family['value'] = 'georgia, times, serif';
			$font_family['option_name'] = 'Georgia';
			$font_family_options[] = $font_family;
			
			
			// init selectboxes for image repeat  
			$img_repeat['value'] = 'no-repeat';
			$img_repeat['option_name'] = 'no repeat';
			$img_repeat_options[] = $img_repeat;
			
			$img_repeat['value'] = 'repeat-x';
			$img_repeat['option_name'] = 'horizontal';
			$img_repeat_options[] = $img_repeat;
			
			$img_repeat['value'] = 'repeat-y';
			$img_repeat['option_name'] = 'vertical';
			$img_repeat_options[] = $img_repeat;
			
			$img_repeat['value'] = 'repeat';
			$img_repeat['option_name'] = 'repeat both';
			$img_repeat_options[] = $img_repeat;
			
			
			// init selectboxes for font weight  
			$font_weight['value'] = 'normal';
			$font_weight['option_name'] = 'normal';
			$font_weight_options[] = $font_weight;
			
			$font_weight['value'] = 'bold';
			$font_weight['option_name'] = 'bold';
			$font_weight_options[] = $font_weight;

			
			// init selectboxes for font style  
			$font_style['value'] = 'normal';
			$font_style['option_name'] = 'normal';
			$font_style_options[] = $font_style;
			
			$font_style['value'] = 'italic';
			$font_style['option_name'] = 'italic';
			$font_style_options[] = $font_style;
			
			
			// init selectboxes for shadow style  
			$shadow_style['value'] = 'outside';
			$shadow_style['option_name'] = 'outside';
			$shadow_style_options[] = $shadow_style;
			
			$shadow_style['value'] = 'inside';
			$shadow_style['option_name'] = 'inside';
			$shadow_style_options[] = $shadow_style;
			
							
					
			// post entry options (the post container) ///////////////////////////////// 
			
				// post entry: clickable box or not? 
				echo '<!-- list_post_template_entry_clickable'. tk_form_checkbox( 'list_post_template_entry_clickable', array( 'multi_index' => 0 ) ).'<br>//-->'; 
				
				// post entry: background color fade 
				echo 'list_post_template_background_color'.tk_form_colorpicker( 'list_post_template_background_color', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'list_post_template_background_color_top'.tk_form_colorpicker( 'list_post_template_background_color_top', array( 'multi_index' => 0 ) ).'<br>'; 

				// post entry: background image 
				echo 'list_post_template_background_image'.tk_form_fileuploader( 'list_post_template_background_image', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'list_post_template_background_image_repeat'. tk_form_select( 'list_post_template_background_image_repeat', $img_repeat_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// post entry: width and height 
				echo 'list_post_template_width'.tk_form_textfield( 'list_post_template_width', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'list_post_template_height'.tk_form_textfield( 'list_post_template_height', array( 'multi_index' => 0 ) ).'<br>'; 
			
				// post entry: corner radius 
				echo 'list_post_template_corner_radius'. tk_form_textfield( 'list_post_template_corner_radius', array( 'multi_index' => 0 ) ).'<br>'; 

				// post entry: border color 
				echo 'list_post_template_border_color'.tk_form_colorpicker( 'list_post_template_border_color', array( 'multi_index' => 0 ) ).'<br>'; 	

				// post entry: box shadows for everyone! 
				echo 'list_post_template_box_shadow_color'.tk_form_colorpicker( 'list_post_template_box_shadow_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				echo 'list_post_template_box_shadow_style'. tk_form_select( 'list_post_template_box_shadow_style', $shadow_style_options, array( 'multi_index' => 0 ) ).'<br>'; 	


			// featured image options ////////////////////////////////////////////////// 

				// featured image: show or hide. * checked = show = default *  
				echo '<!-- list_post_template_image_show'. tk_form_checkbox( 'list_post_template_image_show', array( 'multi_index' => 0 ) ).'<br> //-->'; 

				// featured image: position 
				echo 'list_post_template_image_position'. tk_form_select( 'list_post_template_image_position', $img_position_options, array( 'multi_index' => 0 ) ).'<br>'; 	

				// featured image: width and height 
				echo 'list_post_template_image_height'.tk_form_textfield( 'list_post_template_image_height', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'list_post_template_image_width'.tk_form_textfield( 'list_post_template_image_width', array( 'multi_index' => 0 ) ).'<br>'; 

				// featured image: corner radius 
				echo 'list_post_template_entry_corner_radius'. tk_form_textfield( 'list_post_template_entry_corner_radius', array( 'multi_index' => 0 ) ).'<br>'; 

				// featured image: border color 
				echo 'list_post_template_image_border_color'.tk_form_colorpicker( 'list_post_template_image_border_color', array( 'multi_index' => 0 ) ).'<br>'; 	
	
				// featured image: box shadows 
				echo 'list_post_template_image_box_shadow_color'.tk_form_colorpicker( 'list_post_template_image_box_shadow_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				echo 'list_post_template_image_box_shadow_style'. tk_form_select( 'list_post_template_image_box_shadow_style', $shadow_style_options, array( 'multi_index' => 0 ) ).'<br>'; 	
	
	
			// title options ////////////////////////////////////////////////// 

				// title: show or hide. * checked = show = default *  
				echo '<!-- list_post_template_title_show'. tk_form_checkbox( 'list_post_template_title_show', array( 'multi_index' => 0 ) ).'<br>//-->'; 

				// title: font color 
				echo 'list_post_template_title_color'.tk_form_colorpicker( 'list_post_template_title_color', array( 'multi_index' => 0 ) ).'<br>'; 	

				// title: font size 
				echo 'list_post_template_title_size'. tk_form_textfield( 'list_post_template_title_size', array( 'multi_index' => 0 ) ).'<br>'; 
				
				// title: font family 
				echo 'list_post_template_title_font_family'. tk_form_select( 'list_post_template_title_font_family', $font_family_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// title: font weight 
				echo 'list_post_template_title_font_weight'. tk_form_select( 'list_post_template_title_font_weight', $font_weight_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// title: font style 
				echo 'list_post_template_title_font_style'. tk_form_select( 'list_post_template_title_font_style', $font_style_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// title: text shadows 
				echo 'list_post_template_title_text_shadow_color'.tk_form_colorpicker( 'list_post_template_title_text_shadow_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				echo 'list_post_template_title_text_shadow_style'. tk_form_select( 'list_post_template_title_text_shadow_style', $shadow_style_options, array( 'multi_index' => 0 ) ).'<br>'; 	


			// content options ////////////////////////////////////////////////// 

				// content: show or hide. * checked = show = default *  
				echo '<!-- list_post_template_content_show'. tk_form_checkbox( 'list_post_template_content_show', array( 'multi_index' => 0 ) ).'<br>//-->'; 

				// content: font color 
				echo 'list_post_template_content_font_color'.tk_form_colorpicker( 'list_post_template_content_font_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				
				// content: link color 
				echo 'list_post_template_content_link_color'.tk_form_colorpicker( 'list_post_template_content_link_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				
				// content: font size 
				echo 'list_post_template_content_font_size'. tk_form_textfield( 'list_post_template_content_font_size', array( 'multi_index' => 0 ) ).'<br>'; 
				
				// content: font family 
				echo 'list_post_template_content_font_family'. tk_form_select( 'list_post_template_content_font_family', $font_family_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// content: font weight 
				echo 'list_post_template_content_font_weight'. tk_form_select( 'list_post_template_content_font_weight', $font_weight_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// content: font style 
				echo 'list_post_template_content_font_style'. tk_form_select( 'list_post_template_content_font_style', $font_style_options, array( 'multi_index' => 0 ) ).'<br>'; 

				// content: text shadows 
				echo 'list_post_template_content_text_shadow_color'.tk_form_colorpicker( 'list_post_template_content_text_shadow_color', array( 'multi_index' => 0 ) ).'<br>'; 	
				echo 'list_post_template_content_text_shadow_style'. tk_form_select( 'list_post_template_content_text_shadow_style', $shadow_style_options, array( 'multi_index' => 0 ) ).'<br>'; 	
				
				// names and amount of created list post templates  					
				echo 'list_post_template_amount'. tk_form_textfield( 'list_post_template_amount', array( 'multi_index' => 0 ) ).'<br>'; 
				echo 'list_post_template_name'.tk_form_textfield( 'list_post_template_name', array( 'multi_index' => 0 ) ).'<br>'; 
					
				// custom css
				//echo 'list_post_template_custom_css'. tk_form_textarea( 'list_post_template_entry_corner_radius', array( 'multi_index' => 0 ) ).'<br>'; 	
						
			?>
		
		</div>	



		<!-- sheepIt Form -->
		<div id="list_post_template">
		 
		  <!-- Form template-->
		  <div id="list_post_template_template">
		  <input type="hidden" id="cc-config_values[list_post_template_amount][#index#]" name="cc-config_values[list_post_template_amount][#index#]" value="#index#">
			
			
		  <div class="subcontainer">
		  	<a class="clickable" href="javascript:togglediv('list_post_template_options_#index#','display_list_post_template_#index#');" id="list_post_template_#index#">
					<div class="tk_field_row">
						<p><span class="tk_row_title">Template  <span id="widgetarea_lines_label">
						
						Name : <input id="list_post_template_#index#_cc-config_values[list_post_template_name]" type="text" value="" name="cc-config_values[list_post_template_name][#index#]">								
						
					<span id="list_post_template_remove_current" style="float: right;">
						<img class="delete" src="<?php echo get_template_directory_uri(); ?>/core/includes/tkf/includes/img/cross.png" width="16" height="16" border="0" title="Remove this template!">
				    </span>
					</p>
					
					</div>
			</a>
				
					
		 	   
				
			<div style="display: none;" class="subcontainer" id="list_post_template_options_#index#">
					
			
			<!-- post entry options (the post container) ///////////////////////////////// -->
			
					<a href="javascript:togglediv('list_post_template_entry_options_#index#','list_post_template_entry#index#');" id="list_post_template_entry#index#">
						<div class="tk_field_row">
							<p><span class="tk_row_title">Post entry (the post container) </span></p>
						
						</div>
					</a>
					
					<div style="display: none;" class="subcontainer" id="list_post_template_entry_options_#index#">
						
						<!-- post entry: clickable box or not? -->
						
						<div class="tk_field_row">
							Make the post a clickable box:
							<input type="checkbox" id="list_post_template_#index#_cc-config_values[list_post_template_entry_clickable]" name="cc-config_values[list_post_template_entry_clickable][#index#]">
						</div>
						
						<!-- post entry: background color or fade -->
							
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" Background Color ">Background color</label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_background_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_background_color][#index#]">
								</div>
						</div>
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" Background Color ">Background color (fade top) </label>
							</div>
							<div class="tk_field">
								<input  onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_background_color_top]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_background_color_top][#index#]">
								</div>
						</div>
					
						<!-- post entry: background image -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" Background Image ">Background image</label>
						
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_background_image]" type="text"  name="cc-config_values[list_post_template_background_image][#index#]">
								<input class="tk_fileuploader" type="button" value="Browse ...">
								<img id="list_post_template_#index#_cc-config_values[home_widgets_line_background_image]" class="tk_image_preview">
							</div>
						</div>
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="background image repeat">Background image repeat</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_background_image_repeat]" name="cc-config_values[list_post_template_background_image_repeat][#index#]">
									<option value="no-repeat">no repeat</option>
									<option value="repeat-x">repeat horizontal</option>
									<option value="repeat-y">repeat vertical</option>
									<option value="repeat">repeat both</option>
								</select>									
							</div>
						</div>
					
						<!-- post entry: width and height -->
							
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Template width, in pixel or %, example: 200px or 50%">Post entry width <br><span style="font-size:10px; color: #999999;">example: 200px or 50%</span> </label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_width]" type="text" value="" name="cc-config_values[list_post_template_width][#index#]">								
							</div>
						</div>
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Template height, in pixel, example: 120px">Post entry height <br><span style="font-size:10px; color: #999999;">in px, just enter a number</span> </label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_height]" type="text" value="" name="cc-config_values[list_post_template_height][#index#]">								
							</div>
						</div>	
						
						<!-- post entry: corner radius -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="post entries with rounded corners? example: 11px **note: IE doesn't support Rounded Corners..">Corner radius <br><span style="font-size:10px; color: #999999;">in px, just enter a number</span> </label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_corner_radius]" type="text" value="" name="cc-config_values[list_post_template_corner_radius][#index#]">								
							</div>
						</div>	
						
						<!-- post entry: border color -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" border color ">Border color</label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_border_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_border_color][#index#]">
								</div>
						</div>
				
						<!-- post entry: box shadows for everyone! -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" border color ">Box shadow color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_box_shadow_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_box_shadow_color][#index#]">
								</div>
						</div>
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Box shadow style, inside or outside? **note for inside effect: the shadowcolor should be brighter than the post-entry and background color">Box shadow style</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_box_shadow_style]" name="cc-config_values[list_post_template_box_shadow_style][#index#]">
									<option value="outside">outside</option>
									<option value="inside">inside</option>
								</select>									
							</div>
						</div>
					
					</div>
					
			
			<!-- featured image options ////////////////////////////////////////////////// -->
		
					<a href="javascript:togglediv('list_post_template_image_options_#index#','list_post_template_image#index#');" id="list_post_template_image#index#">
						<div class="tk_field_row">
							<p><span class="tk_row_title">Featured image</span></p>
						
						</div>
					</a>
					
					<div style="display: none;" class="subcontainer" id="list_post_template_image_options_#index#">
						
						<!-- featured image: show or hide. * checked = show = default *  -->
						
						<div class="tk_field_row">
							Hide featured image:
							<input type="checkbox" id="list_post_template_#index#_cc-config_values[list_post_template_image_show]" name="cc-config_values[list_post_template_image_show][#index#]">
						</div>
						
						<!-- featured image: position -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Image position">Image position</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_image_position]" name="cc-config_values[list_post_template_image_position][#index#]">
									<option value="posts-img-left-content-right">left from title and content</option>
									<option value="posts-img-right-content-left">right from title and content</option>
									<option value="posts-img-over-content">over title and content</option>
									<option value="posts-img-between-title-content">between title and content</option>
									<option value="posts-img-under-content">under title and content</option>
								</select>									
							</div>
						</div>
						
						<!-- featured image: width and height -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Image width, in pixel, just enter a number. example: 150px">Image width <br><span style="font-size:10px; color: #999999;">in px, just enter a number</span></label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_image_width]" type="text" value="" name="cc-config_values[list_post_template_image_width][#index#]">								
							</div>
						</div>	
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Image height, in pixel, just enter a number. example: 150px">Image height <br><span style="font-size:10px; color: #999999;">in px, just enter a number</span></label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_image_height]" type="text" value="" name="cc-config_values[list_post_template_image_height][#index#]">								
							</div>
						</div>	
						
						<!-- featured image: corner radius -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Images with rounded corners? In pixel, just enter a number, example: 11">Corner radius <br><span style="font-size:10px; color: #999999;">in px, just enter a number</span></label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_entry_corner_radius]" type="text" value="" name="cc-config_values[list_post_template_entry_corner_radius][#index#]">								
							</div>
						</div>	
						
						
						<!-- featured image: border color -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Choose a border color for the images.">Border color</label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_image_border_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_image_border_color][#index#]">
								</div>
						</div>
				
						<!-- featured image: box shadows -->
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title=" border color ">Box shadow color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_image_box_shadow_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_image_box_shadow_color][#index#]">
								</div>
						</div>
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="background image repeat">Box shadow style </label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_image_box_shadow_style]" name="cc-config_values[list_post_template_image_box_shadow_style][#index#]">
									<option value="outside">outside</option>
									<option value="inside">inside</option>
								</select>
							</div>
						</div>
					</div>
					
					
			<!-- title options ////////////////////////////////////////////////// -->
					
					<a href="javascript:togglediv('list_post_template_title_options_#index#','list_post_template_title#index#');" id="list_post_template_title#index#">
						<div class="tk_field_row">
							<p><span class="tk_row_title">Title </span></p>
						
						</div>
					</a>
		
					<div style="display: none;" class="subcontainer" id="list_post_template_title_options_#index#">
										
						<!-- title: show or hide. * checked = show = default *  -->
						<div class="tk_field_row">
							Hide the title:
							<input type="checkbox" id="list_post_template_#index#_cc-config_values[list_post_template_title_show]" name="cc-config_values[list_post_template_title_show][#index#]">
						</div>
						
						<!-- title: font color -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title font color ">Title font color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_title_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_title_color][#index#]">
								</div>
						</div>
						
						<!-- title: font size -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title font size: ">Title font size </label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_title_size]" type="text" value="" name="cc-config_values[list_post_template_title_size][#index#]">								
							</div>
						</div>	
						
						<!-- title: font family -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title font family">Title font family</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_title_font_family]" name="cc-config_values[list_post_template_title_font_family][#index#]">
									<option value="arial, sans-serif">Arial</option>
									<option value="arial black, arial, sans-serif">Arial Black</option>
									<option value="helvetica, arial, sans-serif">Helvetica</option>
									<option value="century gothic, avant garde, arial, sans-serif">Century Gothic</option>
									<option value="impact, arial, sans-serif">Impact</option>
									<option value="times new roman, times">Times New Roman</option>
									<option value="garamond, times new roman, times, serif">Garamond</option>
									<option value="georgia, times, serif">Georgia</option>
								</select>
							</div>
						</div>
						
						<!-- title: font weight -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title font weight: bold or normal?">Title font weight</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_title_font_weight]" name="cc-config_values[list_post_template_title_font_weight][#index#]">
									<option value="normal">normal</option>
									<option value="bold">bold</option>
								</select>
							</div>
						</div>
					
						<!-- title: font style -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title font style: italic or normal?">Title font style</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_title_font_style]" name="cc-config_values[list_post_template_title_font_style][#index#]">
									<option value="normal">normal</option>
									<option value="italic">italic</option>
								</select>
							</div>
						</div>
				
						<!-- title: text shadows -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title text shadow color ">Title text shadow color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_title_text_shadow_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_title_text_shadow_color][#index#]">
							</div>
						</div>
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Title text shadow style">Title text shadow style</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_title_text_shadow_style]" name="cc-config_values[list_post_template_title_text_shadow_style][#index#]">
									<option value="outside">outside</option>
									<option value="inside">inside</option>
								</select>									
							</div>
						</div>
					
							
					</div>
					
					
			<!-- content options ////////////////////////////////////////////////// -->
		
					<a href="javascript:togglediv('list_post_template_content_options_#index#','list_post_template_content#index#');" id="list_post_template_content#index#">
						<div class="tk_field_row">
							<p><span class="tk_row_title">Content </span></p>
						
						</div>
					</a>
					
					<div style="display: none;" class="subcontainer" id="list_post_template_content_options_#index#">
						
						<!-- content: show or hide. * checked = show = default *  -->
						<div class="tk_field_row">
							Hide content:
							<input type="checkbox" id="list_post_template_#index#_cc-config_values[list_post_template_content_show]" name="cc-config_values[list_post_template_content_show][#index#]">
						</div>
						
						<!-- content: font color -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content font color ">Content font color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_content_font_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_content_font_color][#index#]">
								</div>
						</div>
						
						<!-- content: link color -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content link color ">Content link color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_content_link_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_content_link_color][#index#]">
								</div>
						</div>
						
						<!-- content: font size -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content font size: ">Content font size </label>
							</div>
							<div class="tk_field">
								<input id="list_post_template_#index#_cc-config_values[list_post_template_content_font_size]" type="text" value="" name="cc-config_values[list_post_template_content_font_size][#index#]">								
							</div>
						</div>	
						
						<!-- content: font family -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content font family">Content font family</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_content_font_family]" name="cc-config_values[list_post_template_content_font_family][#index#]">
									<option value="arial, sans-serif">Arial</option>
									<option value="arial black, arial, sans-serif">Arial Black</option>
									<option value="helvetica, arial, sans-serif">Helvetica</option>
									<option value="century gothic, avant garde, arial, sans-serif">Century Gothic</option>
									<option value="impact, arial, sans-serif">Impact</option>
									<option value="times new roman, times">Times New Roman</option>
									<option value="garamond, times new roman, times, serif">Garamond</option>
									<option value="georgia, times, serif">Georgia</option>
								</select>
							</div>
						</div>
						
						<!-- content: font weight -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content font weight">Content font weight</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_content_font_weight]" name="cc-config_values[list_post_template_content_font_weight][#index#]">
									<option value="normal">normal</option>
									<option value="bold">bold</option>
								</select>
							</div>
						</div>
					
						<!-- content: font style -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content font style">Content font style</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_content_font_style]" name="cc-config_values[list_post_template_content_font_style][#index#]">
									<option value="normal">normal</option>
									<option value="italic">italic</option>
								</select>
							</div>
						</div>
				
						<!-- content: text shadows -->
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content text shadow color ">Content text shadow color </label>
							</div>
							<div class="tk_field">
								<input onfocus="ColorPicker('.entryimage76576566764#index#');" id="list_post_template_#index#_cc-config_values[list_post_template_content_text_shadow_color]" class="entryimage76576566764#index#" type="text"  name="cc-config_values[list_post_template_content_text_shadow_color][#index#]">
							</div>
						</div>
						
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label for="" title="Content text shadow style">Content text shadow style</label>
							</div>
							<div class="tk_field">
								<select id="list_post_template_#index#_cc-config_values[list_post_template_content_text_shadow_style]" name="cc-config_values[list_post_template_content_text_shadow_style][#index#]">
									<option value="inside">inside</option>
									<option value="outside">outside</option>
								</select>									
							</div>
						</div>
							
						
								
					</div>
					
				<!-- Custom CSS ////////////////////////////////////////////////// -->
		
					<a href="javascript:togglediv('list_post_template_css_options_#index#','list_post_template_content#index#');" id="list_post_template_css#index#">
						<div class="tk_field_row">
							<p><span class="tk_row_title">Custom CSS </span></p>
						
						</div>
					</a>
					
					<div style="display: none;" class="subcontainer" id="list_post_template_css_options_#index#">
						<div class="tk_field_row">
							<div class="tk_field_label">
								<label title="Add your own custom CSS styles" for="list_post_template_css">Custom CSS</label>
							</div>
							<div class="tk_field">
					<!--				<textarea class="lined" id="list_post_template_#index#_cc-config_values[list_post_template_custom_css]" name="list_post_template_#index#_cc-config_values[list_post_template_custom_css][#index#]"></textarea> -->
							</div>
						</div>
					</div>
								
							



			</div>
		</div>
	</div>
		  <!-- /Form template-->
		   
		<div class="tk_field_row">
			  <!-- No forms template -->
			  <div id="list_post_template_noforms_template">No custom templates at the moment. Why not create one?</div>
			  <!-- /No forms template-->
			 
		</div>	 
			   
		  <!-- Controls -->
		  <div id="list_post_template_controls" class="tk_field_row">
		    <div id="list_post_template_add"><a class="button button-secondary"><span>Add a template</span></a></div>
		    <div id="list_post_template_remove_last"><a class="button button-secondary"><span>Remove</span></a></div>
		    <div id="list_post_template_remove_all"><a class="button button-secondary"><span>Remove all</span></a></div>
		    <div id="list_post_template_add_n">
		      <input id="list_post_template_add_n_input" type="text" size="4" />
		      <div id="list_post_template_n_button"><a><span>Add</span></a></div></div>
		  </div>
		  <!-- /Controls -->
		   
		</div>
		<!-- /sheepIt Form -->
		
		<?php
		$tmp = ob_get_contents();
		ob_end_clean();
		
		$element['id'] = 'list_posts_template_generator'; 
		$element['title'] = 'List Posts Templates'; 
		$element['content'] = $tmp; 
		
		$elements[] = $element;
		
		$widget_config = tk_accordion( 'list_posts_template_generator', $elements, FALSE );
		
		return  $html. $widget_config;
	}
	
	
	
	function widgetarea_generator( $html ){
		global $tkf;
		
		$tmp .= '';
			
		ob_start();?>
		
		<script type="text/javascript">
		jQuery.noConflict();

		jQuery(document).ready(function() {
		
			var widgetarea_lines = jQuery('#widgetarea_lines').sheepIt({
			
				separator: '',
				allowRemoveLast: false,
				allowRemoveCurrent: true,
				allowRemoveAll: true,
				allowAdd: true,
				allowAddN: false,
				
				maxFormsCount: 10,
				minFormsCount: 0,
				iniFormsCount: 0,
				nestedForms: [
		            {
		                id: 'widgetarea_lines_#index#_widgets',
		                options: {
		                	separator: '',
				        	allowRemoveLast: false,
							allowRemoveCurrent: true,
							allowRemoveAll: true,
							allowAdd: true,
							allowAddN: false,
		                    indexFormat: '#index_widgets#',
		                    iniFormsCount: 1,
		                	maxFormsCount: 0,
		                }
		            }
		        ],
		        <?php if (is_array($tkf->home_widgets_line_amount)){ ?>
	
			        data: [
					<?php foreach( $tkf->home_widgets_line_amount as $line){ ?>
					{
		                'widgetarea_lines_#index#_cc-config_values[home_widgets_line_height]': '<?php echo $tkf->home_widgets_line_height[$line]; ?>',
		                'widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_color]': '<?php echo $tkf->home_widgets_line_background_color[$line]; ?>',
		                'widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_image]': '<?php echo $tkf->home_widgets_line_background_image[$line]; ?>',
			           	'widgetarea_lines_#index#_widgets': [
		 				<?php 
		 					foreach( $tkf->home_widgets_line_widgets_amount[$line] as $widget){ ?>
							{
								'height':'<?php echo $tkf->home_widgets_line_widgets_height[$line][$widget]; ?>',
								'width':'<?php echo $tkf->home_widgets_line_widgets_width[$line][$widget]; ?>',
								'background_color':'<?php echo $tkf->home_widgets_line_widgets_background_color[$line][$widget]; ?>',
								'background_image':'<?php echo $tkf->home_widgets_line_widgets_background_image[$line][$widget]; ?>',
							},
						<?php } ?>
		                ],
		                
		          	
					},
					
				<?php } ?>
	
	       		]
			
			<?php } ?>
			
			});
		
		});
		
		
		</script>

		
		<style>
			div.subcontainer {
			    border: 1px solid #DDDDDD;
			    margin: 20px;
			}
			
			span.tk_row_title {
				font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
    			font-size: 15px;
    			text-shadow: -1px 1px 0 #FFFFFF;
			}
			
			div.subcontainer .tk_field {
			    width: 69%;
			}
			div.subcontainer .tk_field_label {
			    width: 30%;
			}
			div.subcontainer .tk_field .tk_field {
			    width: 49%;
			}
			div.subcontainer .tk_field .tk_field_label {
			    width: 50%;
			}
			div#widgetarea_lines_add {
			    padding-top: 7px;
			}
			div#widgetarea_lines_noforms_template {
			    color: #AAAAAA;
			    display: block;
			    font-family: georgia,times,serif;
			    font-size: 16px;
			    font-style: italic;
			    text-shadow: -1px 1px 0 #FFFFFF;
			}
			.sub_field_line {
			    border-bottom: 1px solid #DFDFDF;
			    box-shadow: 0 1px 0 #FFFFFF;
			    margin-bottom: 15px;
			    padding: 0 0 20px;
			}
		</style>


		

		<script language="javascript"> 
	      function toggle(showHideDiv, switchTextDiv) {
	        var ele = document.getElementById(showHideDiv);
	        var text = document.getElementById(switchTextDiv);
	        if(ele.style.display == "block") {
	              ele.style.display = "none";
	          text.innerHTML = "show options";
	          }
	        else {
	          ele.style.display = "block";
	          text.innerHTML = "hide options";
	        }
	      } 
	    </script>

		<div class="tk_field_row">

			<?php
			//echo "<pre>";
				
			//print_r($tkf);	
				
			//echo "</pre>"
			?>
			
			Use widget homepage: <?php echo tk_form_checkbox('use_widgetized_home') ?><br>
			
			
		<div class="hidden_fields" style="display: none;">
			
			<?php 
			
			echo "<pre>";
				
			print_r($tkf->home_widgets_line_widgets_height);	
				
			echo "</pre>"; 
			
			echo "<pre>";
				
			print_r($tkf->home_widgets_line_amount);	
				
			echo "</pre>";  
			
			?>
			
			<?php echo 'home_widgets_line_amount'. tk_form_textfield( 'home_widgets_line_amount', array( 'multi_index' => 0 ) ).'<br>'; ?>
			<?php echo 'home_widgets_line_height'.tk_form_textfield( 'home_widgets_line_height', array( 'multi_index' => 0 ) ).'<br>'; ?>
			<?php echo 'home_widgets_line_background_color'.tk_form_colorpicker( 'home_widgets_line_background_color', array( 'multi_index' => 0 ) ).'<br>'; ?>
			<?php echo 'home_widgets_line_background_image'.tk_form_fileuploader( 'home_widgets_line_background_image', array( 'multi_index' => 0 ) ).'<br>'; ?>
			
				<?php echo 'home_widgets_line_widgets_amount'.tk_form_textfield( 'home_widgets_line_widgets_amount', array( 'multi_index' => array( 0, 0 ) ) ).'<br>'; ?>
		
				<?php echo 'home_widgets_line_widgets_height'. tk_form_textfield( 'home_widgets_line_widgets_height', array( 'multi_index' => array( 0, 0 )  ) ).'<br>'; ?>
				<?php echo 'home_widgets_line_widgets_width'. tk_form_textfield( 'home_widgets_line_widgets_width', array( 'multi_index' => array( 0, 0 ) )).'<br>'; ?>
				<?php echo 'home_widgets_line_widgets_background_color'. tk_form_colorpicker( 'home_widgets_line_widgets_background_color', array( 'multi_index' => array( 0, 0 ) )).'<br>'; ?>
				<?php echo 'home_widgets_line_widgets_background_image'. tk_form_fileuploader( 'home_widgets_line_widgets_background_image', array( 'multi_index' => array( 0, 0 ) )).'<br>'; ?>
				<?php echo 'home_widgets_line_widgets_background_image_repeat'. TK_Form_select( 'home_widgets_line_widgets_background_image_repeat', array( 'no repeat' ,'x', 'y', 'x+y' )).'<br>'; ?>
		</div>	
		
		</div>
			
		<!-- sheepIt Form -->
		<div id="widgetarea_lines">
		 
		  <!-- Form template-->
		  <div id="widgetarea_lines_template">
		  <input type="hidden" id="cc-config_values[home_widgets_line_amount][#index#]" name="cc-config_values[home_widgets_line_amount][#index#]" value="#index#">
			
		  <div class="subcontainer">
			<div class="tk_field_row">
				<p><span class="tk_row_title">Horizontal line <span id="widgetarea_lines_label"></span>: <a href="javascript:toggle('line_#index#','display_line_#index#');" id="display_line_#index#">show options</a></span>
			
			<a id="widgetarea_lines_remove_current" style="cursor: pointer; float: right;" title="Remove this line!">
				<img class="delete" src="<?php echo get_template_directory_uri(); ?>/core/includes/tkf/includes/img/cross.png" width="16" height="16" border="0">
		    </a>
			</p>
			
			</div>
			
			<div style="display: none;" id="line_#index#">
				
				<div class="tk_field_row">
	   				<div class="tk_field_label">
						<label for="" title="Line #index# styling. These options are for the container around the widgetareas of line #index#."><b> Line styling</b></label>
					</div>
		 	   
		 	   	<div class="tk_field">
		 	   
	            <div class="tk_field_label" style="padding: 0 0 0 20px;">
					<label><a href="javascript:toggle('options_line_#index#','display_line_#index#_css');" id="display_line_#index#_css">show options</a></label>
	            </div>
	
				
				
				<div style="display: none;" class="subcontainer" id="options_line_#index#">
					<div class="tk_field_row">
						<div class="tk_field_label">
							<label for="" title="Line height in pixel. Just enter a number">Line height</label>
						</div>
						<div class="tk_field">
							<input id="widgetarea_lines_#index#_cc-config_values[home_widgets_line_height]" type="text" value="" name="cc-config_values[home_widgets_line_height][#index#]" style="min-width: 70px; width: 70px;">px								</div>
					</div>
					<div class="tk_field_row">
						<div class="tk_field_label">
							<label for="" title="Background colour in hexcode (like #000000). Just pick a colour or enter the number (no # before).">Background colour </label>
						</div>
						<div class="tk_field">
							<input onfocus="ColorPicker('.image765765764#index#');" id="widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_color]" class="image765765764#index#" type="text"  name="cc-config_values[home_widgets_line_background_color][#index#]">
						</div>
					</div>
					<div class="tk_field_row">
						<div class="tk_field_label">
							<label for="" title="Background image, upload or insert url.">Background image </label>
					
						</div>
						<div class="tk_field">
							<input id="widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_image]" type="text"  name="cc-config_values[home_widgets_line_background_image][#index#]">
							<input class="tk_fileuploader" type="button" value="Browse ...">
							<img id="image_widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_image]" class="tk_image_preview">
						</div>
					</div>
				</div>
			</div>
			</div>	
					<!-- Embeded sheepIt Form -->
			         
			    		<div class="tk_field_row">
				 	  
				            <div class="tk_field_label">
								<label><b>Widget areas</b> </label>
				            </div>
			             
				            <div id="widgetarea_lines_#index#_widgets" class="tk_field">
				             
				                <!-- Form template-->
				                <div id="widgetarea_lines_#index#_widgets_template" class="sub_field_line">
				                	<input type="hidden" id="cc-config_values[home_widgets_line_widgets_amount][#index#][#index_widgets#]" name="cc-config_values[home_widgets_line_widgets_amount][#index#][#index_widgets#]" value="#index_widgets#">
			
			                		<label for="widgetarea_lines_#index#_widgets_#index_widgets#_widget" style="padding: 0px 0px 0px 20px;">Widgetarea #index#: </label>
			              			<a href="javascript:toggle('options_line_#index#_widget_#index_widgets#','display_widget_line_#index#_widget_#index_widgets#');" id="display_widget_line_#index#_widget_#index_widgets#" style="padding-top: 2px;">show options</a>
									<a id="widgetarea_lines_#index#_widgets_remove_current" style="cursor: pointer; float: right;" title="Remove this line!"><img src="<?php echo get_template_directory_uri(); ?>/core/includes/tkf/includes/img/cross.png" width="16" height="16" border="0"></a>
				                
			                		<div id="options_line_#index#_widget_#index_widgets#" class="subcontainer" style="display: none" >
									
									
										<div class="tk_field_row">
											<div class="tk_field_label">
												<label for="" title="Widgetarea #index_widgets# height: ">Height: </label>
											</div>
											<div class="tk_field">
												<input type="text" id="widgetarea_lines_#index#_widgets_#index_widgets#_height" name="cc-config_values[home_widgets_line_widgets_height][#index#][#index_widgets#]">	
											</div>
										</div>
							
							
										<div class="tk_field_row">
											<div class="tk_field_label">
												<label for="" title="Widgetarea #index_widgets# width">Width</label>
											</div>
											<div class="tk_field">
												<input type="text"  id="widgetarea_lines_#index#_widgets_#index_widgets#_width" name="cc-config_values[home_widgets_line_widgets_width][#index#][#index_widgets#]">
											</div>
										</div>
									
									
										<div class="tk_field_row">
											<div class="tk_field_label">
												<label for="" title="Widgetarea #index# background colour">Background colour</label>
											</div>
											<div class="tk_field">
												<input type="text" onfocus="ColorPicker('.image765765764#index#');" class="image7657[#index_widgets#]65764#index#" name="cc-config_values[home_widgets_line_widgets_background_color][#index#][#index_widgets#]" id="widgetarea_lines_#index#_widgets_#index_widgets#_background_color">						
											</div>
										</div>
										
										
										<div class="tk_field_row">
											<div class="tk_field_label">
												<label for="" title="Widgetarea #index_widgets# Background image">Background image</label>
											</div>
											<div class="tk_field">
												<input type="text" id="widgetarea_lines_#index#_widgets_#index_widgets#_background_image" name="cc-config_values[home_widgets_line_widgets_background_image][#index#][#index_widgets#]">
												<input type="button" value="Browse ..." class="tk_fileuploader"><br><img id="" class="tk_image_preview">									</div>
										</div>
											
									
										<div class="tk_field_row">
											<div class="tk_field_label">
												<label for="" title="&gt;Widgetarea #index_widgets# background image repeat">Background image repeat</label>
											</div>
											<div class="tk_field">
												<select id="widgetarea_lines_#index#_widgets_#index_widgets#_background_image_repeat" name="cc-config_values[home_widgets_line_widgets_background_image_repeat[#index#][#index_widgets#]]">
													<option value="no-repeat">no repeat</option>
													<option value="repeat-x">repeat horizontal</option>
													<option value="repeat-y">repeat vertical</option>
													<option value="repeat">repeat both</option></select>									
											</div>
										</div>
											
									
									</div>
			                
				                
				                
				                    
				                </div>
				                <!-- /Form template-->
				                 
				                <!-- No forms template -->
				                <div id="widgetarea_lines_#index#_widgets_noforms_template">Nix da</div>
				                <!-- /No forms template-->
				                 
				                <!-- Controls -->
				                <div id="widgetarea_lines_#index#_widgets_controls" class="controls">
				                    <div id="widgetarea_lines_#index#_widgets_add"><a class="button"><span>Add Widget</span></a></div>
				                    <div id="widgetarea_lines_#index#_widgets_remove_last"><a class="button"><span>Remove last</span></a></div>
				                    <div id="widgetarea_lines_#index#_widgets_remove_all"><a class="button"><span>Remove all</span></a></div>
				                    <div id="widgetarea_lines_#index#_widgets_add_n">
				                        <div id="widgetarea_lines_#index#_widgets_add_n_button"><a class="button"><span>Add</span></a></div>
				                        <input id="widgetarea_lines_#index#_widgets_add_n_input" style="min-width: 40px;" type="text" size="4" value="2"/> more lines
				                    </div>
				                </div>
				                <!-- /Controls -->
				                 
				            </div>
				    	</div> 
			        <!-- /Embeded sheepIt Form -->
 						
				
			</div>
		</div>
		  
		  </div>
		  <!-- /Form template-->
		   
		<div class="tk_field_row">
			  <!-- No forms template -->
			  <div id="widgetarea_lines_noforms_template">No horizontal widgetareas at the moment</div>
			  <!-- /No forms template-->
			 
		</div>	 
			   
		  <!-- Controls -->
		  <div id="widgetarea_lines_controls" class="tk_field_row">
		    <div id="widgetarea_lines_add"><a class="button button-secondary"><span>Add a line</span></a></div>
		    <div id="widgetarea_lines_remove_last"><a class="button button-secondary"><span>Remove last</span></a></div>
		    <div id="widgetarea_lines_remove_all"><a class="button button-secondary"><span>Remove all</span></a></div>
		    <div id="widgetarea_lines_add_n">
		      <div id="widgetarea_lines_add_n_button">
		      	<a class="button button-secondary"><span>Add</span></a>
		      </div>
		      <input id="widgetarea_lines_add_n_input" type="text" size="4" value="2"/> more lines
		    </div>
		  </div>
		  <!-- /Controls -->
		   
		</div>
		<!-- /sheepIt Form -->

			
		<?php
		$tmp = ob_get_contents();
		ob_end_clean();
		
		$element['id'] = 'global_hompage_add_widget'; 
		$element['title'] = 'Home Widget Areas'; 
		$element['content'] = $tmp; 
		
		$elements[] = $element;
		
		$widget_config = tk_accordion( 'global_hompage_add_widget', $elements, FALSE );
		
		return $widget_config . $html;
	}
	
	function framework_init(){
		
		// Registering the form where the data have to be saved
		$args['forms'] = array( 'cc-config' );
		$args['text_domain'] = 'my_text_domain';
		tk_framework( $args ); 
		
		 
	}
	 
	function set_globals(){
		global $tkf;
			
		$tkf = tk_get_values( 'cc-config' );
		
	}
	 
	function init_backend(){
		/*
		* WML
		*/
	 	tk_wml_parse_file( $this->require_path('/core/includes/admin/cc-config.xml') );
	}
	
	/**
	 * defines custom community init action
	 *
	 * this action fires on WP's init action and provides a way for the rest of custom community,
	 * as well as other dependend plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function init_hook() {
		do_action( 'cc_init' );
	}
	
	/**
	 * defines custom community action
	 *
	 * this action tells custom community and other plugins that the main initialization process has
	 * finished.
	 * 
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function loaded() {
		do_action( 'cc_loaded' );
	}
	
	/**
	 * defines constants needed throughout the theme.
	 *
	 * these constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */		
	function load_constants() {
		
		// The slug used when deleting a doc
		if ( !defined( 'CC_TEMPLATE_PATH' ) )
			define( 'CC_TEMPLATE_PATH', 'CC_TEMPLATE_PATH' );
			
	}	
	
	/**
	 * includes files needed by custom community
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function includes() {
		
		// TKF
		require_once($this->require_path('/core/includes/tkf/loader.php'));
		require_once($this->require_path('/_inc/ajax.php'));	
		require_once($this->require_path('/core/includes/child-theme-creator/child-theme-creator.php'));
		
		// helper functions
		require_once($this->require_path('/core/includes/helper-functions.php'));

		// HOOKS
		require_once($this->require_path('/custom-community-hooks.php'));
		
		// theme layout specific functions
		require_once($this->require_path('/core/includes/theme-generator/style.php'));
		require_once($this->require_path('/core/includes/theme-generator/theme-generator.php'));
		
		// wordpress specific functions
		require_once($this->require_path('/core/includes/shortcodes.php'));
		require_once($this->require_path('/core/includes/shortcodes.php'));
		require_once($this->require_path('/core/includes/widgets.php'));

		// buddypress specific functions
		if(defined('BP_VERSION')){
			require_once($this->require_path('/core/includes/bp/templatetags.php'));
			require_once($this->require_path('/core/includes/bp/buddydev-search.php'));	
		}
				
	}
	
	function add_header_script() { ?>
	
		<?php global $tkf; ?>
		


	<?php }

	function add_footer_script() { 
		global $tkf;
		if( $tkf->css_inspector == 'on'){ ?>
	
			<style type="text/css" media="screen">
		      .custom-hover {
		        box-shadow: black 0 0 5px;
		        -moz-box-shadow: black 0 0 5px;
		        -webkit-box-shadow: black 0 0 5px;
		      }
		    </style>
		    
		    <script type="text/javascript">
		      jQuery(document).ready(function() {
		        jQuery.fn.brosho({						//call to the brosho plugin
		          position:           'bottom',			//initial position of the editor ('top', 'bottom', 'left', 'right')
		          elementHoverClass:  'custom-hover',	//a custom hover class
		          editorOpacity:      1					//full opacity on editor
		        });
		      });
		    </script>
		<?php } ?>
	<?php }
		
	### add css and js
 	function enqueue_script() {
 		global $tkf;
		
	     if( is_admin() )
	        return;
	
		// on single blog post pages with comments open and threaded comments
		if(defined('BP_VERSION')){
			if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) ) {
		    // enqueue the javascript that performs in-link comment reply fanciness
	        wp_enqueue_script( 'comment-reply' ); 
	    	}
	    } else {
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { 
	        // enqueue the javascript that performs in-link comment reply fanciness
	        wp_enqueue_script( 'comment-reply' ); 
	    	}
	    }
	        
	    wp_deregister_script( 'ep-jquery-css' );
	        
	    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );	
		wp_enqueue_script( 'jquery-ui-tabs' );
		
		wp_register_script('reflection',get_template_directory_uri() . '/_inc/js/reflection.js','','' );
		wp_enqueue_script('reflection');
		

		
		if($tkf->css_inspector == 'on'){
			
			wp_register_script('brosho',get_template_directory_uri() . '/_inc/js/jquery.brosho.js','','' );
			wp_enqueue_script('brosho');
			
			wp_register_style( 'brosho-css', get_template_directory_uri() .'/_inc/css/jquery.brosho.css' );
			wp_enqueue_style( 'brosho-css' );
			
		}
		
	}	
	
	/** check if it's a child theme or parent theme and return the correct path */
	function require_path($path){
	if( TEMPLATEPATH != STYLESHEETPATH && is_file(STYLESHEETPATH . $path) ): 	
        return STYLESHEETPATH . $path;
    else:
        return TEMPLATEPATH . $path;
    endif;
	}
}

?>