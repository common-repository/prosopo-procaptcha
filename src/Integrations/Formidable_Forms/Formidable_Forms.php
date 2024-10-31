<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;

class Formidable_Forms extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array( 'FrmAppHelper' );
	}

	public function get_field_class( string $class_name, string $field_type ): string {
		if ( self::SHORTCODE_NAME !== $field_type ) {
			return $class_name;
		}

		return Formidable_Form_Field::class;
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function sign_up_field_type( array $fields ): array {
		return array_merge(
			$fields,
			array(
				self::SHORTCODE_NAME => array(
					'name' => self::get_field_name(),
					'icon' => 'frm_icon_font frm_shield_check_icon',
				),
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		// static is a necessary workaround, since the field constructor is called by the form plugin.
		Formidable_Form_Field::$pro_captcha        = $this->pro_captcha;
		Formidable_Form_Field::$plugin_integration = $this;

		add_filter( 'frm_get_field_type_class', array( $this, 'get_field_class' ), 10, 2 );
		add_filter( 'frm_available_fields', array( $this, 'sign_up_field_type' ) );
	}
}
