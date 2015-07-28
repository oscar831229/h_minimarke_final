
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
 * Clase Amortizacion
 *
 * Cada formulario de Amortizacion de Activos en pantalla tiene asociado una instancia de esta clase
 */
var PermisosComprob = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){

		var usuariosId = this.selectOne('select#usuariosId');
		var comprob = this.selectOne('select#comprob');
		var submitElement = this.selectOne('input#submitButton');

		usuariosId.observe('change', this._showRolesPerms.bind(this, usuariosId, comprob, submitElement));
		comprob.observe('change', this._showRolesPerms.bind(this, usuariosId, comprob, submitElement));
		submitElement.observe('click', this._savePerfilesUsuarios.bind(this, usuariosId, comprob));
	},

	/**
	 *
	 * Muestra/Oculta campos segun seleccion de usuario
	 */
	_showRolesPerms: function(usuariosId, comprob, submitElement){
		this._desSelectAll();
		if(usuariosId.getValue()!='@'){
			this.getElement('chooseRole').hide();
			this.getElement('comprobBlock').show();
			if(comprob.getValue()!='@'){
				this.getElement('chooseComprob').hide();
				this.getElement('tabAppChoose').show();
				this.getElement('PerfilesContent').show();
				submitElement.show();
				this._loadStoredPerfilesUsuarios(usuariosId, comprob);
			} else {
				this.getElement('chooseComprob').show();
				this.getElement('tabAppChoose').hide()
				this.getElement('PerfilesContent').hide();
				submitElement.hide();
			}
		} else {
			this.getElement('chooseRole').show();
			this.getElement('comprobBlock').hide();
			if(!comprob.getValue()){
				this.getElement('chooseComprob').show();
			}else{
				this.getElement('chooseComprob').hide();
			}
			this.getElement('tabAppChoose').hide();
			this.getElement('PerfilesContent').hide();
			submitElement.hide();
		};

		trustComprob=false;
	},

	/**
	 * Lee los permisos_comprob guardados de un usuario
	 */
	_loadStoredPerfilesUsuarios: function(usuariosId, comprob){
		new HfosAjax.JsonRequest('permisos_comprob/loadPermisosComprob', {
			parameters: 'usuariosId='+usuariosId.getValue()+"&comprob="+comprob.getValue(),
			onSuccess: function(response){
				if(response.pOpcion.length>0){
					this._activeChecksByArray(response.pOpcion);
				};
				this.getMessages().notice(response.message);
			}.bind(this)
		});
	},

	/**
	 * Metodo que checkea una lista de checkboxes desde un Array
	 */
	_activeChecksByArray: function(checkeds){
		if(!checkeds){
			return false;
		};
		$(checkeds).each(function(idVal){
			var checkedObj = this.selectOne('.resource-access > input[type="checkbox"][value="'+idVal+'"]');
			if(checkedObj){ // si existe
				checkedObj.checked=true;
			}
		}.bind(this));
	},

	/**
	 *
	 * Desactiva tood los checkbox de perfiles
	 */
	_desSelectAll: function(){
		var checkeds = this.select('.resource-access > input[type="checkbox"]');
		if(checkeds){ // si existe
			checkeds.each(function(s){
				s.checked=false;
			}.bind(this));
		}
	},

	/**
	 *
	 * Metod que verifica por un ajax si un comprobante existe y si eixste continua logica
	 */
	_checkIfComprob: function(usuariosId, comprob, submitElement){
		//Enviamos un ajax al action del controlador para que verifique si un comprobante exite o no
		new HfosAjax.JsonRequest('permisos_comprob/checkComprob', {
			parameters: "comprob="+comprob.getValue(),
			onSuccess: function(response){
				alert(response.status);
				if(response.status=='FAILED'){
					comprob.setValue("");
					submitElement.hide();
					this.getElement('PerfilesContent').hide();
					this.getMessages().notice(response.message);
				}else{
					this._showRolesPerms(usuariosId, comprob, submitElement);
				}
			}.bind(this)
		});
	},

	/**
	 * Método usa un ajax para guardar en la BD
	 */
	_savePerfilesUsuarios: function(usuariosId, comprob){

		var checkeds = this.select('.resource-access > input[type="checkbox"]');
		if(checkeds){ // si existe

			var i = 0;
			var permisos = "";
			checkeds.each(function(inputElement){
				if(inputElement.checked && inputElement.value){
					permisos += "&permisos["+i+"]="+inputElement.value;
					i++;
				}
			});

			new HfosAjax.JsonRequest('permisos_comprob/savePermisosComprob', {
				parameters: 'usuariosId='+usuariosId.getValue()+"&comprob="+comprob.getValue()+permisos,
				onLoading: function(){
					this.getElement('headerSpinner').show();
				}.bind(this),
				onSuccess: function(response){
					if(response.status=='OK'){
						this.getMessages().success(response.message);
					} else {
						this.getMessages().error(response.message);
					}
				}.bind(this),
				onComplete: function(){
					this.getElement('headerSpinner').hide();
				}.bind(this)
			});
		}
	}

});

HfosBindings.late('win-permisos-comprob', 'afterCreate', function(hfosWindow){
	var permisosComprob = new PermisosComprob(hfosWindow);
});
