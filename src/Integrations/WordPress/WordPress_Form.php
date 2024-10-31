<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use Io\Prosopo\Procaptcha\Integrations\Form_Integration;

defined( 'ABSPATH' ) || exit;

abstract class WordPress_Form extends Form_Integration {
	protected function get_print_field_action(): string {
		return '';
	}

	public function print_form_field(): void {
		$this->pro_captcha->print_form_field(
			array(
				'styles' => array(
					'margin' => '0 0 10px',
				),
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		$print_field_action = $this->get_print_field_action();

		// it can be missing, if a special approach is required.
		if ( '' === $print_field_action ) {
			return;
		}

		add_action( $print_field_action, array( $this, 'print_form_field' ) );
	}
}
