<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations;

defined( 'ABSPATH' ) || exit;

abstract class Plugin_Integration extends Form_Integration {
	// Do not use dash, it doesn't work with some (e.g. ContactForm7).
	const SHORTCODE_NAME = 'prosopo_procaptcha';

	/**
	 * 1. Using classes instead of plugin names, since is_active_plugin doesn't work on the front.
	 * 2. Support multiple, since one plugin can have both Lite and Pro versions.
	 *
	 * @return string[]
	 */
	abstract protected function get_plugin_classes(): array;

	public static function get_field_name(): string {
		return __( 'Prosopo Procaptcha', 'prosopo-procaptcha' );
	}

	public function is_active( bool $is_admin_area ): bool {
		$plugin_classes = $this->get_plugin_classes();

		foreach ( $plugin_classes as $plugin_class ) {
			if ( false === class_exists( $plugin_class, false ) ) {
				continue;
			}

			return true;
		}

		return false;
	}
}
