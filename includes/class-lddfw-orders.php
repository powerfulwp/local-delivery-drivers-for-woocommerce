<?php
/**
 * Orders page.
 *
 * All the orders functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */

/**
 * Orders class.
 *
 * All the orders functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Orders {


	/**
	 * Orders count query.
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_orders_count_query( $driver_id ) {
		global $wpdb;

		$query = $wpdb->get_results(
			$wpdb->prepare(
				"select post_status , count(*) as orders from {$wpdb->prefix}posts p
				inner join {$wpdb->prefix}postmeta pm on p.id=pm.post_id and pm.meta_key = 'lddfw_driverid' and pm.meta_value = %s
				left join {$wpdb->prefix}postmeta pm1 on p.id=pm1.post_id and pm1.meta_key = 'lddfw_delivered_date'
				where post_type='shop_order' and
				(
					post_status in (%s,%s,%s) or
					( post_status = %s and CAST( pm1.meta_value AS DATE ) >= %s and CAST( pm1.meta_value AS DATE ) <= %s )
				)
				group by post_status",
				array(
					$driver_id,
					get_option( 'lddfw_processing_status', '' ),
					get_option( 'lddfw_out_for_delivery_status', '' ),
					get_option( 'lddfw_failed_attempt_status', '' ),
					get_option( 'lddfw_delivered_status', '' ),
					gmdate( 'Y-m-d' ),
					gmdate( 'Y-m-d' ),
				)
			)
		); // db call ok; no-cache ok.

		return $query;

	}
	/**
	 * Claim orders count query.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function lddfw_claim_orders_count_query() {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"select count(*) as orders from {$wpdb->prefix}posts p
				left join {$wpdb->prefix}postmeta pm on p.id=pm.post_id and pm.meta_key = 'lddfw_driverid'
				where post_type='shop_order' and post_status in (%s) and ( pm.meta_value is null or pm.meta_value = '-1' or pm.meta_value = '')  group by post_status
				",
				array(
					get_option( 'lddfw_processing_status', '' ),
				)
			)
		); // db call ok; no-cache ok.
	}
	/**
	 * Assign to driver count query.
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return array
	 */
	public function lddfw_assign_to_driver_count_query( $driver_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"select count(*) as orders from {$wpdb->prefix}posts p
				inner join {$wpdb->prefix}postmeta pm on p.id=pm.post_id and pm.meta_key = 'lddfw_driverid'
				where post_type='shop_order' and post_status in (%s)
				and pm.meta_value = %s group by post_status",
				array(
					get_option( 'lddfw_processing_status', '' ),
					$driver_id,
				)
			)
		); // db call ok; no-cache ok.
	}

	/**
	 * Assign to driver count query.
	 *
	 * @since 1.0.0
	 * @param int    $driver_id driver user id.
	 * @param int    $status order status.
	 * @param string $screen current screen.
	 * @return object
	 */
	public function lddfw_orders_query( $driver_id, $status, $screen = null ) {

		$posts_per_page = -1;
		$paged = 1;

		$sort_array = array(
			'sort_meta_not_exist'      => 'ASC',
			'sort_city_meta_not_exist' => 'ASC',
		);

		if ( 'claim_orders' === $screen ) {
			$array = array(

				array(
					'relation' => 'or',
					array(
						'key'     => 'lddfw_driverid',
						'value'   => '-1',
						'compare' => '=',
					),
					array(
						'key'     => 'lddfw_driverid',
						'value'   => '',
						'compare' => '=',
					),
					array(
						'key'     => 'lddfw_driverid',
						'compare' => 'NOT EXISTS',
					),

				),
			);
		} elseif ( 'delivered' === $screen ) {
			global $lddfw_dates, $lddfw_page;

			$posts_per_page = 20;
			$paged = $lddfw_page;

			if ( '' === $lddfw_dates ) {
				$from_date = gmdate( 'Y-m-d' ) ;
				$to_date   = gmdate( 'Y-m-d' ) ;
			} else{
				$lddfw_dates_array = explode( ',' , $lddfw_dates );
				if ( 1 < count( $lddfw_dates_array ) ){
					if (  $lddfw_dates_array[0] ===  $lddfw_dates_array[1]  ) {
						$from_date = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[0] ) ) ;
						$to_date   = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[0] ) ) ;
					}
					else
					{
						$from_date = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[0] ) ) ;
						$to_date   = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[1] ) ) ;
					}
				}
				else{
					$from_date = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[0] ) ) ;
					$to_date   = gmdate( 'Y-m-d' , strtotime ( $lddfw_dates_array[0] ) ) ;
				}
			}
 
			$array = array( 'relation' => 'and',
				array(
					'key'     => 'lddfw_driverid',
					'value'   => $driver_id,
					'compare' => '=',
				),
				array(
					'key'     => 'lddfw_delivered_date',
					'value'   => $from_date,
					'compare' => '>=',
					'type' => 'DATE',
				),
				array(
					'key'     => 'lddfw_delivered_date',
					'value'   => $to_date,
					'compare' => '<=',
					'type' => 'DATE'
				)
			);
		} else {
			$array = array(
				'key'     => 'lddfw_driverid',
				'value'   => $driver_id,
				'compare' => '=',
			);
		}

		$params = array(
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'post_status'    => $status,
			'post_type'      => 'shop_order',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'or',
					array(
						'sort_city_meta_not_exist' => array(
							'key'     => '_shipping_city',
							'compare' => 'NOT EXISTS',
						),
					),
					array(
						'sort_city_meta_exist' => array(
							'key'     => '_shipping_city',
							'compare' => 'EXISTS',
						),
					),
					array(
						'sort_meta_exist' => array(
							'key'     => 'lddfw_order_sort',
							'compare' => 'EXISTS',
							'type'    => 'NUMERIC',
						),
					),
					array(
						'sort_meta_not_exist' => array(
							'key'     => 'lddfw_order_sort',
							'compare' => 'NOT EXISTS',
							'type'    => 'NUMERIC',
						),
					),
				),
				$array,
			),
			'orderby' => $sort_array,
		);

		$result = new WP_Query( $params );
		return $result;
	}
	/**
	 * Out for delivery orders counter.
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return object
	 */
	public function lddfw_out_for_delivery_orders_counter( $driver_id ) {
		$wc_query = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_out_for_delivery_status', '' ) );
		return $wc_query->found_posts;
	}
	/**
	 * Out for delivery orders.
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_out_for_delivery( $driver_id ) {
		$html = '';

		$counter  = 0;
		$wc_query = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_out_for_delivery_status', '' ) );

		if ( $wc_query->have_posts() ) {
			$html .= '<div id="lddfw_orders_table" sort_url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">';

			while ( $wc_query->have_posts() ) {
				$wc_query->the_post();
				$orderid = get_the_ID();
				$order   = new WC_Order( $orderid );

				$billing_address_1 = $order->get_billing_address_1();
				$billing_address_2 = $order->get_billing_address_2();
				$billing_city      = $order->get_billing_city();
				$billing_state     = $order->get_billing_state();
				$billing_postcode  = $order->get_billing_postcode();
				$billing_country   = $order->get_billing_country();

				$shipping_address_1 = $order->get_shipping_address_1();
				$shipping_address_2 = $order->get_shipping_address_2();
				$shipping_city      = $order->get_shipping_city();
				$shipping_state     = $order->get_shipping_state();
				$shipping_postcode  = $order->get_shipping_postcode();
				$shipping_country   = $order->get_shipping_country();

				if ( '' === $shipping_address_1 ) {
					$shipping_address_1 = $billing_address_1;
					$shipping_address_2 = $billing_address_2;
					$shipping_city      = $billing_city;
					$shipping_state     = $billing_state;
					$shipping_postcode  = $billing_postcode;
					$shipping_country   = $billing_country;
				}

				$route           = get_post_meta( $orderid, 'lddfw_order_route', true );
				$shippingaddress = $shipping_address_1;
				if ( '' !== $shipping_address_2 ) {
					$shippingaddress .= ', ' . $shipping_address_2 . ', ';
				}
				$distance = '';
				if ( is_array( $route ) ) {
					$distance = $route['distance_text'];
				}

				++$counter;
				$html .= '
				<div class="lddfw_box">
					<div class="row">
						<div class="col-12">
							<span class="lddfw_index lddfw_counter">'.$counter.'</span>
							<input style="display:none" orderid="'.$orderid.'" type="checkbox" value="' . str_replace( "'", '', $shipping_address_1 . ' ' . $shipping_city ) . '" class="lddfw_address_chk">
							<a class="lddfw_order_number" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '"><b>' . esc_html( __( 'Order #', 'lddfw' ) ) . $orderid.'</b></a>
							<a class="lddfw_order_address" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">'. $shippingaddress .'<br> '.$shipping_city . ' ' . $shipping_state . '</a>
							<a class="lddfw_order_distance" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">' . esc_html( __( 'Distance: ', 'lddfw' ) ) . $distance .'</a>
							<div class="lddfw_handle_column"  style="display:none"><button  class="lddfw_sort-up btn btn-outline-secondary "><i class="fas fa-chevron-up"></i></button><button class="btn btn-outline-secondary lddfw_sort-down"><i class="fas fa-chevron-down"></i></button></div>
						</div>
					</div>
				</div>';

			} // end while

			$html .= '</div>';
		} else {
			$html .= '<div class="lddfw_box min lddfw_no_orders"><p>' . esc_html( __( 'There are no orders.', 'lddfw' ) ) . '</p></div>';
		}
		return $html;
	}


	/**
	 * Failed delivery
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_failed_delivery( $driver_id ) {
		$date_format = lddfw_date_format( 'date' );
		$time_format = lddfw_date_format( 'time' );
		$html        = '<div id=\'lddfw_orders_table\' >';
		$counter     = 0;
		$wc_query    = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_failed_attempt_status', '' ) );
		if ( $wc_query->have_posts() ) {

			while ( $wc_query->have_posts() ) {
				$wc_query->the_post();
				$orderid = get_the_ID();
				$order   = new WC_Order( $orderid );

				$billing_address_1 = $order->get_billing_address_1();
				$billing_address_2 = $order->get_billing_address_2();
				$billing_city      = $order->get_billing_city();
				$billing_state     = $order->get_billing_state();
				$billing_postcode  = $order->get_billing_postcode();
				$billing_country   = $order->get_billing_country();

				$shipping_address_1 = $order->get_shipping_address_1();
				$shipping_address_2 = $order->get_shipping_address_2();
				$shipping_city      = $order->get_shipping_city();
				$shipping_state     = $order->get_shipping_state();
				$shipping_postcode  = $order->get_shipping_postcode();
				$shipping_country   = $order->get_shipping_country();

				if ( '' === $shipping_address_1 ) {
					$shipping_address_1 = $billing_address_1;
					$shipping_address_2 = $billing_address_2;
					$shipping_city      = $billing_city;
					$shipping_state     = $billing_state;
					$shipping_postcode  = $billing_postcode;
					$shipping_country   = $billing_country;
				}
				$delivered_date  = get_post_meta( $orderid, 'lddfw_delivered_date', true );
				$route           = get_post_meta( $orderid, 'lddfw_order_route', true );
				$failed_date     = get_post_meta( $orderid, 'lddfw_failed_attempt_date', true );
				$shippingaddress = $shipping_address_1;
				if ( '' !== $shipping_address_2 ) {
					$shippingaddress .= ', ' . $shipping_address_2 . ', ';
				}
				$distance = '';
				if ( is_array( $route ) ) {
					$distance = $route['distance_text'];
				}

				++$counter;
				$html .= '
				<div class="lddfw_box">
					<div class="row">
						<div class="col-12">
							<span class="lddfw_counter">'.$counter.'</span>
							<a class="lddfw_order_number line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '"><b>' . esc_html( __( 'Order #', 'lddfw' ) ) . $orderid.'</b></a>
							<a class="lddfw_order_address line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">'.$shippingaddress .'<br>'. $shipping_city  . ' ' . $shipping_state.'</a>';
								if ( '' !== $distance ) {
									$html .= '<a class=\'lddfw_order_distance lddfw_line\' href=\'' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid )  . '\'>' . esc_html( __( 'Distance: ', 'lddfw' ) ) . $distance . '</a>';
								}
								if ( '' !== $delivered_date ) {
									$html .= '<a class=\'lddfw_order_failed_date lddfw_line\' href=\'' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid )  . '\'>' . esc_html( __( 'Failed Date: ', 'lddfw' ) ) . date( $date_format . ' ' . $time_format, strtotime( $failed_date ) ) . '</a>';
								}
								$html .= '<input style="display:none" orderid="'.$orderid.'" type="checkbox" value="' . str_replace( "'", '', $shipping_address_1 . ' ' . $shipping_city ) . '" class="lddfw_address_chk">
						</div>
					</div>
				</div>';
			}
		} else {
			$html .= '<div class="lddfw_box min lddfw_no_orders"><p>' . esc_html( __( 'There are no orders.', 'lddfw' ) ) . '</p></div>';
		}
		$html .= '</div>';
		return $html;
	}


	/**
	 * Assign to driver
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_assign_to_driver( $driver_id ) {
		$html     = '';
		$counter  = 0;
		$wc_query = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_processing_status', '' ) );
		if ( $wc_query->have_posts() ) {

			while ( $wc_query->have_posts() ) {
				$wc_query->the_post();
				$orderid = get_the_ID();
				$order   = new WC_Order( $orderid );

				$billing_address_1 = $order->get_billing_address_1();
				$billing_address_2 = $order->get_billing_address_2();
				$billing_city      = $order->get_billing_city();
				$billing_state     = $order->get_billing_state();
				$billing_postcode  = $order->get_billing_postcode();
				$billing_country   = $order->get_billing_country();

				$shipping_address_1 = $order->get_shipping_address_1();
				$shipping_address_2 = $order->get_shipping_address_2();
				$shipping_city      = $order->get_shipping_city();
				$shipping_state     = $order->get_shipping_state();
				$shipping_postcode  = $order->get_shipping_postcode();
				$shipping_country   = $order->get_shipping_country();

				if ( '' === $shipping_address_1 ) {
					$shipping_address_1 = $billing_address_1;
					$shipping_address_2 = $billing_address_2;
					$shipping_city      = $billing_city;
					$shipping_state     = $billing_state;
					$shipping_postcode  = $billing_postcode;
					$shipping_country   = $billing_country;
				}

				$route           = get_post_meta( $orderid, 'lddfw_order_route', true );
				$shippingaddress = $shipping_address_1;

				if ( '' !== $shipping_address_2 ) {
					$shippingaddress .= ', ' . $shipping_address_2 . ', ';
				}

				++$counter;
				$html .= '
					<div class="lddfw_box lddfw_multi_checkbox">
						<div class="row">
							<div class="col-12">
								<div class="custom-control custom-checkbox mr-sm-2 lddfw_order_checkbox">
									<input value="' . $orderid .'" type="checkbox" class="custom-control-input" name="lddfw_order_id" id="lddfw_chk_order_id_'. $counter .'">
									<label class="custom-control-label" for="lddfw_chk_order_id_'. $counter .'"></label>
								</div>
								<div class="lddfw_order">
									<div class="lddfw_order_number"><b>' . esc_html( __( 'Order #', 'lddfw' ) ) . $orderid . '</b></div>
									<div class="lddfw_order_address">' . $shippingaddress . '<br>' . $shipping_city . ' ' . $shipping_state . '</div>
								</div>
							</div>
						</div>
					</div>';
			}
		} else {
			$html .= '<div class="lddfw_box min lddfw_no_orders"><p>' . esc_html( __( 'There are no orders.', 'lddfw' ) ) . '</p></div>';
		}

		return $html;
	}


	/**
	 * Claim orders
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_claim_orders( $driver_id ) {
		$html     = '';
		$counter  = 0;
		$wc_query = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_processing_status', '' ), 'claim_orders' );
		if ( $wc_query->have_posts() ) {

			while ( $wc_query->have_posts() ) {
				$wc_query->the_post();
				$orderid = get_the_ID();
				$order   = new WC_Order( $orderid );
				$billing_address_1  = $order->get_billing_address_1();
				$billing_address_2  = $order->get_billing_address_2();
				$billing_city       = $order->get_billing_city();
				$billing_state      = $order->get_billing_state();
				$billing_postcode   = $order->get_billing_postcode();
				$billing_country    = $order->get_billing_country();

				$shipping_address_1 = $order->get_shipping_address_1();
				$shipping_address_2 = $order->get_shipping_address_2();
				$shipping_city      = $order->get_shipping_city();
				$shipping_state     = $order->get_shipping_state();
				$shipping_postcode  = $order->get_shipping_postcode();
				$shipping_country   = $order->get_shipping_country();

				if ( '' === $shipping_address_1 ) {
					$shipping_address_1 = $billing_address_1;
					$shipping_address_2 = $billing_address_2;
					$shipping_city      = $billing_city;
					$shipping_state     = $billing_state;
					$shipping_postcode  = $billing_postcode;
					$shipping_country   = $billing_country;
				}

				$shippingaddress = $shipping_address_1;
				if ( '' !== $shipping_address_2 ) {
					$shippingaddress .= ', ' . $shipping_address_2 . ', ';
				}

				++$counter;
				$html .= '
				<div class="lddfw_box lddfw_multi_checkbox">
					<div class="row">
						<div class="col-12">
							<div class="custom-control custom-checkbox mr-sm-2 lddfw_order_checkbox">
								<input value="'.$orderid.'" type="checkbox" class="custom-control-input" name="lddfw_order_id" id="lddfw_chk_order_id_'.$counter.'">
								<label class="custom-control-label" for="lddfw_chk_order_id_'.$counter.'"></label>
							</div>
							<div class="lddfw_order">
								<div class="lddfw_order_number"><b>' . esc_html( __( 'Order #', 'lddfw' ) ) . $orderid . '</b></div>
								<div class="lddfw_order_address">' . $shippingaddress . '<br>' . $shipping_city . ' ' . $shipping_state . ' </div>
							</div>
						</div>
					</div>
				</div>';
			}
		} else {
			$html .= '<div class="lddfw_box min lddfw_no_orders"><p>' . esc_html( __( 'There are no orders.', 'lddfw' ) ) . '</p></div>';
		}
		return $html;
	}

	/**
	 * Delivered orders
	 *
	 * @since 1.0.0
	 * @param int $driver_id driver user id.
	 * @return html
	 */
	public function lddfw_delivered( $driver_id ) {
		$html        = '<div id=\'lddfw_orders_table\' >';
		$date_format = lddfw_date_format( 'date' );
		$time_format = lddfw_date_format( 'time' );
		$counter     = 0;
		$wc_query    = $this->lddfw_orders_query( $driver_id, get_option( 'lddfw_delivered_status', '' ), 'delivered' );
		if ( $wc_query->have_posts() ) {

			// Pagination.
			global $lddfw_page , $lddfw_dates;
			$base = lddfw_drivers_page_url( 'lddfw_screen=delivered&lddfw_dates=' . $lddfw_dates ) . '&lddfw_page=%#%' ;
			$pagination = paginate_links(array(
				'base'         => $base,
				'total'        => $wc_query->max_num_pages,
				'current'      => $lddfw_page,
				'format'       => '&lddfw_page=%#%',
				'show_all'     => false,
				'type'         => 'array',
				'end_size'     => 2,
				'mid_size'     => 0,
				'prev_next'    => true,
				'prev_text'    => sprintf('<i></i> %1$s', __('<<', 'lddfw')),
				'next_text'    => sprintf('%1$s <i></i>', __('>>', 'lddfw')),
				'add_args'     => false,
				'add_fragment' => '',
			));

			if (!empty($pagination) ) {
				$html .= '<div class="pagination text-sm-center"><nav aria-label="Page navigation" style="width:100%"><ul class="pagination justify-content-center">';
				foreach ($pagination as $page) {
					$html .=  "<li class='page-item ";
					if (strpos($page, 'current') !== false) {
						$html .=  ' active';
					}
					$html .=  "'> " . str_replace("page-numbers", "page-link",  $page) . "</li>";
				}
				$html .=  "</nav></div>";

			}
		
			// Results.
			$html .= '<div class="lddfw_orders_count">' . $wc_query->found_posts . ' ' . __('Orders', 'lddfw') . '</div>';


			while ( $wc_query->have_posts() ) {
				$wc_query->the_post();
				$orderid = get_the_ID();
				$order   = new WC_Order( $orderid );

				$billing_address_1 = $order->get_billing_address_1();
				$billing_address_2 = $order->get_billing_address_2();
				$billing_city      = $order->get_billing_city();
				$billing_state     = $order->get_billing_state();
				$billing_postcode  = $order->get_billing_postcode();
				$billing_country   = $order->get_billing_country();

				$shipping_address_1 = $order->get_shipping_address_1();
				$shipping_address_2 = $order->get_shipping_address_2();
				$shipping_city      = $order->get_shipping_city();
				$shipping_state     = $order->get_shipping_state();
				$shipping_postcode  = $order->get_shipping_postcode();
				$shipping_country   = $order->get_shipping_country();

				if ( '' === $shipping_address_1 ) {
					$shipping_address_1 = $billing_address_1;
					$shipping_address_2 = $billing_address_2;
					$shipping_city      = $billing_city;
					$shipping_state     = $billing_state;
					$shipping_postcode  = $billing_postcode;
					$shipping_country   = $billing_country;
				}

				$route           = get_post_meta( $orderid, 'lddfw_order_route', true );
				$delivered_date  = get_post_meta( $orderid, 'lddfw_delivered_date', true );
				$shippingaddress = $shipping_address_1;
				if ( '' !== $shipping_address_2 ) {
					$shippingaddress .= ', ' . $shipping_address_2 . ', ';
				}
				$distance = '';
				if ( is_array( $route ) ) {
					$distance = $route['distance_text'];
				}

				++$counter;
				$html .= '
				<div class="lddfw_box">
					<div class="row">
						<div class="col-12">
							<span class="lddfw_counter">'.$counter.'</span>
							<a class="lddfw_order_number lddfw_line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '"><b>' . esc_html( __( 'Order #', 'lddfw' ) ) . $orderid.'</b></a>
							<a class="lddfw_order_address lddfw_line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">'.$shippingaddress .'<br>'. $shipping_city. ' ' .$shipping_state.'</a>';
							if ( '' !== $distance ){
								$html .= '<a class="lddfw_order_distance lddfw_line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">' . esc_html( __( 'Distance: ', 'lddfw' ) ) . $distance . '</a>';
							}
							if ( '' !== $delivered_date  ){
								$html .= '<a class="lddfw_order_delivered_date lddfw_line" href="' . lddfw_drivers_page_url( 'lddfw_screen=order&lddfw_orderid=' . $orderid ) . '">' . esc_html( __( 'Delivered Date: ', 'lddfw' ) ) . date( $date_format . ' ' . $time_format, strtotime( $delivered_date ) ) . '</a>';
							}
							$html .= '<input style="display:none" orderid="'.$orderid.'" type="checkbox" value="' . str_replace( "'", '', $shipping_address_1 . ' ' . $shipping_city ) . '" class="address_chk">
						</div>
					</div>
				</div>';

			} // end while

			if (!empty($pagination) ) {
				$html .= '<div class="pagination text-sm-center"><nav aria-label="Page navigation" style="width:100%"><ul class="pagination justify-content-center">';
				foreach ($pagination as $page) {
					$html .=  "<li class='page-item ";
					if (strpos($page, 'current') !== false) {
						$html .=  ' active';
					}
					$html .=  "'> " . str_replace("page-numbers", "page-link",  $page) . "</li>";
				}
				$html .=  "</nav></div>";

			}

		} else {
			$html .= '<div class="lddfw_box min lddfw_no_orders"><p>' . esc_html( __( 'There are no orders.', 'lddfw' ) ) . '</p></div>';
		}
		$html .= '</div>';
		return $html;
	}
}
