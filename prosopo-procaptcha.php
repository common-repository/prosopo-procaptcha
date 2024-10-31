<?php
/**
 * Plugin Name: Prosopo Procaptcha
 * Description: GDPR compliant, privacy friendly and better value captcha.
 * Version: 1.3.0
 * Author: Prosopo Team
 * Author URI: https://prosopo.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prosopo-procaptcha
 * Domain Path: /lang
 */

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

require_once join( DIRECTORY_SEPARATOR, array( __DIR__, 'src', 'Autoloader.php' ) );

new Autoloader( __NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'src' );

( new Plugin( __FILE__ ) )->set_hooks( is_admin() );
