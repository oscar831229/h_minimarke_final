
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
 * Clase CambioContratos
 *
 * Cada formulario de Cambio de numero de Contratos en pantalla tiene asociado una instancia de esta clase
 */
var CambioContratos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de proyeccionCuentas
	 */
	initialize: function(container){
	    this.setContainer(container);
	    
	    var cambioContratosButton = this.getElement('importButton');
	    var sociosIdButton = this.selectOne('#sociosId');
	    var sociosIdDetButton = this.selectOne('#sociosId_det');

	    sociosIdButton.observe('change', this._getNumeroContrato.bind(this));
	    sociosIdDetButton.observe('change', this._getNumeroContrato.bind(this));
	    cambioContratosButton.observe('click', this._cambioContratos.bind(this, cambioContratosButton));
	},

	/**
	 * Asigna el numero de contrato a la caja nueva
	 */
	_getNumeroContrato: function() {
		var sociosId = this.selectOne('#sociosId');
	    var numeroContrato = this.selectOne('#numeroContrato');

	    if (sociosId && sociosId.getValue() && numeroContrato) {
	    	new HfosAjax.JsonRequest('contratos/getNumeroContrato', {
    		    parameters: {
    				'socio': sociosId.getValue()
    			},
    			onSuccess: function(response) {
    				if (response.status=='OK') {
                		numeroContrato.setValue(response.numeroContrato);
    				}
    			}.bind(this)
    		});
	    }
	},

	/**
	 * Genera el cambio de numero de contratos
	 */
	_cambioContratos: function(cambioContratosButton){
		cambioContratosButton.disable();
		this.setIgnoreTermSignal(true);
		var cambioContratosForm = this.getElement('cambioContratosForm');
		new HfosAjax.JsonFormRequest(cambioContratosForm, {
			onLoading: function(cambioContratosForm) {
				this.getMessages().notice('Se está cambiando el número de contrato...');
				this.getElement('headerSpinner').show();
				cambioContratosForm.disable();
			}.bind(this, cambioContratosForm),
			onSuccess: function(response) {
				if (response.status=='FAILED') {
					this.getMessages().error(response.message);
					if (typeof response.url != "undefined") {
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success(response.message);
					if (typeof response.file != "undefined") {
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(cambioContratosForm, cambioContratosButton){
				this.getElement('headerSpinner').hide();
				cambioContratosForm.enable();
				cambioContratosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cambioContratosForm, cambioContratosButton)
		});
	}

});

HfosBindings.late('win-cambio-contratos-tpc', 'afterCreateOrRestore', function(hfosWindow){
	var cambioContratos = new CambioContratos(hfosWindow);
});

