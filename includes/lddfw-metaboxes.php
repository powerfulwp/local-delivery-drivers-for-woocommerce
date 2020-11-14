<?php

/**
 * Admin panel metaboxes
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
/**
 * Admin panel metaboxes
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
function lddfw_metaboxes()
{
    add_meta_box(
        'lddfw_metaboxes',
        __( 'Drivers', 'lddfw' ),
        'lddfw_metaboxes_build',
        'shop_order',
        'side',
        'default'
    );
}

add_action( 'add_meta_boxes', 'lddfw_metaboxes' );
/**
 * Building the metabox
 */
function lddfw_metaboxes_build()
{
    global  $post ;
    echo  '<input type="hidden" name="lddfw_metaboxes_key" id="lddfw_metaboxes_key" value="' . esc_attr( wp_create_nonce( "lddfw-save-order" ) ) . '" />' ;
    $lddfw_driverid = get_post_meta( $post->ID, 'lddfw_driverid', true );
    echo  '<div class="lddfw-driver-box">
	<label>' . esc_html( __( 'Delivery Driver', 'lddfw' ) ) . '</label>' ;
    $drivers = LDDFW_Driver::lddfw_get_drivers();
    echo  esc_html( lddfw_driver_drivers_selectbox(
        $drivers,
        $lddfw_driverid,
        $post->ID,
        ''
    ) ) ;
    echo  '</div> ' ;
}

/**
 * Drivers selectbox
 *
 * @param object $drivers drivers object.
 * @param int    $driver_id user id number.
 * @param int    $order_id order number.
 * @param string $type type.
 * @return void
 */
function lddfw_driver_drivers_selectbox(
    $drivers,
    $driver_id,
    $order_id,
    $type
)
{
    
    if ( 'bulk' === $type ) {
        echo  "<select name='lddfw_driverid_" . esc_attr( $order_id ) . "' id='lddfw_driverid_" . esc_attr( $order_id ) . "'>" ;
    } else {
        echo  "<select name='lddfw_driverid' id='lddfw_driverid_" . esc_attr( $order_id ) . "' order='" . esc_attr( $order_id ) . "' class='widefat'>" ;
    }
    
    echo  "<option value=''>" . esc_html( __( 'Assign a driver', 'lddfw' ) ) . '</option>
    ' ;
    $last_availability = '';
    foreach ( $drivers as $driver ) {
        $driver_name = $driver->display_name;
        $availability = get_user_meta( $driver->ID, 'lddfw_driver_availability', true );
        $driver_account = get_user_meta( $driver->ID, 'lddfw_driver_account', true );
        $availability = ( '1' === $availability ? 'Available' : 'Unavailable' );
        $selected = '';
        if ( intval( $driver_id ) === $driver->ID ) {
            $selected = 'selected';
        }
        
        if ( $last_availability !== $availability ) {
            if ( '' !== $last_availability ) {
                echo  '</optgroup>' ;
            }
            echo  '<optgroup label="' . esc_attr( $availability . ' ' . __( 'drivers', 'lddfw' ) ) . '">' ;
            $last_availability = $availability;
        }
        
        if ( '1' === $driver_account || '1' != $driver_account && intval( $driver_id ) === $driver->ID ) {
            echo  '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $driver->ID ) . '">' . esc_html( $driver_name ) . '</option>' ;
        }
    }
    echo  '</optgroup></select>' ;
}

/**
 * Save the Metabox Data
 *
 * @param int    $post_id post number.
 * @param object $post post object.
 */
function lddfw_driver_save_order_details( $post_id, $post )
{
    $driver = new LDDFW_Driver();
    if ( !isset( $_POST['lddfw_metaboxes_key'] ) || !wp_verify_nonce( $_POST['lddfw_metaboxes_key'], 'lddfw-save-order' ) ) {
        return $post->ID;
    }
    $lddfw_driver_order_meta['lddfw_driverid'] = $_POST['lddfw_driverid'];
    foreach ( $lddfw_driver_order_meta as $key => $value ) {
        /**
         * Cycle through the $thccbd_meta array!
         */
        if ( 'revision' === $post->post_type ) {
            /**
             * Don't store custom data twice
             */
            return;
        }
        $value = implode( ',', (array) $value );
        $driver->assign_delivery_driver( $post->ID, $value, 'store' );
        if ( !$value ) {
            /**
             * Delete if blank
             */
            delete_post_meta( $post->ID, $key );
        }
    }
}

add_action(
    'save_post',
    'lddfw_driver_save_order_details',
    10,
    2
);
// Save the custom fields.