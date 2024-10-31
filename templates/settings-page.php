<?php

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$args = true === isset( $args ) &&
		true === is_array( $args ) ?
	$args :
	array();

$form_nonce    = $args['form_nonce'] ?? '';
$is_just_saved = $args['is_just_saved'] ?? false;

$pro_captcha = $args['pro_captcha'] ?? null;

$field_values = $args['field_values'] ?? array();
$field_values = true === is_array( $field_values ) ?
	$field_values :
	array();

$site_key   = $field_values['site_key'] ?? '';
$secret_key = $field_values['secret_key'] ?? '';

$style_preferences = array(
	array(
		'theme',
		__( 'Theme:', 'prosopo-procaptcha' ),
		$args['themes'] ?? array(),
		$field_values['theme'] ?? 'light',
	),
	array(
		'type',
		__( 'Type:', 'prosopo-procaptcha' ),
		$args['types'] ?? array(),
		$field_values['type'] ?? 'frictionless',
	),
);

$wp_integration_checkboxes = array(
	array(
		'wp-login-form',
		__( 'Protect the login form:', 'prosopo-procaptcha' ),
		$field_values['is_on_wp_login_form'] ?? false,
	),
	array(
		'wp-register-form',
		__( 'Protect the register form:', 'prosopo-procaptcha' ),
		$field_values['is_on_wp_register_form'] ?? false,
	),
	array(
		'wp-lost-password-form',
		__( 'Protect the lost password form:', 'prosopo-procaptcha' ),
		$field_values['is_on_wp_lost_password_form'] ?? false,
	),
	array(
		'wp-comment-form',
		__( 'Protect the comment form:', 'prosopo-procaptcha' ),
		$field_values['is_on_wp_comment_form'] ?? false,
	),
	array(
		'wp-post-form',
		__( 'Protect the post/page password form:', 'prosopo-procaptcha' ),
		$field_values['is_on_wp_post_form'] ?? false,
	),
);

?>

<div class="prosopo-captcha-settings wrap">
	<div class="prosopo-captcha-settings__head">
		<?php
		printf(
			'<h1 class="prosopo-captcha-settings__title">%s</h1>',
			esc_html__( 'Prosopo Procaptcha', 'prosopo-procaptcha' )
		);
		printf(
			'<a class="prosopo-captcha-settings__link" href="https://prosopo.io/">%s</a>',
			esc_html__( 'Visit website', 'prosopo-procaptcha' )
		);
		?>
	</div>
	<div class="prosopo-captcha-settings__description">
		<?php
		printf(
			'<span>%s</span>',
			esc_html__(
				'GDPR compliant, privacy friendly and better value captcha.',
				'prosopo-procaptcha'
			)
		);
		?>
	</div>
	<?php
	if ( true === $is_just_saved ) {
		printf(
			'<div class="prosopo-captcha-settings__message">%s</div>',
			esc_html__( 'Settings successfully saved.', 'prosopo-procaptcha' )
		);
	}
	?>
	<div class="prosopo-captcha-settings__container">
		<form action="" method="post" class="prosopo-captcha-settings__form" autocomplete="off">
			<?php
			printf( '<input type="hidden" name="_wpnonce" value="%s">', esc_attr( $form_nonce ) );
			?>
			<div class="prosopo-captcha-settings__row">
				<?php
				printf(
					'<p class="prosopo-captcha-settings__label">%s</p>',
					esc_html__( 'Your Site Key:', 'prosopo-procaptcha' )
				);
				printf(
					'<input name="prosopo-captcha__site-key" type="text" required class="prosopo-captcha-settings__input" placeholder="%s" value="%s">',
					esc_html__( 'Site Key', 'prosopo-procaptcha' ),
					esc_attr( $site_key )
				);
				?>
			</div>
			<div class="prosopo-captcha-settings__row">
				<?php
				printf(
					'<p class="prosopo-captcha-settings__label">%s</p>',
					esc_html__( 'Your Secret Key:', 'prosopo-procaptcha' )
				);
				printf(
					'<input name="prosopo-captcha__secret-key" type="password" required class="prosopo-captcha-settings__input" placeholder="%s" value="%s">',
					esc_html__( 'Secret Key', 'prosopo-procaptcha' ),
					esc_attr( $secret_key )
				);
				?>
			</div>
			<div class="prosopo-captcha-settings__row">
				<p class="prosopo-captcha-settings__section-title"><?php echo esc_html__( 'Style preferences', 'prosopo-procaptcha' ); ?></p>
			</div>
			<?php
			foreach ( $style_preferences as $style_preference ) {
				?>
				<label class="prosopo-captcha-settings__row">
					<span class="prosopo-captcha-settings__label">
					<?php
					printf(
						'<span class="prosopo-captcha-settings__label">%s</span>',
						esc_html( $style_preference[1] )
					);
					?>
					</span>
					<select class="prosopo-captcha-settings__input"
							name="prosopo-captcha__<?php echo esc_attr( $style_preference[0] ); ?>">
						<?php
						$chosen = esc_attr( $style_preference[3] );
						foreach ( $style_preference[2] as $key => $value ) {
							$selected_attribute = $key === $chosen ? ' selected' : '';
							printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $key ),
								esc_attr( $selected_attribute ),
								esc_html( $value )
							);
						}
						?>
					</select>
				</label>
				<?php
			}
			?>
			<div class="prosopo-captcha-settings__row">
				<p class="prosopo-captcha-settings__section-title"><?php echo esc_html__( 'WordPress integration', 'prosopo-procaptcha' ); ?></p>
			</div>
			<?php
			foreach ( $wp_integration_checkboxes as $wp_integration_checkbox ) {
				$checked_attribute = true === $wp_integration_checkbox[2] ? ' checked' : '';
				?>
				<label class="prosopo-captcha-settings__row">
					<?php
					printf(
						'<span class="prosopo-captcha-settings__label">%s</span>',
						esc_html( $wp_integration_checkbox[1] )
					);
					?>
					<div class="prosopo-captcha-settings__switcher">
						<?php
						printf(
							'<input name="prosopo-captcha__%s" type="checkbox"  class="prosopo-captcha-settings__checkbox"%s>',
							esc_attr( $wp_integration_checkbox[0] ),
							esc_attr( $checked_attribute )
						);
						?>
						<div class="prosopo-captcha-settings__toggle"></div>
					</div>
				</label>
				<?php
			}
			?>
			<br>
			<?php
			printf(
				'<input class="button button-primary button-large prosopo-captcha-settings__button" type="submit" name="prosopo-captcha__submit" value="%s">',
				esc_html__( 'Save', 'prosopo-procaptcha' )
			);
			?>
		</form>
		<?php
		if ( '' !== $site_key &&
			true === is_object( $pro_captcha ) &&
			true === is_callable( array( $pro_captcha, 'print_form_field' ) ) ) {
			?>
			<div class="prosopo-captcha-settings__status">

				<?php
				printf(
					'<p class="prosopo-captcha-settings__label">%s</p>',
					esc_html__( 'Preview: if the credentials are valid, you should be able to complete the captcha below:', 'prosopo-procaptcha' )
				);

				$pro_captcha->print_form_field();
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>
