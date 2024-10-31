<?php
// unfortunately, Ninja Forms doesn't work with hidden or even plain input, so we must deal with their JS API.
?>
<script type="module">
	class ProsopoProcaptchaNinjaFormsIntegration extends HTMLElement{
		connectedCallback() {
			"loading" === document.readyState ?
				document.addEventListener("DOMContentLoaded", this.setup.bind(this)) :
				this.setup()
		}

		clearValidationError(modelId){
			Backbone.Radio.channel( 'fields' )
				.request(
					'remove:error',
					modelId,
					'required-error'
				);
		}

		makeMarionetteObject(input){
			let _this = this;

			let integration = Marionette.Object.extend( {
				initialize() {
					let submitChannel = Backbone.Radio.channel( 'submit' );
					this.listenTo( submitChannel, 'validate:field', this.updateProcaptcha );
				},
				updateProcaptcha( model ) {
					let type = model.get( 'type' );

					if('prosopo_procaptcha' !== type){
						return;
					}

					model.set( 'value', input.value );

					_this.clearValidationError(model.get('id'));
				},
			} );

			new integration();
		}
		
		setup() {
			let input = this.parentElement.querySelector('.prosopo-procaptcha-input');
			let modelId = input.dataset['id'] || '';

			this.makeMarionetteObject(input);

			this.parentElement.addEventListener('_prosopo-procaptcha__filled',()=>{
				this.clearValidationError(modelId);
			});
		}
	}
	customElements.define("prosopo-procaptcha-ninja-forms-integration", ProsopoProcaptchaNinjaFormsIntegration);
</script>