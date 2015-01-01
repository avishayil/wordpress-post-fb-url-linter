<?php
/*
Plugin Name: Facebook URL Linter for Posts
Plugin URI: http://www.geektime.co.il
Description: This Plugin allows you to send any published or updated post to facebook scraper
Version: 0.2
Author: Avishay Bassa
Author URI: http://www.geektime.co.il
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'FB_LINTER_VERSION', '0.2' );
define( 'FB_LINTER_RELEASE_DATE', date_i18n( 'F j, Y', '1420115770' ) );
define( 'FB_LINTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'FB_LINTER_URL', plugin_dir_url( __FILE__ ) );


if (!class_exists("fb_linter")) :

class fb_linter {
	var $settings, $options_page;

	function __construct() {

		if (is_admin()) {
			// Load example settings page
			if (!class_exists("FB_Linter_Settings"))
				require(FB_LINTER_DIR . 'fb-linter-settings.php');
			$this->settings = new FB_Linter_Settings();
		}

		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );
		if ($this->settings->options['fb_active'] == "on") {
			add_action( 'publish_post', array($this,'post_published_notification'), 10, 2 );
			add_action( 'post_updated', array($this,'post_published_notification'), 10, 2 );
		}

		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}

	/*
		Enter our plugin activation code here.
	*/
	function _activate() {}

	/*
		Enter our plugin deactivation code here.
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

	function post_published_notification( $ID, $post ) {
	    $title = $post->post_title;
	    $permalink = get_permalink( $ID );

		include_once ("lib/facebooksdk.php");
		$result = url_linter($permalink, $this->settings->options['fb_id'], $this->settings->options['fb_secret']);

	    $notice = sprintf ('Congratulations, %s! Your article “%s” has been published and url-linted.' . "\n\n", $name, $title );
	    $notice .= sprintf( 'View Lint: %s', "https://developers.facebook.com/tools/debug/og/object?q=".urlencode($permalink));
	}

} // end class
endif;

// Initialize our plugin object.
global $fb_linter;
if (class_exists("fb_linter") && !$fb_linter) {
    $fb_linter = new fb_linter();
}
?>