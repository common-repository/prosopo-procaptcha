<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Formidable_Forms;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;
use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Safe_Array_Arguments;

defined( 'ABSPATH' ) || exit;

class Formidable_Form_Field extends \FrmFieldType {
	use Safe_Array_Arguments;

	// static is a necessary workaround, since the field constructor is called by the form plugin.
	public static ?Pro_Captcha $pro_captcha               = null;
	public static ?Plugin_Integration $plugin_integration = null;

	/**
	 * @var string
	 */
	protected $type = Formidable_Forms::SHORTCODE_NAME;
	/**
	 * @var bool
	 */
	protected $has_input = false;
	/**
	 * @var bool
	 */
	protected $has_html = false;

	protected function get_field_key( string $field_id ): string {
		return 'field' . $field_id;
	}

	protected function is_captcha_present(): bool {
		return null !== self::$plugin_integration &&
		true === self::$plugin_integration->is_captcha_present();
	}

	protected function is_human_made_request(): bool {
		return null !== self::$pro_captcha &&
				true === self::$pro_captcha->is_human_made_request();
	}

	/**
	 * @param mixed $field
	 *
	 * @return array<string, bool>
	 */
	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	/**
	 * @param array<string,mixed> $args
	 * @param array<string,mixed> $shortcode_atts
	 *
	 * @return string
	 */
	// @phpstan-ignore-next-line
	public function front_field_input( $args, $shortcode_atts ) {

		if ( false === $this->is_captcha_present() ||
		null === self::$pro_captcha ) {
			return '';
		}

		$field_id  = $this->get_string_arg( 'field_id', $args );
		$field_key = $this->get_field_key( $field_id );

		$form_errors = $this->get_array_arg( 'errors', $args );
		$is_error    = true === key_exists( $field_key, $form_errors );

		return self::$pro_captcha->print_form_field(
			array(
				'is_error_visible' => $is_error,
			),
			true
		);
	}

	/**
	 * @param array<string,mixed> $args
	 *
	 * @return array<string,string>
	 */
	// @phpstan-ignore-next-line
	public function validate( $args ) {
		$errors = array();

		if ( false === $this->is_captcha_present() ||
		true === $this->is_human_made_request() ) {
			return $errors;
		}

		$field_id  = $this->get_string_arg( 'id', $args );
		$field_key = $this->get_field_key( $field_id );

		$error_message = null !== self::$pro_captcha ?
			self::$pro_captcha->get_validation_error_message() :
			'';

		return array_merge(
			$errors,
			array(
				$field_key => $error_message,
			)
		);
	}
}
