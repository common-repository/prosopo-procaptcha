<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;

class Ninja_Forms extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array(
			'Ninja_Forms',
		);
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function register_field( array $fields ): array {
		/**
		 * @var array<string,mixed>
		 */
		return array_merge(
			$fields,
			array(
				self::SHORTCODE_NAME => new Ninja_Form_Field( $this->pro_captcha, $this ),
			)
		);
	}

	/**
	 * @param string[] $paths
	 *
	 * @return string[]
	 */
	public function register_templates_path( array $paths ): array {
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

		return $paths;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'ninja_forms_register_fields', array( $this, 'register_field' ) );
		add_filter( 'ninja_forms_field_template_file_paths', array( $this, 'register_templates_path' ) );
	}
}
