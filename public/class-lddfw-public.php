<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    LDDFW
 * @subpackage LDDFW/public
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LDDFW_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LDDFW_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in LDDFW_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The LDDFW_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}


	public function lddfw_page_template( $page_template ) {
		global $post;

		if ( $post->ID === intval( get_option( 'lddfw_delivery_drivers_page', '' ) ) ) {
			$page_template = WP_PLUGIN_DIR . '/' . LDDFW_FOLDER . '/index.php';
		}
		return $page_template;
	}

}
