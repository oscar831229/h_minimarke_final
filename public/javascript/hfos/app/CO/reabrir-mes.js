
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
 * Clase Reabrir Cierre
 *
 * Cada formulario de Reabrir Cierre en pantalla tiene asociado una instancia de esta clase
 */
var ReabrirMes = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Balance
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		var reabrirButton = this.getElement('importButton');
		reabrirButton.observe('click', this._reabrirCierre.bind(this, reabrirButton));
	},

	_reabrirCierre: function(reabrirButton){
		reabrirButton.disable();
		this.setIgnoreTermSignal(true);
		var reabrirForm = this.getElement('reabrirForm');
		new HfosAjax.JsonFormRequest(reabrirForm, {
			onCreate: function(reabrirForm){
				this.getMessages().notice('Se está re-abriendo el mes...');
				this.getElement('headerSpinner').show();
				reabrirForm.disable();
			}.bind(this, reabrirForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					this.getMessages().success('Se re-abrió el mes correctamente');
					this.selectOne('#anteriorCierre').update(response.anteriorCierre);
					this.selectOne('#cierreActual').update(response.cierreActual);
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

HfosBindings.late('win-reabrir-mes', 'afterCreateOrRestore', function(hfosWindow){
	var reabrirCierre = new ReabrirMes(hfosWindow);
});

