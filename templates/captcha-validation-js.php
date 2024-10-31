<?php
declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

// This script performs client-side validation for the CAPTCHA field.
// It is injected inline to avoid an additional HTTP request on the front for a separate JS file.
?>
<script data-name="prosopo-procaptcha-validation" type="module">
	class WPForm extends HTMLElement {
		connectedCallback() {
			"loading" === document.readyState ?
				document.addEventListener("DOMContentLoaded", this.setup.bind(this)) :
				this.setup()
		}

		maybePreventSubmission(event){
			let form = event.target;
			let captchaResponse = form.querySelector('input[name="procaptcha-response"]');
			let captchaInput = form.querySelector('input.prosopo-procaptcha-input');

			if(null !== captchaResponse ||
				(null!==captchaInput && '' !== captchaInput.value)){
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			this.querySelector('.prosopo-procaptcha-wp-form__error').style.visibility = 'visible';
		}

		hideError(){
			this.querySelector('.prosopo-procaptcha-wp-form__error').style.visibility = 'hidden';
		}

		setup(){
			let form = this.closest('form');

			if(null === form){
				return;
			}

			form.addEventListener('submit',this.maybePreventSubmission.bind(this));
			this.addEventListener('_procaptcha-filled',this.hideError.bind(this));
		}
	}

	customElements.define("prosopo-procaptcha-wp-form", WPForm);
</script>
