<?php
/*
Plugin Name: FCC Global Nav
Plugin URI: https://github.com/openfcci/fcc-global-nav
Description: AreaVoices network global navigation plugin.
Author: Forum Communications Company
Version: 0.16.04.15
Author URI: http://forumcomm.com/
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/*--------------------------------------------------------------
# Load CSS
--------------------------------------------------------------*/

function fcc_gn_register_plugin_styles() {
	wp_enqueue_style( 'fcc_wpadminbar_css', plugin_dir_url( __FILE__ ) . '/fcc-global-nav.css' );
}
add_action( 'wp_enqueue_scripts', 'fcc_gn_register_plugin_styles' );


/*--------------------------------------------------------------
# Always Show Admin Bar
--------------------------------------------------------------*/

function fcc_always_show_admin_bar() {
	return true;
}
add_filter('show_admin_bar', 'fcc_always_show_admin_bar');
//add_filter('show_admin_bar', 'fcc_always_show_admin_bar', 1000 ); // Priority for Styles

function fcc_admin_bar_space() {
	if ( is_admin_bar_showing() ) {
		echo "
			<style type='text/css'>
			#nav-wrap {top: 32px !important;}
			</style>
		";
	}
}
add_action( 'wp_head', 'fcc_admin_bar_space' );


/*--------------------------------------------------------------
# Remove Default Menu Items
--------------------------------------------------------------*/

function remove_admin_bar_links() {
	if ( ! is_admin() ) {
    global $wp_admin_bar;

		/* Default Group: Left Side (wp-admin-bar-root-default) */
    $wp_admin_bar->remove_menu('wp-logo');          	// Remove the WordPress logo
		$wp_admin_bar->remove_menu('my-sites');
    $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
		$wp_admin_bar->remove_menu('customize');
		$wp_admin_bar->remove_menu('updates');          // Remove the updates link
		$wp_admin_bar->remove_menu('comments');         // Remove the comments link
		$wp_admin_bar->remove_menu('new-content');      // Remove the content link
		$wp_admin_bar->remove_menu('stats');						// Remove Jetpack Stats

    /* Secondary Group: Right Side (wp-admin-bar-top-secondary)
			HTML Order:   Search, My Account, Debug Bar
			Screen Order: Debug, My Account, Search */
		$wp_admin_bar->remove_menu('search');
    $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab

	}
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

/*--------------------------------------------------------------
# "AreaVoices" Logo Homepage Link
--------------------------------------------------------------*/

add_action( 'admin_bar_menu', 'add_av_logo_admin_bar_link', 1 );
	function add_av_logo_admin_bar_link( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'av-logo',
		//'title' => '<span class="ab-icon"></span><span class="screen-reader-text">' . __( 'AreaVoices Homepage' ) . '</span>',
		'title' => __( 'AreaVoices' ),
		'href'  => '//areavoices.com',
	) );
}

/*--------------------------------------------------------------
# CHANNELS
--------------------------------------------------------------*/

add_action( 'admin_bar_menu', 'fcc_add_channels', 500 );
function fcc_add_channels( $wp_admin_bar ) {

	$wp_admin_bar->add_menu( array(
		'id'        => 'av-channels',
		'parent'    => 'top-secondary',
		'title'     => 'Channels',
		'href'      => FALSE,
		'meta'      => array(
			'class'     => 'channels',
		),
	) );

  /*$args = array(
    'id' => 'channels',
    'title' => __( 'Channels' ),
    'href' => false);
  $wp_admin_bar->add_node($args);*/

// Add child items
$wp_admin_bar->add_node( array(
    'parent' => 'av-channels',
    'title' => 'AreaVoices',
    'href' => '//areavoices.com',
    'meta' => FALSE) );
$wp_admin_bar->add_node( array(
    'parent' => 'av-channels',
    'title' => 'Northland Outdoors',
    'href' => '//northlandoutdoors.com',
    'meta' => FALSE) );
$wp_admin_bar->add_node( array(
    'parent' => 'av-channels',
    'title' => 'Say Anything',
    'href' => '//sayanythingblog.com',
    'meta' => FALSE) );
$wp_admin_bar->add_node( array(
    'parent' => 'av-channels',
    'title' => 'Bison Media',
    'href' => '//bisonmedia.areavoices.com',
    'meta' => FALSE) );
}

/*--------------------------------------------------------------
# Account
--------------------------------------------------------------*/
add_action( 'admin_bar_menu', 'av_admin_bar_add_secondary_groups', 0 );
function av_admin_bar_add_secondary_groups( $wp_admin_bar ) {
	$wp_admin_bar->add_group( array(
		'id'     => 'top-secondary',
		'meta'   => array(
			'class' => 'ab-top-secondary',
		),
	) );
}

add_action( 'admin_bar_menu', 'add_homepage_admin_bar_link', 400 );
function add_homepage_admin_bar_link( $wp_admin_bar ) {
	$user_id = get_current_user_id();

	if ( ! $user_id ) {
		// Show 'Log In'
		$wp_admin_bar->add_menu( array(
			'id'        => 'av-login',
			'parent'    => 'top-secondary',
			'title'     => 'Log In',
			'href'      => wp_login_url(),
			'meta'      => array(
				'class'     => '$class',
			),
		) );


		/*$args = array(
	    'id' => 'av-login',
	    'title' => __( 'Log In' ),
	    'href' => wp_login_url()
	  );
	  $wp_admin_bar->add_node($args);*/
	} else {
		// Show 'Dashboard'
		$wp_admin_bar->add_menu( array(
			'id'        => 'av-account',
			'parent'    => 'top-secondary',
			'title'     => 'Account',
			'href'      => FALSE,
			'meta'      => array(
				'class'     => '$class',
			),
		) );

		/*$args = array(
	    'id' => 'av-account',
	    'title' => __( 'Account' ),
	    'href' => FALSE
	  );
	  $wp_admin_bar->add_node($args);*/

		$wp_admin_bar->add_node( array(
		    'parent' => 'av-account',
		    'title' => 'Dashboard',
		    'href' => admin_url(),
		    'meta' => FALSE) );
		$wp_admin_bar->add_node( array(
		    'parent' => 'av-account',
		    'title' => 'Log Out',
		    'href' => wp_logout_url(),
		    'meta' => FALSE) );
	}
}
