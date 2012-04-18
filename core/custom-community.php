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
		
		
		
		if(defined('BP_VERSION')){
			
			if ( bp_is_active( 'xprofile' ) ) :
				 if ( bp_has_profile() ) : 
					while ( bp_profile_groups() ) : bp_the_profile_group(); 
						tk_select_add_option( 'register_profile_groups',  bp_get_the_profile_group_id() , bp_get_the_profile_group_name() );
					endwhile;
				endif;
			endif; 
		
		}
		
		add_filter( 'tk_jqueryui_accordion_content_section_after_global-hompage-settings', array( $this, 'global_hompage_add_widget' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_shop', array( $this, 'getting_startet_add_shop' ) );
		add_filter( 'tk_wp_jqueryui_tabs_after_content_child_theme_creator', array( $this, 'child_theme_creator' ) );
		
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
	
	
	function global_hompage_add_widget( $html ){
		global $tkf;
		
		$tmp .= '';
			
		ob_start();?>
		
		
			<?php global $tkf; ?>
		
		
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
							<input onfocus="ColorPicker('.image765765764#index#');" id="widgetarea_lines_#index#_cc-config_values[home_widgets_line_background_color]" class="image765765764#index# " type="text"  name="cc-config_values[home_widgets_line_background_color][#index#]">
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

	function add_footer_script() { ?>
	
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