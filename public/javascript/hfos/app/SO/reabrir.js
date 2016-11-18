
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
 * Clase Reabrir
 *
 * Cada formulario de Reabrir en pantalla tiene asociado una instancia de esta clase
 */
var Reabrir = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Reabrir
	 */
	initialize: function(container){
		this.setContainer(container);
		var reabrirButton = this.getElement('importButton');
		reabrirButton.observe('click', this._cierreContable.bind(this, reabrirButton));
	},

	_cierreContable: function(reabrirButton){
		reabrirButton.disable();
		this.setIgnoreTermSignal(true);
		var cierreForm = this.getElement('reabrirForm');
		new HfosAjax.JsonFormRequest(cierreForm, {
			onCreate: function(cierreForm){
				this.getMessages().notice('Se está reabriendo el periodo de inventarios, esto tardará algunos minutos...');
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
					this.getMessages().success('Se reabrió el mes correctamente');
					this.selectOne('#periodoAbrir').update(response.periodoAbrir);
					this.selectOne('#periodoActual').update(response.periodoActual);
				}
			}.bind(this),
			onComplete: function(cierreForm, reabrirButton){
				this.getElement('headerSpinner').hide();
				cierreForm.enable();
				reabrirButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cierreForm, reabrirButton)
		});
	}

});

HfosBindings.late('win-reabrir-socios', 'afterCreateOrRestore', function(hfosWindow){
	var reabrir = new Reabrir(hfosWindow);
});

