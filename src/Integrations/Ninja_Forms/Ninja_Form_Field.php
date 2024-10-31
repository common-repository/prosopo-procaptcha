<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Ninja_Forms;

use Io\Prosopo\Procaptcha\Integrations\Form_Integration_Interface;
use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Safe_Array_Arguments;

defined( 'ABSPATH' ) || exit;

class Ninja_Form_Field extends \NF_Abstracts_Input {
	use Safe_Array_Arguments;

	private Pro_Captcha $pro_captcha;
	private Form_Integration_Interface $form_integration;

	public function __construct( Pro_Captcha $pro_captcha, Form_Integration_Interface $form_integration ) {
		parent::__construct();

		$field_name = Ninja_Forms::SHORTCODE_NAME;

		$this->pro_captcha      = $pro_captcha;
		$this->form_integration = $form_integration;
		$this->_name            = $field_name;
		$this->_nicename        = Ninja_Forms::get_field_name();
		$this->_type            = $field_name;
		$this->_templates       = array( $field_name );
		$this->_section         = 'misc';
		$this->_icon            = 'filter';

		add_filter( sprintf( 'ninja_forms_localize_field_%s', $field_name ), array( $this, 'render_field' ) );
	}

	/**
	 * @param array<string,mixed> $field
	 *
	 * @return array<string,mixed>
	 */
	public function render_field( array $field ): array {
		$element = '';

		if ( true === $this->form_integration->is_captcha_present() ) {
			$element  = $this->pro_captcha->print_form_field(
				array(
					'classes' => array(
						// Without this class, the border around field won't appear when its validation is failed.
						'ninja-forms-field',
					),
					'styles'  => array(
						'padding' => '0',
					),
				),
				true,
				true
			);
			$element .= '<prosopo-procaptcha-ninja-forms-integration></prosopo-procaptcha-ninja-forms-integration>';
			$this->pro_captcha->add_integration_js( 'ninja-forms' );
		}

		$settings = $this->get_array_arg( 'settings', $field );
		$settings = array_merge(
			$settings,
			array(
				'procaptcha' => $element,
				'label_pos'  => 'hidden', // Hide the label.
			)
		);

		return array_merge(
			$field,
			array(
				'settings' => $settings,
			)
		);
	}

	/**
	 * Validate
	 *
	 * @param mixed $field
	 * @param mixed $data
	 * @return mixed[] $errors
	 */
	public function validate( $field, $data ) {
		if ( false === is_array( $field ) ||
		false === is_array( $data ) ||
		false === $this->form_integration->is_captcha_present() ) {
			return array();
		}

		$token = $this->get_string_arg( 'value', $field );

		if ( false === $this->pro_captcha->is_human_made_request( $token ) ) {
			// For some reason it doesn't display error if array is returned...
			return $this->pro_captcha->get_validation_error_message(); // @phpstan-ignore-line.
		}

		return array();
	}
}
