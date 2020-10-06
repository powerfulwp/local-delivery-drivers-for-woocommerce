<?php

/**
 * Driver Login.
 *
 * All the login functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
/**
 * Driver Login.
 *
 * All the login functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Login
{
    /**
     * Drivers logout.
     *
     * @since 1.0.0
     * @return void
     */
    public static function lddfw_logout()
    {
        wp_logout();
        header( 'Location: ' . home_url( '/lddfwapp/' ) );
        exit;
    }
    
    /**
     * Drivers login page.
     *
     * @since 1.0.0
     * @return html
     */
    public function lddfw_login_screen()
    {
        // Login page.
        $html = "<div class='lddfw_page' id='lddfw_login' style='display:none;'>\r\n\t\t\t\t<div class='container-fluid lddfw_cover'>\r\n\t\t\t\t\t<div class='row'>\r\n\t\t\t\t\t\t<div class='col-12'>\r\n\t\t\t\t\t\t<i class='fas fa-sign-in-alt'></i>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t\t<div class='container'>\r\n\t\t\t\t\t<div class='row'>\r\n\t\t\t\t\t\t<div class='col-12'>\r\n\t\t\t\t\t\t\t<h1>" . esc_html( __( 'Login', 'lddfw' ) ) . '</h1>
							<p>' . esc_html( __( 'Enter your details below to continue.', 'lddfw' ) ) . "</p>\r\n\t\t\t\t\t\t\t<form method='post' name='lddfw_login_frm' id='lddfw_login_frm' action='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' nextpage='" . esc_url( get_site_url() ) . "/lddfwapp/dashboard'>\r\n\t\t\t\t\t\t\t<div class='lddfw_alert_wrap'></div>\r\n\r\n\t\t\t\t\t\t\t<input type='text' autocapitalize=off class='form-control form-control-lg' placeholder='" . esc_attr( __( 'Email', 'lddfw' ) ) . "' name='lddfw_login_email' id='lddfw_login_email'  value=''>\r\n\t\t\t\t\t\t\t\t<input type='password' autocapitalize=off class='form-control form-control-lg' placeholder='" . esc_attr( __( 'Password', 'lddfw' ) ) . "' name='lddfw_login_password' id='lddfw_login_password' value=''>\r\n\t\t\t\t\t\t\t\t<button class=\"submit_btn btn btn-lg btn-primary btn-block\" type=\"submit\">\r\n\t\t\t\t\t\t\t\t" . esc_html( __( 'Login', 'lddfw' ) ) . "\r\n\t\t\t\t\t\t\t\t</button>\r\n\t\t\t\t\t\t\t\t<button style='display:none' class=\"loading_btn btn-lg btn btn-block btn-primary\" type=\"button\" disabled>\r\n\t\t\t\t\t\t\t\t<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>\r\n\t\t\t\t\t\t\t\t" . esc_html( __( 'Loading', 'lddfw' ) ) . "\r\n\t\t\t\t\t\t\t\t</button>\r\n\t\t\t\t\t\t\t\t<a href='#' id='lddfw_forgot_password_link'>" . esc_html( __( 'Forgot password?', 'lddfw' ) ) . '</a>
							</form>
						</div>';
        $html .= '</div>
				</div>
				</div>
				';
        return $html;
    }
    
    /**
     * Drivers login.
     *
     * @since 1.0.0
     * @return json
     */
    public function lddfw_login_driver()
    {
        $error = '';
        $result = '0';
        // Security check.
        
        if ( isset( $_POST['lddfw_wpnonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
            
            if ( !wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
                $error = __( 'Security Check Failure', 'lddfw' );
            } else {
                if ( isset( $_POST['lddfw_login_email'] ) ) {
                    $email = sanitize_text_field( wp_unslash( $_POST['lddfw_login_email'] ) );
                }
                if ( isset( $_POST['lddfw_login_password'] ) ) {
                    $password = sanitize_text_field( wp_unslash( $_POST['lddfw_login_password'] ) );
                }
                // Check for empty fields.
                
                if ( empty($email) ) {
                    // No email.
                    $error = __( 'The email field is empty.', 'lddfw' );
                } else {
                    
                    if ( empty($password) ) {
                        // No password.
                        $error = __( 'The password field is empty.', 'lddfw' );
                    } else {
                        
                        if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                            // Invalid Email.
                            $error = __( 'The email is invalid.', 'lddfw' );
                        } else {
                            // Check if user exists in WordPress database.
                            $user = get_user_by( 'email', $email );
                            // Bad email.
                            
                            if ( !$user ) {
                                $error = __( 'Either the email or password you entered is invalid.', 'lddfw' );
                            } else {
                                // Check password.
                                
                                if ( !wp_check_password( $password, $user->user_pass, $user->ID ) ) {
                                    // Bad password.
                                    $error = __( 'Either the email or password you entered is invalid.', 'lddfw' );
                                } else {
                                    
                                    if ( 'driver' !== $user->roles[0] ) {
                                        $error = __( 'You are not a registered delivery driver.', 'lddfw' );
                                    } else {
                                        $user_login = $user->user_login;
                                        $creds = array();
                                        $creds['user_login'] = $user_login;
                                        $creds['user_password'] = $password;
                                        $creds['remember'] = true;
                                        $user = wp_signon( $creds, false );
                                        $user_id = $user->ID;
                                        wp_set_current_user( $user_id, $user_login );
                                        wp_set_auth_cookie( $user_id, true, false );
                                        do_action( 'wp_login', $user_login, $user );
                                        $error = '';
                                        $result = '1';
                                    }
                                
                                }
                            
                            }
                        
                        }
                    
                    }
                
                }
            
            }
        
        }
        
        return "{\"result\":\"{$result}\",\"error\":\"{$error}\"}";
    }

}