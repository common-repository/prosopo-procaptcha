<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

use WP_Error;

defined( 'ABSPATH' ) || exit;

class Pro_Captcha implements Hooks_Interface {
	use Safe_Array_Arguments;
	use Safe_Query_Arguments;

	const SCRIPT_URL                 = 'https://js.prosopo.io/js/procaptcha.bundle.js';
	const API_URL                    = 'https://api.prosopo.io/siteverify';
	const FORM_FIELD_NAME            = 'procaptcha-response';
	const ALLOW_BYPASS_CONSTANT_NAME = 'PROSOPO_PROCAPTCHA_ALLOW_BYPASS';
	const DEFAULT_THEME              = 'light';
	const DEFAULT_TYPE               = 'frictionless';

	private bool $is_in_use;
	private bool $is_validation_js_in_use;
	private Settings $settings;
	private Templates $templates;
	/**
	 * Some JS-based forms (like NinjaForms) require a separate JS integration.
	 *
	 * @var string[]
	 */
	private array $integrations_js;

	public function __construct( Settings $settings, Templates $templates ) {
		$this->is_in_use               = false;
		$this->is_validation_js_in_use = false;
		$this->settings                = $settings;
		$this->templates               = $templates;
		$this->integrations_js         = array();
	}

	/**
	 * @param array<string,mixed> $attributes
	 */
	protected function print_opening_tag( string $tag, array $attributes = array() ): void {
		printf( '<%s', esc_html( $tag ) );

		foreach ( $attributes as $attr_name => $attr_value ) {
			if ( false === is_string( $attr_value ) &&
				false === is_numeric( $attr_value ) ) {
				continue;
			}

			printf( ' %s="%s"', esc_attr( $attr_name ), esc_attr( (string) $attr_value ) );
		}
		echo '>';
	}

	/**
	 * @param array<string,mixed> $attributes
	 * @param string[] $extra_classes
	 *
	 * @return array<string,mixed>
	 */
	protected function turn_classes_into_class_attribute( array $attributes, array $extra_classes = array() ): array {
		$attributes['classes'] = $this->get_array_arg( 'classes', $attributes );
		$attributes['classes'] = array_merge( $attributes['classes'], $extra_classes );
		$attributes['class']   = implode( ' ', $attributes['classes'] );

		unset( $attributes['classes'] );

		return $attributes;
	}

	public function get_validation_error_message(): string {
		$message = __( 'Please verify that you are human.', 'prosopo-procaptcha' );

		return apply_filters( 'prosopo/procaptcha/validation_error_message', $message );
	}

	public function enable_on_page(): void {
		$this->is_in_use = true;
	}

	public function maybe_enqueue_captcha_js(): void {
		if ( false === $this->is_in_use ) {
			return;
		}

		// do not use wp_enqueue_module() because it doesn't work on the login screens.
		wp_enqueue_script(
			'prosopo-procaptcha',
			self::SCRIPT_URL,
			array(),
			// Don't add any version, since it's remote, and can be changed regardless of the releases.
			null, // @phpcs:ignore
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$captcha_attributes = array(
			'siteKey'     => $this->settings->get_site_key(),
			'theme'       => $this->settings->get_theme(),
			'captchaType' => $this->settings->get_type(),
		);

		$captcha_attributes = apply_filters( 'prosopo/procaptcha/captcha_attributes', $captcha_attributes );

		$this->templates->print_captcha_element_js( $captcha_attributes );

		if ( true === $this->is_validation_js_in_use ) {
			$this->templates->print_captcha_validation_js();
		}

		foreach ( $this->integrations_js as $integration_js ) {
			$this->templates->print_integration_js( $integration_js );
		}
	}

	public function maybe_add_async_attribute( string $tag, string $handle, string $src ): string {
		if (
			'prosopo-procaptcha' !== $handle ||
			// make sure we don't make it twice if other Procaptcha integrations are present.
			false !== strpos( 'type="module"', $tag )
		) {
			return $tag;
		}

		// for old WP versions.
		$tag = str_replace( ' type="text/javascript"', '', $tag );

		return str_replace( 'src', 'type="module" src', $tag );
	}

	/**
	 * @param array<string,mixed> $attributes
	 * @param bool $is_without_client_validation In case the form includes its own validation.
	 */
	public function print_form_field(
		array $attributes = array(),
		bool $is_return = false,
		bool $is_without_client_validation = false
	): string {
		// automatically mark as in use.
		$this->is_in_use = true;

		if ( true === $is_return ) {
			ob_start();
		}

		$hidden_input_attrs = array();

		if ( true === key_exists( 'hidden_input', $attributes ) ) {
			/**
			 * @var array<string,mixed> $hidden_input_attrs
			 */
			$hidden_input_attrs = array_merge(
				array(
					'type' => 'hidden',
				),
				$this->get_array_arg( 'hidden_input', $attributes )
			);

			unset( $attributes['hidden_input'] );

			$hidden_input_attrs = $this->turn_classes_into_class_attribute(
				$hidden_input_attrs,
				array(
					'prosopo-procaptcha-input',
				)
			);
		}

		// 'classes' instead of 'class' to avoid been overridden by a mistake.
		$attributes = $this->turn_classes_into_class_attribute(
			$attributes,
			array(
				'prosopo-procaptcha-wrapper',
			)
		);

		unset( $attributes['classes'] );

		// 'styles' instead of 'style' to avoid been overridden by a mistake.
		$style                = '';
		$attributes['styles'] = $this->get_array_arg( 'styles', $attributes );
		$attributes['styles'] = array_merge(
			array(
				'display' => 'block', // add d:block since it's a web component.
			),
			$attributes['styles']
		);

		foreach ( $attributes['styles'] as $style_name => $style_value ) {
			$style_name  = true === is_string( $style_name ) ?
				$style_name :
				'';
			$style_value = true === is_string( $style_value ) ||
							true === is_numeric( $style_value ) ?
				$style_value :
				'';

			$style .= sprintf( '%s:%s;', $style_name, $style_value );
		}
		unset( $attributes['styles'] );
		$attributes['style'] = $style;

		$is_error_visible = false;

		if ( true === key_exists( 'is_error_visible', $attributes ) ) {
			$is_error_visible = $this->get_bool_arg( 'is_error_visible', $attributes );
			unset( $attributes['is_error_visible'] );
		}

		$this->print_opening_tag( 'prosopo-procaptcha-wrapper', $attributes );

		echo '<div class="prosopo-procaptcha"></div>';

		if ( false === $is_without_client_validation ) {
			$this->is_validation_js_in_use = true;
			$this->templates->print_captcha_validation_element( $this->get_validation_error_message(), $is_error_visible );
		}

		if ( array() !== $hidden_input_attrs ) {
			$this->print_opening_tag( 'input', $hidden_input_attrs );
		}

		echo '</prosopo-procaptcha-wrapper>'; // Close the form field div.

		if ( true === $is_return ) {
			return (string) ob_get_clean();
		}

		return '';
	}

	public function add_integration_js( string $integration ): void {
		$this->integrations_js[] = $integration;
	}

	/**
	 * @param string|null $token Allows to define the token value for JS-based custom forms (like NinjaForms).
	 */
	public function is_human_made_request( ?string $token = null ): bool {
		$token = null === $token ?
			$this->get_query_string_arg_for_non_action( self::FORM_FIELD_NAME, 'post' ) :
			$token;

		// bail early if the token is empty.
		if ( '' === $token ) {
			return false;
		}

		if ( defined( self::ALLOW_BYPASS_CONSTANT_NAME ) &&
			true === constant( self::ALLOW_BYPASS_CONSTANT_NAME ) &&
			'bypass' === $token ) {
			return true;
		}

		$response = wp_remote_post(
			self::API_URL,
			array(
				'method'  => 'POST',
				// limit waiting time to 20 seconds.
				'timeout' => 20,
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => (string) wp_json_encode(
					array(
						'secret' => $this->settings->get_secret_key(),
						'token'  => $token,
					)
				),
			)
		);

		if ( true === is_wp_error( $response ) ) {
			// something went wrong, maybe connection issue, but we still shouldn't allow the request.
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );
		$body = true === is_array( $body ) ?
			$body :
			array();

		$is_verified = $this->get_bool_arg( 'verified', $body );

		return true === $is_verified;
	}

	public function add_validation_error( WP_Error $error = null ): WP_Error {
		$error_code    = 'procaptcha-failed';
		$error_data    = 400; // must be numeric, e.g. for the WP comment form, likely HTTP code.
		$error_message = $this->get_validation_error_message();

		if ( null === $error ) {
			$error = new WP_Error( $error_code, $error_message, $error_data );
		} else {
			$error->add( $error_code, $error_message, $error_data );
		}

		return $error;
	}

	/**
	 * @return array<string,string>
	 */
	public function get_themes(): array {
		return array(
			'light' => __( 'Light', 'prosopo-procaptcha' ),
			'dark'  => __( 'Dark', 'prosopo-procaptcha' ),
		);
	}

	/**
	 * @return array<string,string>
	 */
	public function get_types(): array {
		return array(
			'frictionless' => __( 'Frictionless', 'prosopo-procaptcha' ),
			'pow'          => __( 'Pow', 'prosopo-procaptcha' ),
			'image'        => __( 'Image', 'prosopo-procaptcha' ),
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'maybe_add_async_attribute' ), 10, 3 );

		$hook = true === $is_admin_area ?
			'admin_print_footer_scripts' :
			'wp_print_footer_scripts';

		// priority must be less than 10, to make sure the wp_enqueue_script still has effect.
		add_action( $hook, array( $this, 'maybe_enqueue_captcha_js' ), 9 );
	}
}
