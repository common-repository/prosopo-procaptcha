<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Error;
use WP_User;

defined( 'ABSPATH' ) || exit;

class Login_Form extends WordPress_Form {
	public function is_active( bool $is_admin_area ): bool {
		return false === $is_admin_area &&
				true === $this->settings->is_on_wp_login_form();
	}

	/**
	 * @param WP_User|WP_Error $user_or_error
	 * @param string $password
	 *
	 * @return WP_User|WP_Error
	 */
	public function verify_submission( $user_or_error, string $password ) {
		if ( false === $this->pro_captcha->is_human_made_request() ) {
			$error_instance = true === ( $user_or_error instanceof WP_Error ) ?
				$user_or_error :
				null;

			$user_or_error = $this->pro_captcha->add_validation_error( $error_instance );
		}

		return $user_or_error;
	}

	/**
	 * @param mixed $type
	 */
	public function maybe_print_field( $type = null ): void {
		if ( 'resetpass' === $type ) {
			return;
		}

		$this->print_form_field();
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter(
			'wp_authenticate_user',
			array( $this, 'verify_submission' ),
			10,
			2
		);

		// Ignore bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}
}
