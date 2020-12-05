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
            $availability_icon = '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" class="lddfw_availability text-success svg-inline--fa fa-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path></svg>';
        } else {
            $availability_icon = '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" class="lddfw_availability text-danger svg-inline--fa fa-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path></svg>';
        }
        
        $html = '
            <div id="lddfw_header">
            <div class="container">
                <div class="row">';
        $html .= '<div class="col-2">';
        if ( null !== $back_url ) {
            $html .= '<a href="' . $back_url . '" class="lddfw_back_link">
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrow-left" class="svg-inline--fa fa-arrow-left fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z"></path></svg></a>';
        }
        $html .= '</div>';
        $html .= '<div class="col-8 text-center lddfw_header_title">';
        $html .= $title;
        $html .= '</div>';
        global 
            $lddfw_driver_assigned_status_name,
            $lddfw_out_for_delivery_status_name,
            $lddfw_failed_attempt_status_name,
            $lddfw_out_for_delivery_counter,
            $lddfw_failed_attempt_counter,
            $lddfw_delivered_counter,
            $lddfw_assign_to_driver_counter,
            $lddfw_claim_orders_counter
        ;
        $driver_photo = '';
        $html .= '<div class="col-2 text-right">
				<a href="#" id="lddfw_menu" onclick="lddfw_openNav()">
				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bars" class="svg-inline--fa fa-bars fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"></path></svg>' . $availability_icon . '
				</a>
				<div id="lddfw_mySidenav" class="lddfw_sidenav">
				<a href="javascript:void(0)" class="lddfw_closebtn" onclick="lddfw_closeNav()">&times;</a>
				<span class="dropdown-header">
					<h3>' . $driver_photo . $lddfw_user->display_name . '</h3>
				</span>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="' . lddfw_drivers_page_url( "lddfw_screen=dashboard" ) . '">
				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="home" class="svg-inline--fa fa-home fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"></path></svg> ' . esc_html( __( "Dashboard", 'lddfw' ) ) . '</a>
				<div class="dropdown-divider"></div>';
        $html .= '
					<a class="dropdown-item" href="' . lddfw_drivers_page_url( "lddfw_screen=assign_to_driver" ) . '">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-double-right" class="svg-inline--fa fa-angle-double-right fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg> ' . $lddfw_driver_assigned_status_name . ' (' . $lddfw_assign_to_driver_counter . ')</a>
					<a class="dropdown-item" href="' . lddfw_drivers_page_url( "lddfw_screen=out_for_delivery" ) . '">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-double-right" class="svg-inline--fa fa-angle-double-right fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg> ' . $lddfw_out_for_delivery_status_name . ' (' . $lddfw_out_for_delivery_counter . ')</a>
					<a class="dropdown-item" href="' . lddfw_drivers_page_url( "lddfw_screen=failed_delivery" ) . '">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-double-right" class="svg-inline--fa fa-angle-double-right fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg> ' . $lddfw_failed_attempt_status_name . ' (' . $lddfw_failed_attempt_counter . ')</a>
					<a class="dropdown-item" href="' . lddfw_drivers_page_url( "lddfw_screen=delivered" ) . '">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-double-right" class="svg-inline--fa fa-angle-double-right fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"></path></svg> ' . esc_html( __( 'Delivered', 'lddfw' ) ) . ' (' . $lddfw_delivered_counter . ')</a>
					';
        $html .= '<div class="dropdown-divider"></div>
					<a class="dropdown-item" title="' . esc_attr( __( "Log out", 'lddfw' ) ) . '" href="' . lddfw_drivers_page_url( "lddfw_screen=logout" ) . '">
					<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign-out-alt" class="svg-inline--fa fa-sign-out-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M497 273L329 441c-15 15-41 4.5-41-17v-96H152c-13.3 0-24-10.7-24-24v-96c0-13.3 10.7-24 24-24h136V88c0-21.4 25.9-32 41-17l168 168c9.3 9.4 9.3 24.6 0 34zM192 436v-40c0-6.6-5.4-12-12-12H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h84c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12H96c-53 0-96 43-96 96v192c0 53 43 96 96 96h84c6.6 0 12-5.4 12-12z"></path></svg> ' . esc_html( __( 'Log out', 'lddfw' ) ) . '</a>
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
        // show delivery driver homepage.
        global  $lddfw_screen, $lddfw_reset_key, $lddfw_reset_login ;
        $style_home = '';
        if ( 'resetpassword' === $lddfw_screen ) {
            $style_home = 'style="display:none"';
        }
        // home page.
        $html = '<div class="lddfw_wpage" id="lddfw_home" ' . $style_home . '>
		<div class="container-fluid lddfw_cover"><span class="lddfw_helper"></span>';
        $title = esc_html( __( 'WELCOME', 'lddfw' ) );
        $subtitle = esc_html( __( 'To delivery drivers manager', 'lddfw' ) );
        $logo = '<img class="lddfw_header_image" src="' . plugins_url() . '/' . LDDFW_FOLDER . '/public/images/lddfw.png?ver=' . LDDFW_VERSION . '">';
        $html .= $logo;
        $html .= '</div>
		<div class="container">
			<h1>' . $title . '</h1>
			<p>' . $subtitle . '</p>
			<button id="lddfw_start" class="btn btn-primary btn-lg btn-block" type="button">' . esc_html( __( 'Get started', 'lddfw' ) ) . '</button>
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
        global  $lddfw_out_for_delivery_status_name ;
        $orders = new LDDFW_Orders();
        $orders_counter = $orders->lddfw_out_for_delivery_orders_counter( $driver_id );
        $title = $lddfw_out_for_delivery_status_name;
        $back_url = lddfw_drivers_page_url( 'lddfw_screen=dashboard' );
        $html = $this->lddfw_header( $title, $back_url );
        $html .= '<div id="lddfw_content" class="container lddfw_page_content">
            <div class="row">';
        $html .= '<div class="col-12">';
        
        if ( lddfw_is_free() ) {
            $button = esc_attr( __( "Plan your route", "lddfw" ) );
            $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( "Plan your Route by Distance or Manually.", "lddfw" ) ) . '
					<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( "View your Route on Google Maps.", "lddfw" ) ) . '
					<hr>' . lddfw_premium_feature( '' ) . ' ' . esc_html( __( "Navigate with Waze, Apple Maps, or Google Maps.", "lddfw" ) );
            $html .= '<div style="margin-bottom:15px;">' . lddfw_premium_feature_notice( $button, $content, '' ) . '</div>';
        }
        
        $html .= '<div id="lddfw_plain_route_container">';
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
            $lddfw_driver_assigned_status_name,
            $lddfw_out_for_delivery_status_name,
            $lddfw_failed_attempt_status_name,
            $lddfw_driver_availability,
            $lddfw_out_for_delivery_counter,
            $lddfw_failed_attempt_counter,
            $lddfw_delivered_counter,
            $lddfw_assign_to_driver_counter,
            $lddfw_claim_orders_counter
        ;
        $title = __( 'Dashboard', 'lddfw' );
        $html = $this->lddfw_header( $title );
        $html .= '<div id="lddfw_content" class="container lddfw_dashboard lddfw_page_content">
				<div class="row">
				<div class="col-12">
				<div class="lddfw_box availability">
				<div class="row">
				<div class="col-9 availability-text">' . esc_html( __( 'I am', 'lddfw' ) );
        
        if ( '1' === $lddfw_driver_availability ) {
            $html .= '
				<span id="lddfw_availability_status" available="' . esc_attr( __( 'Available', 'lddfw' ) ) . '" unavailable="' . esc_attr( __( 'Unavailable', 'lddfw' ) ) . '">' . esc_html( __( 'Available', 'lddfw' ) ) . '</span>
				</div>
				<div class="col-3 text-right">
				<a id="lddfw_availability" class="lddfw_active" title="' . esc_attr( __( 'Availability status', 'lddfw' ) ) . '" href="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">
				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-on" class="svg-inline--fa fa-toggle-on fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M384 64H192C86 64 0 150 0 256s86 192 192 192h192c106 0 192-86 192-192S490 64 384 64zm0 320c-70.8 0-128-57.3-128-128 0-70.8 57.3-128 128-128 70.8 0 128 57.3 128 128 0 70.8-57.3 128-128 128z"></path></svg></a></div>
				';
        } else {
            $html .= '
				<span id="lddfw_availability_status" available="' . esc_attr( __( 'Available', 'lddfw' ) ) . '" unavailable="' . esc_attr( __( 'Unavailable', 'lddfw' ) ) . '">' . esc_html( __( 'Unavailable', 'lddfw' ) ) . '</span>
				</div>
				<div class="col-3 text-right">
				<a id="lddfw_availability" class="" title="' . esc_attr( __( 'Availability status', 'lddfw' ) ) . '" href="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">

<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-off" class="svg-inline--fa fa-toggle-off fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M384 64H192C85.961 64 0 149.961 0 256s85.961 192 192 192h192c106.039 0 192-85.961 192-192S490.039 64 384 64zM64 256c0-70.741 57.249-128 128-128 70.741 0 128 57.249 128 128 0 70.741-57.249 128-128 128-70.741 0-128-57.249-128-128zm320 128h-48.905c65.217-72.858 65.236-183.12 0-256H384c70.741 0 128 57.249 128 128 0 70.74-57.249 128-128 128z"></path></svg></a></div>';
        }
        
        $html .= '
			</div>
			</div>
			</div>';
        // Driver report.
        $report = new LDDFW_Reports();
        $report_array = $report->lddfw_drivers_commission_query( gmdate( 'Y-m-d' ), gmdate( 'Y-m-d' ), $driver_id );
        $commission = 0;
        if ( !empty($report_array) ) {
        }
        $html .= '<div class = "col-12 "><div class="lddfw_box min">' . esc_html( __( 'Today Earnings: ', 'lddfw' ) );
        
        if ( lddfw_is_free() ) {
            $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( "View how much money did you make today.", "lddfw" ) );
            $html .= lddfw_premium_feature_notice( '', $content, 'lddfw_inline' );
        } else {
            $html .= '<b>' . lddfw_premium_feature( get_woocommerce_currency_symbol() . $commission ) . '</b>';
        }
        
        $html .= '</div></div>';
        $html .= '	<div class="col-6">
				<div class="lddfw_box min text-center">
				<a href="' . lddfw_drivers_page_url( 'lddfw_screen=assign_to_driver' ) . '">
				<span class="lddfw_number">' . $lddfw_assign_to_driver_counter . '</span>
				<span class="lddfw_label">' . $lddfw_driver_assigned_status_name . '</span></a>
				</div>
			</div>
			<div class="col-6">
				<div class="lddfw_box min text-center">
				<a href="' . lddfw_drivers_page_url( 'lddfw_screen=out_for_delivery' ) . '">
				<span class="lddfw_number">' . $lddfw_out_for_delivery_counter . '</span>
				<span class="lddfw_label">' . $lddfw_out_for_delivery_status_name . '</span></a>
				</div>
			</div>
			<div class="col-6">
				<div class="lddfw_box min text-center">
				<a href="' . lddfw_drivers_page_url( 'lddfw_screen=failed_delivery' ) . '">
				<span class="lddfw_number">' . $lddfw_failed_attempt_counter . '</span>
				<span class="lddfw_label">' . $lddfw_failed_attempt_status_name . '</span></a>
				</div>
			</div>
			<div class="col-6">
				<div class="lddfw_box min text-center">
				<a href="' . lddfw_drivers_page_url( 'lddfw_screen=delivered' ) . '">
				<span class="lddfw_number">' . $lddfw_delivered_counter . '</span>
				<span class="lddfw_label">' . esc_html( __( 'Delivered', 'lddfw' ) ) . '</span></a>
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
        global  $lddfw_failed_attempt_status_name ;
        $title = $lddfw_failed_attempt_status_name;
        $back_url = lddfw_drivers_page_url( 'lddfw_screen=dashboard' );
        $html = $this->lddfw_header( $title, $back_url );
        $html .= '<div id="lddfw_content" class="container lddfw_page_content">
		<div class="row">
		<div class="col-12">';
        $orders = new LDDFW_Orders();
        $html .= $orders->lddfw_failed_delivery( $driver_id );
        $html .= ' </div>
	  </div>
	</div>';
        $html .= $this->lddfw_footer();
        return $html;
    }
    
    /**
     * Driver assigned screen.
     *
     * @since 1.0.0
     * @param int $driver_id driver user id.
     * @return html
     */
    public function lddfw_assign_to_driver_screen( $driver_id )
    {
        global  $lddfw_driver_assigned_status_name ;
        $title = $lddfw_driver_assigned_status_name;
        $back_url = lddfw_drivers_page_url( 'lddfw_screen=dashboard' );
        $html = $this->lddfw_header( $title, $back_url );
        $orders = new LDDFW_Orders();
        $array = $orders->lddfw_assign_to_driver_count_query( $driver_id );
        $html .= '<div id="lddfw_content" class="container lddfw_page_content">
		<div class="row">';
        
        if ( !empty($array) ) {
            $html .= '
			<div class="col-12">
				<h1>' . esc_html( __( 'Mark orders as out for delivery', 'lddfw' ) ) . '</h1>
				<div class="lddfw_subtitle" >' . esc_html( __( 'Choose orders and click on the Out For Delivery button', 'lddfw' ) );
            
            if ( lddfw_is_free() ) {
                $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( "View orders details on this screen.", "lddfw" ) );
                $html .= ' ' . lddfw_premium_feature_notice( '', $content, 'lddfw_inline' );
            }
            
            $html .= '</div></div>';
        }
        
        $html .= '<div class="col-12">
					<div id="lddfw_alert" style="margin-top: 17px; display: none;"></div>
			 	  </div>';
        $html .= '<div class="col-12">';
        $html .= $orders->lddfw_assign_to_driver( $driver_id );
        $html .= ' </div>
	  </div>
	</div>';
        if ( !empty($array) ) {
            $html .= '
		<div class="lddfw_footer_buttons">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<a href="#" id="lddfw_out_for_delivery_button" class="btn btn-lg btn-block btn-success"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="truck-loading" class="svg-inline--fa fa-truck-loading fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M50.2 375.6c2.3 8.5 11.1 13.6 19.6 11.3l216.4-58c8.5-2.3 13.6-11.1 11.3-19.6l-49.7-185.5c-2.3-8.5-11.1-13.6-19.6-11.3L151 133.3l24.8 92.7-61.8 16.5-24.8-92.7-77.3 20.7C3.4 172.8-1.7 181.6.6 190.1l49.6 185.5zM384 0c-17.7 0-32 14.3-32 32v323.6L5.9 450c-4.3 1.2-6.8 5.6-5.6 9.8l12.6 46.3c1.2 4.3 5.6 6.8 9.8 5.6l393.7-107.4C418.8 464.1 467.6 512 528 512c61.9 0 112-50.1 112-112V0H384zm144 448c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z"></path></svg> ' . esc_html( __( 'Out for delivery', 'lddfw' ) ) . '</a>
						<a href="#" id="lddfw_out_for_delivery_button_loading"  style="display:none" class="lddfw_loading_btn btn-lg btn btn-block btn-success"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
						' . esc_html( __( 'Loading', 'lddfw' ) ) . '</a>
					</div>
				</div>
			</div>
		</div>';
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
        global  $lddfw_dates ;
        $title = __( 'Delivered', 'lddfw' );
        $back_url = lddfw_drivers_page_url( 'lddfw_screen=dashboard' );
        $html = $this->lddfw_header( $title, $back_url );
        $html .= '<div id="lddfw_content" class="container lddfw_page_content">
		<div class="row">
		<div class="col-12">
		<select class="custom-select custom-select-lg" id="lddfw_dates_range" data="' . lddfw_drivers_page_url( 'lddfw_screen=delivered' ) . '">
		<option value="' . gmdate( 'Y-m-d' ) . ',' . gmdate( 'Y-m-d' ) . '">Today</option>
		<option value="' . gmdate( 'Y-m-d', strtotime( '-1 days' ) ) . ',' . gmdate( 'Y-m-d', strtotime( '-1 days' ) ) . '">Yesterday</option>
		<option value="' . gmdate( 'Y-m-d', strtotime( 'first day of this month' ) ) . ',' . gmdate( 'Y-m-d', strtotime( 'last day of this month' ) ) . '">This month</option>
		<option value="' . gmdate( 'Y-m-d', strtotime( 'first day of last month' ) ) . ',' . gmdate( 'Y-m-d', strtotime( 'last day of last month' ) ) . '">Last month</option>
		</select>
		<div class="lddfw_date_range">
		';
        
        if ( '' === $lddfw_dates ) {
            $html .= gmdate( lddfw_date_format( 'date' ) );
            $from_date = gmdate( 'Y-m-d' );
            $to_date = gmdate( 'Y-m-d' );
        } else {
            $lddfw_dates_array = explode( ',', $lddfw_dates );
            
            if ( 1 < count( $lddfw_dates_array ) ) {
                
                if ( $lddfw_dates_array[0] === $lddfw_dates_array[1] ) {
                    $html .= gmdate( lddfw_date_format( 'date' ), strtotime( $lddfw_dates_array[0] ) );
                    $from_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[0] ) );
                    $to_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[0] ) );
                } else {
                    $html .= gmdate( lddfw_date_format( 'date' ), strtotime( $lddfw_dates_array[0] ) ) . ' - ' . gmdate( lddfw_date_format( 'date' ), strtotime( $lddfw_dates_array[1] ) );
                    $from_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[0] ) );
                    $to_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[1] ) );
                }
            
            } else {
                $html .= gmdate( lddfw_date_format( 'date' ), strtotime( $lddfw_dates_array[0] ) );
                $from_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[0] ) );
                $to_date = gmdate( 'Y-m-d', strtotime( $lddfw_dates_array[0] ) );
            }
        
        }
        
        $html .= '</div>';
        // Driver report.
        $report = new LDDFW_Reports();
        $refund_array = $report->lddfw_drivers_refund_query( $from_date, $to_date, $driver_id );
        $report_array = $report->lddfw_drivers_commission_query( $from_date, $to_date, $driver_id );
        $orders_price = 0;
        $shipping_price = 0;
        $commission = 0;
        
        if ( !empty($report_array) ) {
            $orders_counter = $report_array[0]->orders;
            $content = lddfw_premium_feature( '' ) . ' ' . esc_html( __( "View how much money did you make, orders total, and shipping total.", "lddfw" ) );
            $html .= '
				<div class="row delivered-report">
					 <div class = "col-6 col-md-3">
						<div class="lddfw_box min text-center">
							<b class="lddfw_text">' . $orders_counter . '</b>
							<div class="lddfw_break"></div>' . esc_html( __( 'Orders', 'lddfw' ) ) . '</div>
					 </div>
					 <div class = "col-6 col-md-3"><div class="lddfw_box min text-center">';
            
            if ( lddfw_is_free() ) {
                $html .= lddfw_premium_feature_notice( '', $content, 'lddfw_inline' );
            } else {
                $html .= '<b class="lddfw_text">' . lddfw_premium_feature( get_woocommerce_currency_symbol() . $orders_price ) . '</b>';
            }
            
            $html .= '<div class="lddfw_break"></div> ' . esc_html( __( 'Orders Total', 'lddfw' ) ) . '</div></div>
					 <div class = "col-6 col-md-3"><div class="lddfw_box min text-center">';
            
            if ( lddfw_is_free() ) {
                $html .= lddfw_premium_feature_notice( '', $content, 'lddfw_inline' );
            } else {
                $html .= '<b class="lddfw_text">' . lddfw_premium_feature( get_woocommerce_currency_symbol() . $shipping_price ) . '</b>';
            }
            
            $html .= '<div class="lddfw_break"></div> ' . esc_html( __( 'Shipping Total', 'lddfw' ) ) . '</div></div>
					 <div class = "col-6 col-md-3"><div class="lddfw_box min text-center">';
            
            if ( lddfw_is_free() ) {
                $html .= lddfw_premium_feature_notice( '', $content, 'lddfw_inline' );
            } else {
                $html .= '<b class="lddfw_text">' . lddfw_premium_feature( get_woocommerce_currency_symbol() . $commission ) . '</b>';
            }
            
            $html .= '<div class="lddfw_break"></div> ' . esc_html( __( 'Commission', 'lddfw' ) ) . '</div></div>
				</div>';
        }
        
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
        // Set back url.
        $back_url = lddfw_drivers_page_url( 'lddfw_screen=dashboard' );
        switch ( 'wc-' . $order_status ) {
            case get_option( 'lddfw_delivered_status' ):
                $back_url = lddfw_drivers_page_url( 'lddfw_screen=delivered' );
                break;
            case get_option( 'lddfw_failed_attempt_status' ):
                $back_url = lddfw_drivers_page_url( 'lddfw_screen=failed_delivery' );
                break;
            case get_option( 'lddfw_out_for_delivery_status' ):
                $back_url = lddfw_drivers_page_url( 'lddfw_screen=out_for_delivery' );
                break;
            case get_option( 'lddfw_driver_assigned_status' ):
                $back_url = lddfw_drivers_page_url( 'lddfw_screen=assign_to_driver' );
                break;
        }
        // Set back url from parameter.
        $back_url = ( isset( $_GET['lddfw_dates'] ) ? $back_url . '&lddfw_dates=' . sanitize_text_field( wp_unslash( $_GET['lddfw_dates'] ) ) : $back_url );
        $back_url = ( isset( $_GET['lddfw_page'] ) ? $back_url . '&lddfw_page=' . sanitize_text_field( wp_unslash( $_GET['lddfw_page'] ) ) : $back_url );
        $title = __( 'Order #', 'lddfw' ) . ' ' . $lddfw_order_id;
        $html = $this->lddfw_header( $title, $back_url );
        
        if ( intval( $order_driverid ) === intval( $driver_id ) ) {
            $html .= $order_class->lddfw_order_page( $order, $driver_id );
        } else {
            $html .= '<div class="alert alert-danger">' . esc_html( __( 'Access Denied, You do not have permissions to access this order', 'lddfw' ) ) . '</div>';
        }
        
        $html .= $this->lddfw_footer();
        return $html;
    }

}