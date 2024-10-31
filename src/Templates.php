<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use WP_Filesystem_Base;

class Templates {
	private ?WP_Filesystem_Base $wp_filesystem;

	public function __construct() {
		$this->wp_filesystem = null;
	}

	protected function get_wp_filesystem(): WP_Filesystem_Base {
		if ( null === $this->wp_filesystem ) {
			global $wp_filesystem;

			require_once ABSPATH . 'wp-admin/includes/file.php';

			WP_Filesystem();

			$this->wp_filesystem = $wp_filesystem;
		}

		return $this->wp_filesystem;
	}

	/**
	 * @param array<string,mixed> $args
	 */
	protected function print( string $name, array $args = array() ): void {
		$path_to_view = implode(
			DIRECTORY_SEPARATOR,
			array( __DIR__, '..', 'templates', $name . '.php' )
		);

		$wp_filesystem = $this->get_wp_filesystem();

		if ( false === $wp_filesystem->is_file( $path_to_view ) ) {
			return;
		}

		$view = $args;

		include $path_to_view;
	}

	/**
	 * @param array<string,mixed> $args
	 */
	protected function print_js( string $name, array $args = array() ): void {
		ob_start();
		$this->print( $name, $args );

		$safe_js = (string) ob_get_clean();

		// remove all multiline comments.
		$safe_js = preg_replace( '|\/\*[\s\S]+\*\/|U', '', $safe_js );
		$safe_js = null !== $safe_js ?
			$safe_js :
			'';

		// remove all single line comments.
		// \s at the begin is used to make sure url's aren't affected, e.g. 'url(http://example.com)' in CSS.
		$safe_js = preg_replace( '|[\s]+\/\/(.?)+\n|', '', $safe_js );
		$safe_js = null !== $safe_js ?
			$safe_js :
			'';

		// remove unnecessary spaces.
		$safe_js = str_replace( array( "\t", "\n", "\r" ), '', $safe_js );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $safe_js;
	}

	/**
	 * @param array<string,mixed> $field_values
	 */
	public function print_settings_page(
		bool $is_just_saved,
		string $form_nonce,
		array $field_values,
		Pro_Captcha $pro_captcha
	): void {
		$this->print(
			'settings-page',
			array(
				'is_just_saved' => $is_just_saved,
				'form_nonce'    => $form_nonce,
				'field_values'  => $field_values,
				'themes'        => $pro_captcha->get_themes(),
				'types'         => $pro_captcha->get_types(),
				'pro_captcha'   => $pro_captcha,
			)
		);
	}

	public function print_captcha_validation_element( string $error_message, bool $is_visible ): void {
		$this->print(
			'captcha-validation-element',
			array(
				'error_message' => $error_message,
				'is_visible'    => $is_visible,
			)
		);
	}

	public function print_captcha_validation_js(): void {
		$this->print_js( 'captcha-validation-js' );
	}

	/**
	 * @param array<string,mixed> $captcha_attributes
	 */
	public function print_captcha_element_js( array $captcha_attributes ): void {
		$this->print_js(
			'captcha-element-js',
			array(
				'captcha_attributes' => $captcha_attributes,
			)
		);
	}

	public function print_integration_js( string $integration ): void {
		$this->print_js( 'integrations' . DIRECTORY_SEPARATOR . $integration );
	}
}
