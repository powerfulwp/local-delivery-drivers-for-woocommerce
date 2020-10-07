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
class LDDFW_Order {
	/**
	 * Order page.
	 *
	 * @since 1.0.0
	 * @param object $order order object.
	 * @param int    $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_order_page( $order, $driver_id ) {
		global $lddfw_order_id;
		$date_format = lddfw_date_format( 'date' );
		$time_format = lddfw_date_format( 'time' );

		$order_status             = $order->get_status();
		$order_status_name        = wc_get_order_status_name( $order_status );
		$date_created             = $order->get_date_created()->format( $date_format );
		$discount_total           = $order->get_discount_total();
		$shipping_total           = $order->get_shipping_total();
		$total                    = $order->get_total();
		$currency_symbol          = get_woocommerce_currency_symbol();
		$shipping_address_map_url = $order->get_shipping_address_map_url();
		$billing_first_name       = $order->get_billing_first_name();
		$billing_last_name        = $order->get_billing_last_name();
		$billing_company          = $order->get_billing_company();
		$billing_address_1        = $order->get_billing_address_1();
		$billing_address_2        = $order->get_billing_address_2();
		$billing_city             = $order->get_billing_city();
		$billing_state            = $order->get_billing_state();
		$billing_country          = WC()->countries->countries[ $order->get_billing_country() ];
		$billing_postcode         = $order->get_billing_postcode();

		$billing_phone       = $order->get_billing_phone();
		$shipping_first_name = $order->get_shipping_first_name();
		$shipping_last_name  = $order->get_shipping_last_name();
		$shipping_company    = $order->get_shipping_company();
		$shipping_address_1  = $order->get_shipping_address_1();
		$shipping_address_2  = $order->get_shipping_address_2();
		$shipping_city       = $order->get_shipping_city();
		$shipping_state      = $order->get_shipping_state();
		$shipping_postcode   = $order->get_shipping_postcode();
		$shipping_country    = WC()->countries->countries[ $order->get_shipping_country() ];

		$customer_note  = $order->get_customer_note();
		$payment_method = $order->get_payment_method();

		// Format billing address.
		$billing_full_name = $billing_first_name . ' ' . $billing_last_name . '<br>';
		if ( '' !== $billing_company ) {
			$billing_full_name .= $billing_company . '<br>';
		}

		$billing_address = $billing_address_1 . '<br>';
		if ( '' !== $billing_address_2 ) {
			$billing_address .= $billing_address_2 . '<br>';
		}
		$billing_address .= $billing_city;
		if ( '' !== $billing_state ) {
			$billing_address .= ', ' . $billing_state;
		}
		if ( '' !== $billing_postcode ) {
			$billing_address .= ', ' . $billing_postcode;
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

			$shipping_address = $shipping_address_1 . ' <br>';
			if ( '' !== $shipping_address_2 ) {
				$shipping_address .= $shipping_address_2 . ' <br>';
			}
			$shipping_address .= $shipping_city;
			if ( '' !== $shipping_state ) {
				$shipping_address .= ', ' . $shipping_state . ' ';
			}
			if ( '' !== $shipping_postcode ) {
				$shipping_address .= ', ' . $shipping_postcode . ' ';
			}
			if ( '' !== $shipping_country ) {
				$shipping_address .= '<br>' . $shipping_country;
			}
		} else {
			$shipping_full_name = $billing_full_name;
			$shipping_address   = $billing_address;
		}

		// Waze buttons.
		$shipping_direction_address = str_replace( '<br>', '', $shipping_address );
		$shipping_direction_address = str_replace( ',', '', $shipping_direction_address );
		$waze                       = rawurlencode( $shipping_direction_address );
		$shipping_direction_address = str_replace( ' ', '+', $shipping_direction_address );

		$store         = new LDDFW_Store();
		$store_address = $store->lddfw_store_address( 'map_address' );
		$origin        = get_post_meta( $lddfw_order_id, 'lddfw_order_origin', true );
		$route         = get_post_meta( $lddfw_order_id, 'lddfw_order_route', true );

		$failed_date    = get_post_meta( $lddfw_order_id, 'lddfw_failed_attempt_date', true );
		$delivered_date = get_post_meta( $lddfw_order_id, 'lddfw_delivered_date', true );

		$html = "<div class='lddfw_page_content'>";

		if ( '' === $origin ) {
			$origin = $store_address;
		}

		$lddfw_google_api_key        = get_option( 'lddfw_google_api_key', '' );
		$lddfw_dispatch_phone_number = get_option( 'lddfw_dispatch_phone_number', '' );

		// Map.
		if ( '' !== $lddfw_google_api_key ) {
			$html .= "
		<div class='container-fluid p-0' >
			<iframe
				width=\"100%\"
				height=\"350\"
				frameborder=\"0\"
				style=\"border:0\"
				src=\"https://www.google.com/maps/embed/v1/directions?origin=" . $origin . '&destination=' . $shipping_direction_address . '&key=' . $lddfw_google_api_key . '"
				allowfullscreen>
			</iframe>
		</div>
		';
		}
		// Wase button.
		$html .= "<div class='container lddfw_map_buttons' >
			<div class='row'>
				<div class='col-12'>
					<a class='lddfw_waze btn btn-secondary  btn-block' href=\"https://waze.com/ul?q=" . $waze . "&nevigate=yes\"><i class='fab fa-waze'></i> " . esc_html( __( 'Navigate', 'lddfw' ) ) . '</a>
				</div>
			</div>
		</div>';

		$html .= "
	<div class='container' id='lddfw_order'>
		<div class='row'>
			<div class='col-12'>
			</div>
			<div class='col-12'>";

		// Orders info.
		$html .= "	<div class='lddfw_box'>
					<div class='title'><i class='fas fa-info-circle'></i> Info</div>
					<div class='row'>
					<div class='col-12'>
						<p id='lddfw_order_date'>" . esc_html( __( 'Date', 'lddfw' ) ) . ': ' . $date_created . "</p>
					</div> 
					<div class='col-12'>
						<p id='lddfw_order_status'>" . esc_html( __( 'Status', 'lddfw' ) ) . ': ' . $order_status_name . '</p>
					</div>';

		if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $order_status && '' !== $failed_date ) {
			$html .= "<div class='col-12'>
						<p id='lddfw_order_status_date'>" . esc_html( __( 'Failed date', 'lddfw' ) ) . ': ' . date( $date_format . ' ' . $time_format, strtotime( $failed_date ) ) . '</p>
			 </div>';
		}

		if ( get_option( 'lddfw_delivered_status', '' ) === 'wc-' . $order_status && '' !== $delivered_date ) {
			$html .= "<div class='col-12'>
						<p id='lddfw_order_status_date'>" . esc_html( __( 'Delivered date', 'lddfw' ) ) . ': ' . date( $date_format . ' ' . $time_format, strtotime( $delivered_date ) ) . '</p>
			 </div>';
		}

		if ( '' !== $payment_method ) {
			$html .= "<div class='col-12'>
						<p id='lddfw_order_payment_method'>"
				. esc_html( __( 'Payment method', 'lddfw' ) ) . ': ' . $payment_method .
				'</p>
					</div>';
		}
		$html .= "<div class='col-12'>
						<p id='lddfw_order_total'>"
			. esc_html( __( 'Total', 'lddfw' ) ) . ': ' . $currency_symbol . $total .
			'</p>
					</div>';
		if ( '' !== $lddfw_dispatch_phone_number ) {
			$html .= "<div class='col-12  mt-2'>
						<a class='btn btn-block btn-secondary'  href='tel:" . esc_attr( get_option( 'lddfw_dispatch_phone_number', '' ) ) . "'><i class='fas fa-phone-alt'></i> " . esc_html( __( 'Call Dispatch', 'lddfw' ) ) . '</a>
					</div>';
		}

		$html .= '</div>
			</div>';

		// Shipping address.
		$html .= "<div id='lddfw_shipping_address' class='lddfw_box'>
					<h3 class='title'><i class='fas fa-map-marker-alt'></i>  " .
					esc_html( __( 'Shipping Address', 'lddfw' ) ) . "</h3>
					$shipping_full_name
					$shipping_address";
		// Map button.
		if ( '' === $lddfw_google_api_key ) {
			$html .= "<div class='row' id='lddfw_navigation_buttons'>
										<div class='col-12  mt-2'>
											<a class='btn btn-secondary btn-block' href='https://www.google.com/maps/search/?api=1&query=" . $shipping_direction_address . "'><i class=\"fas fa-map-marker-alt\"></i> " . esc_html( __( 'Map', 'lddfw' ) ) . '</a> 
										</div>
									</div>';
		}
		$html .= '</div>';

		// Customer.
		if ( '' !== $billing_first_name ) {
			$html .= "<div class=' lddfw_box'>
						<div class='row' id='lddfw_customer'>
							<div class='col-12'>
								<h3 class='lddfw_title'><i class='fas fa-user'></i> " . esc_html( __( 'Customer', 'lddfw' ) ) . '</h3>'
				. $billing_first_name . ' ' . $billing_last_name . '
							</div>';

			if ( '' !== $billing_phone ) {
				$html .= "	<div class='col-12 mt-1'>
								<span class='lddfw_label'>" . esc_html( __( 'Phone', 'lddfw' ) ) . ": $billing_phone</span>
							</div>
							<div class='col-12  mt-2'>
								<a class='btn btn-secondary btn-block ' href='tel:$billing_phone'><i class='fas fa-phone-alt'></i> " . esc_html( __( 'Call customer', 'lddfw' ) ) . '</a>
							</div>';
			}

			$html .= '</div>
					</div>';
		}

		// Note.
		if ( '' !== $customer_note ) {
			$html .= "<div class='alert alert-info'><span><i class='fas fa-sticky-note'></i> Note</span><p>$customer_note</p></div>";
		}

		// Items.
		$product_html = $this->lddfw_order_items( $order );
		$html        .= $product_html;

		// Billing address.
		if ( '' !== $billing_first_name ) {
			$html .= "<div class='lddfw_box'>
					<h3 class='lddfw_title'><i class='fas fa-address-card'></i> " . esc_html( __( 'Billing Address', 'lddfw' ) ) . "</h3>
					$billing_full_name
					$billing_address
				</div>";
		}

		$html .= '</div>
        	</div>
       	</div></div> ';

		// Action screens.
		$html .= $this->lddfw_order_delivered_screen( $driver_id );
		$html .= $this->lddfw_order_failed_delivery_screen( $driver_id );
		$html .= $this->lddfw_order_thankyou_screen();

		// Action buttons.
		if ( get_option( 'lddfw_failed_attempt_status', '' ) === 'wc-' . $order_status || get_option( 'lddfw_out_for_delivery_status', '' ) === 'wc-' . $order_status ) {
			$html .= "<div class='lddfw_footer_buttons'>
					<div class='container'>
						<div class='row'> 
						 <div class='col-12'><a href='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' id='lddfw_delivered_screen_btn' order_status='" . esc_attr( get_option( 'lddfw_delivered_status', '' ) ) . "' order_id='$lddfw_order_id' driver_id='$driver_id' class='btn btn-block btn-lg btn-success'>" . esc_html( __( 'Delivered', 'lddfw' ) ) . "</a></div>
					 	 <div class='col-12'><a href='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' id='lddfw_failed_delivered_screen_btn' order_status='" . esc_attr( get_option( 'lddfw_failed_attempt_status', '' ) ) . "' order_id='$lddfw_order_id' driver_id='$driver_id' class='btn  btn-lg  btn-block btn-danger'>" . esc_html( __( 'Not Delivered', 'lddfw' ) ) . '</a></div>
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
	private function lddfw_order_thankyou_screen() {
		$html = " <div id='lddfw_thankyou' class='lddfw_lightbox' style='display:none'>
	<div class='lddfw_lightbox_wrap'>
	<div class='container'>
	<form>
		<div class='row'>
		<div class='col-12'>
		<i class='far fa-check-circle'></i>
		<h1>" . esc_html( __( 'Thank you!', 'lddfw' ) ) . "</h1>
		<div id='lddfw_next_delivery'></div>
		<a class='btn btn-block btn-lg btn-secondary' href='" . lddfw_drivers_page_url() . 'lddfw_action=out_for_delivery' . "'>" . esc_html( __( 'View deliveries', 'lddfw' ) ) . '</a>
		</div>
		</div>
		</form>
	</div>
	</div>
	</div>';
		return $html;
	}

	/**
	 * Delivered page.
	 *
	 * @since 1.0.0
	 * @param int $driver_id user id.
	 * @return html
	 */
	private function lddfw_order_delivered_screen( $driver_id ) {
		global $lddfw_order_id;

		$html = "<div id='lddfw_delivered' class='lddfw_lightbox' style='display:none'>
	<div class='lddfw_lightbox_wrap'>
	<div class='container'>
	<a href='#' class='lddfw_lightbox_close'>×</a>
	<form id='lddfw_delivered_form' class='lddfw_delivery_form'>
		<div class='row'>
		<div class='col-12'>
		<h1>" . esc_html( __( 'Where did you leave the order?', 'lddfw' ) ) . '</h1>';

		$lddfw_delivery_dropoff_1 = get_option( 'lddfw_delivery_dropoff_1', '' );
		if ( '' !== $lddfw_delivery_dropoff_1 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio' checked class='custom-control-input' id='lddfw_delivery_dropoff_1' value='" . $lddfw_delivery_dropoff_1 . "' name='lddfw_delivery_dropoff_location'>
			<label class='custom-control-label' for='lddfw_delivery_dropoff_1'>" . $lddfw_delivery_dropoff_1 . '</label>
			</div>';
		}

		$lddfw_delivery_dropoff_2 = get_option( 'lddfw_delivery_dropoff_2', '' );
		if ( '' !== $lddfw_delivery_dropoff_2 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio' class='custom-control-input' id='lddfw_delivery_dropoff_2' value='" . $lddfw_delivery_dropoff_2 . "' name='lddfw_delivery_dropoff_location'>
			<label class='custom-control-label' for='lddfw_delivery_dropoff_2'>" . $lddfw_delivery_dropoff_2 . '</label>
			</div>';
		}

		$lddfw_delivery_dropoff_3 = get_option( 'lddfw_delivery_dropoff_3', '' );
		if ( '' !== $lddfw_delivery_dropoff_3 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio' class='custom-control-input' id='lddfw_delivery_dropoff_3' value='" . $lddfw_delivery_dropoff_3 . "' name='lddfw_delivery_dropoff_location'>
			<label class='custom-control-label' for='lddfw_delivery_dropoff_3'>" . $lddfw_delivery_dropoff_3 . '</label>
			</div>';
		}

		$html .= "<div class='custom-control custom-radio'>
		<input type='radio' checked class='custom-control-input' id='lddfw_delivery_dropoff_other' name='lddfw_delivery_dropoff_location'>
		<label class='custom-control-label' for='lddfw_delivery_dropoff_other'>" . esc_html( __( 'Other', 'lddfw' ) ) . "</label>
		</div>

		<div id='lddfw_driver_delivered_note_wrap'>
		<label id='lddfw_driver_note_label' for='lddfw_driver_delivered_note'>" . esc_html( __( 'Note', 'lddfw' ) ) . ":</label>
		<textarea class='form-control' id='lddfw_driver_delivered_note' name='driver_note'></textarea>
		</div>

		<a href='" . admin_url( 'admin-ajax.php' ) . "' id='lddfw_driver_delivered_note_btn' order_id='$lddfw_order_id' driver_id='$driver_id' order_status='" . get_option( 'lddfw_delivered_status', '' ) . "' class='btn btn-block btn-lg btn-primary'>" . esc_html( __( 'Send', 'lddfw' ) ) . '</a>
		</div>
		</div>
		</form>
	</div>
	</div>
	</div>';
		$html .= $this->lddfw_confirmation_screen( 'lddfw_delivered_confirmation' );
		return $html;
	}

	/**
	 * Confirmation page.
	 *
	 * @since 1.0.0
	 * @param int $id div id.
	 * @return html
	 */
	public function lddfw_confirmation_screen( $id ) {
		$html = '<div id="' . $id . '" style="display:none" class="lddfw_confirmation lddfw_lightbox">
		<div class="lddfw_lightbox_wrap">
		<a href="#" class="lddfw_lightbox_close">×</a>
				<div class="container">
					<div class="row">
						<div class="col-12"><h2>' . esc_html( __( 'Are you sure?', 'lddfw' ) ) . '</h2></div>
						<div class="col-6"><button class="lddfw_cancel btn btn-lg btn-block btn btn-secondary">' . esc_html( __( 'Cancel', 'lddfw' ) ) . '</button></div>
						<div class="col-6"><button class="lddfw_ok btn btn-lg btn-block btn-primary">' . esc_html( __( 'Ok', 'lddfw' ) ) . '</button></div>
					</div>
				</div>
				</div>
				</div>';
		return $html;
	}
	/**
	 * Failed delivery page.
	 *
	 * @since 1.0.0
	 * @param int $driver_id user id.
	 * @return html
	 */
	private function lddfw_order_failed_delivery_screen( $driver_id ) {
		global $lddfw_order_id;

		$lddfw_failed_delivery_reason_1 = get_option( 'lddfw_failed_delivery_reason_1', '' );
		$lddfw_failed_delivery_reason_2 = get_option( 'lddfw_failed_delivery_reason_2', '' );
		$lddfw_failed_delivery_reason_3 = get_option( 'lddfw_failed_delivery_reason_3', '' );
		$lddfw_failed_delivery_reason_4 = get_option( 'lddfw_failed_delivery_reason_4', '' );
		$lddfw_failed_delivery_reason_5 = get_option( 'lddfw_failed_delivery_reason_5', '' );

		$html = "<div id='lddfw_failed_delivery' class='lddfw_lightbox' style='display:none'>
	<div class='lddfw_lightbox_wrap'>
	<a href='#' class='lddfw_lightbox_close'>×</a>
	<div class='container'>
	<form id='lddfw_failed_delivery_form' class='lddfw_delivery_form'>
		<div class='row'>
		<div class='col-12'>
		<h1>" . esc_html( __( 'Why did the attempted delivery fail?', 'lddfw' ) ) . '</h1>
		<p>Please select an option or write a note and click the send button</p>';
		if ( '' !== $lddfw_failed_delivery_reason_1 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio'  class='custom-control-input' id='lddfw_delivery_failed_1' value='" . $lddfw_failed_delivery_reason_1 . "' name='lddfw_delivery_failed_reason'>
			<label class='custom-control-label' for='lddfw_delivery_failed_1'>" . $lddfw_failed_delivery_reason_1 . '</label>
			</div>';
		}

		if ( '' !== $lddfw_failed_delivery_reason_2 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio'  class='custom-control-input' id='lddfw_delivery_failed_2' value='" . $lddfw_failed_delivery_reason_2 . "' name='lddfw_delivery_failed_reason'>
			<label class='custom-control-label' for='lddfw_delivery_failed_2'>" . $lddfw_failed_delivery_reason_2 . '</label>
			</div>';
		}

		if ( '' !== $lddfw_failed_delivery_reason_3 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio'  class='custom-control-input' id='lddfw_delivery_failed_3' value='" . $lddfw_failed_delivery_reason_3 . "' name='lddfw_delivery_failed_reason'>
			<label class='custom-control-label' for='lddfw_delivery_failed_3'>" . $lddfw_failed_delivery_reason_3 . '</label>
			</div>';
		}

		if ( '' !== $lddfw_failed_delivery_reason_4 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio'  class='custom-control-input' id='lddfw_delivery_failed_4' value='" . $lddfw_failed_delivery_reason_4 . "' name='lddfw_delivery_failed_reason'>
			<label class='custom-control-label' for='lddfw_delivery_failed_4'>" . $lddfw_failed_delivery_reason_4 . '</label>
			</div>';
		}

		if ( '' !== $lddfw_failed_delivery_reason_5 ) {
			$html .= "<div class='custom-control custom-radio'>
			<input type='radio'  class='custom-control-input' id='lddfw_delivery_failed_5' value='" . $lddfw_failed_delivery_reason_5 . "' name='lddfw_delivery_failed_reason'>
			<label class='custom-control-label' for='lddfw_delivery_failed_5'>" . $lddfw_failed_delivery_reason_5 . '</label>
			</div>';
		}

		$html .= "<div class='custom-control custom-radio'>
		<input type='radio' checked class='custom-control-input' id='lddfw_delivery_failed_6' name='lddfw_delivery_failed_reason'>
		<label class='custom-control-label' for='lddfw_delivery_failed_6'>" . esc_html( __( 'Other issues', 'lddfw' ) ) . "</label>
		</div>
		<div id='lddfw_driver_note_wrap'>
		<label id='lddfw_driver_note_label' for='lddfw_driver_note'>" . esc_html( __( 'Note', 'lddfw' ) ) . ":</label>
		<textarea class='form-control' id='lddfw_driver_note' name='lddfw_driver_note'></textarea>
		</div>
		<a href='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' id='lddfw_driver_note_btn' order_id='$lddfw_order_id' driver_id='$driver_id'  order_status='" . get_option( 'lddfw_failed_attempt_status', '' ) . "' class='btn btn-block btn-lg btn-primary'>" . esc_html( __( 'Send', 'lddfw' ) ) . '</a>
		</div>
		</div>
		</form>
	</div>
	</div>
	</div>';
		$html .= $this->lddfw_confirmation_screen( 'lddfw_failed_delivery_confirmation' );
		return $html;
	}
	/**
	 * Order items.
	 *
	 * @since 1.0.0
	 * @param object $order order data.
	 * @return html
	 */
	private function lddfw_order_items( $order ) {
		$items           = $order->get_items();
		$total           = $order->get_total();
		$discount_total  = $order->get_discount_total();
		$currency_symbol = get_woocommerce_currency_symbol();
		if ( ! empty( $items ) ) {

			$product_html = "<div class='lddfw_box'>
	<div class='lddfw_title'><i class='fas fa-shopping-cart'></i> " . esc_html( __( 'Products', 'lddfw' ) ) . "</div>
	<table class='table' >
	<tbody>
	<tr>
	<th align='center' >" . esc_html( __( 'Item', 'lddfw' ) ) . "</th>
	<td></td>
	<th align='center' class='lddfw_total_col' >" . esc_html( __( 'Total', 'lddfw' ) ) . '</th>
	</tr>';

			foreach ( $items as $item_id  => $item_data ) {

				$product_id          = $item_data['product_id'];
				$variation_id        = $item_data['variation_id'];
				$product_description = '';
				$product             = false;

				if ( null !== $product_id && 0 !== $product_id ) {
					if ( 0 !== $variation_id ) {
						$product             = wc_get_product( $variation_id );
						$product_description = $product->get_description();
					} else {
						$product             = wc_get_product( $product_id );
						$product_description = $product->get_short_description();
					}
				}

				$item_name     = $item_data['name'];
				$item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
				$item_total    = wc_get_order_item_meta( $item_id, '_line_total', true );
				$item_subtotal = wc_get_order_item_meta( $item_id, '_line_subtotal', true );

				$discount = '';
				if ( $item_subtotal > $item_total ) {
					$discount = '<br>' . ( $item_subtotal - $item_total ) . ' discount';
				}
				$unit_price = $item_total / $item_quantity;

				$product_html .= "<tr class='lddfw_items'>
		<td colspan=2>" . $item_name .
					'<br>X ' . $item_quantity . "</td>
		<td class='lddfw_total_col'>" . $currency_symbol . $item_subtotal . '</td>
		</tr>';
			}
			$product_html .= "</table></div><table class='table lddfw_order_total_tbl'>";
			if ( '' !== $discount_total ) {
				$product_html .= "<tr><th colspan='2'>" . esc_html( __( 'Discount', 'lddfw' ) ) . '</th>' . " <td class='lddfw_total_col'>-" . $currency_symbol . $discount_total . '</td>';
			}
			foreach ( $order->get_items( 'shipping' ) as $item_id => $line_item ) {
				// Get the data in an unprotected array.
				$shipping_data      = $line_item->get_data();
				$shipping_name      = $shipping_data['name'];
				$shipping_meta      = $shipping_data['meta_data'];
				$shipping_total     = $shipping_data['total'];
				$shipping_meta_html = '';
				foreach ( $shipping_meta as $meta_id => $line_item ) {
					$shipping_meta_html .= '<br>' . $line_item->key . ': ' . $line_item->value;
				}

				$product_html .= '<tr class=\'lddfw_items\'>
		<th colspan=\'2\'>' . esc_html( __( 'Shipping', 'lddfw' ) ) . '<br><i>' . esc_html( __( 'via', 'lddfw' ) ) . ' ' . $shipping_name . $shipping_meta_html . '</i></th>
		<td class=\'lddfw_total_col\'>' . $currency_symbol . $shipping_total . '</td>
		</tr>';
			}

			foreach ( $order->get_items( 'fee' ) as $item_id => $line_item ) {
				$fee_data  = $line_item->get_data();
				$fee_meta  = $fee_data['meta_data'];
				$fee_total = $fee_data['total'];
				$fee_name  = $fee_data['name'];

				$feemeta_html = '';
				foreach ( $fee_meta as $meta_id => $meta_lineitem ) {
					$feemeta_html .= '<br>' . $meta_lineitem->key . ': ' . $meta_lineitem->value;
				}

				$product_html .= "<tr class='lddfw_items'>
		<th colspan='2'>" . $fee_name . $feemeta_html . "</th>
		<td class='lddfw_total_col'>" . $currency_symbol . $fee_total . '</td>
		</tr>';
			}

			if ( '' !== $total ) {
				$product_html .= "<tr> <th colspan='2'>" . __( 'Total', 'lddfw' ) . '</th>' . " <td class='lddfw_total_col'>" . $currency_symbol . $total . '</td>';
			}

			$product_html .= '</tbody></table>';
			return $product_html;
		}
	}
}
