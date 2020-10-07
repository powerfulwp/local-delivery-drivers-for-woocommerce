<?php
/**
 * Password page.
 *
 * All the Password functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */

/**
 * Password page.
 *
 * All the Password functions.
 *
 * @package    LDDFW
 * @subpackage LDDFW/includes
 * @author     powerfulwp <cs@powerfulwp.com>
 */
class LDDFW_Password {



	/**
	 * Reset password
	 *
	 * @since 1.0.0
	 * @return json
	 */
	public function lddfw_reset_password() {
		$error  = '';
		$result = '0';
		if ( isset( $_POST['lddfw_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
				$error = __( 'Security Check Failure', 'lddfw' );
			} else {
				if ( isset( $_POST['lddfw_user_email'] ) ) {
					$email = sanitize_text_field( wp_unslash( $_POST['lddfw_user_email'] ) );
				}

				if ( empty( $email ) ) {
					// email is empty.
					$error = __( 'Email field is empty.', 'lddfw' );
				} else {
					// email is invalid.
					if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
						$error = __( 'The email you entered is invalid.', 'lddfw' );
					} else {
						// Check if user exists in WordPress database.
						$user = get_user_by( 'email', $email );
						if ( ! $user ) {
							// user not founded.
							$error = __( 'The email you entered was not found.', 'lddfw' );
						} else {
							if ( 'driver' !== $user->roles[0] ) {
								// user is not driver.
								$error = __( 'You are not a registered delivery driver.', 'lddfw' );
							} else {
								$user_login = $user->user_login;
								$reset_url  = lddfw_drivers_page_url() . "lddfw_action=resetpassword&lddfw_orderid=" . get_password_reset_key( $user ) . '&lddfw_page=' . rawurlencode( $user_login );

								// email content.
								$message  = __( 'Someone requested that the password be reset for the following account:', 'lddfw' ) . "\r\n\r\n";
								/* translators: %s: email */
								$message .= sprintf( __( 'Email: %s', 'lddfw' ), $email ) . "\r\n\r\n";
								$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'lddfw' ) . "\r\n\r\n";
								$message .= __( 'To reset your password, visit the following address:', 'lddfw' ) . "\r\n\r\n";
								$message .= '<' . $reset_url . ">\r\n";

								// send email.
								$mail = wp_mail( $email, __( 'Password reset', 'lddfw' ), $message, '', '' );
								if ( false === $mail ) {
									$error  = __( 'An error occurred while sending mail.', 'lddfw' );
									$result = '0';
								} else {
									$result = '1';
								}
							}
						}
					}
				}
			}
		}
		return "{\"result\":\"$result\",\"error\":\"$error\"}";
	}
	/**
	 * New password
	 *
	 * @since 1.0.0
	 * @return json
	 */
	public function lddfw_new_password() {
		if ( isset( $_POST['lddfw_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['lddfw_wpnonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'lddfw-nonce' ) ) {
				$error = __( 'Security Check Failure', 'lddfw' );
			} else {
				$new_password     = ( isset( $_POST['lddfw_new_password'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_new_password'] ) ) : '';
				$confirm_password = ( isset( $_POST['lddfw_confirm_password'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_confirm_password'] ) ) : '';
				$reset_key        = ( isset( $_POST['lddfw_reset_key'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_reset_key'] ) ) : '';
				$reset_login      = ( isset( $_POST['lddfw_reset_login'] ) ) ? sanitize_text_field( wp_unslash( $_POST['lddfw_reset_login'] ) ) : '';

				$result = '0';
				$error  = '';
				if ( empty( $new_password ) ) {
					// new password is empty.
					$error = __( 'The new password field is empty.', 'lddfw' );
				} else {
					if ( empty( $confirm_password ) ) {
						// confirm password is empty.
						$error = __( 'The confirm password field is empty.', 'lddfw' );
					} else {
						if ( $new_password !== $confirm_password ) {
							// Passwords not match.
							$error = __( 'New password and confirm password do not match.', 'lddfw' );
						} else {
							$new_password     = wp_unslash( $new_password );
							$confirm_password = wp_unslash( $confirm_password );

							$user = WC_Shortcode_My_Account::check_password_reset_key( $reset_key, $reset_login );
							if ( $user instanceof WP_User ) {
								WC_Shortcode_My_Account::reset_password( $user, $new_password );
								$result = '1';
							} else {
								$error = __( 'This key is invalid or has already been used. Please reset your password again if needed.', 'lddfw' );
							}
						}
					}
				}
			}
		}
		return "{\"result\":\"$result\",\"error\":\"$error\"}";
	}
	/**
	 * Forgot password screen
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function lddfw_forgot_password_screen() {
		// forgot password page.
		$html = "<div class='lddfw_page' id='lddfw_forgot_password' style='display:none;'>
<div class='container-fluid lddfw_cover'>
	<div class='row'>
		<div class='col-12'>
		<i class='fas fa-key'></i>
		</div>
	</div>
</div>
<div class='container'>
	<div class='row'>
		<div class='col-12'>
			<h1>" . esc_html( __( 'Forgot your password?', 'lddfw' ) ) . '</h1>
			<p>' . esc_html( __( "Enter your email, and we'll email you a link to change your password.", 'lddfw' ) ) . "</p>
			<form method='post' name='lddfw_forgot_password_frm' id='lddfw_forgot_password_frm' action='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' nextpage='" . lddfw_drivers_page_url()  . "lddfw_action=dashboard'>
			<div class='lddfw_alert_wrap'></div>
			<input type='text' autocapitalize='off' class='form-control form-control-lg' placeholder='" . esc_attr( __( 'Email', 'lddfw' ) ) . "' name='lddfw_user_email' id='lddfw_user_email' value=''>
				<button class=\"lddfw_submit_btn btn btn-primary btn-lg btn-block\" type=\"submit\">
				" . esc_html( __( 'Send', 'lddfw' ) ) . "
				</button>
				<button style='display:none' class=\"lddfw_loading_btn btn-lg btn btn-block btn-primary\" type=\"button\" disabled>
				<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>
				" . esc_html( __( 'Loading', 'lddfw' ) ) . "
				</button>
				<a href='#' class='lddfw_back_to_login_link'><i class=\"fas fa-chevron-left\"></i> " . esc_html( __( 'Back to login', 'lddfw' ) ) . '</a>
			</form>
		</div>
	</div>
</div>
</div>
';
		return $html;
	}




	/**
	 * New password created screen
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function lddfw_new_password_created_screen() {
		// forgot password email sent.
		$html = "<div class='lddfw_page' id='lddfw_new_password_created' style='display:none;'>
<div class='container-fluid lddfw_cover'>
	<div class='row'>
		<div class='col-12'>
			<i class=\"far fa-check-circle\"></i>
		</div>
	</div>
</div>
<div class='container'>
	<div class='row'>
		<div class='col-12'>
			<h1>" . esc_html( __( 'Your password has been changed successfully.', 'lddfw' ) ) . '</h1>
			<p>' . esc_html( __( 'Please click on the login button to login with your new password', 'lddfw' ) ) . "</p>
			<button id='lddfw_login_button' class=\"btn btn-lg btn-primary btn-block\" type=\"button\">
				" . esc_html( __( 'Login', 'lddfw' ) ) . '
			</button>
		</div>
	</div>
</div>
</div>
';
		return $html;
	}

	/**
	 * Forgot password email sent screen
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function lddfw_forgot_password_email_sent_screen() {
		// forgot password email sent.
		$html = "<div class='lddfw_page' id='lddfw_forgot_password_email_sent' style='display:none;'>
<div class='container-fluid lddfw_cover'>
	<div class='row'>
		<div class='col-12'>
			<i class=\"far fa-paper-plane\"></i>
		</div>
	</div>
</div>
<div class='container'>
	<div class='row'>
		<div class='col-12'>
			<h1>" . esc_html( __( 'Reset password', 'lddfw' ) ) . '</h1>
			<p>' . esc_html( __( 'A password reset link was sent. Click the link in the email to create a new password. If you do not receive an email within 5 minutes, please click on the resend email button below.', 'lddfw' ) ) . "</p>
			<button id='lddfw_resend_button' class=\"btn btn-lg btn-primary btn-block\" type=\"button\">
				" . esc_html( __( 'Resend email', 'lddfw' ) ) . "
			</button>
			<a href='#' id='lddfw_back_to_forgot_password_link'><i class=\"fas fa-chevron-left\"></i> " . esc_html( __( 'Back to forgot password', 'lddfw' ) ) . '</a>
		</div>
	</div>
</div>
</div>
';
		return $html;
	}

	/**
	 * Create password screen
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function lddfw_create_password_screen() {
		// show delivery guy homepage.
		global $lddfw_action, $lddfw_reset_key, $lddfw_reset_login;

		$style_password_reset = 'style="display:none"';

		if ( 'resetpassword' === $lddfw_action ) {

			$style_password_reset = 'style="display:block"';
		}
		// New password.
		$html = "<div class='lddfw_page' id='lddfw_create_new_password' $style_password_reset>
<div class='container-fluid lddfw_cover'>
	<div class='row'>
		<div class='col-12'>
			<i class=\"fas fa-key\"></i>
		</div>
	</div>
</div>
<div class='container'>
	<div class='row'>
		<div class='col-12'>
			<form method='post' name='lddfw_new_password_frm' id='lddfw_new_password_frm' action='" . esc_url( admin_url( 'admin-ajax.php' ) ) . "' nextpage='" . lddfw_drivers_page_url() . "lddfw_action=dashboard'>
			<h1>" . esc_html( __( 'Create a new password.', 'lddfw' ) ) . "</h1>
			<div class='lddfw_alert_wrap'></div>
			<input type='text' autocapitalize=off class='form-control form-control-lg' placeholder='" . __( 'New password', 'lddfw' ) . "' name='lddfw_new_password'  id='lddfw_new_password' value=''>
			<input type='text' autocapitalize=off class='form-control form-control-lg' placeholder='" . __( 'Confirm password', 'lddfw' ) . "' name='lddfw_confirm_password' id='lddfw_confirm_password' value=''>
			<input type='hidden' id='lddfw_reset_key' name='lddfw_reset_key' value='" . $lddfw_reset_key . "'>
			<input type='hidden' id='lddfw_reset_login' name='lddfw_reset_login' value='" . $lddfw_reset_login . "'>
			<button class=\"lddfw_submit_btn btn btn-lg btn-primary btn-block\" type=\"submit\">
			" . esc_html( __( 'Send', 'lddfw' ) ) . "
			</button>
			<button style='display:none' class=\"lddfw_loading_btn btn btn-lg btn-block btn-primary\" type=\"button\" disabled>
			<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>
			" . esc_html( __( 'Send', 'lddfw' ) ) . "
			</button>
			<div class='lddfw_links'>
			<a href='#' id='lddfw_new_password_reset_link'><i class=\"fas fa-chevron-left\"></i> " . esc_html( __( 'Back to forgot password', 'lddfw' ) ) . '</a>
  			</div>
			</form>
		</div>
	</div>
</div>
</div>
';
		return $html;
	}
}
