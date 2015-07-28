
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
var CambioAccion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cambiarAccion
	 */
	initialize: function(container){
		this.setContainer(container);
		var cambiarAccionButton = this.getElement('importButton');
		cambiarAccionButton.observe('click', this._cambioAccion.bind(this, cambiarAccionButton));
	},

	_cambioAccion: function(cambiarAccionButton){
	    cambiarAccionButton.disable();
		this.setIgnoreTermSignal(true);
		var cierreForm = this.getElement('cambioAccionForm');
		new HfosAjax.JsonFormRequest(cierreForm, {
			onCreate: function(cierreForm){
				this.getMessages().notice('Se está realizando el cambio, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				cierreForm.disable();
			}.bind(this, cierreForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el cambio correctamente');
				}
			}.bind(this),
			onComplete: function(cierreForm, cambiarAccionButton){
				this.getElement('headerSpinner').hide();
				cierreForm.enable();
				cambiarAccionButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cierreForm, cambiarAccionButton)
		});
	}

});

HfosBindings.late('win-cambio-accion-socios', 'afterCreateOrRestore', function(hfosWindow){
	var cambioAccion = new CambioAccion(hfosWindow);
});

