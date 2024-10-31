<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Error;
use WP_User;

defined( 'ABSPATH' ) || exit;

class Lost_Password_Form extends WordPress_Form {
	protected function get_print_field_action(): string {
		return 'lostpassword_form';
	}

	public function is_active( bool $is_admin_area ): bool {
		return false === $is_admin_area &&
				true === $this->settings->is_on_wp_lost_password_form();
	}

	/**
	 * @param WP_Error $errors
	 * @param WP_User|false $user_data
	 *
	 * @return WP_Error
	 */
	public function verify_submission( WP_Error $errors, $user_data ): WP_Error {
		if ( false === $this->pro_captcha->is_human_made_request() ) {
			$this->pro_captcha->add_validation_error( $errors );
		}

		return $errors;
	}

	/**
	 * @param mixed $type
	 */
	public function maybe_print_field( $type = null ): void {
		if ( 'resetpass' !== $type ) {
			return;
		}

		$this->print_form_field();
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'lostpassword_errors', array( $this, 'verify_submission' ), 10, 2 );
		// bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}
}
