
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
 * Clase Reabrir Año
 *
 * Cada formulario de Reabrir Año en pantalla tiene asociado una instancia de esta clase
 */
var ReabrirAno = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ReabrirAno
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		var reabrirButton = this.getElement('importButton');
		reabrirButton.observe('click', this._reabrirAno.bind(this, reabrirButton));
	},

	_reabrirAno: function(reabrirButton){
		var reabrirForm = this.getElement('reabrirAnoForm');
		if(reabrirForm!==null){
			reabrirButton.disable();
			this.setIgnoreTermSignal(true);
			new HfosAjax.JsonFormRequest(reabrirForm, {
				onCreate: function(reabrirForm){
					this.getMessages().notice('Se está re-abriendo el año...');
					this.getElement('headerSpinner').show();
					reabrirForm.disable();
				}.bind(this, reabrirForm),
				onSuccess: function(response){
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					} else {
						this.getMessages().success('Se re-abrió el año correctamente');
					}
				}.bind(this),
				onComplete: function(reabrirForm, reabrirButton){
					this.getElement('headerSpinner').hide();
					reabrirForm.enable();
					reabrirButton.enable();
					this.setIgnoreTermSignal(false);
				}.bind(this, reabrirForm, reabrirButton)
			});
		} else {
			this.getMessages().error('No se puede reabrir el año en este momento');
		}
	}

});

HfosBindings.late('win-reabrir-ano', 'afterCreateOrRestore', function(hfosWindow){
	var reabrirAno = new ReabrirAno(hfosWindow);
});

