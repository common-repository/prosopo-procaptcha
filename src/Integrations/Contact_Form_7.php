<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations;

defined( 'ABSPATH' ) || exit;

// Note: CF7 v5.9.8 calls the RestAPI without the nonce, so we can't omit captcha for authorized users.
class Contact_Form_7 extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array(
			'WPCF7',
		);
	}

	public function add_field(): void {
		if ( false === function_exists( 'wpcf7_add_form_tag' ) ) {
			return;
		}

		wpcf7_add_form_tag(
			self::SHORTCODE_NAME,
			array( $this, 'print_field' ),
			array(
				'display-block' => true,
			)
		);
	}

	public function print_field(): string {
		ob_start();

		printf(
			'<div class="wpcf7-form-control-wrap" data-name="%s">',
			esc_attr( self::SHORTCODE_NAME ),
		);

		$this->pro_captcha->print_form_field(
			array(
				'classes' => array(
					'wpcf7-form-control',
				),
			),
			false,
			// no validation, since CF7 has its own.
			true
		);

		echo '</div>';

		return (string) ( ob_get_clean() );
	}

	/**
	 * @param object $result
	 * @param object $tag
	 *
	 * @return object
	 */
	public function validate( $result, $tag ) {
		if ( true === property_exists( $tag, 'name' ) &&
			'' === $tag->name ) {
			$tag->name = self::SHORTCODE_NAME;
		}

		if ( true === $this->pro_captcha->is_human_made_request() ) {
			return $result;
		}

		if ( true === method_exists( $result, 'invalidate' ) ) {
			$result->invalidate( $tag, $this->pro_captcha->get_validation_error_message() );
		}

		return $result;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'wpcf7_init', array( $this, 'add_field' ) );

		add_filter(
			sprintf( 'wpcf7_validate_%s', self::SHORTCODE_NAME ),
			array( $this, 'validate' ),
			10,
			2
		);
	}
}
