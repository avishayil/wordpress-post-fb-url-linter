<?php
/*
Plugin Name: Facebook URL Linter for Posts
Plugin URI: http://www.geektime.com
Description: This Plugin allows you to send any published or updated post to facebook scraper. This Plugin was developed by Geektime's dev team for internal purposes only, and was shared for your convenience. We do not provide any support services for this plugin.
Version: 0.5
Author: Avishay Bassa, Geektime
Author URI: http://www.geektime.com
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// define some constants for the plugin
define( 'FB_LINTER_VERSION', '0.5' );
define( 'FB_LINTER_RELEASE_DATE', date_i18n( 'F j, Y', '1420115770' ) );
define( 'FB_LINTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'FB_LINTER_URL', plugin_dir_url( __FILE__ ) );

// define the plugin class
if (!class_exists("fb_linter")) :

class fb_linter {
	var $settings, $options_page;

	// construct the plugin
	function __construct() {

		if (is_admin()) {
			// Load settings page
			if (!class_exists("FB_Linter_Settings"))
				require(FB_LINTER_DIR . 'fb-linter-settings.php');
			$this->settings = new FB_Linter_Settings();
		}

		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );
		if ($this->settings->options['fb_active'] == "on") {
			add_action( 'publish_post', array(&$this,'post_published_notification'), 10, 1);
			add_action( 'future_post',  array(&$this, 'on_post_scheduled'), 10, 2 );
			add_action( 'admin_notices', array(&$this, 'post_admin_notice'), 10);
		}

		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}

	/*
		Plugin activation code here.
	*/
	function _activate() {}

	/*
		Plugin deactivation code here.
	*/
	function _deactivate() {}


	/*
		Load language translation files (if any) for our plugin.
	*/
	function init() {
		load_plugin_textdomain( 'fb_linter', FB_LINTER_DIR . 'lang',
							   basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function admin_init() {
	}

	function admin_menu() {
	}

	// declare the function for post direct publish
	function post_published_notification($post_id) {
	    $title = get_the_title($post_id);
	    $permalink = get_permalink($post_id);

	    // call the facebook sdk and check if lint is successful
		include_once ("lib/facebooksdk.php");
		$result = url_linter($permalink, $this->settings->options['fb_id'], $this->settings->options['fb_secret']);
		if ($result) {
			//start the session for displaying success message on post edit screen
			session_start();
			$_SESSION[$permalink] = "linted";

			// send an email to the admins if notifications are active with the result
			if (($this->settings->options['notifications_active'] == "on") && ($this->settings->options['notifications_email'] != "")) {			
		    	$headers = '[Geektime] Your Post have been linted.' . "\r\n";
		    	wp_mail($this->settings->options['notifications_email'], '[Geektime] Your published post ' . $title . ' have been linted', 'The Post ' . $title . ' with the permalink ' . $permalink . ' was linted. <br/>Result: ' . $result . '<br/><a href="https://developers.facebook.com/tools/debug/og/object?q=' . $permalink . '"><strong>View Lint</strong></a>', $headers);
			}
		}
	}

	// build the cron on post schedule
	function on_post_scheduled( $ID, $post ) {
		$title = $post->post_title;
		if ($post->post_status == 'future') {
			wp_schedule_single_event(get_post_time('U', true , $post->ID) + 120, 'activate_cron', array($post->ID,$this->settings->options['fb_id'], $this->settings->options['fb_secret'], $this->settings->options['notifications_active'], $this->settings->options['notifications_email']));
		}
	}

	// display admin notice when post is published, if session started
	function post_admin_notice() {
		global $post;
	    $title = $post->post_title;
	    $permalink = get_permalink($post->ID);
		$screen = get_current_screen();
		if( ('post' == $screen->post_type) && ('edit' == $screen->parent_base) && (isset($_SESSION[$permalink])) && (is_admin()) ) {
			echo '<div class="error"><p>Congratulations! Your article <a href="'. $permalink . '">' . $title . '</a> has been url-linted.';
			$linturl = "https://developers.facebook.com/tools/debug/og/object?q=" . $permalink;
	    	echo ' <a target="_blank" href="'. $linturl .'">View Lint</a></p></div>';
	    	session_unset();
	    	session_destroy();
	    }
	}
} // end class
endif;

// Initialize the plugin object.
global $fb_linter;
if (class_exists("fb_linter") && !$fb_linter) {
    $fb_linter = new fb_linter();
}

// declare the function for scheduled post publish
function post_notification($post_id,$fbid,$fbpwd,$isnotify,$notifemail) {
    $title = get_the_title($post_id);
    $permalink = get_permalink($post_id);
    $result = file_get_contents(FB_LINTER_URL . "lib/facebooksdk.php?linturl=". $permalink . "&appid=" . $fbid . "&appsecret=" . $fbpwd);
    if (($isnotify == "on") && ($notifemail != "")) {
    	$headers = 'Message From Geektime' . "\r\n";
    	wp_mail($notifemail, '[Geektime] Your scheduled post ' . $title . ' have been linted', 'The scheduled Post ' . $title . ' with the permalink ' . $permalink . ' was linted. <br/>Result: ' . $result . '<br/><a href="https://developers.facebook.com/tools/debug/og/object?q=' . $permalink . '"><strong>View Lint</strong></a>', $headers);
    }
}
add_action( 'activate_cron', 'post_notification', 10, 5);

// Add settings link on plugins page
add_filter( "plugin_action_links", 'plugin_settings_link', 10, 4 );

function plugin_settings_link( $links, $file ) {
	$plugin_file = 'facebook-url-linter-for-posts/fb-linter.php';
	//make sure it is our plugin we are modifying
	if ( $file == $plugin_file ) {
		$settings_link = '<a href="' .
			admin_url( 'options-general.php?page=fb_linter' ) . '">' .
			__( 'Settings', 'content-split-pro' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
?>