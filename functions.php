<?php

require_once( dirname(__FILE__) . '/core/get-pro.php' );
require_once( dirname(__FILE__) . '/core/post-metabox.php' );
require_once( dirname(__FILE__) . '/core/loader.php');

function remove_sidebar_left(){
		
	remove_action( 'sidebar_left', 'sidebar_left', 2 );
	
}
function remove_sidebar_right(){
		
	remove_action( 'sidebar_right', 'sidebar_right', 2 );
	
}

if ( ! function_exists( 'cc_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 */
function cc_header_style() {
	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		#blog-description, #header div#logo h1 a, #header div#logo h4 a {
			position: absolute;
			left: -9000px;
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#blog-description, #header div#logo h1 a, #header div#logo h4 a {
		    color: #555555;
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif;


if ( ! function_exists( 'cc_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in cc_setup().
 *
 */
function cc_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background: #<?php echo get_background_color(); ?>;
		border: none;
		text-align: center;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 36px;
		letter-spacing: -0.03em;
		line-height: 42px;
		text-decoration: none;
	}
	#desc {
		font-size: 18px;
		line-height: 31px;
		padding: 0 0 9px 0;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 990px;
		width: 100%;
	}
	</style>
<?php
}
endif;

if ( ! function_exists( 'cc_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in cc_setup().
 *
 */
function cc_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<img src="<?php esc_url ( header_image() ); ?>" alt="" />
	</div>
<?php }
endif;

add_filter('widget_text', 'do_shortcode');
add_action( 'widgets_init', 'cc_widgets_init' );
function cc_widgets_init(){
	global $tkf;
	register_sidebars( 1,
		array(
			'name' => 'sidebar right',
			'id' => 'sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'sidebar left',
			'id' => 'leftsidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'home sidebar right',
			'id' => 'home-sidebar-right',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'home sidebar left',
			'id' => 'home-sidebar-left',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	### Add Sidebars
	register_sidebars( 1,
		array(
			'name' => 'header full width',
			'id' => 'headerfullwidth',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'header left',
			'id' => 'headerleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'header center',
			'id' => 'headercenter',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'header right',
			'id' => 'headerright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'footer full width',
			'id' => 'footerfullwidth',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'footer left',
			'id' => 'footerleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'footer center',
			'id' => 'footercenter',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'footer right',
			'id' => 'footerright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member header',
			'id' => 'memberheader',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member header left',
			'id' => 'memberheaderleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member header center',
			'id' => 'memberheadercenter',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member header right',
			'id' => 'memberheaderright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member sidebar left',
			'id' => 'membersidebarleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'member sidebar right',
			'id' => 'membersidebarright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group header',
			'id' => 'groupheader',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group header left',
			'id' => 'groupheaderleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group header center',
			'id' => 'groupheadercenter',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group header right',
			'id' => 'groupheaderright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group sidebar left',
			'id' => 'groupsidebarleft',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebars( 1,
		array(
			'name' => 'group sidebar right',
			'id' => 'groupsidebarright',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><div class="clear"></div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	
	if($tkf->widget_shortcode_number == '')
			$tkf->widget_shortcode_number = '3';
		
	for ($i = 1; $i <= $tkf->widget_shortcode_number; $i++) {
	
		register_sidebars(1,
			array(
				'name' => 'shortcode '.$i,
				'id' => 'shortcode',
				'description' => 'Use shortcode: [cc_widget id="'.$i.'"]',
				'before_widget' => '<div id="'.$i.'" class="widget '.$i.'">',
				'after_widget' => '</div><div class="clear"></div>',
				'before_title' => '<h3 class="widgettitle">',
				'after_title' => '</h3>'
			)
		);
		
	}	
		
	foreach( $tkf->home_widgets_line_amount as $line){
					
		foreach( $tkf->home_widgets_line_widgets_amount[$line] as $widget){
					
			register_sidebars(1,
				array(
					'name' => 'home widget line '.$line.' widget '.$widget ,
					'id' => 'home_widget_line_'.$line.'_widget_'.$widget,
					'description' => 'Home widget line '.$line.' widget '.$widget,
					'before_widget' => '<div id="line_'.$line.'_widget_'.$widget.'" class="widget home_widget_line">',
					'after_widget' => '</div>',
					'before_title' => '<h3 class="widgettitle">',
					'after_title' => '</h3>'
				)
			);
			 
		}	
		$wn = 0;	
	}	


}
?>