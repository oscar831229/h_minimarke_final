
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
 * Clase Cerrar
 *
 * Cada formulario de Cerrar en pantalla tiene asociado una instancia de esta clase
 */
var Cerrar = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Cerrar
	 */
	initialize: function(container){
		this.setContainer(container);
		var cerrarButton = this.getElement('importButton');
		cerrarButton.observe('click', this._cierreContable.bind(this, cerrarButton));
	},

	_cierreContable: function(cerrarButton){
		cerrarButton.disable();
		this.setIgnoreTermSignal(true);
		var cierreForm = this.getElement('cerrarForm');
		new HfosAjax.JsonFormRequest(cierreForm, {
			onCreate: function(cierreForm){
				this.getMessages().notice('Se está realizando el cierre de inventarios, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				cierreForm.disable();
			}.bind(this, cierreForm),
			onLoading: function(cierreForm){
				this.getMessages().notice('Se está realizando el cierre de inventarios, esto tardará algunos minutos...');
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
					this.selectOne('#proximoCierre').update(response.proximoCierre);
					this.selectOne('#cierreActual').update(response.cierreActual);
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

HfosBindings.late('win-cerrar', 'afterCreateOrRestore', function(hfosWindow){
	var cerrar = new Cerrar(hfosWindow);
});

