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
		
		add_action( 'init', array( $this, 'generate_theme'), 10 );

		add_action( 'admin_menu',  array( $this, 'init_backend' ) );
		
		add_action( 'after_setup_theme', array( $this, 'set_globals' ), 10 );
		
		add_action( 'after_setup_theme', array( $this, 'cc_setup' ), 20 );

	}
	
	/** Tell WordPress to run cc_setup() when the 'after_setup_theme' hook is run. */
//add_action( 'after_setup_theme', 'cc_setup' );
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
		add_image_size( 'slider-top-large', 1006, 250, true  );
		add_image_size( 'slider-large', 990, 250, true  );
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
	
}
	
	function generate_theme(){
		
		$Theme_Generator = new CC_Theme_Generator();
	
		$args = array('echo' => '0','hide_empty' => '0');
		$categories = get_categories($args);
		foreach($categories as $category) {
			tk_select_add_option( 'slideshow_cat', $category->slug , $category->name );
		}

		/*
		 * Hiding elemts by id 
		 */
		if(!defined('BP_VERSION'))
			tk_hide_element( 'buddypress' );

				
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
		
		
		// helper functions
		require_once($this->require_path('/core/includes/helper-functions.php'));
		
		// theme layout specific functions
		require_once($this->require_path('/core/includes/theme-generator/style.php'));
		require_once($this->require_path('/core/includes/theme-generator/theme-generator.php'));
		
		// wordpress specific functions
		require_once($this->require_path('/core/includes/wp/shortcodes.php'));
		//require_once($this->require_path('/core/includes/wp/templatetags.php'));
		require_once($this->require_path('/core/includes/wp/widgets.php'));

		// buddypress specific functions
		if(defined('BP_VERSION')){
			require_once($this->require_path('/core/includes/bp/templatetags.php'));
			require_once($this->require_path('/core/includes/bp/buddydev-search.php'));	
		}
				
	}
	
	### add css and js
 	function enqueue_script() {
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