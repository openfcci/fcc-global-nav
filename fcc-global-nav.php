<?php
/*
Plugin Name: FCC Global Nav
Plugin URI: https://github.com/openfcci/fcc-global-nav
Description: AreaVoices network global navigation plugin.
Author: Forum Communications Company
Version: 1.16.10.20
Author URI: http://forumcomm.com/
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Site Exemptions
 * Do not display the global nav on these sites.
 */
$site_exceptions = array(
	'67574', // fcc2.areavoices.com / forumcomm.com
	'67563', //forumcontentstudios.areavoices.com (old FCS site)
	'67782', //forumcontentstudio.areavoices.com (new FCS site)
);
if ( in_array( get_current_blog_id(), $site_exceptions ) ) {
	return;
} else {
	# set constant for other plugins and themes to check against
	define( 'FCC__GLOBAL_NAV', true );
}

/*--------------------------------------------------------------
# Load JS
--------------------------------------------------------------*/

wp_register_script( 'globalnav-clicktracking', plugin_dir_url( __FILE__ ) . '/js/globalnav-clicktracking.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'globalnav-clicktracking' );

/*--------------------------------------------------------------
# Load CSS (Front End Only)
--------------------------------------------------------------*/

function fcc_gn_register_plugin_styles() {
	if ( ! is_admin() ) {
		wp_dequeue_style( 'admin-bar' );
	}
	wp_enqueue_style( 'fcc_wpadminbar_css', plugin_dir_url( __FILE__ ) . '/fcc-global-nav.css' );
}
add_action( 'wp_enqueue_scripts', 'fcc_gn_register_plugin_styles' );

/*--------------------------------------------------------------
# Display the Admin Bar for Logged Out Users (Front End Only)
--------------------------------------------------------------*/

function fcc_always_show_admin_bar() {
	return true;
}
add_filter( 'show_admin_bar', 'fcc_always_show_admin_bar' );

function fcc_admin_bar_space() {
	if ( ! is_admin() && is_admin_bar_showing() ) { // is_user_logged_in()
		echo "
			<style type='text/css'>
			#nav-wrap {top: 32px !important;}
			</style>
		";
	}
}
add_action( 'wp_head', 'fcc_admin_bar_space' );

/*--------------------------------------------------------------
# Remove All Default Menu Items/Nodes (Front End Only)
--------------------------------------------------------------*/

function fcc_remove_default_nodes() {
	global $wp_admin_bar;

	if ( ! is_object( $wp_admin_bar ) or is_admin() ) {
		return;
	}

	$nodes = $wp_admin_bar->get_nodes();
	foreach ( $nodes as $node ) {
		if ( ! $node->parent || 'top-secondary' == $node->parent ) {
			// 'top-secondary' is used for the User Actions right side menu
			$wp_admin_bar->remove_menu( $node->id );
		}
	}

}
add_action( 'admin_bar_menu', 'fcc_remove_default_nodes', 200 );

/**
 * Remove Debug Bar Link on Front End
 */
function remove_debug_bar_link() {
	if ( ! is_admin() ) {
		global $wp_admin_bar;
		// Remove the debug bar button. Priority 1000
		$wp_admin_bar->remove_menu( 'debug-bar' );
		// Add the 'Debug Bar' node to the 'Account' menu for Super Admins
		if ( is_super_admin() || is_admin_bar_showing() || Debug_Bar::is_wp_login() ) {
			$wp_admin_bar->add_node( array(
				'parent'  => 'av-account',
				'title'   => 'Debug Bar',
				'meta'    => array( 'class' => 'fcc-debug-bar' ),
			));
		}
	}
}
add_action( 'wp_before_admin_bar_render', 'remove_debug_bar_link' );

/*--------------------------------------------------------------
# Add Global Nav Menu Items (Front End Only)
--------------------------------------------------------------*/

if ( ! is_admin() ) {
	add_action( 'admin_bar_menu', 'add_av_logo_admin_bar_link', 300 );
	add_action( 'admin_bar_menu', 'fcc_add_channels', 500 );
	add_action( 'admin_bar_menu', 'av_admin_bar_add_secondary_groups', 0 );
	add_action( 'admin_bar_menu', 'add_homepage_admin_bar_link', 400 );
}

/*--------------------------------------------------------------
# "AreaVoices" Logo Homepage Link
--------------------------------------------------------------*/

function add_av_logo_admin_bar_link( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'av-logo',
		'title' => '<span class="ab-icon"></span><span class="screen-reader-text">' . __( 'AreaVoices Homepage' ) . '</span>',
		'title' => __( '' ),
		'href'  => 'http://areavoices.com',
	) );
}

/*--------------------------------------------------------------
# CHANNELS
--------------------------------------------------------------*/

function fcc_add_channels( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'        => 'av-channels',
		'parent'    => 'top-secondary',
		'title'     => 'Channels',
		'href'      => false,
		'meta'      => array(
			'class'     => 'channels',
		),
	) );

	// Add child items
	$wp_admin_bar->add_node( array(
		'id' => 'areavoices',
		'parent' => 'av-channels',
		'title' => '',
		'href' => 'http://areavoices.com',
		'meta' => false,
	));

	$wp_admin_bar->add_node( array(
		'id' => 'northlandoutdoors',
		'parent' => 'av-channels',
		'title' => '',
		'href' => 'http://northlandoutdoors.com',
		'meta' => false,
	));

	$wp_admin_bar->add_node( array(
		'id' => 'sayanything',
		'parent' => 'av-channels',
		'title' => '',
		'href' => 'https://www.sayanythingblog.com',
		'meta' => false,
	));

	$wp_admin_bar->add_node( array(
		'id' => 'bisonmedia',
		'parent' => 'av-channels',
		'title' => '',
		'href' => 'http://bisonmedia.areavoices.com',
		'meta' => false,
	));
}

/*--------------------------------------------------------------
# Account
--------------------------------------------------------------*/

function av_admin_bar_add_secondary_groups( $wp_admin_bar ) {
	$wp_admin_bar->add_group( array(
		'id' => 'top-secondary',
		'meta' => array( 'class' => 'ab-top-secondary' ),
	));
}

function add_homepage_admin_bar_link( $wp_admin_bar ) {
	$user_id = get_current_user_id();

	if ( ! $user_id ) { // Show 'Log In'
		$wp_admin_bar->add_menu( array(
			'id'        => 'av-login',
			'parent'    => 'top-secondary',
			'title'     => 'Log In',
			'href'      => wp_login_url(),
			'meta'    => false,
		));
	} else { // Show 'Dashboard'
		$wp_admin_bar->add_menu( array(
			'id'      => 'av-account',
			'parent'  => 'top-secondary',
			'title'   => 'Account',
			'href'    => false,
			'meta'    => false,
		));
		$wp_admin_bar->add_node( array(
			'parent'  => 'av-account',
			'title'   => 'Dashboard',
			'href'    => admin_url(),
			'meta'    => false,
		));
		$wp_admin_bar->add_node( array(
			'parent'  => 'av-account',
			'title'   => 'Log Out',
			'href'    => wp_logout_url(),
			'meta'     => false,
		));
	}
}
