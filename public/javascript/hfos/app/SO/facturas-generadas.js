
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
var FacturasGeneradas = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cambiarAccion
	 */
	initialize: function(container){
		this.setContainer(container);
		var facturasGeneradasButton = this.getElement('importButton');
		if (!facturasGeneradasButton) {
			alert('facturasGeneradasButton not found');
			return false;
		}
		facturasGeneradasButton.observe('click', this._consultaAccion.bind(this, facturasGeneradasButton));
	},

	_consultaAccion: function(facturasGeneradasButton){
	    facturasGeneradasButton.disable();
		this.setIgnoreTermSignal(true);
		var facturasGeneradasForm = this.getElement('facturasGeneradasForm');
		if (!facturasGeneradasForm) {
			alert('form ".facturasGeneradasForm" not found');
			return false;
		}
		new HfosAjax.JsonFormRequest(facturasGeneradasForm, {
			onCreate: function(facturasGeneradasForm){
				this.getMessages().notice('Se está realizando la consulta, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				facturasGeneradasForm.disable();
			}.bind(this, facturasGeneradasForm),
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
			onComplete: function(facturasGeneradasForm, facturasGeneradasButton){
				this.getElement('headerSpinner').hide();
				facturasGeneradasForm.enable();
				facturasGeneradasButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, facturasGeneradasForm, facturasGeneradasButton)
		});
	}

});

HfosBindings.late('win-facturas-generadas-socios', 'afterCreateOrRestore', function(hfosWindow){
	var facturasGeneradas = new FacturasGeneradas(hfosWindow);
});

