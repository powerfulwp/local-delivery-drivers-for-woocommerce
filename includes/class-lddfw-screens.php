<?php

/**
 * Plugin Screens.
 *
 * All the screens functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
/**
 * Plugin Screens.
 *
 * All the screens functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Screens
{
    /**
     * Footer.
     *
     * @since 1.0.0
     * @return html
     */
    public function lddfw_footer()
    {
        return "<div id='footer'></div>";
    }
    
    /**
     * Header.
     *
     * @since 1.0.0
     * @param string $title page title.
     * @param string $back_url the url for back.
     * @return html
     */
    public function lddfw_header( $title = null, $back_url = null )
    {
        global  $lddfw_user, $lddfw_driver_availability ;
        
        if ( '1' === $lddfw_driver_availability ) {
            $availability_icon = "<i class='fas lddfw_availability text-success fa-circle'></i>";
        } else {
            $availability_icon = "<i class='fas lddfw_availability text-danger fa-circle'></i>";
        }
        
        $html = "\r\n            <div id='lddfw_header'>\r\n            <div class='container'>\r\n                <div class='row'>";
        $html .= "<div class='col-2'>";
        if ( '' !== $back_url ) {
            $html .= "<a href='" . $back_url . "' class='lddfw_back_link'><i class='fas fa-arrow-left'></i></a>";
        }
        $html .= '</div>';
        $html .= "<div class='col-8 text-center'>";
        $html .= $title;
        $html .= '</div>';
        global 
            $lddfw_out_for_delivery_counter,
            $lddfw_failed_attempt_counter,
            $lddfw_delivered_counter,
            $lddfw_assign_to_driver_counter,
            $lddfw_claim_orders_counter
        ;
        $html .= "<div class='col-2 text-right'>\r\n\t\t\t\t\t<a href='#' id='lddfw_menu' onclick='lddfw_openNav()'>\r\n\t\t\t\t\t<i class=\"fas fa-bars\"></i>\r\n\t\t\t\t\t{$availability_icon}\r\n\t\t\t\t\t</a>\r\n\t\t\t\t\t<div id='lddfw_mySidenav' class='lddfw_sidenav'>\r\n\t\t\t\t\t<a href='javascript:void(0)' class='lddfw_closebtn' onclick='lddfw_closeNav()'>&times;</a>\r\n\t\t\t\t\t\t\t<span class=\"dropdown-header\">\r\n\t\t\t\t\t\t\t\t<h3>" . $lddfw_user->first_name . ' ' . $lddfw_user->last_name . "</h3>\r\n\t\t\t\t\t\t\t</span>\r\n\t\t\t\t\t\t\t<div class=\"dropdown-divider\"></div>\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href='" . lddfw_drivers_page_url() . '' . "'><i class='fas fa-home'></i> " . esc_html( __( 'Dashboard', 'lddfw' ) ) . '</a>
							<div class="dropdown-divider"></div>';
        $html .= "\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href='" . lddfw_drivers_page_url() . 'lddfw_action=assign_to_driver' . "'><i class='fas fa-angle-double-right'></i> " . esc_html( __( 'Assign to driver', 'lddfw' ) ) . ' (' . $lddfw_assign_to_driver_counter . ")</a>\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href='" . lddfw_drivers_page_url() . 'lddfw_action=out_for_delivery' . "'><i class='fas fa-angle-double-right'></i> " . esc_html( __( 'Out for delivery', 'lddfw' ) ) . ' (' . $lddfw_out_for_delivery_counter . ")</a>\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href='" . lddfw_drivers_page_url() . 'lddfw_action=failed_delivery' . "'><i class='fas fa-angle-double-right'></i> " . esc_html( __( 'Failed delivery', 'lddfw' ) ) . ' (' . $lddfw_failed_attempt_counter . ")</a>\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href='" . lddfw_drivers_page_url() . 'lddfw_action=delivered' . "'><i class='fas fa-angle-double-right'></i> " . esc_html( __( 'Delivered', 'lddfw' ) ) . ' (' . $lddfw_delivered_counter . ')</a>
							';
        $html .= "<div class=\"dropdown-divider\"></div>\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" title='" . esc_attr( __( 'Log out', 'lddfw' ) ) . "' href='" . lddfw_drivers_page_url() . 'lddfw_action=logout' . "'><i class='fas fa-sign-out-alt'></i> " . esc_html( __( 'Log out', 'lddfw' ) ) . '</a>
					</div>
					</div>
                </div>
            </div>
        </div>';
        return $html;
    }
    
    /**
     * Homepage.
     *
     * @since 1.0.0
     * @return html
     */
    public function lddfw_home()
    {
        // show delivery guy homepage.
        global  $lddfw_action, $lddfw_reset_key, $lddfw_reset_login ;
        $style_home = '';
        if ( 'resetpassword' === $lddfw_action ) {
            $style_home = 'style="display:none"';
        }
        // home page.
        $html = "<div class='lddfw_wpage' id='lddfw_home' {$style_home}>\r\n    <div class='container-fluid lddfw_cover'>\r\n\r\n                <img class='lddfw_header_image'  src='" . plugins_url() . "/" . LDDFW_FOLDER . "/public/images/lddfw.png?v=1'>\r\n\r\n    </div>\r\n    <div class='container'>\r\n        <h1>" . esc_html( __( 'WELCOME', 'lddfw' ) ) . '</h1>
        <p>' . esc_html( __( 'To delivery drivers manager', 'lddfw' ) ) . "</p>\r\n        <button id='lddfw_start' class='btn btn-primary btn-lg btn-block' type='button'>" . esc_html( __( 'Get started', 'lddfw' ) ) . '</button>
    </div>
</div>
';
        $login = new LDDFW_Login();
        $html .= $login->lddfw_login_screen();
        $password = new LDDFW_Password();
        $html .= $password->lddfw_forgot_password_screen();
        $html .= $password->lddfw_forgot_password_email_sent_screen();
        $html .= $password->lddfw_create_password_screen();
        $html .= $password->lddfw_new_password_created_screen();
        return $html;
    }
    
    /**
     * Delivery page.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_out_for_delivery_screen( $driver_id )
    {
        $orders = new LDDFW_Orders();
        $orders_counter = $orders->lddfw_out_for_delivery_orders_counter( $driver_id );
        $title = __( 'Out For Delivery', 'lddfw' );
        $back_url = lddfw_drivers_page_url() . 'lddfw_action=dashboard';
        $html = $this->lddfw_header( $title, $back_url );
        $html .= "<div id='lddfw_content' class='container lddfw_page_content'>\r\n            <div class='row'>";
        $html .= "<div class='col-12'>";
        $html .= "<div id='lddfw_plain_route_container'>";
        $html .= $orders->lddfw_out_for_delivery( $driver_id );
        $html .= '</div>
	 				</div>
          		</div>
		  	</div>';
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Dashboard screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_dashboard_screen( $driver_id )
    {
        global 
            $lddfw_driver_availability,
            $lddfw_out_for_delivery_counter,
            $lddfw_failed_attempt_counter,
            $lddfw_delivered_counter,
            $lddfw_assign_to_driver_counter,
            $lddfw_claim_orders_counter
        ;
        $title = __( 'Dashboard', 'lddfw' );
        $html = $this->lddfw_header( $title );
        $html .= "<div id='lddfw_content' class='container lddfw_dashboard lddfw_page_content'>\r\n\t\t\t<div class='row'>\r\n\t\t\r\n\t\t\t<div class='col-12'>\r\n\t\t\t<div class='lddfw_box'>\r\n\t\t\t<div class='row'>\r\n\t\t\t <div class='col-9'>" . esc_html( __( 'I am', 'lddfw' ) );
        
        if ( '1' === $lddfw_driver_availability ) {
            $html .= "\r\n\t\t\t\t<span id='lddfw_availability_status' available='" . esc_attr( __( 'Available', 'lddfw' ) ) . "' unavailable='" . esc_attr( __( 'Unavailable', 'lddfw' ) ) . "'>" . esc_html( __( 'Available', 'lddfw' ) ) . "</span>\r\n\t\t\t\t</div>\r\n\t\t\t\t<div class='col-3 text-right'>\r\n\t\t\t\t<a id='lddfw_availability' class='lddfw_active' title='" . esc_attr( __( 'Availability status', 'lddfw' ) ) . "' href='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "'>\r\n\t\t\t\t<i class='fas fa-toggle-on'></i></a></div>\r\n\t\t\t\t";
        } else {
            $html .= "\r\n\t\t\t\t<span id='lddfw_availability_status' available='" . esc_attr( __( 'Available', 'lddfw' ) ) . "' unavailable='" . esc_attr( __( 'Unavailable', 'lddfw' ) ) . "'>" . esc_html( __( 'Unavailable', 'lddfw' ) ) . "</span>\r\n\t\t\t\t</div>\r\n\t\t\t\t<div class='col-3 text-right'>\r\n\t\t\t\t<a id='lddfw_availability' class='' title='" . esc_attr( __( 'Availability status', 'lddfw' ) ) . "' href='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "'>\r\n\t\t\t\t<i class='fas fa-toggle-off'></i></a></div>";
        }
        
        $html .= '
			</div>
			</div>
			</div>';
        $html .= "\t<div class='col-6'>\r\n\t\t\t\t<div class='lddfw_box min text-center'>\r\n\t\t\t\t<a href='" . lddfw_drivers_page_url() . 'lddfw_action=assign_to_driver' . "'>\r\n\t\t\t\t<span class='lddfw_number'>" . $lddfw_assign_to_driver_counter . "</span>\r\n\t\t\t\t<span class='lddfw_label'>" . esc_html( __( 'Assign to driver', 'lddfw' ) ) . "</span></a>\r\n\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t\t<div class='col-6'>\r\n\t\t\t\t<div class='lddfw_box min text-center'>\r\n\t\t\t\t<a href='" . lddfw_drivers_page_url() . 'lddfw_action=out_for_delivery' . "'>\r\n\t\t\t\t<span class='lddfw_number'>" . $lddfw_out_for_delivery_counter . "</span>\r\n\t\t\t\t<span class='lddfw_label'>" . esc_html( __( 'Out for delivery', 'lddfw' ) ) . "</span></a>\r\n\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t\t<div class='col-6'>\r\n\t\t\t\t<div class='lddfw_box min text-center'>\r\n\t\t\t\t<a href='" . lddfw_drivers_page_url() . 'lddfw_action=failed_delivery' . "'>\r\n\t\t\t\t<span class='lddfw_number'>" . $lddfw_failed_attempt_counter . "</span>\r\n\t\t\t\t<span class='lddfw_label'>" . esc_html( __( 'Failed delivery', 'lddfw' ) ) . "</span></a>\r\n\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t\t<div class='col-6'>\r\n\t\t\t\t<div class='lddfw_box min text-center'>\r\n\t\t\t\t<a href='" . lddfw_drivers_page_url() . 'lddfw_action=delivered' . "'>\r\n\t\t\t\t<span class='lddfw_number'>" . $lddfw_delivered_counter . "</span>\r\n\t\t\t\t<span class='lddfw_label'>" . esc_html( __( 'Delivered', 'lddfw' ) ) . '</span></a>
				</div>
			</div>
		  </div>
		</div>';
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Failed delivery screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_failed_delivery_screen( $driver_id )
    {
        $title = __( 'Failed Delivery', 'lddfw' );
        $back_url = lddfw_drivers_page_url() . 'lddfw_action=dashboard';
        $html = $this->lddfw_header( $title, $back_url );
        $html .= "<div id='lddfw_content' class='container lddfw_page_content'>\r\n\t\t<div class='row'>\r\n\r\n\t\t<div class='col-12'>";
        $orders = new LDDFW_Orders();
        $html .= $orders->lddfw_failed_delivery( $driver_id );
        $html .= ' </div>
	  </div>
	</div>';
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Assign to driver screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_assign_to_driver_screen( $driver_id )
    {
        $title = __( 'Assign to driver', 'lddfw' );
        $back_url = lddfw_drivers_page_url() . 'lddfw_action=dashboard';
        $html = $this->lddfw_header( $title, $back_url );
        $orders = new LDDFW_Orders();
        $array = $orders->lddfw_assign_to_driver_count_query( $driver_id );
        $html .= "<div id='lddfw_content' class='container lddfw_page_content'>\r\n\t\t<div class='row'>";
        if ( !empty($array) ) {
            $html .= "\r\n\t\t\t<div class='col-12'>\r\n\t\t\t\t<h1>" . esc_html( __( 'Mark orders as out for delivery', 'lddfw' ) ) . '</h1>
				<p>' . esc_html( __( 'Choose orders and click on the button', 'lddfw' ) ) . '</p>
			</div>';
        }
        $html .= "<div class='col-12'>\r\n\t\t\t\t\t<div id='lddfw_alert' style='margin-top: 17px; display: none;'></div>\r\n\t\t\t \t  </div>";
        $html .= "<div class='col-12'>";
        $html .= $orders->lddfw_assign_to_driver( $driver_id );
        $html .= ' </div>
	  </div>
	</div>';
        if ( !empty($array) ) {
            $html .= "\r\n\t\t<div class='lddfw_footer_buttons'>\r\n\t\t\t<div class='container'>\r\n\t\t\t\t<div class='row'>\r\n\t\t\t\t\t<div class='col-12'>\r\n\t\t\t\t\t\t<a href='#' id='lddfw_out_for_delivery_button' class='btn btn-lg btn-block btn-success'><i class='fas fa-truck-loading'></i> " . esc_html( __( 'Out for delivery', 'lddfw' ) ) . "</a>\r\n\t\t\t\t\t\t<a href='#' id='lddfw_out_for_delivery_button_loading'  style='display:none' class='lddfw_loading_btn btn-lg btn btn-block btn-success'><span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>\r\n\t\t\t\t\t\t" . esc_html( __( 'Loading', 'lddfw' ) ) . "</a>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t</div>";
        }
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Delivered screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_delivered_screen( $driver_id )
    {
        $title = __( 'Delivered', 'lddfw' );
        $back_url = lddfw_drivers_page_url() . 'lddfw_action=dashboard';
        $html = $this->lddfw_header( $title, $back_url );
        $html .= "<div id='lddfw_content' class='container lddfw_page_content'>\r\n\t\t<div class='row'>\r\n\t\t<div class='col-12'>";
        $orders = new LDDFW_Orders();
        $html .= $orders->lddfw_delivered( $driver_id );
        $html .= ' </div>
	  </div>
	</div>';
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Order screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_order_screen( $driver_id )
    {
        global  $lddfw_order_id ;
        $order_class = new LDDFW_Order();
        $order = new WC_Order( $lddfw_order_id );
        $order_driverid = get_post_meta( $lddfw_order_id, 'lddfw_driverid', true );
        $order_status = $order->get_status();
        $back_url = lddfw_drivers_page_url() . 'lddfw_action=dashboard';
        switch ( 'wc-' . $order_status ) {
            case get_option( 'lddfw_delivered_status' ):
                $back_url = lddfw_drivers_page_url() . 'lddfw_action=delivered';
                break;
            case get_option( 'lddfw_failed_attempt_status' ):
                $back_url = lddfw_drivers_page_url() . 'lddfw_action=failed_delivery';
                break;
            case get_option( 'lddfw_out_for_delivery_status' ):
                $back_url = lddfw_drivers_page_url() . 'lddfw_action=out_for_delivery';
                break;
        }
        $title = __( 'Order #', 'lddfw' ) . ' ' . $lddfw_order_id;
        $html = $this->lddfw_header( $title, $back_url );
        
        if ( intval( $order_driverid ) === intval( $driver_id ) ) {
            $html .= $order_class->lddfw_order_page( $order, $driver_id );
        } else {
            $html .= "<div class='alert alert-danger'>" . esc_html( __( 'Access Denied, You do not have permissions to access this order', 'lddfw' ) ) . '</div>';
        }
        
        $html .= $this->lddfw_footer();
        return $html;
    }

}