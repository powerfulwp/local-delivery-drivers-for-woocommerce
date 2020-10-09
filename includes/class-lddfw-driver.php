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
class LDDFW_Driver
{
    /**
     * Drivers query
     *
     * @since 1.0.0
     * @return array
     */
    public static function lddfw_get_drivers()
    {
        $args = array(
            'role'           => 'driver',
            'meta_query'     => array(
            'relation' => 'OR',
            array(
            'key'     => 'lddfw_driver_availability',
            'compare' => 'NOT EXISTS',
            'value'   => '',
        ),
            array(
            'key'     => 'lddfw_driver_availability',
            'compare' => 'EXISTS',
        ),
        ),
            'orderby'        => 'meta_value ASC,display_name ASC',
            'posts_per_page' => -1,
        );
        return get_users( $args );
    }
    
    /**
     *  Assign delivery order
     *
     * @param int    $order_id The order ID.
     * @param int    $driver_id The driver ID.
     * @param string $operator The type.
     * @return void
     */
    public static function assign_delivery_driver( $order_id, $driver_id, $operator )
    {
        $order = wc_get_order( $order_id );
        $order_driverid = get_post_meta( $order_id, 'lddfw_driverid', true );
        
        if ( $driver_id !== $order_driverid && '-1' !== $driver_id && '' !== $driver_id ) {
            $driver = get_userdata( $driver_id );
            $driver_name = $driver->first_name . ' ' . $driver->last_name;
            /* translators: %s: driver name */
            $note = sprintf( __( 'Delivery driver %s has been assigned to order', 'lddfw' ), $driver_name );
            update_post_meta( $order_id, 'lddfw_driverid', $driver_id );
            $order->add_order_note( $note );
        }
    
    }
    
    /**
     * Auto assign delivery orders
     *
     * @param int $order_id The order id.
     * @return void
     */
    public function auto_assign_delivery_drivers( $order_id )
    {
        global  $wpdb ;
        $array = $wpdb->get_results( $wpdb->prepare( ' select mt.user_id from ' . $wpdb->prefix . 'users u
				inner join ' . $wpdb->prefix . 'usermeta mt on mt.user_id = u.id and mt.meta_key = \'wp_capabilities\'
				inner join ' . $wpdb->prefix . 'usermeta mt1 on mt1.user_id = u.id and mt1.meta_key = \'lddfw_driver_availability\' 
				left join (
					select mt.meta_value as driver_id ,wp_posts.ID as orders 
					from ' . $wpdb->prefix . 'posts
					inner join ' . $wpdb->prefix . 'postmeta mt on mt.post_id = wp_posts.ID
					where  post_type = \'shop_order\' and
					post_status in (%s,%s,%s)
					and mt.meta_key = \'lddfw_driverid\'
					and mt.meta_value <> \'\' and mt.meta_value <> \'-1\'
				) t on t.driver_id = mt.user_id
				where mt.meta_value like %s and mt1.meta_value = \'1\'
				group by mt.user_id
				order by count(t.orders)
				limit 1 ', array(
            get_option( 'lddfw_processing_status', '' ),
            get_option( 'lddfw_out_for_delivery_status', '' ),
            get_option( 'lddfw_failed_attempt_status', '' ),
            '%\\"driver\\"%'
        ) ) );
        // db call ok; no-cache ok.
        
        if ( !empty($array) ) {
            $driver_id = $array[0]->user_id;
            $this->assign_delivery_driver( $order_id, $driver_id, 'store' );
        }
    
    }

}