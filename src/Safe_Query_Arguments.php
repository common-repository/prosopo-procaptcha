<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

trait Safe_Query_Arguments {
	/**
	 * @param mixed $value
	 */
	private function sanitize_to_string( $value ): string {
		if ( true === is_numeric( $value ) ) {
			$value = (string) $value;
		}

		if ( false === is_string( $value ) ) {
			return '';
		}

		$value = wp_unslash( $value );
		$value = sanitize_text_field( $value );

		return trim( $value );
	}

	/**
	 * @return array<int|string,mixed>
	 */
	private function get_from_source( string $from ): array {
		switch ( $from ) {
			case 'get':
				// phpcs:ignore WordPress.Security.NonceVerification
				return $_GET;
			case 'post':
				// phpcs:ignore WordPress.Security.NonceVerification
				return $_POST;
			case 'server':
				// phpcs:ignore WordPress.Security.NonceVerification
				return $_SERVER;
			default:
				return array();
		}
	}

	protected function get_query_string_arg_for_non_action( string $arg_name, string $from = 'get' ): string {
		$source = $this->get_from_source( $from );

		if ( false === key_exists( $arg_name, $source ) ) {
			return '';
		}

		return $this->sanitize_to_string( $source[ $arg_name ] );
	}

	protected function get_query_int_arg_for_non_action(
		string $arg_name,
		string $from = 'get'
	): int {
		$value = $this->get_query_string_arg_for_non_action( $arg_name, $from );

		return '' !== $value &&
				true === is_numeric( $value ) ?
			(int) $value :
			0;
	}

	protected function get_query_bool_arg_for_non_action(
		string $arg_name,
		string $from = 'get'
	): bool {
		$value = $this->get_query_string_arg_for_non_action( $arg_name, $from );

		return 'on' === $value ||
			'1' === $value;
	}

	protected function get_query_string_arg_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): string {
		$source = $this->get_from_source( $from );

		if ( false === key_exists( $arg_name, $source ) ) {
			return '';
		}

		if ( false === check_admin_referer( $nonce_action_name ) ) {
			return '';
		}

		return $this->get_query_string_arg_for_non_action( $arg_name, $from );
	}

	protected function get_query_int_arg_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): int {
		$value = $this->get_query_string_arg_for_admin_action( $arg_name, $nonce_action_name, $from );

		return '' !== $value &&
				true === is_numeric( $value ) ?
			(int) $value :
			0;
	}

	protected function get_query_bool_arg_for_admin_action(
		string $arg_name,
		string $nonce_action_name,
		string $from = 'get'
	): bool {
		$value = $this->get_query_string_arg_for_admin_action( $arg_name, $nonce_action_name, $from );

		return 'on' === $value;
	}
}
