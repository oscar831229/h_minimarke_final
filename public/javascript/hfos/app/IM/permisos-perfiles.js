
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

var PermisosPerfiles = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){

		var aplicacionId = this.selectOne('select#aplicacionId');
		var perfilesId = this.selectOne('select#perfilesId');
		var submitElement = this.selectOne('input#submitButton');

		aplicacionId.observe('change', this._showRolesPerms.bind(this, perfilesId, aplicacionId, true));
		perfilesId.observe('change', this._showRolesPerms.bind(this, perfilesId, aplicacionId, false));
		submitElement.observe('click', this._savePerfilesPermisos.bind(this, perfilesId, aplicacionId));
	},

	/**
	 * Consulta los perfiles asignados a una aplicación
	 */
	_getRolesOnApp: function(aplicacionId){
		new HfosAjax.JsonRequest('permisos_perfiles/getPerfilesPorApp', {
			parameters: {
				'aplicacionId': aplicacionId
			},
			onSuccess: function(response){
				if(response.status=='OK'){
					var perfilesId = this.selectOne('select#perfilesId');
					perfilesId.innerHTML = '';
					perfilesId.appendChild(new Element('OPTION', {
						'value': '@'
					}).update('Seleccione...'));
					response.perfiles.each(function(perfil){
						perfilesId.appendChild(new Element('OPTION', {
							'value': perfil.id
						}).update(perfil.nombre));
					});
					this.getElement('chooseRole').show();
					this.getElement('chooseApp').hide();
					this.getElement('tabRolesChoose').show();
				} else {
					this.getMessages().error(response.message);
					this.getElement('chooseRole').hide();
					this.getElement('chooseApp').show();
					this.getElement('tabRolesChoose').hide();
				}
			}.bind(this)
		});
	},

	_showRolesPerms: function(perfilesId, aplicacionId, appChanged){

		if(aplicacionId.getValue()!='@'){
			if(appChanged==false){
				if(perfilesId.getValue()=='@'){
					this._getRolesOnApp(aplicacionId.getValue());
				}
			} else {
				this.getElement("permsContent").update("");
				this.selectOne('input#submitButton').hide();
				this._getRolesOnApp(aplicacionId.getValue());
				return;
			}
		} else {
			this.getElement('chooseRole').hide();
			this.getElement('chooseApp').show();
			this.getElement('tabRolesChoose').hide();
		};

		if(perfilesId.getValue()!='@'){
			if(aplicacionId.getValue()!='@'){
				this.getElement('chooseRole').hide();
				this.selectOne("input#submitButton").show();
				new HfosAjax.Request('permisos_perfiles/getPermisos', {
					parameters: 'perfilesId='+perfilesId.getValue()+'&aplicacionId='+aplicacionId.getValue(),
					onSuccess: function(transport){
						this.getMessages().notice('Seleccione los permisos asignados al perfil y dé click en "Guardar"');
						this.getElement('permsContent').update(transport.responseText).show();
						this.GardienInitialize();
					}.bind(this)
				});
			}
		} else {
			this.getMessages().hide();
			this.getElement('chooseRole').show();
			this.getElement("permsContent").update("");
			this.selectOne('input#submitButton').hide();
		}
	},

	_savePerfilesPermisos: function(perfilesId, aplicacionId){

		//Buscamos los check s cargados por un selector
		var checkeds = this.select('.resource-access > input[type="checkbox"]');
		if(checkeds){ // si existe

			//Verificamos lo que tengan una ruta signada y que esten seleccioandos o chequeados
			var i = 0;
			var access = "";
			checkeds.each(function(inputElement){
				if(inputElement.checked && inputElement.value){
					access += "&access["+i+"]="+inputElement.value;
					i++;
				}
			});

			//Enviamos un ajax al action del controlador para q guarde en DB
			new HfosAjax.JsonRequest('permisos_perfiles/savePerfilesPermisos', {
				parameters: 'perfilesId='+perfilesId.getValue()+'&aplicacionId='+aplicacionId.getValue()+''+access,
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
	 * Agrega los callbacks cada vez que se muestre la grilla
	 */
	_prepareForInput: function(){
		this._hyperGrid.clearData();
		return true;
	},

	/**
	 * Metodo que activa o desactiva sub menus de tree desde checkbox seleccionado
	 */
	rollTree: function(element){
		var enable = element.checked;
		var ulElement = element.adjacent('UL')[0];
		element.parentNode.select('input[type="checkbox"]').each(function(inputElement){
			if(inputElement.up(1)==ulElement){
				if(enable==true){
					inputElement.enable();
				} else {
					inputElement.disable();
				}
			}
		});
	},

	GardienInitialize: function(){
		var first = true;
		this.select('div#acl input[type="checkbox"]').each(function(element){
			if(first==true){
				first = false;
			} else {
				if(element.checked==false){
					element.disable();
				}
			};
			element.observe('click', function(element){
				this.rollTree(element);
			}.bind(this, element));
		}.bind(this));

		this.select('div#acl input[type="checkbox"]').each(function(element){
			if(element.checked==true){
				this.rollTree(element);
			}
		}.bind(this));

		//Asignamos un listener a (des)seleccionar todos los permisos
		var allCheckeds = this.selectOne('input#selectAll');
		allCheckeds.observe('click', this._activeOrDesactiveChecks.bind(this, allCheckeds));

		this._notifyContentChange();
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

HfosBindings.late('win-permisos-perfiles', 'afterCreate', function(hfosWindow){
	var permisosPerfiles = new PermisosPerfiles(hfosWindow);
});