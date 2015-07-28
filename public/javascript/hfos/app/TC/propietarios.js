
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
 * Clase Proyeccion
 *
 * Cada formulario de proyeccion en pantalla tiene asociado una instancia de esta clase
 */
var Propietarios = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de proyeccionCuentas
	 */
	initialize: function(container){
		this.setContainer(container);
		var propietariosButton = this.getElement('importButton');
		propietariosButton.observe('click', this._propietarios.bind(this, propietariosButton));
	},

	/**
	 * Genera el informe de propietarios
	 */
	_propietarios: function(propietariosButton){
		propietariosButton.disable();
		this.setIgnoreTermSignal(true);
		var propietariosForm = this.getElement('propietariosForm');
		new HfosAjax.JsonFormRequest(propietariosForm, {
			onLoading: function(propietariosForm){
				this.getMessages().notice('Se está generando el informe...');
				this.getElement('headerSpinner').show();
				propietariosForm.disable();
			}.bind(this, propietariosForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el informe de propietarios correctamente');
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(propietariosForm, propietariosButton){
				this.getElement('headerSpinner').hide();
				propietariosForm.enable();
				propietariosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, propietariosForm, propietariosButton)
		});
	}

});

HfosBindings.late('win-propietarios-tpc', 'afterCreateOrRestore', function(hfosWindow){
	var propietarios = new Propietarios(hfosWindow);
});
