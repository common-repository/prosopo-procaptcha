<?php

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$args = true === isset( $args ) &&
		true === is_array( $args ) ?
	$args :
	array();

$error_message = $args['error_message'] ?? '';
$is_visible    = $args['is_visible'] ?? false;

$visibility = false === $is_visible ?
	'hidden' :
	'visible';

?>
<prosopo-procaptcha-wp-form class="prosopo-procaptcha-wp-form" style="display: block;line-height: 1;">
	<span class="prosopo-procaptcha-wp-form__error" style="display:block;visibility: <?php echo esc_html( $visibility ); ?>;color:red;line-height:1;font-size: 12px;padding:3px 0 0 10px;">
		<?php echo esc_html( $error_message ); ?>
	</span>
</prosopo-procaptcha-wp-form>

