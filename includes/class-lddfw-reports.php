<?php

/**
 * Plugin Reports.
 *
 * All the screens functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
/**
 * Plugin Reports.
 *
 * All the Reports functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Reports
{
    public function driver_status_orders( $driver_id, $status, $array )
    {
        $orders = 0;
        foreach ( $array as $row ) {
            
            if ( '' === $driver_id ) {
                
                if ( $row->post_status === $status ) {
                    $orders = $row->orders;
                    break;
                }
            
            } else {
                
                if ( $row->post_status === $status && $driver_id === $row->driver_id ) {
                    $orders = $row->orders;
                    break;
                }
            
            }
        
        }
        return $orders;
    }
    
    /**
     * Drivers orders dashboard report.
     *
     * @since 1.1.0
     * @return html
     */
    public function claim_orders_dashboard_report()
    {
        $orders = new LDDFW_Orders();
        $report_array = $orders->lddfw_claim_orders_dashboard_report_query();
        echo  '<h2>' . esc_html( __( 'Orders without drivers', 'lddfw' ) ) . '</h2>
	<table class="wp-list-table widefat fixed striped table-view-list posts">
	<thead>
		<tr>
			<th class="manage-column column-primary ">' . esc_html( __( 'Ready for claim', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Driver assigned', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Out for delivery', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Delivered today', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Failed delivery', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Total', 'lddfw' ) ) . '</td>
		</tr>
	</thead>
	<tbody>' ;
        $lddfw_driver_assigned_status = get_option( 'lddfw_driver_assigned_status', '' );
        $lddfw_out_for_delivery_status = get_option( 'lddfw_out_for_delivery_status', '' );
        $lddfw_failed_attempt_status = get_option( 'lddfw_failed_attempt_status', '' );
        $lddfw_delivered_status = get_option( 'lddfw_delivered_status', '' );
        $lddfw_processing_status = get_option( 'lddfw_processing_status', '' );
        
        if ( empty($report_array) ) {
            echo  '
		<tr>
			<td colspan="6" class="lddfw-text-center">' . esc_html( __( 'No orders', 'lddfw' ) ) . '</td>
		</tr>' ;
        } else {
            $processing_status = '';
            $out_for_delivery_orders = '';
            $driver_assigned_orders = '';
            $failed_attempt_orders = '';
            $delivered_orders = '';
            $total = '';
            echo  '
				<tr>
					<td>' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_processing_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-2">' . $processing_status . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_driver_assigned_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-2">' . $driver_assigned_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_out_for_delivery_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-2">' . $out_for_delivery_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_delivered_status' ) ) . '&lddfw_delivered_today=1&post_type=shop_order&lddfw_orders_filter=-2">' . $delivered_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_failed_attempt_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-2">' . $failed_attempt_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( $total ) . '</td>
				</tr>' ;
            echo  '</tbody>' ;
        }
        
        echo  '</table>' ;
    }
    
    /**
     * Drivers orders dashboard report.
     *
     * @since 1.1.0
     * @return html
     */
    public function drivers_orders_dashboard_report()
    {
        $orders = new LDDFW_Orders();
        $report_array = $orders->lddfw_drivers_orders_dashboard_report_query();
        echo  '<h2>' . esc_html( __( 'Drivers orders', 'lddfw' ) ) . '</h2>
	<table class="wp-list-table widefat fixed striped table-view-list posts">
	<thead>
		<tr>
			<th class="manage-column column-primary ">' . esc_html( __( 'Drivers', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center ">' . esc_html( __( 'Phone', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Driver assigned', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Out for delivery', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Delivered today', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Failed delivery', 'lddfw' ) ) . '</td>
			<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Total', 'lddfw' ) ) . '</td>
		</tr>
	</thead>
	<tbody>' ;
        $lddfw_driver_assigned_status = get_option( 'lddfw_driver_assigned_status', '' );
        $lddfw_out_for_delivery_status = get_option( 'lddfw_out_for_delivery_status', '' );
        $lddfw_failed_attempt_status = get_option( 'lddfw_failed_attempt_status', '' );
        $lddfw_delivered_status = get_option( 'lddfw_delivered_status', '' );
        $last_driver = '';
        $out_for_delivery_orders_total = 0;
        $driver_assigned_orders_total = 0;
        $failed_attempt_orders_total = 0;
        $delivered_orders_total = 0;
        $total = 0;
        $driver_counter = 0;
        $sub_total = 0;
        
        if ( empty($report_array) ) {
            echo  '
		<tr>
			<td colspan="8" class="lddfw-text-center">' . esc_html( __( 'No orders', 'lddfw' ) ) . '</td>
		</tr>' ;
        } else {
            foreach ( $report_array as $row ) {
                $driver_id = $row->driver_id;
                
                if ( $last_driver !== $driver_id ) {
                    $driver_counter += 1;
                    $phone = get_user_meta( $driver_id, 'billing_phone', true );
                    $last_driver = $driver_id;
                    echo  '
				<tr>
					<td>' . $row->driver_name . '</td>
					<td class="lddfw-text-center"><a href="tel:' . $phone . '">' . $phone . '</a></td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_driver_assigned_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=' . esc_attr( $driver_id ) . '">' . $driver_assigned_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_out_for_delivery_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=' . esc_attr( $driver_id ) . '">' . $out_for_delivery_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_delivered_status' ) ) . '&lddfw_delivered_today=1&post_type=shop_order&lddfw_orders_filter=' . esc_attr( $driver_id ) . '">' . $delivered_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_failed_attempt_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=' . esc_attr( $driver_id ) . '">' . $failed_attempt_orders . '</a>' ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( $sub_total ) . '</td>
				</tr>' ;
                }
            
            }
        }
        
        echo  '</tbody>
		<tfoot>
			<td>' . $driver_counter . ' ' . esc_html( __( 'Drivers', 'lddfw' ) ) . '</td>
			<td class="lddfw-text-center"> </td>
			<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_driver_assigned_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-1">' . $driver_assigned_orders_total . '</a>' ) . '</td>
			<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_out_for_delivery_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-1">' . $out_for_delivery_orders_total . '</a>' ) . '</td>
			<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_delivered_status' ) ) . '&lddfw_delivered_today=1&post_type=shop_order&lddfw_orders_filter=-1">' . $delivered_orders_total . '</a>' ) . '</td>
			<td class="lddfw-text-center">' . lddfw_premium_feature( '<a href="edit.php?post_status=' . esc_attr( get_option( 'lddfw_failed_attempt_status' ) ) . '&post_type=shop_order&lddfw_orders_filter=-1">' . $failed_attempt_orders_total . '</a>' ) . '</td>
			<td class="lddfw-text-center">' . lddfw_premium_feature( $total ) . '</td>
		</tfoot>
	</table>' ;
    }
    
    /**
     * Admin dashboard screen.
     *
     * @since 1.1.0
     * @return html
     */
    public function screen_dashboard()
    {
        echo  '<div class="wrap">
		<h1 class="wp-heading-inline">' . esc_html( __( 'Dashboard', 'lddfw' ) ) . '</h1>
		  ' . LDDFW_Admin::lddfw_admin_plugin_bar() . '
		  <hr class="wp-header-end">' ;
        echo  $this->drivers_orders_dashboard_report() ;
        echo  $this->claim_orders_dashboard_report() ;
        echo  $this->drivers_dashboard_report() ;
        echo  '
		</div>' ;
    }
    
    /**
     * Drivers dashboard report.
     *
     * @since 1.1.0
     * @return html
     */
    public function drivers_dashboard_report()
    {
        $drivers = LDDFW_Driver::lddfw_get_drivers();
        echo  '
		<h2 style="margin-bottom: 0px;margin-top: 28px;">' . esc_html( __( 'Active drivers', 'lddfw' ) ) . '
		<a href="user-new.php" class="page-title-action" >' . esc_html( __( 'Add new driver', 'lddfw' ) ) . '</a>
		</h2>
		<ul class="subsubsub">
			<li class="all"><a href="users.php?role=driver">' . esc_html( __( 'All drivers', 'lddfw' ) ) . '</a></li>
		</ul>
		<table class="wp-list-table widefat fixed striped table-view-list posts">
		<thead>
			<tr>
				<th class="manage-column column-primary ">' . esc_html( __( 'Drivers', 'lddfw' ) ) . '</td>
				<th>' . esc_html( __( 'Phone', 'lddfw' ) ) . '</td>
				<th>' . esc_html( __( 'Email', 'lddfw' ) ) . '</td>
				<th>' . esc_html( __( 'Address', 'lddfw' ) ) . '</td>
				<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Availability', 'lddfw' ) ) . '</td>
				<th class="manage-column column-primary lddfw-text-center">' . esc_html( __( 'Claim orders', 'lddfw' ) ) . '</td>
			</tr>
		</thead>
		<tbody>' ;
        $total_driver = 0;
        
        if ( empty($drivers) ) {
            echo  '
			<tr>
				<td colspan="3" class="lddfw-text-center">' . esc_html( __( 'No drivers', 'lddfw' ) ) . '</td>
			</tr>' ;
        } else {
            foreach ( $drivers as $driver ) {
                /**
                 * Driver data.
                 */
                $driver_id = $driver->ID;
                $lddfw_driver_account = get_user_meta( $driver_id, 'lddfw_driver_account', true );
                // Activate exiting drivers account that added before version 1.1.0
                
                if ( '' === $lddfw_driver_account ) {
                    update_user_meta( $driver_id, 'lddfw_driver_account', '1' );
                    $lddfw_driver_account = get_user_meta( $driver_id, 'lddfw_driver_account', true );
                }
                
                
                if ( '1' === $lddfw_driver_account ) {
                    $email = $driver->user_email;
                    $full_name = $driver->display_name;
                    $availability = get_user_meta( $driver_id, 'lddfw_driver_availability', true );
                    $driver_claim = get_user_meta( $driver_id, 'lddfw_driver_claim', true );
                    $phone = get_user_meta( $driver_id, 'billing_phone', true );
                    $billing_address_1 = get_user_meta( $driver_id, 'billing_address_1', true );
                    $billing_address_2 = get_user_meta( $driver_id, 'billing_address_2', true );
                    $billing_city = get_user_meta( $driver_id, 'billing_city', true );
                    $billing_company = get_user_meta( $driver_id, 'billing_company', true );
                    $availability_icon = '';
                    $driver_claim_icon = '';
                    /**
                     * Driver billing address.
                     */
                    $billing_address = '';
                    if ( '' !== $billing_company ) {
                        $billing_address = $billing_address . $billing_company . ', ';
                    }
                    if ( '' !== $billing_address_1 ) {
                        $billing_address = $billing_address . $billing_address_1;
                    }
                    if ( '' !== $billing_address_2 ) {
                        $billing_address = $billing_address . ', ' . $billing_address_2;
                    }
                    if ( '' !== $billing_city ) {
                        $billing_address = $billing_address . ', ' . $billing_city;
                    }
                    $total_driver++;
                    echo  '
				<tr>
					<td><a href="' . get_edit_user_link( $driver_id ) . '">' . esc_html( $full_name ) . '</a></td>
					<td><a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a></td>
					<td  ><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></td>
					<td>' . $billing_address . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( $availability_icon ) . '</td>
					<td class="lddfw-text-center">' . lddfw_premium_feature( $driver_claim_icon ) . '</td>
				</tr>' ;
                }
            
            }
        }
        
        echo  '</tbody>
				<tfoot>
					<td>' . $total_driver . ' ' . esc_html( __( 'Drivers', 'lddfw' ) ) . '</td>
					<td></td>
					<td></td>
					<td></td>
					<td class = "lddfw-text-center">' . lddfw_premium_feature( '<span id="lddfw_available_counter"></span> ' . esc_html( __( 'Availables', 'lddfw' ) ) . ' |  <span id="lddfw_unavailable_counter"></span> ' . esc_html( __( 'Unavailables', 'lddfw' ) ) ) . '</td>
					<td class = "lddfw-text-center">' . lddfw_premium_feature( '<span id="lddfw_claim_counter"></span> ' . esc_html( __( 'Can claim', 'lddfw' ) ) . ' | <span id="lddfw_unclaim_counter"></span> ' . esc_html( __( 'Can\'t claim', 'lddfw' ) ) ) . '</td>
				</tfoot>
			</table>' ;
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
				</div>
				' ;
    }

}