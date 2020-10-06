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
        // Add a rewrite rule for the driver page.
        lddfw_rewrite_rule();
        flush_rewrite_rules();
        // Set default settings options.
        add_option( 'lddfw_out_for_delivery_status', 'wc-out-for-delivery' );
        add_option( 'lddfw_delivered_status', 'wc-delivered' );
        add_option( 'lddfw_failed_attempt_status', 'wc-failed-delivery' );
        add_option( 'lddfw_processing_status', 'wc-processing' );
        add_option( 'lddfw_failed_delivery_reason_1', __( 'Refused by the recipient', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_2', __( 'Incorrect address', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_3', __( 'Failed delivery attempt', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_4', __( 'Item Lost', 'lddfw' ) );
        add_option( 'lddfw_failed_delivery_reason_5', __( 'Item damaged', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_1', __( 'Delivered to the customer', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_2', __( 'Front door', 'lddfw' ) );
        add_option( 'lddfw_delivery_dropoff_3', __( 'Neighbor', 'lddfw' ) );
    }

}