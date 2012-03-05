<?php

define( 'CC_VERSION', '1.8.5' );

/**
 * loads custom community files
 *
 * @package Custom Community
 * @since 2.0
 */

function cc_init() {
	
}
add_action( 'after_setup_theme', 'cc_init', 1, 1 );

?>