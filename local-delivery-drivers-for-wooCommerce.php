<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    http://www.powerfulwp.com
 * @since   1.0.0
 * @package LDDFW
 *
 * @wordpress-plugin
 * Plugin Name:       Local Delivery Drivers for WooCommerce
 * Plugin URI:        https://powerfulwp.com/local-delivery-drivers-for-woocommerce-premium/
 * Description:       Improve the way you deliver, manage drivers, assign drivers to orders, and with premium version much more, send SMS and email notifications, routes planning, navigation & more!
 * Version:           1.5.0
 * Author:            powerfulwp
 * Author URI:        http://www.powerfulwp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lddfw
 * Domain Path:       /languages
 * WC requires at least: 3.0
 * WC tested up to: 4.8
 *
 *
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( !function_exists( 'lddfw_fs' ) ) {
    // Create a helper function for easy SDK access.
    function lddfw_fs()
    {
        global  $lddfw_fs ;
        
        if ( !isset( $lddfw_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $lddfw_fs = fs_dynamic_init( array(
                'id'              => '6995',
                'slug'            => 'local-delivery-drivers-for-woocommerce',
                'type'            => 'plugin',
                'public_key'      => 'pk_5ae065da4addc985fe67f63c46a51',
                'is_premium'      => false,
                'premium_suffix'  => 'Premium',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                'days'               => 14,
                'is_require_payment' => true,
            ),
                'has_affiliation' => 'selected',
                'menu'            => array(
                'slug'    => 'lddfw-dashboard',
                'support' => false,
            ),
                'is_live'         => true,
            ) );
        }
        
        return $lddfw_fs;
    }
    
    // Init Freemius.
    lddfw_fs();
    // Signal that SDK was initiated.
    do_action( 'lddfw_fs_loaded' );
}

$lddfw_plugin_basename = plugin_basename( __FILE__ );
$lddfw_plugin_basename_array = explode( '/', $lddfw_plugin_basename );
$lddfw_plugin_folder = $lddfw_plugin_basename_array[0];
$lddfw_delivery_drivers_page = get_option( 'lddfw_delivery_drivers_page', '' );

if ( !function_exists( 'lddfw_activate' ) ) {
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     */
    define( 'LDDFW_VERSION', '1.5.0' );
    /**
     * Define delivery driver page id.
     */
    define( 'LDDFW_PAGE_ID', $lddfw_delivery_drivers_page );
    /**
     * Define plugin folder name.
     */
    define( 'LDDFW_FOLDER', $lddfw_plugin_folder );
    /**
     * Define suppoerted plugins.
     */
    $lddfw_plugins = array();
    if ( is_plugin_active( 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php' ) ) {
        //Brazil checkout fields.
        $lddfw_plugins[] = "woocommerce-extra-checkout-fields-for-brazil";
    }
    if ( is_plugin_active( 'comunas-de-chile-para-woocommerce/woocoomerce-comunas.php' ) ) {
        //Chile states.
        $lddfw_plugins[] = "comunas-de-chile-para-woocommerce";
    }
    define( 'LDDFW_PLUGINS', $lddfw_plugins );
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-lddfw-activator.php
     */
    function lddfw_activate()
    {
        include_once plugin_dir_path( __FILE__ ) . 'includes/class-lddfw-activator.php';
        LDDFW_Activator::activate();
    }
    
    /**
     * Check for free version
     *
     * @since 1.1.2
     * @return boolean
     */
    function lddfw_is_free()
    {
        
        if ( lddfw_fs()->is__premium_only() && lddfw_fs()->can_use_premium_code() ) {
            return false;
        } else {
            return true;
        }
    
    }
    
    /**
     * Premium feature notice content.
     *
     * @since 1.3.0
     * @param string $html html.
     * @return html
     */
    function lddfw_premium_feature_notice_content( $html )
    {
        return '
		<div class="lddfw_premium-feature-content"><div class="lddfw_title premium_feature_title">
		 <h2>' . esc_html( __( "Premium Feature", "lddfw" ) ) . '</h2>
		 <p>' . esc_html( __( "You Discovered a Premium Feature!", "lddfw" ) ) . '</p>
		</div>
		 <p class="lddfw_content-subtitle">' . esc_html( __( "With premium version you will be able to:", "lddfw" ) ) . '</p>
		' . $html . '</div>';
    }
    
    /**
     * Premium feature notice.
     *
     * @since 1.1.2
     * @param string $button text.
     * @param string $html html.
     * @param string $class class.
     * @return html
     */
    function lddfw_premium_feature_notice( $button, $html, $class )
    {
        return '<div class="lddfw_premium-feature ' . $class . '">
			<button class="btn btn-secondary btn-sm">' . lddfw_premium_feature( '' ) . ' ' . $button . '</button>
			<div class="lddfw_lightbox" style="display:none">
				<div class="lddfw_lightbox_wrap">
					<div class="container">
						<a href="#" class="lddfw_lightbox_close">×</a>' . lddfw_premium_feature_notice_content( $html ) . '
					</div>
				</div>
			</div>
		</div>';
    }
    
    /**
     * Premium feature.
     *
     * @since 1.1.2
     * @param string $value text.
     * @return html
     */
    function lddfw_premium_feature( $value )
    {
        $result = $value;
        if ( lddfw_is_free() ) {
            $result = '<svg style="color:#ffc106" width=20 aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class=" lddfw_premium_iconsvg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"> <title>' . esc_attr( 'Premium Feature', 'lddfw' ) . '</title><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>';
        }
        return $result;
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-lddfw-deactivator.php
     */
    function lddfw_deactivate()
    {
        include_once plugin_dir_path( __FILE__ ) . 'includes/class-lddfw-deactivator.php';
        LDDFW_Deactivator::deactivate();
    }
    
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since 1.0.0
     */
    function lddfw_run()
    {
        $plugin = new LDDFW();
        $plugin->run();
    }
    
    /**
     * Get delivery driver page url.
     *
     * @since 1.0.0
     */
    function lddfw_drivers_page_url( $params )
    {
        $link = get_page_link( LDDFW_PAGE_ID );
        
        if ( '' !== $params ) {
            
            if ( strpos( $link, '?' ) !== false ) {
                $link = esc_url( $link ) . '&' . $params;
            } else {
                $link = esc_url( $link ) . '?' . $params;
            }
            
            $link .= '&rnd=' . rand();
        }
        
        return $link;
    }
    
    /**
     * Register_query_vars for delivery driver page.
     *
     * @since 1.0.0
     * @param array $vars query_vars array.
     * @return array
     */
    function lddfw_register_query_vars( $vars )
    {
        $vars[] = 'lddfw_screen';
        $vars[] = 'lddfw_orderid';
        $vars[] = 'lddfw_page';
        $vars[] = 'lddfw_dates';
        $vars[] = 'lddfw_reset_login';
        $vars[] = 'lddfw_reset_key';
        return $vars;
    }
    
    /**
     * Function that format the date for the plugin.
     *
     * @since 1.0.0
     * @param string $type part of the date.
     * @return statement
     */
    function lddfw_date_format( $type )
    {
        $date_format = get_option( 'date_format', '' );
        $time_format = get_option( 'time_format', '' );
        if ( 'date' === $type ) {
            
            if ( 'F j, Y' !== $date_format && 'Y-m-d' !== $date_format && 'm/d/Y' !== $date_format && 'd/m/Y' !== $date_format ) {
                return 'F j, Y';
            } else {
                return $date_format;
            }
        
        }
        if ( 'time' === $type ) {
            
            if ( 'g:i a' !== $time_format && 'g:i A' !== $time_format && 'H:i' !== $time_format ) {
                return 'g:i a';
            } else {
                return $time_format;
            }
        
        }
    }
    
    /**
     * Function that uninstall the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    function lddfw_fs_uninstall_cleanup()
    {
    }

}

register_activation_hook( __FILE__, 'lddfw_activate' );
register_deactivation_hook( __FILE__, 'lddfw_deactivate' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lddfw.php';
add_filter( 'query_vars', 'lddfw_register_query_vars' );
lddfw_run();