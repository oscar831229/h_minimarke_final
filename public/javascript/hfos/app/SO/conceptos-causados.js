
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
var ConceptosCausados = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cambiarAccion
	 */
	initialize: function(container){
		this.setContainer(container);
		var conceptosCausadosButton = this.getElement('importButton');
		if (!conceptosCausadosButton) {
			alert('conceptosCausadosButton not found');
			return false;
		}
		conceptosCausadosButton.observe('click', this._consultaAccion.bind(this, conceptosCausadosButton));
	},

	_consultaAccion: function(conceptosCausadosButton){
	    conceptosCausadosButton.disable();
		this.setIgnoreTermSignal(true);
		var conceptosCausadosForm = this.getElement('conceptosCausadosForm');
		if (!conceptosCausadosForm) {
			alert('form ".conceptosCausadosForm" not found');
			return false;
		}
		new HfosAjax.JsonFormRequest(conceptosCausadosForm, {
			onCreate: function(conceptosCausadosForm){
				this.getMessages().notice('Se está realizando la consulta, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				conceptosCausadosForm.disable();
			}.bind(this, conceptosCausadosForm),
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
			onComplete: function(conceptosCausadosForm, conceptosCausadosButton){
				this.getElement('headerSpinner').hide();
				conceptosCausadosForm.enable();
				conceptosCausadosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, conceptosCausadosForm, conceptosCausadosButton)
		});
	}

});

HfosBindings.late('win-conceptos-causados-socios', 'afterCreateOrRestore', function(hfosWindow){
	var conceptosCausados = new ConceptosCausados(hfosWindow);
});

