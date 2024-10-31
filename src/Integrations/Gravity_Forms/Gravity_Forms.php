<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Gravity_Forms;

use GF_Fields;
use Io\Prosopo\Procaptcha\Integrations\Plugin_Integration;

defined( 'ABSPATH' ) || exit;

class Gravity_Forms extends Plugin_Integration {
	/**
	 * @return string[]
	 */
	protected function get_plugin_classes(): array {
		return array(
			'GF_Fields',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		Gravity_Form_Field::$pro_captcha        = $this->pro_captcha;
		Gravity_Form_Field::$plugin_integration = $this;

		if ( true === class_exists( 'GF_Fields' ) &&
		true === is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Form_Field() );
		}
	}
}
