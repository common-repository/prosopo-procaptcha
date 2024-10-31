<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;

defined( 'ABSPATH' ) || exit;

class Fluent_Forms extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array(
			'\FluentForm\App\Services\FormBuilder\BaseFieldManager',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Field( $this->pro_captcha, $this );
			}
		);
	}
}
