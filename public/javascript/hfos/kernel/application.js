
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
 * HfosApplication
 *
 * Clase base para instanciar Aplicaciones
 */
var HfosApplication = Class.create({

	//Referencia al HfosWorkspace
	_workspace: null,

	//Referencia al HfosMenu
	_menu: null,

	//Etado local de la aplicación
	_status: 'wakeup',

	//Estado remoto de la aplicación
	_state: null,

	/**
	 * Indica si los recursos externos de la aplicación fueron cargados
	 */
	_sourcesLoaded: false,

	/**
	 * Indica el porcentaje total del recurso
	 */
	_percentLoaded: 0,

	/**
	 * Nombre Real de la aplicación
	 */
	_name: null,

	/**
	 * Código de la aplicación
	 */
	_code: null,

	/**
	 * Icono de la aplicación
	 */
	_icon: null,

	/**
	 * Nombre de la aplicación
	 */
	_uri: null,

	/**
	 * Indica si la aplicación fue booteada en runtime
	 */
	_runtimeLoaded: null,

	/**
	 * Contenedor donde se instaló la aplicación
	 */
	_containerId: 0,

	/**
	 * Opciones de boteo de la aplicación
	 */
	options: null,

	/**
	 * Indica si el usuario logueado tiene acceso a la aplicación
	 */
	_hasAccess: false,

	/**
	 * Referencia al HfosVirtualUser asociado a la aplicación
	 */
	_virtualUser: null,

	/**
	 * Referencia al HfosStorageDb de la aplicación
	 */
	_storage: null,

	/**
	 * Constructor de HfosApplication
	 *
	 * @constructor
	 */
	initialize: function(name, code, uri, icon, runtimeLoaded, options)
	{
		this._name = name;
		this._code = code;
		if(typeof uri == "undefined"){
			this._uri = $Kumbia.app;
		} else {
			this._uri = uri;
			$Kumbia.app = uri;
		};
		this._icon = icon;
		this._options = options;
		if(runtimeLoaded==false){
			this._createWorkspace();
		};
		this._runtimeLoaded = runtimeLoaded;
		document.body.className = this._code;
		this._loadSources();
		this._checkStability();
	},

	/**
	 * Obtiene el ID del contenedor donde se instaló la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getContainerId: function()
	{
		return this._containerId;
	},

	/**
	 * Devuelve el elemento DOM donde se deben agregar todos los elementos de la ventana
	 *
   	 * @this {HfosApplication}
	 */
	getAppContainer: function()
	{
		return document.body.selectOne('div#app-cont-'+this._containerId);
	},

	/**
	 * Crea el workspace de la aplicación una vez están cargados los recursos de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_createWorkspace: function(){
		if(this._menu===null){
			this._containerId = Hfos.getNextContainerId();
			this._menu = new HfosMenu(this);
			this._workspace = new HfosWorkspace(this);
			this._loadState();
			if(this.options!==null){
				if(typeof this.options.afterFinish != "undefined"){
					this.options.afterFinish();
				}
			};
		};
		this._setFavicon();
		this._setWindowTitle();
	},

	/**
	 * Establece el titulo de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_setWindowTitle: function(){
		document.title = this._name+' - '+Hfos.getConsumer();
	},

	/**
	 * Establece el favicon de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_setFavicon: function(){
		if(typeof document.head != "undefined"){
			var favicon = $('favicon');
			if(favicon){
				favicon.erase();
			};
			var favicon = document.createElement('LINK');
			favicon.id = 'favicon';
			favicon.type = 'text/png';
			favicon.rel = 'icon';
			favicon.href = $Kumbia.path+'img/backoffice/hover/'+this._icon;
			document.head.appendChild(favicon);
		};
	},

	/**
	 * Detecta posibles inconsitencias en la ejecución de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_checkStability: function(){
		if(Hfos.getMode()!='production'){
			if(window.location.host!='localhost'&&window.location.host!='127.0.0.1'){
				new HfosModal.alert({
					title: 'Modo desarrollo y pruebas',
					message: 'La aplicación no está en modo producción. '+
					'En este momento puede tener un rendimiento menor y los problemas '+
					'que se generen no serán reportados automáticamente. '+
					'Consulte con soporte técnico para ajustar la configuración.'
				});
			}
		}
	},

	/**
	 * Carga los recursos estáticos de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_loadSources: function(){
		if(this._runtimeLoaded==true){
			this._percentLoaded = 0;
			new HfosAjax.JsonRequest('workspace/getSources', {
				parameters: 'appCode='+this._code,
				onSuccess: function(response){
					if(response.status=='OK'){
						var sources = $A(response.sources);
						if(sources.length>0){
							this._loadSourceBatch(sources, 0, response.mode, response.total);
						}
					} else {
						HfosNotifierServer.add(response.message);
					}
				}.bind(this)
			});
		} else {
			this._sourcesLoaded = true;
		}
	},

	/**
	 * Carga en batch los recursos estáticos de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_loadSourceBatch: function(sources, index, mode, total){
		if(sources[index].type=='js'){
			var script = document.createElement('SCRIPT');
			script.type = 'text/javascript';
			if(mode=='production'){
				script.src = $Kumbia.path+'javascript/hfos/production/'+sources[index].name+'.js?r='+parseInt(Math.random()*1000);
			} else {
				script.src = $Kumbia.path+'javascript/hfos/app/'+this._code+'/'+sources[index].name+'.js?r='+parseInt(Math.random()*1000);
			};
			script.observe('load', function(sources, index, mode, total){
				if(sources.length!=(index+1)){
					this._loadSourceBatch(sources, index+1, mode, total);
				};
				this._notifySourceLoaded(sources[index], total);
			}.bind(this, sources, index, mode, total));
			document.body.appendChild(script);
		} else {
			if(sources[index].type=='css'){
				var link = document.createElement('LINK');
				link.type = 'text/css';
				link.rel = 'stylesheet';
				if(mode=='production'){
					link.href = $Kumbia.path+'css/hfos/app/'+sources[index].name+'.css?r='+parseInt(Math.random()*1000);
				} else {
					link.href = $Kumbia.path+'css/hfos/production/'+sources[index].name+'.css?r='+parseInt(Math.random()*1000);
				};
				document.body.appendChild(link);
				this._notifySourceLoaded(sources[index], total);
			}
		}
	},

	/**
	 * Notifica la carga completa de un recurso externo
	 *
   	 * @this {HfosApplication}
	 */
	_notifySourceLoaded: function(source, total){
		this._percentLoaded+=source.size/total;
		if(this._percentLoaded>0.95){
			this._createWorkspace();
		}
	},

	/**
	 * Obtiene el estado de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	_loadState: function(){
		this._hasAccess = false;
		new HfosAjax.JsonRequest('workspace/getApplicationState', {
			parameters: 'appCode=' + this._code,
			onSuccess: function(response){
				if (typeof response.options == 'undefined') {
					this._noRoleAccess();
					return;
				}
				if(typeof response.options['userRoles'] == "undefined"){
					this._noRoleAccess();
					return;
				} else {
					if(typeof response.options['userRoles'][this._code] == "undefined"){
						this._noRoleAccess();
						return;
					}
				};
				if(typeof response.options['passwordExpired'] != "undefined"){
					if(response.options['passwordExpired']){
						this._passwordExpired();
					}
				} else {
					this._hasAccess = true;
					if(this._code!='PV'&&this._code!='FO'){
						if(Hfos.shouldToUpgrade()==true){
							Hfos.showUpgradeBox();
						} else {
							if(typeof response.options['welcome'] == "undefined"){
								Hfos.showWelcomeBox();
							};
						};
					};
				};
				this._state = response.state;
				this._virtualUser = new HfosVirtualUser(response.tokenId, response.options)
				if(this._hasAccess==true){
					if(Hfos.getMode()=='test'){
						this._storage = null;
					} else {
						this._storage = new HfosStorageDb(this, this._restore.bind(this));					;
					};
					this.fire('onHasAccess');
				};
			}.bind(this)
		});
	},

	/**
	 * Informa al usuario que no tiene roles asignados para trabajar en la aplicación y la cierra
	 *
   	 * @this {HfosApplication}
	 */
	_noRoleAccess: function(){
		new HfosModal.alert({
			title: this._name,
			message: 'No tiene perfiles asignados para trabajar en esta aplicación. Consulte con un administrador del sistema',
			onAccept: function(){
				this.forceClose();
			}.bind(this)
		});
	},

	/**
	 * Informa al usuario que el password debe ser cambiado
	 *
   	 * @this {HfosApplication}
	 */
	_passwordExpired: function(){
		new HfosModal.alert({
			title: this._name,
			message: 'Su contraseña ha expirado, debe cambiar su contraseña en este momento',
			onAccept: function(){
				Hfos.showAccountData();
			}.bind(this)
		});
	},

	/**
	 * Forza el cerrado de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	forceClose: function(){
		Hfos.closeOpenedApp(this);
	},

	/**
	 * Establece el estado de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	setState: function(state){
		new HfosAjax.JsonRequest('workspace/setApplicationState', {
			parameters: 'appCode='+this._code+'&state='+state,
			onSuccess: function(state, response){
				if(response.status=='FAILED'){
					alert(response.message)
				} else {
					this._state = state;
				}
			}.bind(this, state)
		});
	},

	/**
	 * Indica si tiene acceso a la aplicación actual
	 *
   	 * @this {HfosApplication}
	 */
	hasAccess: function(){
		return this._hasAccess;
	},

	/**
	 * Obtiene el estado actual de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getState: function(){
		return this._state;
	},

	/**
	 * Obtiene el espacio de trabajo asociado a la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getWorkspace: function(){
		return this._workspace;
	},

	/**
	 * Obtiene el menú asociado a la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getMenu: function(){
		return this._menu;
	},

	/**
	 * Obtiene la base de datos de almacenamiento local
	 *
   	 * @this {HfosApplication}
	 */
	getStorage: function(){
		if(typeof this._storage !== "undefined"){
			return this._storage;
		} else {
			return null;
		}
	},

	/**
	 * Establece la referencia al objeto de almacenamiento local
	 *
   	 * @this {HfosApplication}
	 */
	setStorage: function(storage){
		this._storage = storage;
	},

	/**
	 * Obtiene el URI de acceso relativo a la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getUri: function(){
		return this._uri;
	},

	/**
	 * Obtiene el código de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getCode: function(){
		return this._code;
	},

	/**
	 * Obtiene el nombre de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getName: function(){
		return this._name;
	},

	/**
	 * Obtener el icono de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getIcon: function(){
		return this._icon;
	},

	/**
	 * Devuelve la referencia al VirtualUser asociado a la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	getVirtualUser: function(){
		return this._virtualUser;
	},

	/**
	 * Devuelve las opciones de sesión del usuario
	 *
   	 * @this {HfosApplication}
	 */
	getUserOptions: function(){
		return this._userOptions;
	},

	/**
	 * Activar los atajos de teclado
	 *
   	 * @this {HfosApplication}
	 */
	activeShortcuts: function(){
		Hfos.activeShortcuts();
	},

	/**
	 * Carga un recurso en la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	loadSource: function(source, procedure){
		var script = document.createElement('SCRIPT');
		script.type = 'text/javascript';
		script.src = $Kumbia.path + 'javascript/hfos/app/' + this._code + '/' + source + '.js';
		script.observe('load', procedure)
		document.body.appendChild(script);
	},

	/**
	 * Ejecuta un proceso dentro de una ventana de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	run: function(options){
		if (navigator.onLine || window.location.hostname != 'hostname') {
			if (typeof options.action == "undefined" && typeof options.externAction == "undefined") {
				new HfosModal.alert({
					title: this._name,
					message: 'No se puede iniciar el proceso porque no se indicó la acción a realizar'
				});
			};
			var windowManager = this._workspace.getWindowManager();
			if (typeof options.action != "undefined") {
				HfosAcl.checkPermission(options.action, windowManager.createAndRun.bind(windowManager, options));
			} else {
				HfosAcl.checkPermission(options.externAction, windowManager.createAndRun.bind(windowManager, options), true);
			}
		} else {
			Hfos._onOfflineMode();
		}
	},

	/**
	 *
   	 * @this {HfosApplication}
   	 */
	open: function(options)
	{
		this._workspace.getWindowManager().create(options);
	},

	/**
	 * Agrega un proceso a un determinado evento
	 *
   	 * @this {HfosApplication}
	 */
	observe: function(eventName, procedure)
	{
		if (Object.isUndefined(this['_' + eventName])) {
			this['_' + eventName] = [];
		};
		this['_' + eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento de la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	fire: function(eventName, param0, param1){
		try {
			if(!Object.isUndefined(this['_'+eventName])){
				for(var i=0;i<this['_'+eventName].length;i++){
					if(this['_'+eventName][i](this, param0, param1)===false){
						return false;
					}
				};
				return true;
			} else {
				return true;
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Notifica una acción sobre la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	notify: function(eventName){
		switch(eventName){
			case 'hibernateCompleted':
				this.setState('hibernate');
				break;
		}
	},

	/**
	 * Duerme la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	sleep: function(){
		if(this._status=='wakeup'){
			this._workspace.sleep();
			this._status = 'sleep';
			this.setState('sleep');
		}
	},

	/**
	 * Despierta la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	wakeup: function(){
		if(this._status=='sleep'){
			this._workspace.wakeup();
			this._status = 'wakeup';
			$Kumbia.app = this._uri;
			this._setFavicon();
			this._setWindowTitle();
			this.setState('active');
		}
	},

	/**
	 * Pasa a modo de hibernación la aplicación
	 *
   	 * @this {HfosApplication}
	 */
	hibernate: function(){
		this._workspace.hibernate();
	},

	/**
	 * Restaura la aplicación desde el estado de hibernación
	 *
   	 * @this {HfosApplication}
	 */
	_restore: function(){
		if(Hfos.getMode()!='test'){
			if(this._storage!==null){
				this._storage.findAll('Objects', function(objects){
					if(objects.length>0){
						Hfos.getUI().blockInput();
						var orderedObjects = [];
						for(var i=0;i<objects.length;i++){
							orderedObjects[objects[i].position] = objects[i];
						};
						for(var i=0;i<orderedObjects.length;i++){
							var object = orderedObjects[i];
							if(typeof object != "undefined"){
								this.open({
									'id': object.id,
									'state': object.state,
									'title': object.title,
									'y': object.y,
									'x': object.x,
									'width': object.width,
									'height': object.height,
									'icon': object.icon,
									'content': object.content
								});
							};
						};
						Hfos.getUI().unblockInput();
					}
				}.bind(this));
				this._storage.free();
			};
			return true;
		};
		this.setState('active');
	}

})
