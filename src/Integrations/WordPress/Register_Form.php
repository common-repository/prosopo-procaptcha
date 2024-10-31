<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Error;

defined( 'ABSPATH' ) || exit;

class Register_Form extends WordPress_Form {
	protected function get_print_field_action(): string {
		return 'register_form';
	}

	public function is_active( bool $is_admin_area ): bool {
		return false === $is_admin_area &&
			true === $this->settings->is_on_wp_register_form();
	}

	public function verify_submission( WP_Error $errors, string $sanitized_user_login, string $user_email ): WP_Error {
		if ( false === $this->pro_captcha->is_human_made_request() ) {
			$this->pro_captcha->add_validation_error( $errors );
		}

		return $errors;
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'registration_errors', array( $this, 'verify_submission' ), 10, 3 );
	}
}
