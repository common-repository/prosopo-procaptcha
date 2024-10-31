<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;

class BBPress extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array(
			'bbPress',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		$bb_press_forum = new BBPress_Forum( $this->pro_captcha, $this );
		$bb_press_forum->set_hooks( $is_admin_area );

		// bbPress also has its own account pages, but they have the same hooks as the WordPress ones.
	}
}
