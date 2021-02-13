<?php

/**
 * Order page.
 *
 * All the order functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
/**
 * Order page.
 *
 * All the order functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Order
{
    /**
     * Order page.
     *
     * @since 1.0.0
     * @param object $order order object.
     * @param int    $driver_id driver user id.
     * @return html
     */
    public function lddfw_order_page( $order, $driver_id )
    {
        global  $lddfw_order_id ;
        $date_format = lddfw_date_format( 'date' );
        $time_format = lddfw_date_format( 'time' );
        $order_status = $order->get_status();
        $order_status_name = wc_get_order_status_name( $order_status );
        $date_created = $order->get_date_created()->format( $date_format );
        $discount_total = $order->get_discount_total();
        $shipping_total = $order->get_shipping_total();
        $total = $order->get_total();
        $currency_symbol = get_woocommerce_currency_symbol();
        $shipping_address_map_url = $order->get_shipping_address_map_url();
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();
        $billing_company = $order->get_billing_company();
        $billing_address_1 = $order->get_billing_address_1();
        $billing_address_2 = $order->get_billing_address_2();
        $billing_city = $order->get_billing_city();
        $billing_state = $this->lddfw_states( $order->get_billing_state() );
        $billing_country = $order->get_billing_country();
        if ( '' !== $billing_country ) {
            $billing_country = WC()->countries->countries[$billing_country];
        }
        $billing_postcode = $order->get_billing_postcode();
        $billing_phone = $order->get_billing_phone();
        $shipping_first_name = $order->get_shipping_first_name();
        $shipping_last_name = $order->get_shipping_last_name();
        $shipping_company = $order->get_shipping_company();
        $shipping_address_1 = $order->get_shipping_address_1();
        $shipping_address_2 = $order->get_shipping_address_2();
        $shipping_city = $order->get_shipping_city();
        $shipping_state = $this->lddfw_states( $order->get_shipping_state() );
        $shipping_postcode = $order->get_shipping_postcode();
        $shipping_country = $order->get_shipping_country();
        if ( '' !== $shipping_country ) {
            $shipping_country = WC()->countries->countries[$shipping_country];
        }
        $customer_note = $order->get_customer_note();
        $payment_method = $order->get_payment_method();
        
        if ( in_array( 'woocommerce-extra-checkout-fields-for-brazil', LDDFW_PLUGINS ) ) {
            // Add shipping number to address.
            $shipping_number = get_post_meta( $lddfw_order_id, '_shipping_number', true );
            $shipping_address_1 .= ' ' . $shipping_number;
            // Add shipping number to address.
            $billing_number = get_post_meta( $lddfw_order_id, '_billing_number', true );
            $billing_address_1 .= ' ' . $billing_number;
        }
        
        // Format billing address.
        $billing_full_name = $billing_first_name . ' ' . $billing_last_name . '<br>';
        if ( '' !== $billing_company ) {
            $billing_full_name .= $billing_company . '<br>';
        }
        $billing_address = $billing_address_1 . ' ';
        if ( '' !== $billing_address_2 ) {
            $billing_address .= ', ' . $billing_address_2 . ' ';
        }
        $billing_address .= '<br>' . $billing_city . ' ';
        if ( '' !== $billing_state ) {
            $billing_address .= $billing_state . ' ';
        }
        if ( '' !== $billing_postcode ) {
            $billing_address .= $billing_postcode . ' ';
        }
        if ( '' !== $billing_country ) {
            $billing_address .= '<br>' . $billing_country;
        }
        
        if ( isset( $shipping_address_1 ) && '' !== $shipping_address_1 ) {
            // Format shipping address.
            $shipping_full_name = $shipping_first_name . ' ' . $shipping_last_name . ' <br>';
            if ( '' !== $shipping_company ) {
                $shipping_full_name .= $shipping_company . ' <br>';
            }
            $shipping_address = $shipping_address_1 . ' ';
            if ( '' !== $shipping_address_2 ) {
                $shipping_address .= ',' . $shipping_address_2 . ' ';
            }
            $shipping_address .= '<br>' . $shipping_city . ' ';
            if ( '' !== $shipping_state ) {
                $shipping_address .= $shipping_state . ' ';
            }
            if ( '' !== $shipping_postcode ) {
                $shipping_address .= $shipping_postcode . ' ';
            }
            if ( '' !== $shipping_country ) {
                $shipping_address .= '<br>' . $shipping_country;
            }
        } else {
            $shipping_full_name = $billing_full_name;
            $shipping_address = $billing_address;
        }
        
        // Waze buttons.
        $shipping_direction_address = str_replace( '<br>', '', $shipping_address );
        $shipping_direction_address = str_replace( ',', '', $shipping_direction_address );
        $navigation_address = rawurlencode( $shipping_direction_address );
        $shipping_direction_address = str_replace( '  ', ' ', $shipping_direction_address );
        $shipping_direction_address = str_replace( ' ', '+', $shipping_direction_address );
        $store = new LDDFW_Store();
        $store_address = $store->lddfw_store_address( 'map_address' );
        $origin = get_post_meta( $lddfw_order_id, 'lddfw_order_origin', true );
        $failed_date = get_post_meta( $lddfw_order_id, 'lddfw_failed_attempt_date', true );
        $delivered_date = get_post_meta( $lddfw_order_id, 'lddfw_delivered_date', true );
        $html = '<div class="lddfw_page_content">';
        if ( '' === $origin ) {
            $origin = $store_address;
        }
        $lddfw_google_api_key = get_option( 'lddfw_google_api_key', '' );
        $lddfw_dispatch_phone_number = get_option( 'lddfw_dispatch_phone_number', '' );
        // Map.
        if ( '' !== $lddfw_google_api_key ) {
            $html .= '
		<div id="google_map" class="container-fluid p-0" >
			<iframe
				width="100%"
				height="350"
				frameborder="0"
				style="border:0"
				src="https://www.google.com/maps/embed/v1/directions?mode=' . LDDFW_Driver::get_driver_driving_mode( $driver_id, 'lowercase' ) . '&origin=' . $origin . '&destination=' . $shipping_direction_address . '&key=' . $lddfw_google_api_key . '"
				allowfullscreen>
			</iframe>
		</div>
		';
        }
        $html .= '
	<div class="container" id="lddfw_order">
		<div class="row">
			<div class="col-12">
			</div>
			<div class="col-12">';
        // Orders info.
        $html .= '	<div class="lddfw_box">
					<h3 class="lddfw_title"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" class="svg-inline--fa fa-info-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg> 
					' . esc_html( __( 'Info', 'lddfw' ) ) . '</h3>
					<div class="row">
					<div class="col-12">
						<p id="lddfw_order_date">' . esc_html( __( 'Date', 'lddfw' ) ) . ': ' . $date_created . '</p>
					</div> 
					<div class="col-12">
						<p id="lddfw_order_status">' . esc_html( __( 'Status', 'lddfw' ) ) . ': ' . $order_status_name . '</p>
					</div>';
        if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $order_status && '' !== $failed_date ) {
            $html .= '<div class=\'col-12\'>
						<p id=\'lddfw_order_status_date\'>' . esc_html( __( 'Failed date', 'lddfw' ) ) . ': ' . date( $date_format . ' ' . $time_format, strtotime( $failed_date ) ) . '</p>
			 		  </div>';
        }
        if ( get_option( 'lddfw_delivered_status', '' ) === 'wc-' . $order_status && '' !== $delivered_date ) {
            $html .= '<div class="col-12">
						<p id="lddfw_order_status_date">' . esc_html( __( 'Delivered date', 'lddfw' ) ) . ': ' . date( $date_format . ' ' . $time_format, strtotime( $delivered_date ) ) . '</p>
			 </div>';
        }
        if ( '' !== $payment_method ) {
            $html .= '<div class="col-12">
						<p id="lddfw_order_payment_method">' . esc_html( __( 'Payment method', 'lddfw' ) ) . ': ' . $payment_method . '</p>
					</div>';
        }
        $html .= '<div class="col-12">
						<p id="lddfw_order_total">' . esc_html( __( 'Total', 'lddfw' ) ) . ': ' . $currency_symbol . $total . '</p>
					</div>';
        $html .= '</div>
			</div>';
        // Shipping address.
        $html .= '<div id="lddfw_shipping_address" class="lddfw_box">
					<h3 class="lddfw_title">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="map-marker-alt" class="svg-inline--fa fa-map-marker-alt fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path></svg> ' . esc_html( __( 'Shipping Address', 'lddfw' ) ) . '</h3>' . $shipping_full_name . ' ' . $shipping_address;
        // Map button.
        if ( '' === $lddfw_google_api_key ) {
            $html .= '<div class="row" id="lddfw_navigation_buttons">
										<div class="col-12  mt-2">
											<a class="btn btn-secondary btn-block" href="https://www.google.com/maps/search/?api=1&query=' . $shipping_direction_address . '">
											<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="map-marked-alt" class="svg-inline--fa fa-map-marked-alt fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M288 0c-69.59 0-126 56.41-126 126 0 56.26 82.35 158.8 113.9 196.02 6.39 7.54 17.82 7.54 24.2 0C331.65 284.8 414 182.26 414 126 414 56.41 357.59 0 288 0zm0 168c-23.2 0-42-18.8-42-42s18.8-42 42-42 42 18.8 42 42-18.8 42-42 42zM20.12 215.95A32.006 32.006 0 0 0 0 245.66v250.32c0 11.32 11.43 19.06 21.94 14.86L160 448V214.92c-8.84-15.98-16.07-31.54-21.25-46.42L20.12 215.95zM288 359.67c-14.07 0-27.38-6.18-36.51-16.96-19.66-23.2-40.57-49.62-59.49-76.72v182l192 64V266c-18.92 27.09-39.82 53.52-59.49 76.72-9.13 10.77-22.44 16.95-36.51 16.95zm266.06-198.51L416 224v288l139.88-55.95A31.996 31.996 0 0 0 576 426.34V176.02c0-11.32-11.43-19.06-21.94-14.86z"></path></svg> ' . esc_html( __( 'Map', 'lddfw' ) ) . '</a> 
										</div>
									</div>';
        }
        $html .= '</div>';
        // Customer.
        
        if ( '' !== $billing_first_name ) {
            $html .= '<div class=" lddfw_box">
						<div class="row" id="lddfw_customer">
							<div class="col-12">
								<h3 class="lddfw_title">
								<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg> ' . esc_html( __( 'Customer', 'lddfw' ) ) . '</h3>' . $billing_first_name . ' ' . $billing_last_name . '
							</div>';
            if ( '' !== $billing_phone ) {
                $html .= '	<div class="col-12 mt-1">
								<span class="lddfw_label">' . esc_html( __( 'Phone', 'lddfw' ) ) . ': ' . $billing_phone . '</span>
							</div>';
            }
            if ( '' !== $billing_phone ) {
                $html .= '<div class="col-12 mt-2">
								<a class="btn btn-secondary btn-block " href="tel:' . esc_attr( $billing_phone ) . '"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="phone" class="svg-inline--fa fa-phone fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg> ' . esc_html( __( 'Call Customer', 'lddfw' ) ) . '</a>
							</div>';
            }
            $html .= '</div>
					</div>';
        }
        
        // Note.
        if ( '' !== $customer_note ) {
            $html .= '<div class="alert alert-info"><span>
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sticky-note" class="svg-inline--fa fa-sticky-note fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M312 320h136V56c0-13.3-10.7-24-24-24H24C10.7 32 0 42.7 0 56v400c0 13.3 10.7 24 24 24h264V344c0-13.2 10.8-24 24-24zm129 55l-98 98c-4.5 4.5-10.6 7-17 7h-6V352h128v6.1c0 6.3-2.5 12.4-7 16.9z"></path></svg> Note</span><p>' . $customer_note . '</p></div>';
        }
        // Items.
        $product_html = $this->lddfw_order_items( $order );
        $html .= $product_html;
        // Billing address.
        
        if ( '' !== $billing_first_name ) {
            $html .= '<div class="lddfw_box">
					<h3 class="lddfw_title"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="address-card" class="svg-inline--fa fa-address-card fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-352 96c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zm112 236.8c0 10.6-10 19.2-22.4 19.2H86.4C74 384 64 375.4 64 364.8v-19.2c0-31.8 30.1-57.6 67.2-57.6h5c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h5c37.1 0 67.2 25.8 67.2 57.6v19.2zM512 312c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16zm0-64c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16zm0-64c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16z"></path></svg> ' . esc_html( __( 'Billing Address', 'lddfw' ) ) . '</h3>
					' . $billing_full_name . $billing_address;
            $html .= '</div>';
        }
        
        // Driver.
        $html .= '<div class="lddfw_box">
			<h3 class="lddfw_title">
			<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"></path></svg>
			 ' . esc_html( __( 'Driver', 'lddfw' ) ) . '</h3>
			<div class="row">';
        $lddfw_driverid = get_post_meta( $lddfw_order_id, 'lddfw_driverid', true );
        $user = get_userdata( $lddfw_driverid );
        $lddfw_driver_name = $user->display_name;
        $lddfw_driver_note = get_post_meta( $lddfw_order_id, 'lddfw_driver_note', true );
        // Driver name
        if ( '' !== $lddfw_driver_name ) {
            $html .= '<div class="col-12">
							<p>' . esc_html( __( 'Name', 'lddfw' ) ) . ': ' . esc_html( $lddfw_driver_name ) . '</p>
						</div>';
        }
        // Driver note
        if ( '' !== $lddfw_driver_note ) {
            $html .= '<div class="col-12">
				<p>
				<span class="lddfw_label">' . esc_html( __( 'Driver note', 'lddfw' ) ) . ': ' . esc_html( $lddfw_driver_note ) . '</span>
				</p>
			</div>';
        }
        $html .= '</div>
			</div>';
        $html .= '</div>
        	</div>
       	</div></div> ';
        // Action screens.
        $html .= $this->lddfw_order_delivery_screen( $driver_id );
        $html .= $this->lddfw_order_thankyou_screen();
        // Action buttons.
        
        if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $order_status || get_option( 'lddfw_out_for_delivery_status', '' ) === 'wc-' . $order_status ) {
            $html .= '<div class="lddfw_footer_buttons">
					<div class="container">
						<div class="row">';
            $order_status_buttons_style = '';
            $html .= '<div class="col lddfw_order_status_buttons"  ' . $order_status_buttons_style . ' ><a href="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" id="lddfw_delivered_screen_btn" order_status="' . esc_attr( get_option( 'lddfw_delivered_status', '' ) ) . '" order_id="' . esc_attr( $lddfw_order_id ) . '" driver_id="' . esc_attr( $driver_id ) . '" class="btn btn-block btn-lg btn-success">' . esc_html( __( 'Delivered', 'lddfw' ) ) . '</a></div>
					 	 	<div class="col lddfw_order_status_buttons"  ' . $order_status_buttons_style . '><a href="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" id="lddfw_failed_delivered_screen_btn" order_status="' . esc_attr( get_option( 'lddfw_failed_attempt_status', '' ) ) . '" order_id="' . esc_attr( $lddfw_order_id ) . '" driver_id="' . esc_attr( $driver_id ) . '" class="btn  btn-lg  btn-block btn-danger">' . esc_html( __( 'Not Delivered', 'lddfw' ) ) . '</a></div>
			 			</div>
					</div>
				</div>
			';
        }
        
        return $html;
    }
    
    /**
     * Thank you page.
     *
     * @since 1.0.0
     * @return html
     */
    private function lddfw_order_thankyou_screen()
    {
        $html = '<div id="lddfw_thankyou" class="lddfw_lightbox" style="display:none">
		<div class="lddfw_lightbox_wrap">
		<div class="container">
		<form>
			<div class="row">
			<div class="col-12">
			<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="check-circle" class="svg-inline--fa fa-check-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm0 48c110.532 0 200 89.451 200 200 0 110.532-89.451 200-200 200-110.532 0-200-89.451-200-200 0-110.532 89.451-200 200-200m140.204 130.267l-22.536-22.718c-4.667-4.705-12.265-4.736-16.97-.068L215.346 303.697l-59.792-60.277c-4.667-4.705-12.265-4.736-16.97-.069l-22.719 22.536c-4.705 4.667-4.736 12.265-.068 16.971l90.781 91.516c4.667 4.705 12.265 4.736 16.97.068l172.589-171.204c4.704-4.668 4.734-12.266.067-16.971z"></path></svg>
			<h1>' . esc_html( __( 'Thank you!', 'lddfw' ) ) . '</h1>';
        $html .= '<a class="btn btn-block btn-lg btn-secondary" href="' . lddfw_drivers_page_url( 'lddfw_screen=out_for_delivery' ) . '">' . esc_html( __( 'View deliveries', 'lddfw' ) ) . '</a>
			</div>
			</div>
			</form>
		</div>
		</div>
		</div>';
        return $html;
    }
    
    /**
     * Delivery Photo.
     *
     * @since 1.3.0
     * @return html
     */
    private function lddfw_order_picture_screen()
    {
        $html = '<div style="display:none;position:relative" id="lddfw_delivery_photo" class="lddfw_photo_wrap screen_wrap">';
        
        if ( lddfw_is_free() ) {
            $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Get proof of delivery.', 'lddfw' ) ) . '
					<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Add a photo to order.', 'lddfw' ) ) . '
					<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Customer and admin can view the photo at any time.', 'lddfw' ) );
            $html .= lddfw_premium_feature_notice_content( $content );
        }
        
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Delivery Signature.
     *
     * @since 1.3.0
     * @return html
     */
    private function lddfw_order_signature_screen()
    {
        $html = '
				<div style="display:none" id="lddfw_delivery_signature" class="lddfw_signature_wrap screen_wrap">';
        
        if ( lddfw_is_free() ) {
            $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Get proof of delivery.', 'lddfw' ) ) . '
							<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Add a signature to order.', 'lddfw' ) ) . '
							<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( 'Customer and admin can view the signature at any time.', 'lddfw' ) );
            $html .= lddfw_premium_feature_notice_content( $content );
        }
        
        $html .= '</div>
			   ';
        return $html;
    }
    
    /**
     * Delivered page.
     *
     * @since 1.0.0
     * @param int $driver_id user id.
     * @return html
     */
    private function lddfw_order_delivered_screen( $driver_id )
    {
        $lddfw_delivery_dropoff_1 = '';
        $lddfw_delivery_dropoff_2 = '';
        $lddfw_delivery_dropoff_3 = '';
        $html = '
		<form id="lddfw_delivered_form" class="lddfw_delivered_form lddfw_delivery_form screen_wrap">
		<div class="delivery_header">
			<div class="container">
				<div class="row">
					<div class="col-12 lddfw_column"><h4>' . esc_html( __( 'Note', 'lddfw' ) ) . '</h4> </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
			<p>' . esc_html( __( 'Write your note for the customer.', 'lddfw' ) ) . '</p>';
        if ( '' !== $lddfw_delivery_dropoff_1 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio" checked class="custom-control-input" id="lddfw_delivery_dropoff_1" value="' . esc_attr( $lddfw_delivery_dropoff_1 ) . '" name="lddfw_delivery_dropoff_location">
				<label class="custom-control-label" for="lddfw_delivery_dropoff_1">' . $lddfw_delivery_dropoff_1 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_delivery_dropoff_2 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input" id="lddfw_delivery_dropoff_2" value="' . esc_attr( $lddfw_delivery_dropoff_2 ) . '" name="lddfw_delivery_dropoff_location">
				<label class="custom-control-label" for="lddfw_delivery_dropoff_2">' . $lddfw_delivery_dropoff_2 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_delivery_dropoff_3 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input" id="lddfw_delivery_dropoff_3" value="' . esc_attr( $lddfw_delivery_dropoff_3 ) . '" name="lddfw_delivery_dropoff_location">
				<label class="custom-control-label" for="lddfw_delivery_dropoff_3">' . $lddfw_delivery_dropoff_3 . '</label>
				</div>';
        }
        $html .= '<div class="custom-control custom-radio">
			<input type="radio" checked class="custom-control-input" id="lddfw_delivery_dropoff_other" name="lddfw_delivery_dropoff_location">
			<label class="custom-control-label" for="lddfw_delivery_dropoff_other">' . esc_html( __( 'Other', 'lddfw' ) ) . '</label>
			</div>
			<div id="lddfw_driver_delivered_note_wrap">
			<textarea class="form-control" id="lddfw_driver_delivered_note" placeholder="' . esc_attr( esc_html( __( 'Write your note', 'lddfw' ) ) ) . '" name="driver_note"></textarea>
			</div>
			</div>
			</div>
			</form>
		 ';
        return $html;
    }
    
    /**
     * Confirmation page.
     *
     * @since 1.0.0
     * @param int $id div id.
     * @return html
     */
    public function lddfw_confirmation_screen( $id )
    {
        $html = '<div id="' . $id . '" style="display:none" class="lddfw_confirmation lddfw_lightbox">
			<div class="lddfw_lightbox_wrap">
				<a href="#" class="lddfw_lightbox_close">×</a>
				<div class="container">
					<div class="row">
						<div class="col-12"><h2>' . esc_html( __( 'Are you sure?', 'lddfw' ) ) . '</h2></div>
						<div class="col-6"><button class="lddfw_ok btn btn-lg btn-block btn-primary">' . esc_html( __( 'Ok', 'lddfw' ) ) . '</button></div>
						<div class="col-6"><button class="lddfw_cancel btn btn-lg btn-block btn btn-secondary">' . esc_html( __( 'Cancel', 'lddfw' ) ) . '</button></div>
					</div>
				</div>
			</div>
		</div>';
        return $html;
    }
    
    /**
     * Proof of Delivery bar
     *
     * @since 1.3.0
     * @return html
     */
    public function lddfw_order_delivery_proof_bar()
    {
        $html = '<div class="delivery_proof_bar">
			<div class="row">
				<div class="col-4">
					<a href="lddfw_notes_wrap" btn="lddfw_driver_complete_btn" class="active delivery_proof_notes">
					<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
					<br>
					' . esc_html( __( 'Note', 'lddfw' ) ) . '
					</a>
				</div>
				<div class="col-4">
					<a href="lddfw_signature_wrap" btn="lddfw_driver_add_signature_btn" class=" delivery_proof_signature">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="signature" class="svg-inline--fa fa-signature fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M623.2 192c-51.8 3.5-125.7 54.7-163.1 71.5-29.1 13.1-54.2 24.4-76.1 24.4-22.6 0-26-16.2-21.3-51.9 1.1-8 11.7-79.2-42.7-76.1-25.1 1.5-64.3 24.8-169.5 126L192 182.2c30.4-75.9-53.2-151.5-129.7-102.8L7.4 116.3C0 121-2.2 130.9 2.5 138.4l17.2 27c4.7 7.5 14.6 9.7 22.1 4.9l58-38.9c18.4-11.7 40.7 7.2 32.7 27.1L34.3 404.1C27.5 421 37 448 64 448c8.3 0 16.5-3.2 22.6-9.4 42.2-42.2 154.7-150.7 211.2-195.8-2.2 28.5-2.1 58.9 20.6 83.8 15.3 16.8 37.3 25.3 65.5 25.3 35.6 0 68-14.6 102.3-30 33-14.8 99-62.6 138.4-65.8 8.5-.7 15.2-7.3 15.2-15.8v-32.1c.2-9.1-7.5-16.8-16.6-16.2z"></path></svg>
					<br>
					' . esc_html( __( 'Signature', 'lddfw' ) ) . '
					</a>
				</div>
				<div class="col-4">
					<a href="lddfw_photo_wrap" btn="lddfw_driver_add_photo_btn" class="delivery_proof_photo">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="camera" class="svg-inline--fa fa-camera fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M512 144v288c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48h88l12.3-32.9c7-18.7 24.9-31.1 44.9-31.1h125.5c20 0 37.9 12.4 44.9 31.1L376 96h88c26.5 0 48 21.5 48 48zM376 288c0-66.2-53.8-120-120-120s-120 53.8-120 120 53.8 120 120 120 120-53.8 120-120zm-32 0c0 48.5-39.5 88-88 88s-88-39.5-88-88 39.5-88 88-88 88 39.5 88 88z"></path></svg>
					<br>
					' . esc_html( __( 'Photo', 'lddfw' ) ) . '
					</a>
				</div>
			</div>
		</div>';
        return $html;
    }
    
    /**
     * Delivery page.
     *
     * @since 1.0.0
     * @param int $driver_id user id.
     * @return html
     */
    private function lddfw_order_delivery_screen( $driver_id )
    {
        global  $lddfw_order_id ;
        $html = '<div id="lddfw_delivery_screen" class="lddfw_lightbox" style="display:none">
		<div class="lddfw_lightbox_wrap">
		<a href="#" class="lddfw_lightbox_close">×</a>
		<div class="container">';
        $html .= $this->lddfw_order_signature_screen();
        $html .= $this->lddfw_order_picture_screen();
        $html .= $this->lddfw_order_failed_delivery_screen( $driver_id );
        $html .= $this->lddfw_order_delivered_screen( $driver_id );
        $html .= '</div><div class="lddfw_footer_buttons delivery_proof_buttons">
					<div class="container">
						<div class="row"> 
							<div class="col-12">
							' . $this->lddfw_order_delivery_proof_bar() . '
							</div>
							<div class="col-6"><a href="' . esc_url( admin_url( "admin-ajax.php" ) ) . '" delivery="" id="lddfw_driver_complete_btn" order_id="' . esc_attr( $lddfw_order_id ) . '" driver_id="' . esc_attr( $driver_id ) . '"  failed_status="' . esc_attr( get_option( 'lddfw_failed_attempt_status', '' ) ) . '"  delivered_status="' . esc_attr( get_option( 'lddfw_delivered_status', '' ) ) . '" class="lddfw_btn btn btn-block btn-lg btn-primary">' . esc_html( __( 'Done', 'lddfw' ) ) . '</a></div>
							<div class="col-6"><a href="#" id="lddfw_driver_cancel_btn" class="lddfw_btn btn btn-block btn-lg btn-secondary">' . esc_html( __( 'Cancel', 'lddfw' ) ) . '</a></div>
			 			</div>
					</div>
				</div>
		</div>
		</div>';
        $html .= $this->lddfw_confirmation_screen( 'lddfw_failed_delivery_confirmation' );
        $html .= $this->lddfw_confirmation_screen( 'lddfw_delivered_confirmation' );
        return $html;
    }
    
    /**
     * Failed delivery page.
     *
     * @since 1.0.0
     * @param int $driver_id user id.
     * @return html
     */
    private function lddfw_order_failed_delivery_screen( $driver_id )
    {
        $lddfw_failed_delivery_reason_1 = '';
        $lddfw_failed_delivery_reason_2 = '';
        $lddfw_failed_delivery_reason_3 = '';
        $lddfw_failed_delivery_reason_4 = '';
        $lddfw_failed_delivery_reason_5 = '';
        $html = '<form id="lddfw_failed_delivery_form" class="lddfw_failed_delivery_form lddfw_delivery_form lddfw_notes_wrap screen_wrap">
		<div class="delivery_header">
			<div class="container">
				<div class="row">
					<div class="col-12 lddfw_column"><h4>' . esc_html( __( 'Note', 'lddfw' ) ) . '</h4> </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
			<p>' . esc_html( __( 'Write your note for the customer.', 'lddfw' ) ) . '</p>';
        if ( '' !== $lddfw_failed_delivery_reason_1 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio"  class="custom-control-input" id="lddfw_delivery_failed_1" value="' . esc_attr( $lddfw_failed_delivery_reason_1 ) . '" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_1">' . $lddfw_failed_delivery_reason_1 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_failed_delivery_reason_2 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio"  class="custom-control-input" id="lddfw_delivery_failed_2" value="' . esc_attr( $lddfw_failed_delivery_reason_2 ) . '" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_2">' . $lddfw_failed_delivery_reason_2 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_failed_delivery_reason_3 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio"  class="custom-control-input" id="lddfw_delivery_failed_3" value="' . esc_attr( $lddfw_failed_delivery_reason_3 ) . '" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_3">' . $lddfw_failed_delivery_reason_3 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_failed_delivery_reason_4 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio"  class="custom-control-input" id="lddfw_delivery_failed_4" value="' . esc_attr( $lddfw_failed_delivery_reason_4 ) . '" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_4">' . $lddfw_failed_delivery_reason_4 . '</label>
				</div>';
        }
        if ( '' !== $lddfw_failed_delivery_reason_5 ) {
            $html .= '<div class="custom-control custom-radio">
				<input type="radio"  class="custom-control-input" id="lddfw_delivery_failed_5" value="' . esc_attr( $lddfw_failed_delivery_reason_5 ) . '" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_5">' . $lddfw_failed_delivery_reason_5 . '</label>
				</div>';
        }
        $html .= '<div class="custom-control custom-radio">
				<input type="radio" checked class="custom-control-input" id="lddfw_delivery_failed_6" name="lddfw_delivery_failed_reason">
				<label class="custom-control-label" for="lddfw_delivery_failed_6">' . esc_html( __( 'Other issues.', 'lddfw' ) ) . '</label>
			</div>
			<div id="lddfw_driver_note_wrap">
				<textarea class="form-control" id="lddfw_driver_note" placeholder="' . esc_attr( esc_html( __( 'Write your note', 'lddfw' ) ) ) . '" name="lddfw_driver_note"></textarea>
			</div>
			</div>
			</div>
			</form>';
        return $html;
    }
    
    /**
     * Order items.
     *
     * @since 1.0.0
     * @param object $order order data.
     * @return html
     */
    private function lddfw_order_items( $order )
    {
        $items = $order->get_items();
        $total = $order->get_total();
        $discount_total = $order->get_discount_total();
        $currency_symbol = get_woocommerce_currency_symbol();
        
        if ( !empty($items) ) {
            $product_html = '<div class="lddfw_box">
	<h3 class="lddfw_title"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shopping-cart" class="svg-inline--fa fa-shopping-cart fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528.12 301.319l47.273-208C578.806 78.301 567.391 64 551.99 64H159.208l-9.166-44.81C147.758 8.021 137.93 0 126.529 0H24C10.745 0 0 10.745 0 24v16c0 13.255 10.745 24 24 24h69.883l70.248 343.435C147.325 417.1 136 435.222 136 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-15.674-6.447-29.835-16.824-40h209.647C430.447 426.165 424 440.326 424 456c0 30.928 25.072 56 56 56s56-25.072 56-56c0-22.172-12.888-41.332-31.579-50.405l5.517-24.276c3.413-15.018-8.002-29.319-23.403-29.319H218.117l-6.545-32h293.145c11.206 0 20.92-7.754 23.403-18.681z"></path></svg> ' . esc_html( __( 'Products', 'lddfw' ) ) . '</h3>
	<table class="table lddfw_order_products_tbl" >
	<tbody>
	<tr>
	<th align="center" >' . esc_html( __( 'Item', 'lddfw' ) ) . '</th>
	<td></td>
	<th align="center" class="lddfw_total_col" >' . esc_html( __( 'Total', 'lddfw' ) ) . '</th>
	</tr>';
            foreach ( $items as $item_id => $item_data ) {
                $product_id = $item_data['product_id'];
                $variation_id = $item_data['variation_id'];
                $product_description = '';
                $product = false;
                $product_image = '';
                if ( null !== $product_id && 0 !== $product_id ) {
                    
                    if ( 0 !== $variation_id ) {
                        $product = wc_get_product( $variation_id );
                        $product_description = $product->get_description();
                        $product_image = $product->get_image();
                    } else {
                        $product = wc_get_product( $product_id );
                        $product_description = $product->get_short_description();
                        $product_image = $product->get_image();
                    }
                
                }
                $item_name = $item_data['name'];
                $item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
                $item_total = wc_get_order_item_meta( $item_id, '_line_total', true );
                $item_subtotal = wc_get_order_item_meta( $item_id, '_line_subtotal', true );
                $discount = '';
                if ( $item_subtotal > $item_total ) {
                    $discount = '<br>' . ($item_subtotal - $item_total) . ' discount';
                }
                $unit_price = $item_total / $item_quantity;
                $product_html .= '<tr class="lddfw_items">
				<td colspan="2">';
                $product_html .= $item_name . '<br>X ' . $item_quantity . '</td>
		<td class="lddfw_total_col">' . $currency_symbol . $item_subtotal . '</td>
		</tr>';
            }
            $product_html .= '</table></div><table class="table lddfw_order_total_tbl">';
            if ( '' !== $discount_total ) {
                $product_html .= '<tr><th colspan="2">' . esc_html( __( 'Discount', 'lddfw' ) ) . '</th> <td class="lddfw_total_col">-' . $currency_symbol . $discount_total . '</td>';
            }
            foreach ( $order->get_items( 'shipping' ) as $item_id => $line_item ) {
                // Get the data in an unprotected array.
                $shipping_data = $line_item->get_data();
                $shipping_name = $shipping_data['name'];
                $shipping_meta = $shipping_data['meta_data'];
                $shipping_total = $shipping_data['total'];
                $shipping_meta_html = '';
                foreach ( $shipping_meta as $meta_id => $meta_line_item ) {
                    $shipping_meta_html .= '<br>' . $meta_line_item->key . ': ';
                    
                    if ( is_array( $meta_line_item->value ) ) {
                        foreach ( $meta_line_item->value as $shipping_meta_value ) {
                            $shipping_meta_html .= $shipping_meta_value;
                        }
                    } else {
                        $shipping_meta_html .= $meta_line_item->value;
                    }
                
                }
                $product_html .= '<tr class=\'lddfw_items\'>
		<th colspan=\'2\'>' . esc_html( __( 'Shipping', 'lddfw' ) ) . '<br><i>' . esc_html( __( 'via', 'lddfw' ) ) . ' ' . $shipping_name . $shipping_meta_html . '</i></th>
		<td class=\'lddfw_total_col\'>' . $currency_symbol . $shipping_total . '</td>
		</tr>';
            }
            foreach ( $order->get_items( 'fee' ) as $item_id => $line_item ) {
                $fee_data = $line_item->get_data();
                $fee_meta = $fee_data['meta_data'];
                $fee_total = $fee_data['total'];
                $fee_name = $fee_data['name'];
                $feemeta_html = '';
                foreach ( $fee_meta as $meta_id => $meta_lineitem ) {
                    $feemeta_html .= '<br>' . $meta_lineitem->key . ': ' . $meta_lineitem->value;
                }
                $product_html .= '<tr class="lddfw_items">
		<th colspan="2">' . $fee_name . $feemeta_html . '</th>
		<td class="lddfw_total_col">' . $currency_symbol . $fee_total . '</td>
		</tr>';
            }
            
            if ( '' !== $total ) {
                $product_html .= '<tr> <th colspan="2">' . __( 'Total', 'lddfw' ) . '</th>' . ' <td class="lddfw_total_col">' . $currency_symbol . $total . '</td>';
                $refund = $order->get_total_refunded();
                
                if ( '' !== $refund ) {
                    $product_html .= '<tr style="color:#ca0303"> <th colspan="2">' . __( 'Refund', 'lddfw' ) . '</th>' . ' <td class="lddfw_total_col">-' . $currency_symbol . $refund . '</td>';
                    $product_html .= '<tr> <th colspan="2">' . __( 'Net Total', 'lddfw' ) . '</th>' . ' <td class="lddfw_total_col">' . $currency_symbol . ($total - $refund) . '</td>';
                }
            
            }
            
            $product_html .= '</tbody></table>';
            return $product_html;
        }
    
    }
    
    /**
     * Get the state.
     *
     * @since 1.5.0
     * @param string $state state name/code.
     * @return string
     */
    public static function lddfw_states( $state )
    {
        if ( in_array( 'comunas-de-chile-para-woocommerce', LDDFW_PLUGINS ) ) {
            // Get chile states.
            
            if ( function_exists( 'comunas_de_chile' ) ) {
                $chile_states = comunas_de_chile( "" );
                if ( array_key_exists( 'CL', $chile_states ) ) {
                    if ( array_key_exists( $state, $chile_states['CL'] ) ) {
                        $state = $chile_states['CL'][$state];
                    }
                }
            }
        
        }
        return $state;
    }

}