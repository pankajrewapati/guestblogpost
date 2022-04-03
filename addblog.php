<?php
/*
Plugin Name: Add Blog
Plugin URI: https://addblog.com/
Description: Author can add blog and admin will approved it.
Author: Pankaj Rewapati
Author URI: https://wordpress.com/
Text Domain: add-blog
Version: 1.2.4
*/


define( 'ADDBLOG_VERSION', '1.2.4' );
define( 'ADDBLOG_PLUGIN', __FILE__ );
define( 'ADDBLOG_PLUGIN_DIR', untrailingslashit( dirname( ADDBLOG_PLUGIN ) ) );
define( 'ADDBLOG_PLUGIN_BASENAME', plugin_basename( ADDBLOG_PLUGIN ) );

require_once ADDBLOG_PLUGIN_DIR . '/load.php';