
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
 * Clase Reabrir Cuentas
 *
 * Cada formulario de Reabrir Cuentas en pantalla tiene asociado una instancia de esta clase
 */
var ReabrirCuentas = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ReabrirCuentas
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		var reabrirButton = this.getElement('importButton');
		reabrirButton.observe('click', this._reabrirCuentas.bind(this, reabrirButton));
	},

	_reabrirCuentas: function(reabrirButton){
		reabrirButton.disable();
		this.setIgnoreTermSignal(true);
		var reabrirForm = this.getElement('reabrirCuentasForm');
		new HfosAjax.JsonFormRequest(reabrirForm, {
			onCreate: function(reabrirForm){
				this.getMessages().notice('Se están reabriendo las cuentas...');
				this.getElement('headerSpinner').show();
				reabrirForm.disable();
			}.bind(this, reabrirForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se reabrieron las cuentas correctamente');
				}
			}.bind(this),
			onComplete: function(reabrirForm, reabrirButton){
				this.getElement('headerSpinner').hide();
				reabrirForm.enable();
				reabrirButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, reabrirForm, reabrirButton)
		});
	}

});

HfosBindings.late('win-reabrir-cuentas', 'afterCreateOrRestore', function(hfosWindow){
	var reabrirCuentas = new ReabrirCuentas(hfosWindow);
});

