<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hooks_Interface;

interface Form_Integration_Interface extends Hooks_Interface {
	public function is_active( bool $is_admin_area ): bool;

	// By default, skips captcha for authorized users, also can be customized by the filter 'prosopo/procaptcha/is_captcha_present'.
	public function is_captcha_present(): bool;
}
