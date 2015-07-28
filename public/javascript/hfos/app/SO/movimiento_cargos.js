
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
 * Clase movimientoCargos
 *
 * Cada formulario de movimientoCargos en pantalla tiene asociado una instancia de esta clase
 */
var MovimientoCargos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de movimientoCargos
	 */
	initialize: function(container){
		this.setContainer(container);
		var movimientoCargosButton = this.getElement('importButton');
		movimientoCargosButton.observe('click', this._movimientoCargos.bind(this, movimientoCargosButton));
	},

	_movimientoCargos: function(movimientoCargosButton){
	    movimientoCargosButton.disable();
		this.setIgnoreTermSignal(true);
		var movimientoCargosForm = this.getElement('movimientoCargosForm');
		new HfosAjax.JsonFormRequest(movimientoCargosForm, {
			onCreate: function(movimientoCargosForm){
				this.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				movimientoCargosForm.disable();
			}.bind(this, movimientoCargosForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success(response.message);
				}
			}.bind(this),
			onComplete: function(movimientoCargosForm, movimientoCargosButton){
				this.getElement('headerSpinner').hide();
				movimientoCargosForm.enable();
				movimientoCargosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, movimientoCargosForm, movimientoCargosButton)
		});
	}

});

HfosBindings.late('win-movimiento-cargos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var movimientoCargos = new MovimientoCargos(hfosWindow);
});

