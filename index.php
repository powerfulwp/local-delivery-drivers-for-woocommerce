<?php

/**
 * The index file of the plugin.
 *
 * This file shows all driver screen.
 *
 * @link    http://www.powerfulwp.com
 * @since   1.0.0
 * @package LDDFW
 *
 * Description:       Woocommerce Delivery Drivers.
 * Version:           1.0.0
 */
/**
 * Function that get the path of wp-load.php.
 *
 * @since 1.0.0
 * @return string
 */
function lddfw_wp_load_path()
{
    $base = dirname( __FILE__ );
    $path = false;
    if ( @file_exists( dirname( dirname( $base ) ) . '/wp-load.php' ) ) {
        $path = dirname( dirname( $base ) ) . '/wp-load.php';
    }
    if ( @file_exists( dirname( dirname( dirname( $base ) ) ) . '/wp-load.php' ) ) {
        $path = dirname( dirname( dirname( $base ) ) ) . '/wp-load.php';
    }
    if ( false !== $path ) {
        $path = str_replace( '\\', '/', $path );
    }
    return $path;
}

/**
 * Load wp-load file.
*/
require_once lddfw_wp_load_path();
/**
 * Set driver page variable.
*/
$lddfw_driver_page = '1';
/**
 * Load WordPress query_var.
*/
wp();
/**
 * Set header status.
*/
status_header( 200 );
/**
 * Force HTTPS and redirect to www.
*/
lddfw_force_https();
/**
 * Set page title.
 *
 * @since 1.0.0
 * @return string
 */
function lddfw_change_page_title()
{
    return esc_html( __( 'Delivery Drivers Manager', 'lddfw' ) );
}

add_filter( 'pre_get_document_title', 'lddfw_change_page_title' );
/**
 * Remove version from head.
 *
 * @since 1.0.0
 * @return string
 */
function lddfw_remove_version()
{
    return '';
}

add_filter( 'the_generator', 'lddfw_remove_version' );
/**
 * Clean head.
 */
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action(
    'template_redirect',
    'rest_output_link_header',
    11,
    0
);
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
/**
 * Remove unnecessary scripts.
 *
 * @since 1.0.0
 * @return void
 */
function lddfw_filter_scripts()
{
    global  $wp_scripts ;
    foreach ( $wp_scripts->queue as $handle ) {
        if ( 'lddfw-bootstrap' !== $handle && 'lddfw-jquery-validate' !== $handle && 'lddfw' !== $handle && 'jquery-validation-plugin' !== $handle && 'jquery' !== $handle ) {
            // Deregister scripts.
            wp_deregister_script( $handle );
        }
    }
}

add_action( 'wp_print_scripts', 'lddfw_filter_scripts', 100 );
add_action( 'wp_print_footer_scripts', 'lddfw_filter_scripts', 100 );
/**
 * Remove unnecessary styles.
 *
 * @since 1.0.0
 * @return void
 */
function lddfw_filter_styles()
{
    global  $wp_styles ;
    foreach ( $wp_styles->queue as $handle ) {
        if ( 'lddfw' !== $handle && 'lddfw-bootstrap' !== $handle && 'lddfw-bootstrap' !== $handle && 'lddfw-fontawesome' !== $handle && 'lddfw-fonts' !== $handle ) {
            // Deregister style.
            wp_deregister_style( $handle );
        }
    }
}

add_action( 'wp_print_styles', 'lddfw_filter_styles', 100 );
/**
 * Remove emoji.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
/**
 * Get WordPress query_var.
 */
$lddfw_action = ( '' !== get_query_var( 'lddfw_action' ) ? get_query_var( 'lddfw_action' ) : 'dashboard' );
$lddfw_order_id = get_query_var( 'lddfw_orderid' );
$lddfw_service = get_query_var( 'lddfw_orderid' );
$lddfw_reset_key = get_query_var( 'lddfw_orderid' );
$lddfw_page = get_query_var( 'lddfw_page' );
$lddfw_reset_login = get_query_var( 'lddfw_page' );
/**
 * Set global variables.
*/
$lddfw_driver = new LDDFW_Driver();
$lddfw_screen = new LDDFW_Screens();
$lddfw_content = '';
$lddfw_driver_id = '';
/**
 * Log out delivery driver.
*/
if ( 'logout' === $lddfw_action ) {
    LDDFW_Login::lddfw_logout();
}
/**
 * Check if user is logged in.
*/

if ( !is_user_logged_in() ) {
    $lddfw_content = $lddfw_screen->lddfw_home();
} else {
    // Check if user is a delivery driver.
    $lddfw_user = wp_get_current_user();
    
    if ( !in_array( 'driver', (array) $lddfw_user->roles, true ) ) {
        // User is not a delivery driver.
        $lddfw_user_is_driver = 0;
        $lddfw_content = $lddfw_screen->lddfw_home();
    } else {
        // User is a delivery driver.
        // Set global variables.
        $lddfw_user_is_driver = 1;
        $lddfw_driver_id = $lddfw_user->ID;
        $lddfw_driver_name = $lddfw_user->first_name . ' ' . $lddfw_user->last_name;
        $lddfw_driver_availability = get_user_meta( $lddfw_driver_id, 'lddfw_driver_availability', true );
        // Get the number of orders in each status.
        $lddfw_orders = new LDDFW_Orders();
        $lddfw_array = $lddfw_orders->lddfw_orders_count_query( $lddfw_driver_id );
        $lddfw_out_for_delivery_counter = 0;
        $lddfw_failed_attempt_counter = 0;
        $lddfw_delivered_counter = 0;
        $lddfw_assign_to_driver_counter = 0;
        $lddfw_claim_orders_counter = 0;
        foreach ( $lddfw_array as $row ) {
            switch ( $row->post_status ) {
                case get_option( 'lddfw_out_for_delivery_status' ):
                    $lddfw_out_for_delivery_counter = $row->orders;
                    break;
                case get_option( 'lddfw_failed_attempt_status' ):
                    $lddfw_failed_attempt_counter = $row->orders;
                    break;
                case get_option( 'lddfw_delivered_status' ):
                    $lddfw_delivered_counter = $row->orders;
                    break;
                case get_option( 'lddfw_processing_status' ):
                    $lddfw_assign_to_driver_counter = $row->orders;
                    break;
            }
        }
        // Claim orders query.
        $lddfw_array = $lddfw_orders->lddfw_claim_orders_count_query();
        if ( !empty($lddfw_array) ) {
            $lddfw_claim_orders_counter = $lddfw_array[0]->orders;
        }
        /**
         * Drivers screens.
         */
        if ( 'dashboard' === $lddfw_action ) {
            $lddfw_content = $lddfw_screen->lddfw_dashboard_screen( $lddfw_driver_id );
        }
        if ( 'out_for_delivery' === $lddfw_action ) {
            $lddfw_content = $lddfw_screen->lddfw_out_for_delivery_screen( $lddfw_driver_id );
        }
        if ( 'failed_delivery' === $lddfw_action ) {
            $lddfw_content = $lddfw_screen->lddfw_failed_delivery_screen( $lddfw_driver_id );
        }
        if ( 'delivered' === $lddfw_action ) {
            $lddfw_content = $lddfw_screen->lddfw_delivered_screen( $lddfw_driver_id );
        }
        if ( 'assign_to_driver' === $lddfw_action ) {
            $lddfw_content = $lddfw_screen->lddfw_assign_to_driver_screen( $lddfw_driver_id );
        }
        if ( 'order' === $lddfw_action && '' !== $lddfw_order_id ) {
            $lddfw_content = $lddfw_screen->lddfw_order_screen( $lddfw_driver_id );
        }
    }

}

?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" href="<?php 
echo  esc_url( plugins_url() . '/' . LDDFW_FOLDER . '/public/images/favicon-32x32.png?v=1.1' ) ;
?>" >
<?php 
/**
 * Load WordPress head.
 */
wp_head();
?>
</head>
<body>
	<div id = 'lddfw_page' >
		<?php 
echo  $lddfw_content ;
?>
	</div>
	<script>
		var lddfw_driver_id    = "<?php 
echo  esc_js( $lddfw_driver_id ) ;
?>";
		var lddfw_ajax_url 	   = "<?php 
echo  esc_url( admin_url( 'admin-ajax.php' ) ) ;
?>";
		var lddfw_confirm_text = "<?php 
echo  esc_js( __( 'Are you sure?', 'lddfw' ) ) ;
?>";
		var lddfw_nonce 	   = "<?php 
echo  esc_js( wp_create_nonce( 'lddfw-nonce' ) ) ;
?>";
		var lddfw_hour_text    = "<?php 
echo  esc_js( __( 'hour', 'lddfw' ) ) ;
?>";
		var lddfw_hours_text   = "<?php 
echo  esc_js( __( 'hours', 'lddfw' ) ) ;
?>";
		var lddfw_mins_text    = "<?php 
echo  esc_js( __( 'mins', 'lddfw' ) ) ;
?>";
	</script>
<?php 
wp_footer();
?>
</body>
</html>
