<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Settings;

abstract class Form_Integration implements Form_Integration_Interface {
	protected Settings $settings;
	protected Pro_Captcha $pro_captcha;

	public function __construct( Settings $settings, Pro_Captcha $pro_captcha ) {
		$this->settings    = $settings;
		$this->pro_captcha = $pro_captcha;
	}

	// Note: this function is available only after the 'set_current_user' hook.
	public function is_captcha_present(): bool {
		$is_user_authorized = wp_get_current_user()->exists();

		return apply_filters( 'prosopo/procaptcha/is_captcha_present', false === $is_user_authorized );
	}
}
