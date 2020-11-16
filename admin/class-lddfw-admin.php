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
        $page = ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
        if ( 'lddfw-reports' === $page ) {
            wp_enqueue_style(
                'lddfw-jquery-ui',
                plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css',
                array(),
                $this->version,
                'all'
            );
        }
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
        $script_array = array( 'jquery' );
        $page = ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
        if ( 'lddfw-reports' === $page ) {
            // add date picker script
            $script_array = array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' );
        }
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/lddfw-admin.js',
            $script_array,
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'lddfw_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
        wp_localize_script( $this->plugin_name, 'lddfw_nonce', esc_js( wp_create_nonce( 'lddfw-nonce' ) ) );
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
                    $error = __( 'Security Check Failure - This alert may occur when you are logged in as an administrator and as a delivery driver on the same browser and the same device. If you want to work on both panels please try to work with two different browsers.', 'lddfw' );
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
                                $driver_assigned_status = get_option( 'lddfw_driver_assigned_status', '' );
                                $current_order_status = 'wc-' . $order->get_status();
                                // Check if order belongs to driver and status is processing.
                                
                                if ( intval( $order_driverid ) === intval( $driver_id ) && $current_order_status === $driver_assigned_status ) {
                                    // Update order status.
                                    $order->update_status( $out_for_delivery_status, __( 'The delivery driver changed the order status.', 'lddfw' ) );
                                    $order->save();
                                    $result = 1;
                                    $error = '<div class=\'alert alert-success alert-dismissible fade show\'>' . __( 'Orders successfully marked as out for delivery.', 'lddfw' ) . '<button type=\'button\' class=\'close\' data-dismiss=\'alert\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button></div> <a id=\'view_out_of_delivery_orders_button\' href=\'' . lddfw_drivers_page_url( 'lddfw_screen=out_for_delivery' ) . '\'  class=\'btn btn-lg btn-block btn-primary\'>' . __( 'View out for delivery orders', 'lddfw' ) . '</a>';
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
                    $error = __( 'Security Check Failure - This alert may occur when you are logged in as an administrator and as a delivery driver on the same browser and the same device. If you want to work on both panels please try to work with two different browsers.', 'lddfw' );
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
                        $error = '<div class=\'alert alert-success alert-dismissible fade show\'>' . __( 'Orders successfully assigned to you.', 'lddfw' ) . '<button type=\'button\' class=\'close\' data-dismiss=\'alert\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button></div><a id=\'view_assigned_orders_button\' href=\'' . lddfw_drivers_page_url( 'lddfw_screen=assign_to_driver' ) . '\'  class=\'btn btn-lg btn-block btn-primary\'>' . __( 'View assigned orders', 'lddfw' ) . '</a>';
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
        $lddfw_data_type = ( isset( $_POST['lddfw_data_type'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_data_type'] ) ) : '' );
        $lddfw_obj_id = ( isset( $_POST['lddfw_obj_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_obj_id'] ) ) : '' );
        $lddfw_service = ( isset( $_POST['lddfw_service'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_service'] ) ) : '' );
        $lddfw_driver_id = ( isset( $_POST['lddfw_driver_id'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_id'] ) ) : '' );
        $result = 0;
        /**
         * Security check.
         */
        
        if ( isset( $_POST['lddfw_wpnonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
            
            if ( !wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
                $error = esc_js( __( 'Security Check Failure - This alert may occur when you are logged in as an administrator and as a delivery driver on the same browser and the same device. If you want to work on both panels please try to work with two different browsers.', 'lddfw' ) );
                
                if ( 'json' === $lddfw_data_type ) {
                    echo  "{\"result\":\"{$result}\",\"error\":\"{$error}\"}" ;
                } else {
                    echo  '<div class=\'alert alert-danger alert-dismissible fade show\'>' . $error . '<button type=\'button\' class=\'close\' data-dismiss=\'alert\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button></div>' ;
                }
                
                exit;
            }
        
        }
        
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
        Set driver account status.
        */
        
        if ( 'lddfw_account_status' === $lddfw_service ) {
            $user = wp_get_current_user();
            // Switch to driver user if administrator is logged in.
            if ( in_array( 'administrator', (array) $user->roles, true ) && '' !== $lddfw_driver_id ) {
                $user = get_user_by( 'id', $lddfw_driver_id );
            }
            // Check if user has a driver role.
            
            if ( in_array( 'driver', (array) $user->roles, true ) ) {
                $driver_id = $user->ID;
                $account_status = ( isset( $_POST['lddfw_account_status'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_account_status'] ) ) : '' );
                update_user_meta( $driver_id, 'lddfw_driver_account', $account_status );
                $result = 1;
            }
            
            echo  esc_html( $result ) ;
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
                        $status_note = esc_html__( 'Driver changed order status', 'lddfw' );
                        
                        if ( '' !== $note ) {
                            $Driver_note = __( 'Driver note', 'lddfw' ) . ': ' . $note;
                            $order->add_order_note( $Driver_note );
                        }
                        
                        $order->save();
                        $order->update_status( $order_status, $status_note );
                        $result = 1;
                    }
                
                }
            
            }
            
            echo  esc_html( $result ) ;
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
        }
        
        if ( get_option( 'lddfw_delivered_status', '' ) === 'wc-' . $status_to ) {
            $order_driverid = $order->get_meta( 'lddfw_driverid' );
            // Update delivered date.
            update_post_meta( $order_id, 'lddfw_delivered_date', gmdate( 'Y-m-d H:i:s' ) );
            
            if ( '' != $order_driverid ) {
                // Delete route meta
                delete_post_meta( $order_id, 'lddfw_order_origin' );
                delete_post_meta( $order_id, 'lddfw_order_sort' );
            }
        
        }
        
        
        if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $status_to ) {
            // Update failed attempt date.
            update_post_meta( $order_id, 'lddfw_failed_attempt_date', gmdate( 'Y-m-d H:i:s' ) );
            // Delete route meta
            delete_post_meta( $order_id, 'lddfw_order_origin' );
            delete_post_meta( $order_id, 'lddfw_order_sort' );
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
                $lddfw_statuses['wc-driver-assigned'] = __( 'Driver Assigned', 'lddfw' );
                $lddfw_statuses['wc-out-for-delivery'] = __( 'Out for Delivery', 'lddfw' );
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
        register_post_status( 'wc-failed-delivery', array(
            'label'                     => __( 'Failed Delivery Attempt', 'lddfw' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Failed Delivery Attempt <span class="count">(%s)</span>', 'Failed Delivery Attempt <span class="count">(%s)</span>', 'lddfw' ),
        ) );
        register_post_status( 'wc-driver-assigned', array(
            'label'                     => __( 'Driver Assigned', 'lddfw' ),
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Driver Assigned <span class="count">(%s)</span>', 'Driver Assigned <span class="count">(%s)</span>', 'lddfw' ),
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
        //Get settings tab
        $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' );
        register_setting( 'lddfw', 'lddfw_google_api_key' );
        register_setting( 'lddfw', 'lddfw_dispatch_phone_number' );
        register_setting( 'lddfw', 'lddfw_status_section' );
        register_setting( 'lddfw', 'lddfw_driver_assigned_status' );
        register_setting( 'lddfw', 'lddfw_out_for_delivery_status' );
        register_setting( 'lddfw', 'lddfw_delivered_status' );
        register_setting( 'lddfw', 'lddfw_failed_attempt_status' );
        register_setting( 'lddfw', 'lddfw_processing_status' );
        register_setting( 'lddfw', 'lddfw_delivery_drivers_page' );
        /**
         * Update driver_assigned status if empty.
         * This update will be removed in the future versions.
         */
        $lddfw_driver_assigned_status = get_option( 'lddfw_driver_assigned_status', '' );
        if ( '' === $lddfw_driver_assigned_status ) {
            update_option( 'lddfw_driver_assigned_status', 'wc-driver-assigned' );
        }
        // Admin notices
        add_action( 'admin_notices', array( $this, 'lddfw_admin_notices' ) );
        
        if ( $tab === "" ) {
            // General Settings
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
                'lddfw_driver_assigned_status',
                __( 'Driver assigned status', 'lddfw' ),
                array( $this, 'lddfw_driver_assigned_status' ),
                'lddfw',
                'lddfw_status_section'
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
                __( 'Failed delivery attempt status', 'lddfw' ),
                array( $this, 'lddfw_failed_attempt_status' ),
                'lddfw',
                'lddfw_status_section'
            );
            add_settings_field(
                'lddfw_processing_status',
                __( 'Order processing status', 'lddfw' ),
                array( $this, 'lddfw_processing_status' ),
                'lddfw',
                'lddfw_status_section'
            );
        }
    
    }
    
    /**
     * Plugin premium_features.
     *
     * @since 1.0.0
     */
    public function lddfw_premium_features()
    {
        if ( lddfw_is_free() ) {
            echo  '<h2>' . __( 'Premium features:', 'lddfw' ) . '</h2><hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Delivery Route Planning.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Navigation with Waze, Apple Maps, and Google Maps.', 'lddfw' ) . ' 
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Next Delivery - Drivers Easily Navigate to the Next Destination.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Ready Notes for The Drivers.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Drivers can Claim Orders.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Fully Admin Driver Dashboard.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Admin Orders Filters.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'One-click to Update Drivers Availability, Claim Permission, and Accounts.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Users Names in Orders Notes.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Auto-assign Delivery Drivers.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'SMS Notifications for Customers and Drivers.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Emails Notifications for Customers and Drivers.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Bulk-assign Delivery Drivers.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Delivery Drivers Application.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Drivers Commissions.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Drivers Panel Branding - Add Your Logo and Colors.', 'lddfw' ) . '
			<hr>' . lddfw_premium_feature( '' ) . ' ' . __( 'Custom Fields - Add Custom Fields to the Delivery Panel from Third-party Plugins.', 'lddfw' ) . '
			<hr>' ;
        }
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
		<a href='#' data='[delivery_driver_page]'><?php 
        echo  esc_html( __( 'Delivery Driver Page', 'lddfw' ) ) ;
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
        $result = '';
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $result = wc_get_order_statuses();
        }
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
        echo  esc_html( __( 'The orders are ready for delivery and drivers are able to claim.', 'lddfw' ) ) ;
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
        $result = '';
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $result = wc_get_order_statuses();
        }
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
        $result = '';
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $result = wc_get_order_statuses();
        }
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
    public function lddfw_driver_assigned_status()
    {
        $result = '';
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $result = wc_get_order_statuses();
        }
        ?>
		<select name='lddfw_driver_assigned_status'>
			<?php 
        if ( !empty($result) ) {
            foreach ( $result as $key => $status ) {
                ?>
					<option value="<?php 
                echo  esc_attr( $key ) ;
                ?>" <?php 
                selected( esc_attr( get_option( 'lddfw_driver_assigned_status', '' ) ), $key );
                ?>><?php 
                echo  esc_html( $status ) ;
                ?></option>
					<?php 
            }
        }
        ?>
		</select>
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description"><?php 
        echo  esc_html( __( 'The delivery driver was assigned to order.', 'lddfw' ) ) ;
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
        $result = '';
        if ( function_exists( 'wc_get_order_statuses' ) ) {
            $result = wc_get_order_statuses();
        }
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
        //Get the active tab from the $_GET param
        $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' );
        ?>
		<div class="wrap">
		<form action='options.php' method='post'>
			<?php 
        $settings_title = esc_html( __( 'General Settings', 'lddfw' ) );
        ?>
			<h1 class="wp-heading-inline"><?php 
        echo  $settings_title ;
        ?></h1>
			<?php 
        echo  LDDFW_Admin::lddfw_admin_plugin_bar() ;
        echo  '<hr class="wp-header-end">' ;
        
        if ( '' === $tab ) {
            settings_fields( 'lddfw' );
            do_settings_sections( 'lddfw' );
        }
        
        submit_button();
        $this->lddfw_premium_features();
        ?>
		</form>
	</div>
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
            esc_html( __( 'Delivery Drivers Settings', 'lddfw' ) ),
            esc_html( __( 'Delivery Drivers', 'lddfw' ) ),
            'edit_pages',
            'lddfw-dashboard',
            array( &$this, 'lddfw_dashboard' ),
            'dashicons-location',
            56
        );
        add_submenu_page(
            'lddfw-dashboard',
            esc_html( __( 'Dashboard', 'lddfw' ) ),
            esc_html( __( 'Dashboard', 'lddfw' ) ),
            1,
            'lddfw-dashboard',
            array( &$this, 'lddfw_dashboard' )
        );
        add_submenu_page(
            'lddfw-dashboard',
            esc_html( __( 'Reports', 'lddfw' ) ),
            esc_html( __( 'Reports', 'lddfw' ) ),
            1,
            'lddfw-reports',
            array( &$this, 'lddfw_reports' )
        );
        add_submenu_page(
            'lddfw-dashboard',
            esc_html( __( 'Settings', 'lddfw' ) ),
            esc_html( __( 'Settings', 'lddfw' ) ),
            1,
            'lddfw-settings',
            array( &$this, 'lddfw_settings' )
        );
    }
    
    /**
     * Admin plugin bar.
     *
     * @since 1.1.0
     * @return void
     */
    static function lddfw_admin_plugin_bar()
    {
        return '<div class="lddfw_admin_bar">' . esc_html( __( 'Developed by', 'lddfw' ) ) . ' <a href="https://powerfulwp.com/" target="_blank">PowerfulWP</a> | <a href="https://powerfulwp.com/local-delivery-drivers-for-woocommerce-premium/" target="_blank" >' . esc_html( __( 'Premium', 'lddfw' ) ) . '</a> | <a href="https://powerfulwp.com/docs/local-delivery-drivers-for-woocommerce-premium/" target="_blank" >' . esc_html( __( 'Documents', 'lddfw' ) ) . '</a></div>';
    }
    
    /**
     * Plugin dashboard.
     *
     * @since 1.0.0
     */
    public function lddfw_dashboard()
    {
        $dashboard = new LDDFW_Reports();
        echo  $dashboard->screen_dashboard() ;
    }
    
    /**
     * Plugin reports.
     *
     * @since 1.0.0
     */
    public function lddfw_reports()
    {
        $reports = new LDDFW_Reports();
        echo  $reports->screen_reports() ;
    }
    
    public function lddfw_users_list_columns( $column )
    {
        
        if ( isset( $_GET['role'] ) && $_GET['role'] === 'driver' ) {
            $column['lddfw_driver_availability'] = 'Availability';
            $column['lddfw_driver_claim'] = 'Claim orders';
            $column['lddfw_driver_account'] = 'Account';
        }
        
        return $column;
    }
    
    public function lddfw_users_list_columns_raw( $val, $column_name, $user_id )
    {
        $availability_icon = '';
        $driver_claim_icon = '';
        $driver_account_icon = '';
        switch ( $column_name ) {
            case 'lddfw_driver_availability':
                return lddfw_premium_feature( $availability_icon );
            case 'lddfw_driver_claim':
                return lddfw_premium_feature( $driver_claim_icon );
            case 'lddfw_driver_account':
                return lddfw_premium_feature( $driver_account_icon );
            default:
        }
        return $val;
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
        
        $lddfw_driver_account = ( isset( $_POST['lddfw_driver_account'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_account'] ) ) : '' );
        $lddfw_driver_availability = ( isset( $_POST['lddfw_driver_availability'] ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_driver_availability'] ) ) : '' );
        update_user_meta( $user_id, 'lddfw_driver_account', $lddfw_driver_account );
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
        
        if ( in_array( 'driver', (array) $user->roles, true ) ) {
            wp_nonce_field( basename( __FILE__ ), 'lddfw_nonce_user' );
            ?>
			<h3><?php 
            echo  esc_html( __( 'Delivery Driver Info', 'lddfw' ) ) ;
            ?></h3>
			<table class="form-table">
			<tr>
					<th><label for="lddfw_driver_account"><?php 
            echo  esc_html( __( 'Driver account status', 'lddfw' ) ) ;
            ?></label></th>
					<td>
						<select name="lddfw_driver_account" id="lddfw_driver_account">
							<option value="0"><?php 
            echo  esc_html( __( 'Not active', 'lddfw' ) ) ;
            ?></option>
							<?php 
            $selected = ( get_user_meta( $user->ID, 'lddfw_driver_account', true ) === '1' ? 'selected' : '' );
            ?>
							<option <?php 
            echo  esc_attr( $selected ) ;
            ?> value="1"><?php 
            echo  esc_html( __( 'Active', 'lddfw' ) ) ;
            ?></option>
						</select>
						<p class="lddfw_description"><?php 
            echo  esc_html( __( 'Only drivers with active accounts can access the drivers\' panel.', 'lddfw' ) ) ;
            ?></p>
					</td>
			</tr>
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
            echo  esc_html( __( 'The delivery driver availability for work today.', 'lddfw' ) ) ;
            ?></p>
					</td>
			</tr>
			<tr>
					<th><label for="lddfw_driver_claim"><?php 
            echo  esc_html( __( 'Driver can claim orders', 'lddfw' ) ) ;
            ?></label></th>
					<td>
					<?php 
            $html = '';
            echo  lddfw_premium_feature( $html ) ;
            ?>

					</td>
			</tr>
			</table>
			<?php 
        }
    
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
		<p class="lddfw_description" id="lddfw-gooogle-api-key-description">
		<?php 
        echo  '<div class="driver_app">
				<img alt="' . esc_attr( 'Drivers app', 'lddfw' ) . '" title="' . esc_attr( 'Drivers app', 'lddfw' ) . '" src="' . esc_attr( plugins_url() . '/' . LDDFW_FOLDER . '/public/images/drivers_app.png?ver=' . LDDFW_VERSION ) . '">
				<p>
					<b><a target="_blank" href="' . lddfw_drivers_page_url( '' ) . '">' . lddfw_drivers_page_url( '' ) . '</a></b><br>' . sprintf( esc_html( __( 'The link above is the delivery driver\'s Mobile-Friendly panel URL. %s The delivery drivers can access it from their mobile phones. %s', 'lddfw' ) ), '<br>', '<br>' ) . sprintf(
            esc_html( __( 'Notice: If you want to be logged in as an administrator and to check the drivers\' panel on the same device, %s %syou must work with two different browsers otherwise you will log out from the admin panel and the drivers\' panel won\'t function correctly.%s', 'lddfw' ) ),
            '<br>',
            '<b>',
            '</b>'
        ) . '
				</p>
			</div>' ;
        ?>
		</p>
		<?php 
    }
    
    /**
     * Admin notices function.
     *
     * @since 1.0.0
     */
    public function lddfw_admin_notices()
    {
        if ( !class_exists( 'WooCommerce' ) ) {
            echo  '<div class="notice notice-info is-dismissible">
          			<p>' . esc_html( __( 'Local delivery drivers for WooCommerce is a WooCommerce add-on, you must activate a WooCommerce on your site.', 'lddfw' ) ) . '</p>
		 		  </div>' ;
        }
    }

}