
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

var PerfilesUsuarios = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		var usuariosId = this.selectOne('select#usuariosId');
		var submitElement = this.selectOne('input#submitButton');

		usuariosId.observe('change', this._showRolesPerms.bind(this, usuariosId, submitElement));
		submitElement.observe('click', this._savePerfilesUsuarios.bind(this, usuariosId));
	},

	/**
	 *
	 * Muestra/Oculta campos segun seleccion de usuario
	 */
	_showRolesPerms: function(usuariosId, submitElement){
		this._desSelectAll();
		if(usuariosId.getValue()!='@'){
			this.getElement('chooseRole').hide();
			this.getElement('tabAppChoose').show();
			this.getElement('PerfilesContent').show();
			submitElement.show();
			this._loadStoredPerfilesUsuarios(usuariosId);
		} else {
			this.getElement('chooseRole').show();
			this.getElement('tabAppChoose').hide();
			this.getElement('PerfilesContent').hide();
			submitElement.hide();
		};
	},

	/**
	 *
	 * Lee los perfiles_usuarios guardados de  un usuario
	 */
	_loadStoredPerfilesUsuarios: function(usuariosId){
		// consultamos al action de neustro controlador los perfiles guardados al usuario selecionado
		new HfosAjax.JsonRequest('perfiles_usuarios/loadPerfilesUsuarios', {
			parameters: 'usuariosId='+usuariosId.getValue(),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					//Activa los checkboxs de un Array
					this._activeChecksByArray(response.perfiles);
				} else {
					this.getMessages().notice(response.message);
				}
			}.bind(this)
		});
	},

	/**
	 *
	 * Metodo que checkea  una lista de checkboxes desde un Array
	 */
	_activeChecksByArray: function(checkeds){
		if(!checkeds)return false;
		$(checkeds).each(function(idVal){
			var checkedObj = this.selectOne('input[type="checkbox"][value="'+idVal+'"]');
			if(checkedObj){ // si existe
				checkedObj.checked = true;
			}
		}.bind(this));
	},

	/**
	 *
	 * Desactiva tood los checkbox de perfiles
	 */
	_desSelectAll: function(){
		var checkeds = this.select('input[type="checkbox"]');
		if(checkeds){ // si existe
			checkeds.each(function(s){
				s.checked=false;
			}.bind(this));
		}
	},

	_savePerfilesUsuarios: function(usuariosId){

		//Buscamos los check s cargados por un selector
		var checkeds = this.select('input[type="checkbox"]');
		if(checkeds){ // si existe
			var perfiles = "";
			//Verificamos lo que tengan una ruta signada y que esten seleccioandos o chequeados
			var i = 0;
			checkeds.each(function(inputElement){
				if(inputElement.checked && inputElement.value){
					perfiles += "&perfiles["+i+"]="+inputElement.value;
					i++;
				}
			});

			//Enviamos un ajax al action del controlador para que guarde en DB los perfiles de un usuario
			new HfosAjax.JsonRequest('perfiles_usuarios/savePerfilesUsuarios', {
				parameters: 'usuariosId='+usuariosId.getValue()+perfiles,
				onLoading: function(){
					this.getElement('headerSpinner').show();
				}.bind(this),
				onSuccess: function(response){
					if(response.status=='OK'){
						this.getMessages().success(response.message);
					} else {
						this.getMessages().notice(response.message);
					}
				}.bind(this),
				onComplete: function(){
					this.getElement('headerSpinner').hide();
				}.bind(this)
			});
		}
	}


});

HfosBindings.late('win-perfiles-usuarios', 'afterCreate', function(hfosWindow){
	var perfilesUsuarios = new PerfilesUsuarios(hfosWindow);
});