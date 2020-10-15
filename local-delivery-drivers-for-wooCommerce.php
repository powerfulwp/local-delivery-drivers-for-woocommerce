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
 * Description:       Delivery Drivers for Woocommerce.
 * Version:           1.0.4
 * Author:            powerfulwp
 * Author URI:        http://www.powerfulwp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lddfw
 * Domain Path:       /languages
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
                'id'             => '6995',
                'slug'           => 'local-delivery-drivers-for-woocommerce',
                'type'           => 'plugin',
                'public_key'     => 'pk_5ae065da4addc985fe67f63c46a51',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 14,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'    => 'lddfw-settings',
                'support' => false,
            ),
                'is_live'        => true,
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
    define( 'LDDFW_VERSION', '1.0.4' );
    /**
     * Define delivery driver page id.
     */
    define( 'LDDFW_PAGE_ID', $lddfw_delivery_drivers_page );
    /**
     * Define plugin folder name.
     */
    define( 'LDDFW_FOLDER', $lddfw_plugin_folder );
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