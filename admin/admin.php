<?php
// this is for add backend menu
add_action(
	'admin_menu',
	'addblog_admin_menu',
	9, 0
);

function addblog_admin_menu(){
	// using by this hook , user can add menu outside from plugin as well, so user no need to customize flugin files for add new menu , he can simple use this hook.
	do_action( 'addblog_admin_menu' );

	add_menu_page(
		__( 'Add Blog', 'addblog' ),
		__( 'Add Blog', 'addblog' ),
		'manage_options',
		'settingpage',
		'settingPage',
		'dashicons-email',
		30
	);
}



// this is a function which is add action setting link in plugin list page

add_filter( 'plugin_action_links', 'misha_settings_link_addblog', 10, 2 );

function misha_settings_link_addblog( $links, $file ) {
	if ( $file != ADDBLOG_PLUGIN_BASENAME ) {
		return $links;
	}

	$settings_link = admin_url('admin.php?page=settingpage');
	$settings_link = '<a href='.$settings_link.'> Settings </a>';

	array_unshift( $links, $settings_link );

	return $links;
}


function settingPage(){
	require_once ADDBLOG_PLUGIN_DIR . '/admin/setting.php';
}
	
?>