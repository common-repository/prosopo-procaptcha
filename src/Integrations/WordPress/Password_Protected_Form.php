<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Post;

defined( 'ABSPATH' ) || exit;

class Password_Protected_Form extends WordPress_Form {
	public function is_active( bool $is_admin_area ): bool {
		return false === $is_admin_area &&
				true === $this->settings->is_on_wp_post_form();
	}

	public function add_form_field( string $output, WP_Post $post ): string {
		if ( true === $this->is_captcha_present() ) {
			$form_field = $this->pro_captcha->print_form_field( array(), true );

			$output = str_replace( '</form>', $form_field . '</form>', $output );
		}

		return $output;
	}

	public function verify_submission(): void {
		if ( false === $this->is_captcha_present() ||
		true === $this->pro_captcha->is_human_made_request() ) {
			return;
		}

		wp_die(
			esc_html( $this->pro_captcha->get_validation_error_message() ),
			'Procaptcha',
			array(
				'back_link' => true,
				'response'  => 303,
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'the_password_form', array( $this, 'add_form_field' ), 10, 2 );
		add_action( 'login_form_postpass', array( $this, 'verify_submission' ) );
	}
}
