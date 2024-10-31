<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\BBPress\BBPress;
use Io\Prosopo\Procaptcha\Integrations\Contact_Form_7;
use Io\Prosopo\Procaptcha\Integrations\Fluent_Forms\Fluent_Forms;
use Io\Prosopo\Procaptcha\Integrations\Form_Integration_Interface;
use Io\Prosopo\Procaptcha\Integrations\Formidable_Forms\Formidable_Forms;
use Io\Prosopo\Procaptcha\Integrations\Gravity_Forms\Gravity_Forms;
use Io\Prosopo\Procaptcha\Integrations\Ninja_Forms\Ninja_Forms;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Comment_Form;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Login_Form;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Lost_Password_Form;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Password_Protected_Form;
use Io\Prosopo\Procaptcha\Integrations\WordPress\Register_Form;

class Plugin implements Hooks_Interface {
	private string $version = '1.0.0';
	private string $plugin_file;
	private Settings $settings;
	private Pro_Captcha $pro_captcha;
	private Settings_Page $settings_page;

	public function __construct( string $plugin_file ) {
		$this->plugin_file   = $plugin_file;
		$this->settings      = new Settings();
		$templates           = new Templates();
		$this->pro_captcha   = new Pro_Captcha( $this->settings, $templates );
		$this->settings_page = new Settings_Page(
			$this,
			$templates,
			$this->settings,
			$this->pro_captcha
		);
	}

	/**
	 * @return Form_Integration_Interface[]
	 */
	protected function get_integrations(): array {
		return array(
			// WordPress.
			new Login_Form( $this->settings, $this->pro_captcha ),
			new Register_Form( $this->settings, $this->pro_captcha ),
			new Lost_Password_Form( $this->settings, $this->pro_captcha ),
			new Comment_Form( $this->settings, $this->pro_captcha ),
			new Password_Protected_Form( $this->settings, $this->pro_captcha ),
			// plugins.
			new BBPress( $this->settings, $this->pro_captcha ),
			new Fluent_Forms( $this->settings, $this->pro_captcha ),
			new Formidable_Forms( $this->settings, $this->pro_captcha ),
			new Gravity_Forms( $this->settings, $this->pro_captcha ),
			new Ninja_Forms( $this->settings, $this->pro_captcha ),
			new Contact_Form_7( $this->settings, $this->pro_captcha ),
		);
	}

	public function clear_data(): void {
		$this->settings->delete_all();
	}

	public function get_asset_url( string $asset ): string {
		return plugin_dir_url( $this->plugin_file ) . 'assets/' . $asset;
	}

	public function get_basename(): string {
		return plugin_basename( $this->plugin_file );
	}

	public function get_version(): string {
		return $this->version;
	}

	public function load_translations(): void {
		load_plugin_textdomain(
			'prosopo-procaptcha',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/lang'
		);
	}

	public function load_integrations(): void {
		$is_admin_area = is_admin();

		foreach ( $this->get_integrations() as $integration ) {
			if ( false === $integration->is_active( $is_admin_area ) ) {
				continue;
			}

			$integration->set_hooks( $is_admin_area );
		}
	}

	public function set_hooks( bool $is_admin_area ): void {
		// load at the begin, since it's used everywhere.
		$this->settings->load();

		add_action( 'init', array( $this, 'load_translations' ) );

		$this->settings_page->set_hooks( $is_admin_area );
		$this->pro_captcha->set_hooks( $is_admin_area );

		register_deactivation_hook( $this->plugin_file, array( $this, 'clear_data' ) );

		// do not init integrations if the site key is not set.
		if ( '' === $this->settings->get_site_key() ) {
			return;
		}

		/**
		 * 1. Since this hooks we can judge if some plugin is available.
		 * 2. Used -999 priority, as some plugins, like e.g. NinjaForms registers fields here, and we need to add hooks before it.
		 */
		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), -999 );
	}
}
