<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hooks_Interface;
use Io\Prosopo\Procaptcha\Integrations\Form_Integration_Interface;
use Io\Prosopo\Procaptcha\Pro_Captcha;
use Io\Prosopo\Procaptcha\Safe_Query_Arguments;

class BBPress_Forum implements Hooks_Interface {
	use Safe_Query_Arguments;

	const META_KEY = BBPress::SHORTCODE_NAME . '_bbpress_forum_protection';

	private Pro_Captcha $pro_captcha;
	private Form_Integration_Interface $form_integration;

	public function __construct( Pro_Captcha $pro_captcha, Form_Integration_Interface $form_integration ) {
		$this->pro_captcha      = $pro_captcha;
		$this->form_integration = $form_integration;
	}

	protected function is_enabled( int $forum_id ): bool {
		return (bool) get_post_meta( $forum_id, self::META_KEY, true );
	}

	protected function get_current_forum_id(): int {
		return true === function_exists( 'bbp_get_forum_id' ) ?
			bbp_get_forum_id() :
			0;
	}

	public function update_option( int $post_id ): void {
		$value = $this->get_query_bool_arg_for_non_action( self::META_KEY, 'post' );

		update_post_meta( $post_id, self::META_KEY, $value );
	}

	public function print_metabox(): void {
		$forum_id = (int) get_the_ID();

		$value                = $this->is_enabled( $forum_id );
		$enabled_checked_attr = true === $value ?
			' checked' :
			'';

		echo '<label>';
		printf(
			'<input type="checkbox" name="%s" value="1" style="margin:0 3px 0 0;"%s>',
			esc_html( self::META_KEY ),
			esc_html( $enabled_checked_attr )
		);
		echo '<span style="vertical-align: middle;">';
		esc_html_e( 'Enable form protection for this forum (new topic, reply)', 'prosopo-procaptcha' );
		echo '</span>';
		echo '</label>';
	}

	public function add_settings_metabox(): void {
		add_meta_box(
			BBPress::SHORTCODE_NAME . '_bbpress_forum',
			BBPress::get_field_name(),
			array( $this, 'print_metabox' ),
			'forum'
		);
	}

	public function maybe_print_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		if ( false === $this->is_enabled( $forum_id ) ||
			false === $this->form_integration->is_captcha_present() ) {
			return;
		}

		$this->pro_captcha->print_form_field();
	}

	public function maybe_validate_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		if ( false === $this->is_enabled( $forum_id ) ||
			false === $this->form_integration->is_captcha_present() ||
			true === $this->pro_captcha->is_human_made_request() ) {
			return;
		}

		if ( true === function_exists( 'bbp_add_error' ) ) {
			bbp_add_error( BBPress::SHORTCODE_NAME, $this->pro_captcha->get_validation_error_message() );
		}
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'add_meta_boxes', array( $this, 'add_settings_metabox' ) );
		add_action( 'save_post_forum', array( $this, 'update_option' ) );

		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );

		add_action( 'bbp_new_topic_pre_extras', array( $this, 'maybe_validate_captcha' ) );
		add_action( 'bbp_new_reply_pre_extras', array( $this, 'maybe_validate_captcha' ) );
	}
}
