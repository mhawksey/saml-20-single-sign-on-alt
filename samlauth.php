<?php
/*
Plugin Name: SAML 2.0 Single Sign-On (ALT)
Version: 0.9.2
Plugin URI: http://keithbartholomew.com
Description: Authenticate users using <a href="http://rnd.feide.no/simplesamlphp">simpleSAMLphp</a>.
Author: Keith Bartholomew
Author URI: http://keithbartholomew.com
*/

$upload_dir = wp_upload_dir();
define('SAMLAUTH_CONF', $upload_dir['basedir'] . '/saml-20-single-sign-on-alt/etc');
define('SAMLAUTH_CONF_URL', $upload_dir['baseurl'] . '/saml-20-single-sign-on-alt/etc');
define('SAMLAUTH_ROOT',dirname(__FILE__));
define('SAMLAUTH_URL',plugins_url() . '/' . basename( dirname(__FILE__) ) );
define('SAMLAUTH_MD_URL', constant('SAMLAUTH_URL') . '/saml/www/module.php/saml/sp/metadata.php/' . get_current_blog_id() );

// Things needed everywhere
require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_settings.php' );
require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_client.php' );

$SAML_Client = new SAML_Client();

// WordPress action hooks
add_action('lost_password', array($SAML_Client,'disable_function'));
add_action('retrieve_password', array($SAML_Client,'disable_function'));
add_action('password_reset', array($SAML_Client,'disable_function'));
add_filter('show_password_fields', array($SAML_Client,'show_password_fields'));


// Things needed only by the admin portal
if( is_admin() )
{
  require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_admin.php' );
  $SAML_Admin = new SAML_Admin();
}

// disable BuddyPress settings tab
// https://buddypress.org/support/topic/hide-general-settings/
function my_plugin_init() {
	if( class_exists( 'BuddyPress' ) ) {
function alt_bp_change_settings_default() {
	global $bp;
	$args = array(
					'parent_slug' => 'settings',
					'screen_function' => 'bp_core_screen_notification_settings',
					'subnav_slug' => 'notifications'
					);
	bp_core_new_nav_default( $args );
	if ( bp_use_wp_admin_bar() ) {
		add_action( 'wp_before_admin_bar_render', create_function(
		'', 'global $wp_admin_bar; $wp_admin_bar->remove_menu( "my-account-settings-general" );' ) );
	}
}
add_action( 'bp_setup_nav','alt_bp_change_settings_default', 5);

function alt_bp_remove_general() {
	global $bp;
	bp_core_remove_subnav_item( $bp->settings->slug, 'general' );
}
add_action( 'bp_setup_nav', 'alt_bp_remove_general', 200);
	}
}
add_action( 'plugins_loaded', 'my_plugin_init' );


// end of file 
