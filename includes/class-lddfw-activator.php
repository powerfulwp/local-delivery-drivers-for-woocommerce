<?php

/**
 * Fired during plugin activation
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Activator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        // Create a delivery driver role.
        add_role( 'driver', 'Delivery driver', array(
            'read'         => true,
            'edit_posts'   => false,
            'delete_posts' => false,
        ) );
        // Activate exiting drivers account that added before version 1.1.0
        $drivers = LDDFW_Driver::lddfw_get_drivers();
        
        if ( !empty($drivers) ) {
            $driver_id = $driver->ID;
            $lddfw_driver_account = get_user_meta( $driver_id, 'lddfw_driver_account', true );
            if ( '' === $lddfw_driver_account ) {
                update_user_meta( $driver_id, 'lddfw_driver_account', '1' );
            }
        }
        
        // Create delivery page for the first activation.
        $lddfw_delivery_drivers_page = get_option( 'lddfw_delivery_drivers_page', '' );
        
        if ( '' === $lddfw_delivery_drivers_page ) {
            $array = array(
                'post_title'     => 'Delivery Drivers',
                'post_type'      => 'page',
                'post_name'      => 'lddfw',
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            );
            $page_id = wp_insert_post( $array );
            
            if ( !get_option( 'lddfw_delivery_drivers_page' ) ) {
                add_option( 'lddfw_delivery_drivers_page', $page_id );
            } else {
                update_option( 'lddfw_delivery_drivers_page', $page_id );
            }
        
        }
        
        // Set default settings options.
        add_option( 'lddfw_out_for_delivery_status', 'wc-out-for-delivery' );
        add_option( 'lddfw_delivered_status', 'wc-completed' );
        add_option( 'lddfw_failed_attempt_status', 'wc-failed-delivery' );
        add_option( 'lddfw_driver_assigned_status', 'wc-driver-assigned' );
        add_option( 'lddfw_processing_status', 'wc-processing' );
        add_option( 'lddfw_sms_assign_to_driver_template', 'Hello [delivery_driver_first_name], order #[order_id] with [store_name] has been assigned to you. [delivery_driver_page]' );
        add_option( 'lddfw_sms_out_for_delivery_template', 'Hello [billing_first_name], status of your order #[order_id] with [store_name] has been changed to [order_status].' );
        add_option( 'lddfw_failed_delivery_reason_1', __( 'Refused by the recipient.', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_2', __( 'Incorrect address.', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_3', __( 'Failed delivery attempt.', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_4', __( 'Item Lost.', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_5', __( 'Item damaged.', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_1', __( 'Delivered to the customer.', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_2', __( 'Left at the front door.', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_3', __( 'Left with the neighbor.', 'lddfw' ) );
    }

}