<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Settings {
	use Safe_Array_Arguments;

	const OPTION_NAME = 'prosopo-procaptcha__settings';

	private string $site_key;
	private string $secret_key;
	private string $theme;
	private string $type;
	private bool $is_on_wp_login_form;
	private bool $is_on_wp_register_form;
	private bool $is_on_wp_lost_password_form;
	private bool $is_on_wp_comment_form;
	private bool $is_on_wp_post_form;

	public function __construct() {
		$this->site_key                    = '';
		$this->secret_key                  = '';
		$this->theme                       = '';
		$this->type                        = '';
		$this->is_on_wp_login_form         = false;
		$this->is_on_wp_register_form      = false;
		$this->is_on_wp_lost_password_form = false;
		$this->is_on_wp_comment_form       = false;
		$this->is_on_wp_post_form          = false;
	}

	/**
	 * @return mixed
	 */
	protected function get_option( string $name ) {
		return get_option( $name, '' );
	}

	/**
	 * Autoload = true, to avoid real requests to the DB, as settings are common for all
	 *
	 * @param mixed $value
	 */
	protected function update_option( string $name, $value, bool $is_autoload = true ): void {
		update_option( $name, $value, $is_autoload );
	}

	protected function delete_option( string $name ): void {
		delete_option( $name );
	}

	public function load(): void {
		$settings = $this->get_option( self::OPTION_NAME );

		$settings = true === is_array( $settings ) ?
			$settings :
			array();

		$this->site_key   = $this->get_string_arg( 'site_key', $settings );
		$this->secret_key = $this->get_string_arg( 'secret_key', $settings );
		// Style preferences.
		$this->theme = $this->get_string_arg( 'theme', $settings );
		$this->theme = '' === $this->theme ?
			Pro_Captcha::DEFAULT_THEME :
			$this->theme;
		$this->type  = $this->get_string_arg( 'type', $settings );
		$this->type  = '' === $this->type ?
			Pro_Captcha::DEFAULT_TYPE :
			$this->type;
		// WP integration.
		$this->is_on_wp_login_form         = $this->get_bool_arg( 'is_on_wp_login_form', $settings );
		$this->is_on_wp_register_form      = $this->get_bool_arg( 'is_on_wp_register_form', $settings );
		$this->is_on_wp_lost_password_form = $this->get_bool_arg( 'is_on_wp_lost_password_form', $settings );
		$this->is_on_wp_comment_form       = $this->get_bool_arg( 'is_on_wp_comment_form', $settings );
		$this->is_on_wp_post_form          = $this->get_bool_arg( 'is_on_wp_post_form', $settings );
	}

	public function save(): void {
		$this->update_option(
			self::OPTION_NAME,
			array(
				'site_key'                    => $this->site_key,
				'secret_key'                  => $this->secret_key,
				// Style preferences.
				'theme'                       => $this->theme,
				'type'                        => $this->type,
				// WP integration.
				'is_on_wp_login_form'         => $this->is_on_wp_login_form,
				'is_on_wp_register_form'      => $this->is_on_wp_register_form,
				'is_on_wp_lost_password_form' => $this->is_on_wp_lost_password_form,
				'is_on_wp_comment_form'       => $this->is_on_wp_comment_form,
				'is_on_wp_post_form'          => $this->is_on_wp_post_form,
			)
		);
	}

	public function delete_all(): void {
		$this->delete_option( self::OPTION_NAME );
	}

	public function get_site_key(): string {
		return $this->site_key;
	}

	public function set_site_key( string $site_key ): void {
		$this->site_key = $site_key;
	}

	public function get_secret_key(): string {
		return $this->secret_key;
	}

	public function set_secret_key( string $secret_key ): void {
		$this->secret_key = $secret_key;
	}

	// Style preferences.

	public function get_theme(): string {
		return $this->theme;
	}

	public function set_theme( string $theme ): void {
		$this->theme = $theme;
	}

	public function get_type(): string {
		return $this->type;
	}

	public function set_type( string $type ): void {
		$this->type = $type;
	}

	// WP integration.

	public function is_on_wp_login_form(): bool {
		return $this->is_on_wp_login_form;
	}

	public function set_is_on_wp_login_form( bool $is_on_wp_login_form ): void {
		$this->is_on_wp_login_form = $is_on_wp_login_form;
	}

	public function is_on_wp_register_form(): bool {
		return $this->is_on_wp_register_form;
	}

	public function set_is_on_wp_register_form( bool $is_on_wp_register_form ): void {
		$this->is_on_wp_register_form = $is_on_wp_register_form;
	}

	public function is_on_wp_post_form(): bool {
		return $this->is_on_wp_post_form;
	}

	public function set_is_on_wp_post_form( bool $is_on_wp_post_form ): void {
		$this->is_on_wp_post_form = $is_on_wp_post_form;
	}

	public function is_on_wp_comment_form(): bool {
		return $this->is_on_wp_comment_form;
	}

	public function set_is_on_wp_comment_form( bool $is_on_wp_comment_form ): void {
		$this->is_on_wp_comment_form = $is_on_wp_comment_form;
	}

	public function is_on_wp_lost_password_form(): bool {
		return $this->is_on_wp_lost_password_form;
	}

	public function set_is_on_wp_lost_password_form( bool $is_on_wp_lost_password_form ): void {
		$this->is_on_wp_lost_password_form = $is_on_wp_lost_password_form;
	}
}
