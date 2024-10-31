<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Settings_Page implements Hooks_Interface {
	use Safe_Query_Arguments;

	const MENU_SLUG  = 'prosopo-procaptcha';
	const FORM_NONCE = 'prosopo-captcha__settings';

	private Templates $templates;
	private Settings $settings;
	private Plugin $plugin;
	private Pro_Captcha $pro_captcha;

	public function __construct(
		Plugin $plugin,
		Templates $templates,
		Settings $settings,
		Pro_Captcha $pro_captcha
	) {
		$this->plugin      = $plugin;
		$this->templates   = $templates;
		$this->settings    = $settings;
		$this->pro_captcha = $pro_captcha;
	}

	protected function maybe_process_form(): bool {
		if ( false === current_user_can( 'manage_options' ) ) {
			return false;
		}

		$is_submitted = '' !== $this->get_query_string_arg_for_admin_action(
			'prosopo-captcha__submit',
			self::FORM_NONCE,
			'post'
		);

		if ( false === $is_submitted ) {
			return false;
		}

		$supported_themes = array_keys( $this->pro_captcha->get_themes() );
		$supported_types  = array_keys( $this->pro_captcha->get_types() );

		$site_key   = $this->get_query_string_arg_for_admin_action(
			'prosopo-captcha__site-key',
			self::FORM_NONCE,
			'post'
		);
		$secret_key = $this->get_query_string_arg_for_admin_action(
			'prosopo-captcha__secret-key',
			self::FORM_NONCE,
			'post'
		);
		// Style preferences.
		$theme = $this->get_query_string_arg_for_admin_action(
			'prosopo-captcha__theme',
			self::FORM_NONCE,
			'post'
		);
		$theme = true === in_array( $theme, $supported_themes, true ) ?
			$theme :
			Pro_Captcha::DEFAULT_THEME;
		$type  = $this->get_query_string_arg_for_admin_action(
			'prosopo-captcha__type',
			self::FORM_NONCE,
			'post'
		);
		$type  = true === in_array( $type, $supported_types, true ) ?
			$type :
			Pro_Captcha::DEFAULT_TYPE;
		// WP integration.
		$is_on_wp_login_form         = $this->get_query_bool_arg_for_admin_action(
			'prosopo-captcha__wp-login-form',
			self::FORM_NONCE,
			'post'
		);
		$is_on_wp_register_form      = $this->get_query_bool_arg_for_admin_action(
			'prosopo-captcha__wp-register-form',
			self::FORM_NONCE,
			'post'
		);
		$is_on_wp_lost_password_form = $this->get_query_bool_arg_for_admin_action(
			'prosopo-captcha__wp-lost-password-form',
			self::FORM_NONCE,
			'post'
		);
		$is_on_wp_comment_form       = $this->get_query_bool_arg_for_admin_action(
			'prosopo-captcha__wp-comment-form',
			self::FORM_NONCE,
			'post'
		);
		$is_on_wp_post_form          = $this->get_query_bool_arg_for_admin_action(
			'prosopo-captcha__wp-post-form',
			self::FORM_NONCE,
			'post'
		);

		$this->settings->set_site_key( $site_key );
		$this->settings->set_secret_key( $secret_key );
		// Style preferences.
		$this->settings->set_theme( $theme );
		$this->settings->set_type( $type );
		// WP integration.
		$this->settings->set_is_on_wp_login_form( $is_on_wp_login_form );
		$this->settings->set_is_on_wp_register_form( $is_on_wp_register_form );
		$this->settings->set_is_on_wp_lost_password_form( $is_on_wp_lost_password_form );
		$this->settings->set_is_on_wp_comment_form( $is_on_wp_comment_form );
		$this->settings->set_is_on_wp_post_form( $is_on_wp_post_form );

		$this->settings->save();

		return true;
	}

	public function print_settings_page(): void {
		$is_just_saved = $this->maybe_process_form();

		$field_values = array(
			'site_key'                    => $this->settings->get_site_key(),
			'secret_key'                  => $this->settings->get_secret_key(),
			// Style preferences.
			'theme'                       => $this->settings->get_theme(),
			'type'                        => $this->settings->get_type(),
			// WP integration.
			'is_on_wp_login_form'         => $this->settings->is_on_wp_login_form(),
			'is_on_wp_register_form'      => $this->settings->is_on_wp_register_form(),
			'is_on_wp_lost_password_form' => $this->settings->is_on_wp_lost_password_form(),
			'is_on_wp_comment_form'       => $this->settings->is_on_wp_comment_form(),
			'is_on_wp_post_form'          => $this->settings->is_on_wp_post_form(),
		);

		$this->templates->print_settings_page(
			$is_just_saved,
			wp_create_nonce( self::FORM_NONCE ),
			$field_values,
			$this->pro_captcha
		);
	}

	public function register_settings_page(): void {
		add_options_page(
			__( 'Procaptcha Settings', 'prosopo-procaptcha' ),
			__( 'Procaptcha', 'prosopo-procaptcha' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'print_settings_page' )
		);
	}

	/**
	 * @param string[] $links
	 *
	 * @return string[]
	 */
	public function add_settings_link_to_plugin_list( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=' . self::MENU_SLUG ),
			esc_html__( 'Settings', 'prosopo-procaptcha' ),
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	public function maybe_enqueue_settings_page_assets(): void {
		$screen = get_current_screen();

		$settings_page_id = 'settings_page_' . self::MENU_SLUG;

		if ( null === $screen ||
			$settings_page_id !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'prosopo-procaptcha-settings',
			$this->plugin->get_asset_url( 'settings-page.css' ),
			array(),
			$this->plugin->get_version()
		);

		if ( '' !== $this->settings->get_site_key() ) {
			$this->pro_captcha->enable_on_page();
		}
	}

	public function set_hooks( bool $is_admin_area ): void {
		if ( false === $is_admin_area ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_settings_page_assets' ) );

		add_filter(
			sprintf( 'plugin_action_links_%s', $this->plugin->get_basename() ),
			array( $this, 'add_settings_link_to_plugin_list' )
		);
	}
}
