<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$args = true === isset( $args ) &&
		true === is_array( $args ) ?
	$args :
	array();

$captcha_attributes = $args['captcha_attributes'] ?? array();

// This script inits all the Procaptcha instances on the page.
// It is injected inline to avoid an additional HTTP request on the front for a separate JS file.

// Explicit JS is used over the 'data' attributes and 'procaptcha' class, because some JS based forms (like NinjaForms) add field HTML after DOM.Loaded event.
?>
<script data-name="prosopo-procaptcha-element" type="module">
	let attributes = JSON.parse('<?php echo wp_json_encode( $captcha_attributes ); ?>');

	class ProsopoProcaptchaWrapper extends HTMLElement{
		connectedCallback() {
			// wait window.load to make sure 'window.procaptcha' is available.
			"complete" !== document.readyState ?
				window.addEventListener("load", this.setup.bind(this)) :
				this.setup();
		}

		validatedCallback(output){
			let input = this.parentElement.querySelector('.prosopo-procaptcha-input');
			let validationElement = this.querySelector('.prosopo-procaptcha-wp-form');

			// JS-based forms do not send the whole form data, so we must put the token value to right input manually.
			if(null !== input){
				input.value = output;
				// emulate the change event.
				input.dispatchEvent(new Event('change',{
					bubbles: true,
				}));
			}

			// allow third-party listen to this event.
			this.dispatchEvent(new CustomEvent('_prosopo-procaptcha__filled',{
				bubbles: true,
			}));
			
			// it's fully optional.
			if(null !== validationElement){
				// internal event.
				validationElement.dispatchEvent(new CustomEvent('_procaptcha-filled'));
			}
		}

		setup() {
			attributes.callback = this.validatedCallback.bind(this);

			window.procaptcha.render(this.querySelector('.prosopo-procaptcha'),attributes);
		}
	}
	customElements.define("prosopo-procaptcha-wrapper", ProsopoProcaptchaWrapper);
</script>