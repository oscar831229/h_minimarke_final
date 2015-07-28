
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
 * Liquidacion Quincenal
 *
 * Cada formulario de Liquidacion en pantalla tiene asociado una instancia de esta clase
 */
var Liquidacion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Nomina
	 */
	initialize: function(container){
		this.setContainer(container);
		var liquidarButton = this.getElement('importButton');
		liquidarButton.observe('click', this._liquidarNomina.bind(this, liquidarButton));
	},

	_liquidarNomina: function(liquidarButton){
		liquidarButton.disable();
		this.setIgnoreTermSignal(true);
		var liquidarForm = this.getElement('liquidarForm');
		new HfosAjax.JsonFormRequest(liquidarForm, {
			onLoading: function(cierreForm){
				this.getMessages().notice('Se está realizando la liquidación de nomina, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				liquidarForm.disable();
			}.bind(this, liquidarForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó la liquidación correctamente');
					//this.selectOne('#proximoCierre').update(response.proximoCierre);
					//this.selectOne('#cierreActual').update(response.cierreActual);
				}
			}.bind(this),
			onComplete: function(cierreForm, liquidarButton){
				this.getElement('headerSpinner').hide();
				liquidarForm.enable();
				liquidarButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, liquidarForm, liquidarButton)
		});
	}

});

HfosBindings.late('win-liquidacion', 'afterCreateOrRestore', function(hfosWindow){
	var liquidacion = new Liquidacion(hfosWindow);
});

