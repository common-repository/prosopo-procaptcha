<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

use Io\Prosopo\Procaptcha\Integrations\Form_Integration_Interface;
use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Safe_Array_Arguments;

defined( 'ABSPATH' ) || exit;

class Fluent_Forms_Field extends \FluentForm\App\Services\FormBuilder\BaseFieldManager {
	use Safe_Array_Arguments;

	private Pro_Captcha $pro_captcha;
	private Form_Integration_Interface $form_integration;

	public function __construct( Pro_Captcha $pro_captcha, Form_Integration_Interface $form_integration ) {
		$this->pro_captcha      = $pro_captcha;
		$this->form_integration = $form_integration;

		parent::__construct(
			Fluent_Forms::SHORTCODE_NAME,
			Fluent_Forms::get_field_name(),
			array(
				'prosopo',
				'procaptcha',
				'captcha',
			),
			'advanced'
		);

		add_filter( "fluentform/validate_input_item_{$this->key}", array( $this, 'validate' ), 10, 5 );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function getComponent(): array {
		return array(
			'index'          => 1,
			'element'        => $this->key,
			'attributes'     => array(
				'name' => $this->key,
				'type' => 'text',
			),
			'settings'       => array(
				'label' => $this->title,
			),
			'editor_options' => array(
				'title'      => $this->title,
				'icon_class' => 'ff-edit-recaptha',
				'template'   => 'inputText',
			),
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	public function getGeneralEditorElements() {
		return array();
	}

	/**
	 * @param mixed $element
	 * @param mixed $form
	 *
	 * @return void
	 */
	public function render( $element, $form ) {
		if ( false === $this->form_integration->is_captcha_present() ) {
			return;
		}

		echo '<div class="ff-el-group">';
		$this->pro_captcha->print_form_field(
			array(
				'classes'      => array(
					'ff-el-input--content',
				),
				'hidden_input' => array(
					'name'      => $this->key,
					'data-name' => $this->key,
					'classes'   => array(
						'ff-el-form-control',
					),
				),
			)
		);
		echo '</div>';
	}

	/**
	 * @param string|string[] $error_message
	 * @param array<string,mixed> $field
	 * @param array<string,mixed> $form_data
	 * @param array<string,mixed> $fields
	 * @param object $form
	 *
	 * @return string|string[]
	 */
	public function validate( $error_message, array $field, $form_data, $fields, $form ) {
		$token = $this->get_string_arg( $this->key, $form_data );

		if ( false === $this->form_integration->is_captcha_present() ||
		true === $this->pro_captcha->is_human_made_request( $token ) ) {
			return $error_message;
		}

		return $this->pro_captcha->get_validation_error_message();
	}
}
