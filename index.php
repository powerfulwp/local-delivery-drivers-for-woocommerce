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
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !class_exists( 'WooCommerce' ) ) {
    die( esc_html( __( 'Local delivery drivers for WooCommerce is a WooCommerce add-on, you must activate a WooCommerce on your site.', 'lddfw' ) ) );
}
/**
 * Get WordPress query_var.
 */
$lddfw_screen = ( '' !== get_query_var( 'lddfw_screen' ) ? get_query_var( 'lddfw_screen' ) : 'dashboard' );
$lddfw_order_id = get_query_var( 'lddfw_orderid' );
$lddfw_reset_key = get_query_var( 'lddfw_reset_key' );
$lddfw_page = get_query_var( 'lddfw_page' );
$lddfw_reset_login = get_query_var( 'lddfw_reset_login' );
$lddfw_dates = get_query_var( 'lddfw_dates' );
/**
 * Set global variables.
*/
$lddfw_driver = new LDDFW_Driver();
$lddfw_screens = new LDDFW_Screens();
$lddfw_content = '';
$lddfw_driver_id = '';
/**
 * Log out delivery driver.
*/
if ( 'logout' === $lddfw_screen ) {
    LDDFW_Login::lddfw_logout();
}
/**
 * Check if user is logged in.
*/

if ( !is_user_logged_in() ) {
    $lddfw_content = $lddfw_screens->lddfw_home();
} else {
    // Check if user is a delivery driver.
    $lddfw_user = wp_get_current_user();
    $lddfw_driver_id = $lddfw_user->ID;
    $lddfw_driver_account = get_user_meta( $lddfw_driver_id, 'lddfw_driver_account', true );
    
    if ( !in_array( 'driver', (array) $lddfw_user->roles, true ) || '1' !== $lddfw_driver_account ) {
        LDDFW_Login::lddfw_logout();
        // User is not a delivery driver.
        $lddfw_user_is_driver = 0;
        $lddfw_content = $lddfw_screens->lddfw_home();
    } else {
        // User is a delivery driver.
        // Set global variables.
        $lddfw_user_is_driver = 1;
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
                case get_option( 'lddfw_driver_assigned_status' ):
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
        if ( 'dashboard' === $lddfw_screen ) {
            $lddfw_content = $lddfw_screens->lddfw_dashboard_screen( $lddfw_driver_id );
        }
        if ( 'out_for_delivery' === $lddfw_screen ) {
            $lddfw_content = $lddfw_screens->lddfw_out_for_delivery_screen( $lddfw_driver_id );
        }
        if ( 'failed_delivery' === $lddfw_screen ) {
            $lddfw_content = $lddfw_screens->lddfw_failed_delivery_screen( $lddfw_driver_id );
        }
        if ( 'delivered' === $lddfw_screen ) {
            $lddfw_content = $lddfw_screens->lddfw_delivered_screen( $lddfw_driver_id );
        }
        if ( 'assign_to_driver' === $lddfw_screen ) {
            $lddfw_content = $lddfw_screens->lddfw_assign_to_driver_screen( $lddfw_driver_id );
        }
        if ( 'order' === $lddfw_screen && '' !== $lddfw_order_id ) {
            $lddfw_content = $lddfw_screens->lddfw_order_screen( $lddfw_driver_id );
        }
    }

}

/**
 * Register scripts and css files
 */
wp_register_script(
    'lddfw-jquery-validate',
    plugin_dir_url( __FILE__ ) . 'public/js/jquery.validate.min.js',
    array( 'jquery', 'jquery-ui-core' ),
    LDDFW_VERSION,
    true
);
wp_register_script(
    'lddfw-bootstrap',
    plugin_dir_url( __FILE__ ) . 'public/js/bootstrap.min.js',
    array(),
    LDDFW_VERSION,
    false
);
wp_register_script(
    'lddfw-public',
    plugin_dir_url( __FILE__ ) . 'public/js/lddfw-public.js',
    array(),
    LDDFW_VERSION,
    false
);
wp_register_style(
    'lddfw-bootstrap',
    plugin_dir_url( __FILE__ ) . 'public/css/bootstrap.min.css',
    array(),
    LDDFW_VERSION,
    'all'
);
wp_register_style(
    'lddfw-fonts',
    'https://fonts.googleapis.com/css?family=Open+Sans|Roboto&display=swap',
    array(),
    LDDFW_VERSION,
    'all'
);
wp_register_style(
    'lddfw-public',
    plugin_dir_url( __FILE__ ) . 'public/css/lddfw-public.css',
    array(),
    LDDFW_VERSION,
    'all'
);
?>
<!DOCTYPE html>
<html>
<head>
<?php 
echo  '<title>' . esc_js( __( 'Delivery Driver', 'lddfw' ) ) . '</title>' ;
?>

<meta name="robots" content="noindex" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" href="<?php 
echo  esc_url( plugin_dir_url( __FILE__ ) . 'public/images/favicon-32x32.png?ver=' . LDDFW_VERSION ) ;
?>" >
<?php 
wp_print_styles( [ 'lddfw-fonts', 'lddfw-bootstrap', 'lddfw-public' ] );
wp_print_scripts( [ 'lddfw-jquery-validate' ] );
echo  '<script>
	var lddfw_driver_id = "' . esc_js( $lddfw_driver_id ) . '";
	var lddfw_ajax_url = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";
	var lddfw_confirm_text = "' . esc_js( __( 'Are you sure?', 'lddfw' ) ) . '";
	var lddfw_nonce = "' . esc_js( wp_create_nonce( 'lddfw-nonce' ) ) . '";
	var lddfw_hour_text = "' . esc_js( __( 'hour', 'lddfw' ) ) . '";
	var lddfw_hours_text = "' . esc_js( __( 'hours', 'lddfw' ) ) . '";
	var lddfw_mins_text = "' . esc_js( __( 'mins', 'lddfw' ) ) . '";
	var lddfw_dates = "' . esc_js( $lddfw_dates ) . '";
</script>' ;
?>

</head>
<body>
	<div id="lddfw_page" ><?php 
echo  $lddfw_content ;
?></div>
<?php 
wp_print_scripts( [ 'lddfw-bootstrap', 'lddfw-public' ] );
?>
</body>
</html>