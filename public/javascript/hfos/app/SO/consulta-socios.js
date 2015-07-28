
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Clase cambiarAccion
 *
 * Cada formulario de cambiarAccion en pantalla tiene asociado una instancia de esta clase
 */
var ConsultaSocios = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cambiarAccion
	 */
	initialize: function(container){
		this.setContainer(container);
		var consultaSociosButton = this.getElement('importButton');
		consultaSociosButton.observe('click', this._consultaAccion.bind(this, consultaSociosButton));
	},

	_consultaAccion: function(consultaSociosButton){
	    consultaSociosButton.disable();
		this.setIgnoreTermSignal(true);
		var consultaSociosForm = this.getElement('consultaSociosForm');
		new HfosAjax.JsonFormRequest(consultaSociosForm, {
			onCreate: function(consultaSociosForm){
				this.getMessages().notice('Se está realizando la consulta, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				consultaSociosForm.disable();
			}.bind(this, consultaSociosForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el informe correctamente');
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(consultaSociosForm, consultaSociosButton){
				this.getElement('headerSpinner').hide();
				consultaSociosForm.enable();
				consultaSociosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, consultaSociosForm, consultaSociosButton)
		});
	}

});

HfosBindings.late('win-consulta-socios', 'afterCreateOrRestore', function(hfosWindow){
	var consultaSocios = new ConsultaSocios(hfosWindow);
});

