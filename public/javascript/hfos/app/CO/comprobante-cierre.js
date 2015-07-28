
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
 * Clase ComprobanteCierre
 *
 * Cada formulario de Comprobante de Cierre en pantalla tiene asociado una instancia de esta clase
 */
var ComprobanteCierre = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Balance
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		var cerrarButton = this.getElement('saveButton');
		cerrarButton.observe('click', this._cierreContable.bind(this, cerrarButton));
	},

	_cierreContable: function(cerrarButton){
		cerrarButton.disable();
		this.setIgnoreTermSignal(true);
		var cierreForm = this.getElement('cierreForm');
		new HfosAjax.JsonFormRequest(cierreForm, {
			onCreate: function(cierreForm){
				this.getMessages().notice('Se está generando el comprobante de cierre anual...');
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
					this.getMessages().success('Se generó el comprobante de cierre anual correctamente');
				}
			}.bind(this),
			onComplete: function(cierreForm, cerrarButton){
				this.getElement('headerSpinner').hide();
				cierreForm.enable();
				cerrarButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cierreForm, cerrarButton)
		});
	}

});

HfosBindings.late('win-comprobante-cierre', 'afterCreateOrRestore', function(hfosWindow){
	var comprobanteCierre = new ComprobanteCierre(hfosWindow);
});

