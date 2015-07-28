
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
 * Clase Cierre Cuentas
 *
 * Cada formulario de Cierre Cuentas en pantalla tiene asociado una instancia de esta clase
 */
var CierreCuentas = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de CierreCuentas
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		var cerrarButton = this.getElement('importButton');
		cerrarButton.observe('click', this._cierreContable.bind(this, cerrarButton));
	},

	_cierreContable: function(cerrarButton){
		cerrarButton.disable();
		this.setIgnoreTermSignal(true);
		var cierreForm = this.getElement('cierreForm');
		new HfosAjax.JsonFormRequest(cierreForm, {
			onCreate: function(cierreForm){
				this.getMessages().notice('Se están cerrando las cuentas...');
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
					this.getMessages().success('Se realizó el cierre correctamente');
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

HfosBindings.late('win-cierre-cuentas', 'afterCreateOrRestore', function(hfosWindow){
	var cierreCuentas = new CierreCuentas(hfosWindow);
});

