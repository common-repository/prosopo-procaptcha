<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Gravity_Forms;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;
use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Safe_Array_Arguments;

defined( 'ABSPATH' ) || exit;

class Gravity_Form_Field extends \GF_Field {
	use Safe_Array_Arguments;

	// static is a necessary workaround, since the field constructor is called by the form plugin.
	public static ?Pro_Captcha $pro_captcha               = null;
	public static ?Plugin_Integration $plugin_integration = null;

	public string $type = Gravity_Forms::SHORTCODE_NAME;

	protected function is_captcha_present(): bool {
		return null !== self::$plugin_integration &&
				true === self::$plugin_integration->is_captcha_present();
	}

	protected function is_human_made_request(): bool {
		return null !== self::$pro_captcha &&
				true === self::$pro_captcha->is_human_made_request();
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return Gravity_Forms::get_field_name();
	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @return string[]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// Some value is required, otherwise the editor produces the JS error.
			'duplicate_setting',
		);
	}

	/**
	 * Retrieve the field label.
	 *
	 * @since unknown
	 * @since 2.5     Move conditions about the singleproduct and calculation fields to their own class.
	 *
	 * @param bool   $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param string $value                The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 *
	 * @return string
	 */
	public function get_field_label( $force_frontend_label, $value ) {
		return Gravity_Forms::get_field_name();
	}

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--recaptcha';
	}

	/**
	 * Returns the field button properties for the form editor. The array contains two elements:
	 * 'group' => 'standard_fields' // or  'advanced_fields', 'post_fields', 'pricing_fields'
	 * 'text'  => 'Button text'
	 *
	 * Built-in fields don't need to implement this because the buttons are added in sequence in GFFormDetail
	 *
	 * @return array<string, string>
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon'  => $this->get_form_editor_field_icon(),
		);
	}

	/**
	 * Returns the field markup; including field label, description, validation, and the form editor admin buttons.
	 *
	 * The {FIELD} placeholder will be replaced in GFFormDisplay::get_field_content with the markup returned by GF_Field::get_field_input().
	 *
	 * @param string|array<string,mixed> $value                The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param bool $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param array<string,mixed> $form                 The Form Object currently being processed.
	 *
	 * @return string
	 */
	// @phpstan-ignore-next-line
	public function get_field_content( $value, $force_frontend_label, $form ) {
		if ( true === $this->is_form_editor() ) {
			return parent::get_field_content( $value, $force_frontend_label, $form );
		}

		$form_id               = $this->get_int_arg( 'id', $form );
		$validation_message_id = 'validation_message_' . $form_id . '_' . $this->id;

		$validation_message = true === $this->failed_validation &&
								true === is_string( $this->validation_message ) &&
								'' !== $this->validation_message ?
			sprintf(
				"<div id='%s' class='gfield_description validation_message gfield_validation_message'>%s</div>",
				esc_attr( $validation_message_id ),
				esc_html( $this->validation_message )
			) :
				'';

		if ( null !== self::$pro_captcha &&
		true === $this->is_captcha_present() ) {
			return self::$pro_captcha->print_form_field( array(), true, true ) .
					$validation_message;
		}

		return $validation_message;
	}

	/**
	 * Override this method to perform custom validation logic.
	 *
	 * Return the result (bool) by setting $this->failed_validation.
	 * Return the validation message (string) by setting $this->validation_message.
	 *
	 * @since 1.9
	 *
	 * @param string|array<string,mixed> $value The field value from get_value_submission().
	 * @param array<string,mixed>        $form  The Form Object currently being processed.
	 *
	 * @return void
	 */
	// @phpstan-ignore-next-line
	public function validate( $value, $form ) {

		if ( false === $this->is_captcha_present() ||
		true === $this->is_human_made_request() ) {
			return;
		}

		$this->failed_validation  = true;
		$this->validation_message = null !== self::$pro_captcha ?
			self::$pro_captcha->get_validation_error_message() :
		'';
	}
}
