
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
var PermisosCentros = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		var usuariosId = this.selectOne('select#usuariosId');
		var submitElement = this.selectOne('input#submitButton');

		usuariosId.observe('change', this._showRolesPerms.bind(this, usuariosId, submitElement));
		submitElement.observe('click', this._savePermisosCentros.bind(this));
		
		//Asignamos un listener a (des)seleccionar todos los permisos
		var allCheckeds = this.selectOne('input#selectAll');
		allCheckeds.observe('click', this._activeOrDesactiveChecks.bind(this, allCheckeds));

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
			this._loadStoredPermisosCentros();
		} else {
			this.getElement('chooseRole').show();
			this.getElement('tabAppChoose').hide();
			this.getElement('PerfilesContent').hide();
			submitElement.hide();
		};
	},

	/**
	 * Lee los permisos_centros guardados de un usuario
	 */
	_loadStoredPermisosCentros: function(){
		// consultamos al action de neustro controlador los perfiles guardados al usuario selecionado
		new HfosAjax.JsonRequest('permisos_centros/loadPermisosCentros', {
			parameters: 'usuariosId='+usuariosId.getValue(),
			onSuccess: function(response){
				if(response.pOpcion.length>0){
					this._activeChecksByArray(response.pOpcion);
				};
				this.getMessages().notice(response.message);
			}.bind(this)
		});
	},

	/**
	 *
	 * Metodo que checkea una lista de checkboxes desde un Array
	 */
	_activeChecksByArray: function(checkeds){
		if(!checkeds)return false;
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
	 * Metodo usa un ajax para guardar en la BD
	 */
	_savePermisosCentros: function(){
		//Buscamos los check s cargados por un selector
		var checkeds = this.select('.resource-access > input[type="checkbox"]');
		if(checkeds){ // si existe
			var permisos = "";
			//Verificamos lo que tengan una ruta signada y que esten seleccioandos o chequeados
			var i = 0;
			checkeds.each(function(inputElement){
				if(inputElement.checked && inputElement.value){
					permisos += "&permisos["+i+"]="+inputElement.value;
					i++;
				}
			});

			//Enviamos un ajax al action del controlador para que guarde en DB los permisos de un usuario a un centro
			new HfosAjax.JsonRequest('permisos_centros/savePermisosCentros', {
				parameters: 'usuariosId='+usuariosId.getValue()+permisos,
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
	},
	
	/**
	 * Metodo que activa o desactiva todos los checkbos de la lista de permisos
	 */
	_activeOrDesactiveChecks: function(allcheckeds){
		var checked = allcheckeds.checked;
		var checkeds = this.select('.resource-access > input[type="checkbox"]');
		checkeds.each(function(s){
			if(checked==true){
				s.checked=true;
				s.enable();
			} else {
				s.checked=false;
			}
		});
	}

});

HfosBindings.late('win-permisos-centros', 'afterCreate', function(hfosWindow){
	var permisosCentros = new PermisosCentros(hfosWindow);
});

