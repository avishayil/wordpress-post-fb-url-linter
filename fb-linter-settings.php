<?php
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("FB_Linter_Settings")) :

class FB_Linter_Settings {

	public static $default_settings =
		array(
			  	'fb_id' => 'Enter your facebook app id here',
			  	'fb_secret' => 'Enter your facebook app id here',
			  	'fb_active' => '',
				);
	var $pagehook, $page_id, $settings_field, $options;


	function __construct() {
		$this->page_id = 'fb_linter';
		// This is the get_options slug used in the database to store our plugin option values.
		$this->settings_field = 'fb_linter_options';
		$this->options = get_option( $this->settings_field );

		add_action('admin_init', array($this,'admin_init'), 20 );
		add_action( 'admin_menu', array($this, 'admin_menu'), 20);
	}

	function admin_init() {
		register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_options') );
		add_option( $this->settings_field, FB_Linter_Settings::$default_settings );


		/*
			This is needed if we want WordPress to render our settings interface
			for us using -
			do_settings_sections

			It sets up different sections and the fields within each section.
		*/
		add_settings_section('fb_main', '',
			array($this, 'main_section_text'), 'fb_linter_settings_page');
		add_settings_field('fb_active', 'Active',
			array($this, 'render_fb_active'), 'fb_linter_settings_page', 'fb_main');
		add_settings_field('fb_id', 'Facebook App ID',
			array($this, 'render_fb_id'), 'fb_linter_settings_page', 'fb_main');
		add_settings_field('fb_secret', 'Facebook App Secret',
			array($this, 'render_fb_secret'), 'fb_linter_settings_page', 'fb_main');

		add_settings_section('notifications_main', '',
			array($this, 'notifications_section_text'), 'fb_linter_settings_page');
		add_settings_field('notifications_active', 'Active',
			array($this, 'render_notifications_active'), 'fb_linter_settings_page', 'notifications_main');
		add_settings_field('notifications_email', 'Email Address For Notifications',
			array($this, 'render_notifications_email'), 'fb_linter_settings_page', 'notifications_main');
	}

	function admin_menu() {
		if ( ! current_user_can('update_plugins') )
			return;

		// Add a submenu to the standard Settings panel
		$this->pagehook = $page =  add_options_page(
			__('Facebook URL Linter', 'fb_linter'), __('Facebook URL Linter', 'fb_linter'),
			'administrator', $this->page_id, array($this,'render') );

		// Executed on-load. Add all metaboxes.
		add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );

		// Include js, css, or header *only* for settings page
		add_action("admin_print_scripts-$page", array($this, 'js_includes'));
//		add_action("admin_print_styles-$page", array($this, 'css_includes'));
		add_action("admin_head-$page", array($this, 'admin_head') );
	}

	function admin_head() { ?>
		<style>
		.settings_page_fb_linter label { display:inline-block; width: 150px; }
		</style>

	<?php }


	function js_includes() {
		// Needed to allow metabox layout and close functionality.
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'fb-linter', FB_LINTER_URL . 'js/fb-linter.js', array( 'jquery' ));
	}

	/*
		Sanitize plugin settings array.
	*/
	function sanitize_theme_options($options) {
		$options['fb_active'] = stripcslashes($options['fb_active']);
		$options['fb_id'] = stripcslashes($options['fb_id']);
		$options['fb_secret'] = stripcslashes($options['fb_secret']);
		return $options;
	}


	/*
		Settings access functions.

	*/
	protected function get_field_name( $name ) {

		return sprintf( '%s[%s]', $this->settings_field, $name );

	}

	protected function get_field_id( $id ) {

		return sprintf( '%s[%s]', $this->settings_field, $id );

	}

	protected function get_field_value( $key ) {

		return $this->options[$key];

	}


	/*
		Render settings page.

	*/

	function render() {
		global $wp_meta_boxes;

		$title = __('Facebook URL Linter for Posts', 'fb_linter');
		?>
		<div class="wrap">
			<h2><?php echo esc_html( $title ); ?></h2>

			<form method="post" action="options.php">
				<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
				</p>

                <div class="metabox-holder">
                    <div class="postbox-container" style="width: 99%;">
                    <?php
						// Render metaboxes
                        settings_fields($this->settings_field);
                        do_meta_boxes( $this->pagehook, 'main', null );
                      	if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) )
 							do_meta_boxes( $this->pagehook, 'column2', null );
                    ?>
                    </div>
                </div>
                <span id="info1" style="color: #ff0000"></span><br/>
                <span id="info2" style="color: #ff0000"></span><br/>
                <span id="info3"></span><br/>
				<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>" />
				</p>
			</form>
		</div>

        <!-- Needed to allow metabox layout and close functionality. -->
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
	<?php }


	function metaboxes() {

		add_meta_box( 	'fb-linter-all',
						__( 'Facebook Settings', 'fb_linter' ),
						array( $this, 'do_settings_box' ), $this->pagehook, 'main' );

	}

	function do_settings_box() {
		do_settings_sections('fb_linter_settings_page');
	}

	/*
		WordPress settings rendering functions
	*/


	function main_section_text() {
		echo '<p>Enter your Facebook App ID & App Secret here:</p>';
	}

	function render_fb_active() {
		$checked = "";
		if ($this->options['fb_active'] == 'on' ){
			?>
			<input id="fb_active" type="checkbox" name="<?php echo $this->get_field_name( 'fb_active' ); ?>" checked />
			<?php
		} else {
			?>
			<input id="fb_active" type="checkbox" name="<?php echo $this->get_field_name( 'fb_active' ); ?>" />
			<?php
		}
	}

	function render_fb_id() {
		?>
        <input id="fb_id" style="width:50%;"  type="text" name="<?php echo $this->get_field_name( 'fb_id' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'fb_id' ) ); ?>" />
		<?php
	}

	function render_fb_secret() {
		?>
        <input id="fb_secret" style="width:50%;"  type="text" name="<?php echo $this->get_field_name( 'fb_secret' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'fb_secret' ) ); ?>" />
		<?php
	}

	function notifications_section_text() {
		echo '<p>Enter your email address for notifications about linted posts:</p>';
	}

	function render_notifications_active() {
		$checked = "";
		if ($this->options['notifications_active'] == 'on' ){
			?>
			<input id="notifications_active" type="checkbox" name="<?php echo $this->get_field_name( 'notifications_active' ); ?>" checked />
			<?php
		} else {
			?>
			<input id="notifications_active" type="checkbox" name="<?php echo $this->get_field_name( 'notifications_active' ); ?>" />
			<?php
		}
	}

	function render_notifications_email() {
		?>
        <input id="notifications_email" style="width:50%;"  type="text" name="<?php echo $this->get_field_name( 'notifications_email' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'notifications_email' ) ); ?>" />
		<?php
	}

} // end class
endif;
?>