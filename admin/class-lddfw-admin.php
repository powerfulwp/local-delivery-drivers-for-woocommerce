<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  http://www.powerfulwp.com
 * @since 1.0.0
 *
 * @package    LDDFW
 * @subpackage LDDFW/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    LDDFW
 * @subpackage LDDFW/admin
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/lddfw-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/lddfw-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'lddfw_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
    }
    
    /**
     * Service that update order status to out for delivery.
     *
     * @since 1.0.0
     * @param int $driver_id ID of the user.
     * @return json
     */
    public function lddfw_out_for_delivery_service( $driver_id )
    {
        $result = 0;
        $user = get_user_by( 'id', $driver_id );
        
        if ( in_array( 'driver', (array) $user->roles, true ) ) {
            // Security check.
            
            if ( isset( $_POST['lddfw_wpnonce'] ) ) {
                $nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
                
                if ( !wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
                    $error = __( 'Security Check Failure', 'lddfw' );
                } else {
                    // Get list of orders.
                    $orders_list = ( isset( $_POST['lddfw_orders_list'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_orders_list'] ) ) : '' );
                    
                    if ( '' !== $orders_list ) {
                        $orders_list_array = explode( ',', $orders_list );
                        foreach ( $orders_list_array as $order_id ) {
                            
                            if ( '' !== $order_id ) {
                                $order = new WC_Order( $order_id );
                                $order_driverid = $order->get_meta( 'lddfw_driverid' );
                                $out_for_delivery_status = get_option( 'lddfw_out_for_delivery_status', '' );
                                $processing_status = get_option( 'lddfw_processing_status', '' );
                                $current_order_status = 'wc-' . $order->get_status();
                                // Check if order belongs to driver and status is processing.
                                
                                if ( intval( $order_driverid ) === intval( $driver_id ) && $current_order_status === $processing_status ) {
                                    // Update order status.
                                    $order->update_status( $out_for_delivery_status, __( 'The delivery driver changed the order status.', 'lddfw' ) );
                                    $order->save();
                                    $result = 1;
                                    $error = "<div class='alert alert-success alert-dismissible fade show'>" . __( 'Orders successfully marked as out of delivery.', 'lddfw' ) . "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div> <a id='view_out_of_delivery_orders_button' href='" . lddfw_drivers_page_url() . "lddfw_action=out_for_delivery'  class='btn btn-lg btn-block btn-primary'>" . __( 'View out of delivery orders', 'lddfw' ) . "</a>";
                                }
                            
                            }
                        
                        }
                    } else {
                        $error = __( 'Please choose the orders.', 'lddfw' );
                    }
                
                }
            
            }
        
        } else {
            $error = __( 'User is not a delivery driver', 'lddfw' );
        }
        
        return "{\"result\":\"{$result}\",\"error\":\"{$error}\"}";
    }
    
    /**
     * Service that assign order to delivery driver.
     *
     * @since 1.0.0
     * @param int $driver_id ID of the user.
     * @return json
     */
    public function lddfw_claim_orders_service( $driver_id )
    {
        $result = 0;
        $user = get_user_by( 'id', $driver_id );
        $driver = new LDDFW_Driver();
        
        if ( in_array( 'driver', (array) $user->roles, true ) ) {
            // Security check.
            
            if ( isset( $_POST['lddfw_wpnonce'] ) ) {
                $nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
                
                if ( !wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
                    $error = __( 'Security Check Failure', 'lddfw' );
                } else {
                    $orders_list = ( isset( $_POST['lddfw_orders_list'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_orders_list'] ) ) : '' );
                    
                    if ( '' !== $orders_list ) {
                        $orders_list_array = explode( ',', $orders_list );
                        foreach ( $orders_list_array as $order ) {
                            if ( '' !== $order ) {
                                // Assign order to driver.
                                $driver->assign_delivery_driver( $order, $driver_id, 'driver' );
                            }
                        }
                        $result = 1;
                        $error = __( 'Orders successfully assigned to you', 'lddfw' ) . ' <a id=\'view_assigned_orders_button\' href=\'' . lddfw_drivers_page_url() . 'lddfw_action=assign_to_driver\'  class=\'btn btn-block btn-primary\'>' . __( 'View assigned orders', 'lddfw' ) . '</a>';
                    } else {
                        $error = __( 'Please choose the orders.', 'lddfw' );
                    }
                
                }
            
            }
        
        } else {
            $error = __( 'User is not a driver', 'lddfw' );
        }
        
        return "{\"result\":\"{$result}\",\"error\":\"{$error}\"}";
    }
    
    /**
     * The function that handles ajax requests.
     *
     * @since 1.0.0
     * @return void
     */
    public function lddfw_ajax()
    {
        
        if ( isset( $_POST['lddfw_wpnonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
            
            if ( !wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
                echo  "<div class='alert alert-danger' >" . esc_html( __( 'Security Check Failure', 'lddfw' ) ) . '</div>' ;
                exit;
            }
        
        }
        
        $lddfw_obj_id = ( isset( $_POST['lddfw_obj_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_obj_id'] ) ) : '' );
        $lddfw_service = ( isset( $_POST['lddfw_service'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_service'] ) ) : '' );
        $lddfw_driver_id = ( isset( $_POST['lddfw_driver_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_id'] ) ) : '' );
        $result = 0;
        /* login driver service */
        
        if ( 'lddfw_login' === $lddfw_service ) {
            $login = new LDDFW_Login();
            echo  $login->lddfw_login_driver() ;
        }
        
        /* send reset password link */
        
        if ( 'lddfw_forgot_password' === $lddfw_service ) {
            $password = new LDDFW_Password();
            echo  $password->lddfw_reset_password() ;
        }
        
        /* Create a new password*/
        
        if ( 'lddfw_newpassword' === $lddfw_service ) {
            $password = new LDDFW_Password();
            echo  $password->lddfw_new_password() ;
        }
        
        /*
        Log out driver.
        */
        if ( 'lddfw_logout' === $lddfw_service ) {
            LDDFW_Login::lddfw_logout();
        }
        /*
        Set driver availability.
        */
        
        if ( 'lddfw_availability' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $driver_id = $user->ID;
                $availability = ( isset( $_POST['lddfw_availability'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_availability'] ) ) : '' );
                update_user_meta( $driver_id, 'lddfw_driver_availability', $availability );
                $result = 1;
            }
            
            echo  esc_html( $result ) ;
        }
        
        /* claim orders service */
        
        if ( 'lddfw_claim_orders' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $driver_id = $user->ID;
                echo  $this->lddfw_claim_orders_service( $driver_id ) ;
            }
        
        }
        
        /* out for delivery service */
        
        if ( 'lddfw_out_for_delivery' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $driver_id = $user->ID;
                echo  $this->lddfw_out_for_delivery_service( $driver_id ) ;
            }
        
        }
        
        /* Add comment to order */
        
        if ( 'lddfw_note' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $order_id = ( isset( $_POST['lddfw_order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_order_id'] ) ) : '' );
                $note = ( isset( $_POST['lddfw_note'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_note'] ) ) : '' );
                $driver_id = ( isset( $_POST['lddfw_driver_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_id'] ) ) : '' );
                /* check if the variables are not empty */
                
                if ( '' !== $order_id && '' !== $note && '' !== $driver_id ) {
                    $order = new WC_Order( $order_id );
                    $order_driverid = $order->get_meta( 'lddfw_driverid' );
                    $out_for_delivery_status = get_option( 'lddfw_out_for_delivery_status', '' );
                    $current_order_status = 'wc-' . $order->get_status();
                    /* Check if order belongs to driver */
                    
                    if ( intval( $order_driverid ) === intval( $driver_id ) ) {
                        // Add the note.
                        $note = __( 'Driver note', 'lddfw' ) . ': ' . $note;
                        $order->add_order_note( $note );
                        $result = 1;
                    }
                
                }
            
            }
            
            echo  esc_html( $result ) ;
        }
        
        
        if ( 'lddfw_status' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $order_id = ( isset( $_POST['lddfw_order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_order_id'] ) ) : '' );
                $order_status = ( isset( $_POST['lddfw_order_status'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_order_status'] ) ) : '' );
                $driver_id = ( isset( $_POST['lddfw_driver_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_id'] ) ) : '' );
                $note = ( isset( $_POST['lddfw_note'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_note'] ) ) : '' );
                /* Check if the variables are not empty */
                
                if ( '' !== $order_id && '' !== $order_status && '' !== $driver_id ) {
                    $order = new WC_Order( $order_id );
                    $order_driverid = $order->get_meta( 'lddfw_driverid' );
                    $out_for_delivery_status = get_option( 'lddfw_out_for_delivery_status', '' );
                    $failed_attempt_status = get_option( 'lddfw_failed_attempt_status', '' );
                    $current_order_status = 'wc-' . $order->get_status();
                    /* Check if order belongs to driver and status is out for delivery */
                    
                    if ( intval( $order_driverid ) === intval( $driver_id ) && ($current_order_status === $out_for_delivery_status || $current_order_status === $failed_attempt_status) ) {
                        /* Update order status */
                        $order->update_status( $order_status, __( 'Driver changed order status, ', 'lddfw' ) );
                        
                        if ( '' !== $note ) {
                            $note = __( 'Driver note', 'lddfw' ) . ': ' . $note;
                            $order->add_order_note( $note );
                        }
                        
                        $order->save();
                        $result = 1;
                    }
                
                }
            
            }
            
            echo  esc_html( $result ) ;
        }
        
        if ( 'lddfw_get_drivers_list' === $lddfw_service ) {
            echo  lddfw_driver_drivers_selectbox(
                LDDFW_Driver::lddfw_get_drivers(),
                '',
                $lddfw_obj_id,
                'bulk'
            ) ;
        }
        exit;
    }
    
    /**
     * Changed status hook.
     *
     * @since 1.0.0
     * @param int    $order_id order number.
     * @param string $status_from order status from.
     * @param string $status_to order status to.
     * @param object $order order object.
     * @return void
     */
    public function lddfw_status_changed(
        $order_id,
        $status_from,
        $status_to,
        $order
    )
    {
        if ( get_option( 'lddfw_processing_status', true ) === 'wc-' . $status_to ) {
        }
        if ( get_option( 'lddfw_out_for_delivery_status', '' ) === 'wc-' . $status_to ) {
            // Sent email template only to delivery guy status.
            
            if ( 'wc-out-for-delivery' === 'wc-' . $status_to ) {
                WC_Emails::instance();
                do_action( 'lddfw_out_for_delivery_email_notification', $order_id );
            }
        
        }
        // Update delivered data.
        if ( get_option( 'lddfw_delivered_status', '' ) === 'wc-' . $status_to ) {
            update_post_meta( $order_id, 'lddfw_delivered_date', gmdate( 'Y-m-d H:i:s' ) );
        }
        // Update failed attempt data.
        if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $status_to ) {
            update_post_meta( $order_id, 'lddfw_failed_attempt_date', gmdate( 'Y-m-d H:i:s' ) );
        }
        // Sent email template only to delivery guy plugin status (wc-delivered,wc-failed-delivery).
        
        if ( 'wc-delivered' === 'wc-' . $status_to ) {
            WC_Emails::instance();
            do_action( 'lddfw_delivered_email_notification', $order_id );
            do_action( 'lddfw_delivered_email_admin_notification', $order_id );
        }
        
        
        if ( 'wc-failed-delivery' === 'wc-' . $status_to ) {
            WC_Emails::instance();
            do_action( 'lddfw_failed_delivery_email_notification', $order_id );
        }
    
    }
    
    /**
     * Plugin status.
     *
     * @since 1.0.0
     * @param array $statuses_array status array.
     * @return array
     */
    public function lddfw_order_statuses( $statuses_array )
    {
        $lddfw_statuses = array();
        foreach ( $statuses_array as $key => $status ) {
            $lddfw_statuses[$key] = $status;
            
            if ( 'wc-processing' === $key ) {
                $lddfw_statuses['wc-out-for-delivery'] = __( 'Out for Delivery', 'lddfw' );
                $lddfw_statuses['wc-delivered'] = __( 'Delivered', 'lddfw' );
                $lddfw_statuses['wc-failed-delivery'] = __( 'Failed Delivery Attempt', 'lddfw' );
            }
        
        }
        return $lddfw_statuses;
    }
    
    /**
     * Register new post status.
     *
     * @since 1.0.0
     * @return void
     */
    public function lddfw_order_statuses_init()
    {
        register_post_status( 'wc-out-for-delivery', array(
            'label'                     => __( 'Out for Delivery', 'lddfw' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Out for Delivery <span class="count">(%s)</span>', 'Out for Delivery <span class="count">(%s)</span>', 'lddfw' ),
        ) );
        register_post_status( 'wc-delivered', array(
            'label'                     => __( 'Delivered', 'lddfw' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'lddfw' ),
        ) );
        register_post_status( 'wc-failed-delivery', array(
            'label'                     => __( 'Failed Delivery Attempt', 'lddfw' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Failed Delivery Attempt <span class="count">(%s)</span>', 'Failed Delivery Attempt <span class="count">(%s)</span>', 'lddfw' ),
        ) );
    }
    
    /**
     * Plugin register settings.
     *
     * @since 1.0.0
     * @return void
     */
    public function lddfw_settings_init()
    {
        register_setting( 'lddfw', 'lddfw_failed_delivery_reason_1' );
        register_setting( 'lddfw', 'lddfw_failed_delivery_reason_2' );
        register_setting( 'lddfw', 'lddfw_failed_delivery_reason_3' );
        register_setting( 'lddfw', 'lddfw_failed_delivery_reason_4' );
        register_setting( 'lddfw', 'lddfw_failed_delivery_reason_5' );
        register_setting( 'lddfw', 'lddfw_delivery_dropoff_1' );
        register_setting( 'lddfw', 'lddfw_delivery_dropoff_2' );
        register_setting( 'lddfw', 'lddfw_delivery_dropoff_3' );
        register_setting( 'lddfw', 'lddfw_google_api_key' );
        register_setting( 'lddfw', 'lddfw_dispatch_phone_number' );
        register_setting( 'lddfw', 'lddfw_status_section' );
        register_setting( 'lddfw', 'lddfw_out_for_delivery_status' );
        register_setting( 'lddfw', 'lddfw_delivered_status' );
        register_setting( 'lddfw', 'lddfw_failed_attempt_status' );
        register_setting( 'lddfw', 'lddfw_processing_status' );
        register_setting( 'lddfw', 'lddfw_delivery_drivers_page' );
        add_settings_section(
            'lddfw_setting_section',
            '',
            '',
            'lddfw'
        );
        add_settings_field(
            'lddfw_delivery_drivers_page',
            __( 'Delivery drivers page', 'lddfw' ),
            array( $this, 'lddfw_delivery_drivers_page' ),
            'lddfw',
            'lddfw_setting_section'
        );
        add_settings_field(
            'lddfw_google_api_key',
            __( 'Google API key', 'lddfw' ),
            array( $this, 'lddfw_google_api_key' ),
            'lddfw',
            'lddfw_setting_section'
        );
        add_settings_field(
            'lddfw_dispatch_phone_number',
            __( 'Dispatch phone number', 'lddfw' ),
            array( $this, 'lddfw_dispatch_phone_number' ),
            'lddfw',
            'lddfw_setting_section'
        );
        add_settings_section(
            'lddfw_status_section',
            __( 'Delivery statuses', 'lddfw' ),
            '',
            'lddfw'
        );
        add_settings_field(
            'lddfw_out_for_delivery_status',
            __( 'Out for delivery status', 'lddfw' ),
            array( $this, 'lddfw_out_for_delivery_status' ),
            'lddfw',
            'lddfw_status_section'
        );
        add_settings_field(
            'lddfw_delivered_status',
            __( 'Delivered status', 'lddfw' ),
            array( $this, 'lddfw_delivered_status' ),
            'lddfw',
            'lddfw_status_section'
        );
        add_settings_field(
            'lddfw_failed_attempt_status',
            __( 'Failed Delivery attempt status', 'lddfw' ),
            array( $this, 'lddfw_failed_attempt_status' ),
            'lddfw',
            'lddfw_status_section'
        );
        add_settings_field(
            'lddfw_processing_status',
            __( 'Order Processing status.', 'lddfw' ),
            array( $this, 'lddfw_processing_status' ),
            'lddfw',
            'lddfw_status_section'
        );
        add_settings_section(
            'lddfw_failed_delivery_reasons',
            __( 'Common Reasons For Failed Delivery', 'lddfw' ),
            array( $this, 'lddfw_failed_delivery_reasons_section' ),
            'lddfw'
        );
        add_settings_field(
            'lddfw_failed_delivery_reason_1',
            __( 'Common Reasons', 'lddfw' ),
            array( $this, 'lddfw_failed_delivery_reason_1' ),
            'lddfw',
            'lddfw_failed_delivery_reasons'
        );
        add_settings_section(
            'lddfw_delivery_dropoff',
            __( 'Drop off delivery locations', 'lddfw' ),
            array( $this, 'lddfw_delivery_dropoff_section' ),
            'lddfw'
        );
        add_settings_field(
            'lddfw_delivery_dropoff_1',
            __( 'Options', 'lddfw' ),
            array( $this, 'lddfw_delivery_dropoff_1' ),
            'lddfw',
            'lddfw_delivery_dropoff'
        );
    }
    
    /**
     * Plugin template tags.
     *
     * @since 1.0.0
     */
    public function lddfw_template_tags()
    {
        ?>
		<a href='#' data='[delivery_driver_first_name]'><?php 
        echo  esc_html( __( 'Delivery Driver First Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[delivery_driver_last_name]'><?php 
        echo  esc_html( __( 'Delivery Driver Last Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[delivery_driver_order_url]'><?php 
        echo  esc_html( __( 'Delivery Driver Order URL', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[store_name]'><?php 
        echo  esc_html( __( 'Store Name', 'lddfw' ) ) ;
        ?></a> |

		<a href='#' data='[order_id]'><?php 
        echo  esc_html( __( 'Order Id', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[order_create_date]'><?php 
        echo  esc_html( __( 'Order Create Date', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[order_status]'><?php 
        echo  esc_html( __( 'Order Status', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[order_amount]'><?php 
        echo  esc_html( __( 'Order Amount', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[order_currency]'><?php 
        echo  esc_html( __( 'Order Currency', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_method]'><?php 
        echo  esc_html( __( 'Shipping Method', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[payment_method]'><?php 
        echo  esc_html( __( 'Payment Method', 'lddfw' ) ) ;
        ?></a> |
		<br>
		<a href='#' data='[billing_first_name]'><?php 
        echo  esc_html( __( 'Billing First Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_last_name]'><?php 
        echo  esc_html( __( 'Billing Last Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_company]'><?php 
        echo  esc_html( __( 'Billing Company', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_address_1]'><?php 
        echo  esc_html( __( 'Billing Address 1', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_address_2]'><?php 
        echo  esc_html( __( 'Billing Address 2', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_city]'><?php 
        echo  esc_html( __( 'Billing City', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_state]'><?php 
        echo  esc_html( __( 'Billing State', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_postcode]'><?php 
        echo  esc_html( __( 'Billing Postcode', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_country]'><?php 
        echo  esc_html( __( 'Billing Country', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[billing_phone]'><?php 
        echo  esc_html( __( 'Billing Phone', 'lddfw' ) ) ;
        ?></a> |
		<br>
		<a href='#' data='[shipping_first_name]'><?php 
        echo  esc_html( __( 'Shipping First Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_last_name]'><?php 
        echo  esc_html( __( 'Shipping Last Name', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_company]'><?php 
        echo  esc_html( __( 'Shipping Company', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_address_1]'><?php 
        echo  esc_html( __( 'Shipping Address 1', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_address_2]'><?php 
        echo  esc_html( __( 'Shipping Address 2', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_city]'><?php 
        echo  esc_html( __( 'Shipping City', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_state]'><?php 
        echo  esc_html( __( 'Shipping State', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_postcode]'><?php 
        echo  esc_html( __( 'Shipping Postcode', 'lddfw' ) ) ;
        ?></a> |
		<a href='#' data='[shipping_country]'><?php 
        echo  esc_html( __( 'Shipping Country', 'lddfw' ) ) ;
        ?></a>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_failed_delivery_reasons_section()
    {
        echo  esc_html( __( 'These are driver choices when delivery fails, to delete an option leave blank.' ) ) ;
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_delivery_dropoff_section()
    {
        echo  esc_html( __( 'These are driver choices of drop off locations, to delete an option leave blank.' ) ) ;
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_failed_delivery_reason_1()
    {
        ?>
		<p> 1. <input type='text' class='regular-text' name='lddfw_failed_delivery_reason_1' value='<?php 
        echo  esc_attr( get_option( 'lddfw_failed_delivery_reason_1', '' ) ) ;
        ?>'></p>
		<p> 2. <input type='text' class='regular-text' name='lddfw_failed_delivery_reason_2' value='<?php 
        echo  esc_attr( get_option( 'lddfw_failed_delivery_reason_2', '' ) ) ;
        ?>'></p>
		<p> 3. <input type='text' class='regular-text' name='lddfw_failed_delivery_reason_3' value='<?php 
        echo  esc_attr( get_option( 'lddfw_failed_delivery_reason_3', '' ) ) ;
        ?>'></p>
		<p> 4. <input type='text' class='regular-text' name='lddfw_failed_delivery_reason_4' value='<?php 
        echo  esc_attr( get_option( 'lddfw_failed_delivery_reason_4', '' ) ) ;
        ?>'></p>
		<p> 5. <input type='text' class='regular-text' name='lddfw_failed_delivery_reason_5' value='<?php 
        echo  esc_attr( get_option( 'lddfw_failed_delivery_reason_5', '' ) ) ;
        ?>'></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_delivery_dropoff_1()
    {
        ?>
		<p>1. <input type='text' class='regular-text' name='lddfw_delivery_dropoff_1' value='<?php 
        echo  esc_attr( get_option( 'lddfw_delivery_dropoff_1', '' ) ) ;
        ?>'></p>
		<p>2. <input type='text' class='regular-text' name='lddfw_delivery_dropoff_2' value='<?php 
        echo  esc_attr( get_option( 'lddfw_delivery_dropoff_2', '' ) ) ;
        ?>'></p>
		<p>3. <input type='text' class='regular-text' name='lddfw_delivery_dropoff_3' value='<?php 
        echo  esc_attr( get_option( 'lddfw_delivery_dropoff_3', '' ) ) ;
        ?>'></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_dispatch_phone_number()
    {
        ?>
		<input type='text' class='regular-text' name='lddfw_dispatch_phone_number' value='<?php 
        echo  esc_attr( get_option( 'lddfw_dispatch_phone_number', '' ) ) ;
        ?>'>
		<p class="description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'Drivers can call this number if they have questions about orders.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_google_api_key()
    {
        ?>
		<input type='text' class='regular-text' name='lddfw_google_api_key' value='<?php 
        echo  esc_attr( get_option( 'lddfw_google_api_key', '' ) ) ;
        ?>'>
		<p class="description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'To use the Google Maps JavaScript API you must have an API key.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_processing_status()
    {
        $result = wc_get_order_statuses();
        ?>
		<select name='lddfw_processing_status'>
			<?php 
        if ( !empty($result) ) {
            foreach ( $result as $key => $status ) {
                ?>
					<option value="<?php 
                echo  esc_attr( $key ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_processing_status', '' ) ), $key );
                ?>><?php 
                echo  esc_html( $status ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'On this status orders are ready for delivery, the delivery driver can claim orders or its already assigned to him.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_failed_attempt_status()
    {
        $result = wc_get_order_statuses();
        ?>
		<select name='lddfw_failed_attempt_status'>
			<?php 
        if ( !empty($result) ) {
            foreach ( $result as $key => $status ) {
                ?>
					<option value="<?php 
                echo  esc_attr( $key ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_failed_attempt_status', '' ) ), $key );
                ?>><?php 
                echo  esc_html( $status ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'The delivery driver attempted to deliver but failed.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_delivered_status()
    {
        $result = wc_get_order_statuses();
        ?>
		<select name='lddfw_delivered_status'>
			<?php 
        if ( !empty($result) ) {
            foreach ( $result as $key => $status ) {
                ?>
					<option value="<?php 
                echo  esc_attr( $key ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_delivered_status', '' ) ), $key );
                ?>><?php 
                echo  esc_attr( $status ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'The shipment was delivered successfully.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_out_for_delivery_status()
    {
        $result = wc_get_order_statuses();
        ?>
		<select name='lddfw_out_for_delivery_status'>
			<?php 
        if ( !empty($result) ) {
            foreach ( $result as $key => $status ) {
                ?>
					<option value="<?php 
                echo  esc_attr( $key ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_out_for_delivery_status', '' ) ), $key );
                ?>><?php 
                echo  esc_html( $status ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'The delivery driver is about to deliver the shipment.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_settings_section_callback()
    {
        echo  esc_html( __( 'This Section Description', 'lddfw' ) ) ;
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_settings()
    {
        ?>
		<form action='options.php' method='post'>
			<h1><?php 
        echo  esc_html( __( 'General Settings', 'lddfw' ) ) ;
        ?></h1>
			<a target="_blank" href='<?php 
        echo  lddfw_drivers_page_url() ;
        ?>'><?php 
        echo  esc_html( __( 'Click here for Driver Dashboard', 'lddfw' ) ) ;
        ?></a>
			<?php 
        settings_fields( 'lddfw' );
        do_settings_sections( 'lddfw' );
        submit_button();
        ?>
		</form>
		<?php 
    }
    
    /**
     * Plugin submenu.
     *
     * @since 1.0.0
     * @return void
     */
    public function lddfw_admin_menu()
    {
        // add menu to main menu.
        add_menu_page(
            'Delivery Drivers Settings',
            'Delivery Drivers',
            'edit_pages',
            'lddfw-settings',
            array( &$this, 'lddfw_settings' )
        );
    }
    
    /**
     * Print driver name in column
     *
     * @param string $column column name.
     * @param int    $post_id post number.
     * @since 1.0.0
     */
    public function lddfw_orders_list_columns( $column, $post_id )
    {
        switch ( $column ) {
            case 'Driver':
                $lddfw_driverid = get_post_meta( $post_id, 'lddfw_driverid', true );
                $first_name = get_user_meta( $lddfw_driverid, 'first_name', true );
                $last_name = get_user_meta( $lddfw_driverid, 'last_name', true );
                echo  esc_html( $first_name . ' ' . $last_name ) ;
                break;
        }
    }
    
    /**
     * Columns order
     *
     * @param array $columns columns array.
     * @since 1.0.0
     * @return array
     */
    public function lddfw_orders_list_columns_order( $columns )
    {
        $reordered_columns = array();
        // Inserting columns to a specific location.
        foreach ( $columns as $key => $column ) {
            $reordered_columns[$key] = $column;
            if ( 'order_status' === $key ) {
                // Inserting after "Status" column.
                $reordered_columns['Driver'] = __( 'Driver', 'lddfw' );
            }
        }
        return $reordered_columns;
    }
    
    /**
     * Sortable columns
     *
     * @param array $columns columns array.
     * @since 1.0.0
     * @return array
     */
    public function lddfw_orders_list_sortable_columns( $columns )
    {
        $columns['Driver'] = 'Driver';
        return $columns;
    }
    
    /**
     * Save user fields
     *
     * @since 1.0.0
     * @param int $user_id user id.
     */
    public function lddfw_user_fields_save( $user_id )
    {
        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
        $nonce_key = 'lddfw_nonce_user';
        
        if ( isset( $_REQUEST[$nonce_key] ) ) {
            $retrieved_nonce = sanitize_text_field( wp_unslash( $_REQUEST[$nonce_key] ) );
            if ( !wp_verify_nonce( $retrieved_nonce, basename( __FILE__ ) ) ) {
                die( 'Failed security check' );
            }
        }
        
        $lddfw_driver_availability = ( isset( $_POST['lddfw_driver_availability'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_availability'] ) ) : '' );
        update_user_meta( $user_id, 'lddfw_driver_availability', $lddfw_driver_availability );
    }
    
    /**
     * Get user fields
     *
     * @since 1.0.0
     * @param object $user user data object.
     */
    public function lddfw_user_fields( $user )
    {
        wp_nonce_field( basename( __FILE__ ), 'lddfw_nonce_user' );
        ?>
		<h3><?php 
        echo  esc_html( __( 'Driver info', 'lddfw' ) ) ;
        ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="lddfw_driver_availability"><?php 
        echo  esc_html( __( 'Driver availability', 'lddfw' ) ) ;
        ?></label></th>
				<td>
					<select name="lddfw_driver_availability" id="lddfw_driver_availability">
						<option value="0"><?php 
        echo  esc_html( __( 'Unavailable', 'lddfw' ) ) ;
        ?></option>
						<?php 
        $selected = ( get_user_meta( $user->ID, 'lddfw_driver_availability', true ) === '1' ? 'selected' : '' );
        ?>
						<option <?php 
        echo  esc_attr( $selected ) ;
        ?> value="1"><?php 
        echo  esc_html( __( 'Available', 'lddfw' ) ) ;
        ?></option>
					</select>
					<p class="lddfw_description"><?php 
        echo  esc_html( __( 'Driver availability for working today.', 'lddfw' ) ) ;
        ?></p>
				</td>
			</tr>
		</table>
		<?php 
    }
    
    /**
     * Bulk edit assign to driver
     *
     * @since 1.0.0
     * @param array $actions edit action array.
     * @return array
     */
    public function lddfw_bulk_actions_edit( $actions )
    {
        wp_nonce_field( basename( __FILE__ ), 'lddfw_nonce_bulk_orders' );
        $actions['assign_a_driver'] = __( 'Assign orders to the delivery driver', 'lddfw' );
        return $actions;
    }
    
    /**
     * Plugin custom email class
     *
     * @since 1.0.0
     * @param array $email_classes email classes.
     * @return array
     */
    public function lddfw_woocommerce_emails( $email_classes )
    {
        // Add the email class to the list of email classes that WooCommerce loads.
        $email_classes['LDDFW_Out_For_Delivery_Email'] = (include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lddfw-out-for-delivery-email.php');
        $email_classes['LDDFW_Delivered_Email'] = (include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lddfw-delivered-email.php');
        $email_classes['LDDFW_Failed_Delivery_Email'] = (include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lddfw-failed-delivery-email.php');
        $email_classes['LDDFW_Delivered_Email_Admin'] = (include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lddfw-delivered-email-admin.php');
        $email_classes['LDDFW_Assigned_Order_Email_Driver'] = (include plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lddfw-assigned-order-email-driver.php');
        return $email_classes;
    }
    
    /**
     * Plugin locate template
     *
     * @param array  $template template data array.
     * @param string $template_name template name.
     * @param string $template_path template path.
     * @return string
     */
    public function lddfw_woocommerce_locate_template( $template, $template_name, $template_path )
    {
        global  $woocommerce ;
        $_template = $template;
        if ( !$template_path ) {
            $template_path = $woocommerce->template_url;
        }
        $plugin_path = plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/';
        // Look within passed path within the theme - this is priority.
        $template = locate_template( array( $template_path . $template_name, $template_name ) );
        // Modification: Get the template from this plugin, if it exists.
        if ( !$template && file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }
        // Use default template.
        if ( !$template ) {
            $template = $_template;
        }
        // Return what we found.
        return $template;
    }
    
    /**
     * Plugin bulk actions , assign an order to driver
     *
     * @param string $redirect_to redirect to url.
     * @param string $action action.
     * @param array  $post_ids array of posts.
     * @return array
     */
    public function lddfw_handle_bulk_actions( $redirect_to, $action, $post_ids )
    {
        $driver = new LDDFW_Driver();
        
        if ( 'assign_a_driver' === $action ) {
            $nonce_key = 'lddfw_nonce_bulk_orders';
            
            if ( isset( $_REQUEST[$nonce_key] ) ) {
                $retrieved_nonce = sanitize_text_field( wp_unslash( $_REQUEST[$nonce_key] ) );
                if ( !wp_verify_nonce( $retrieved_nonce, basename( __FILE__ ) ) ) {
                    die( 'Failed security check' );
                }
            }
            
            $lddfw_driverid_action = ( isset( $_GET['lddfw_driverid_lddfw_action'] ) ? sanitize_text_field( wp_unslash( $_GET['lddfw_driverid_lddfw_action'] ) ) : '' );
            $lddfw_driverid_action2 = ( isset( $_GET['lddfw_driverid_lddfw_action2'] ) ? sanitize_text_field( wp_unslash( $_GET['lddfw_driverid_lddfw_action2'] ) ) : '' );
            $action_get = ( isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '' );
            $action2_get = ( isset( $_GET['action2'] ) ? sanitize_text_field( wp_unslash( $_GET['action2'] ) ) : '' );
            if ( $action === $action_get ) {
                $driver_id = $lddfw_driverid_action;
            }
            if ( $action === $action2_get ) {
                $driver_id = $lddfw_driverid_action2;
            }
            $processed_ids = array();
            foreach ( $post_ids as $post_id ) {
                // assign an order to driver.
                $driver->assign_delivery_driver( $post_id, $driver_id, 'store' );
                $processed_ids[] = $post_id;
                $redirect_to = add_query_arg( array(
                    'processed_count' => count( $processed_ids ),
                    'processed_ids'   => implode( ',', $processed_ids ),
                ), $redirect_to );
            }
        }
        
        return $redirect_to;
    }
    
    /**
     * Plugin settings.
     *
     * @since 1.0.0
     */
    public function lddfw_delivery_drivers_page()
    {
        $args = array(
            'sort_order'   => 'asc',
            'sort_column'  => 'post_title',
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'meta_key'     => '',
            'meta_value'   => '',
            'authors'      => '',
            'child_of'     => 0,
            'parent'       => -1,
            'exclude_tree' => '',
            'number'       => '',
            'offset'       => 0,
            'post_type'    => 'page',
            'post_status'  => 'publish',
        );
        $pages = get_pages( $args );
        ?>
		<select name='lddfw_delivery_drivers_page'>
			<?php 
        if ( !empty($pages) ) {
            foreach ( $pages as $page ) {
                $page_id = $page->ID;
                $page_title = $page->post_title;
                ?>
					<option value="<?php 
                echo  esc_attr( $page_id ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_delivery_drivers_page', '' ) ), $page_id );
                ?>><?php 
                echo  esc_html( $page_title ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'The delivery driver page.', 'lddfw' ) ) ;
        ?></p>
		<?php 
    }

}