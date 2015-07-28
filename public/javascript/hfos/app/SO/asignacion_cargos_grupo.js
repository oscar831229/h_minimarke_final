
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
 * Clase asignarCargoFijoGrupo
 *
 * Cada formulario de asignarCargoFijoGrupo en pantalla tiene asociado una instancia de esta clase
 */
var AsignacionCargosGrupo = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de asignarCargoFijoGrupo
	 */
	initialize: function(container){
		this.setContainer(container);
		var asignarCargoFijoGrupoButton = this.getElement('importButton');
		asignarCargoFijoGrupoButton.observe('click', this._asignarcargoFijoGrupo.bind(this, asignarCargoFijoGrupoButton));
		var borrarCargoFijoGrupoButton = this.getElement('deleteButton');
		if (borrarCargoFijoGrupoButton) {
			borrarCargoFijoGrupoButton.observe('click', this._borrarCargoFijoGrupo.bind(this, borrarCargoFijoGrupoButton));
		}
	},

	/**
	 * Asigna cargos fijos a grupo
 	 * @param {Object} asignarCargoFijoGrupoButton
	*/
	_asignarcargoFijoGrupo: function(asignarCargoFijoGrupoButton){
	    asignarCargoFijoGrupoButton.disable();
		this.setIgnoreTermSignal(true);
		var asignarCargoFijoGrupoForm = this.getElement('asignarCargoFijoGrupoForm');
		new HfosAjax.JsonFormRequest(asignarCargoFijoGrupoForm, {
			onCreate: function(asignarCargoFijoGrupoForm){
				this.getMessages().notice('Se está realizando el cambio, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				asignarCargoFijoGrupoForm.disable();
			}.bind(this, asignarCargoFijoGrupoForm),
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
			onComplete: function(asignarCargoFijoGrupoForm, asignarCargoFijoGrupoButton){
				this.getElement('headerSpinner').hide();
				asignarCargoFijoGrupoForm.enable();
				asignarCargoFijoGrupoButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, asignarCargoFijoGrupoForm, asignarCargoFijoGrupoButton)
		});
	},
	
	/**
	 * Borra cargos fijos a grupo
 	 * @param {Object} asignarCargoFijoGrupoButton
	*/
	_borrarCargoFijoGrupo: function(borrarCargoFijoGrupoButton){
	    borrarCargoFijoGrupoButton.disable();
		this.setIgnoreTermSignal(true);
		var asignarCargoFijoGrupoForm = this.getElement('asignarCargoFijoGrupoForm');
		
		var tipo = this.getElement('tipo');
		var tipoSociosId = this.getElement('tipo_socios_id');
		var cargosFijosId = this.getElement('cargos_fijos_id');
		
		new HfosAjax.JsonRequest('asignacion_cargos_grupo/borrar', {
			parameters: {
				'tipo': tipo.getValue(),
				'tipo_socios_id': tipoSociosId.getValue(),
				'cargos_fijos_id': cargosFijosId.getValue()
			},
			onCreate: function(asignarCargoFijoGrupoForm){
				this.getMessages().notice('Se está realizando el cambio, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				asignarCargoFijoGrupoForm.disable();
			}.bind(this, asignarCargoFijoGrupoForm),
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
			onComplete: function(asignarCargoFijoGrupoForm, borrarCargoFijoGrupoButton){
				this.getElement('headerSpinner').hide();
				asignarCargoFijoGrupoForm.enable();
				borrarCargoFijoGrupoButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, asignarCargoFijoGrupoForm, borrarCargoFijoGrupoButton)
		});
	}

});

HfosBindings.late('win-asignacion-cargos-grupo-socios', 'afterCreateOrRestore', function(hfosWindow){
	var asignacionCargosGrupo = new AsignacionCargosGrupo(hfosWindow);
});

