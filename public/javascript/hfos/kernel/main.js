
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
 * Hfos
 *
 * Namespace de la aplicación
 */
var Hfos = {

	//Interface de Usuario UI
	_ui: null,

	/**
	 * Indica si el entorno está inicializado para cualquier aplicación
	 *
	 * @type {boolean}
	 */
	_loaded: false,

	/**
	 * Indica el entorno en el que se ejecuta la aplicación
	 *
	 * @type {string}
	 */
	_mode: 'test',

	/**
	 * Cliente que adquirió la aplicación
	 *
	 * @type {string}
	 */
	_consumer: '',

	//Versión activa en el cliente
	_version: null,

	/**
	 * Indica si la aplicación ha sido actualizada y se debe hacer upgrade
	 *
	 * @type {boolean}
	 */
	_shouldUpgrade: false,

	/**
	 * Indica si los atajos de teclado globales están activos
	 *
	 * @type {boolean}
	 */
	_shortCutsActived: false,

	/**
	 * Todas las aplicaciones abiertas
	 *
	 * @type {Object.<HfosApplication>}
	 */
	_applications: {},

	//Aplicación Activa
	_activeApplication: null,

	//Número de aplicaciones abiertas
	_numberApps: 0,

	//Salvapantallas. Referencia a HfosScreenSaver
	_screenSaver: null,

	//Referencia a HfosCommunications
	_communications: null,

	//Referencia a HfosTime
	_time: null,

	//Instancia de CoreAnimation
	_animation: null,

	//Instancia de HfosWelcomeBox
	_welcomeBox: null,

	//Instancia de HfosUpgradeBox
	_upgradeBox: null,

	/**
	 * Contenedor de Aplicaciones Activo
	 *
	 * @type {number}
	 */
	_activeContainerId: 0,

	/**
	 * Inicializar el entorno de aplicación
	 *
	 * @constructor
	 * @public
	 */
	initialize: function(){
		if (Hfos._loaded == false) {
			new Event.observe(document, 'dom:loaded', function() {
				Hfos._loadComponents();
				Hfos._addAppContainers();
				Hfos._loaded = true;
				Hfos._communications.observe('endSession', Hfos.closeApp);
				window.onerror = HfosException.showToDebug;
				if (window.location.hostname != 'localhost') {
					document.body.observe('offline', Hfos._onOfflineMode)
				}
			});
			if (window.location.toString().include('#') == false) {
				window.location = window.location+'#';
			};
		}
	},

	/**
	 * Carga los componentes base requeridos por el kernel
	 *
	 * @private
	 */
	_loadBaseComponents: function(){
		Hfos._ui = new HfosUI();
		Hfos._animation = new CoreAnimation();
	},

	/**
	 * Carga componentes requeridos por el kernel
	 *
	 * @private
	 */
	_loadComponents: function(){
		Hfos._loadBaseComponents();
		Hfos._communications = new HfosCommunications();
		Hfos._productivity = new HfosProductivity();
		Hfos._time = new HfosTime();
	},

	/**
	 * Agrega contenedores donde se instalarán las aplicaciones
	 *
	 * @private
	 */
	_addAppContainers: function(){
		var userSpace = new Element('div', {
			'id': 'userSpace',
			'class': 'userSpace'
		});
		for(var i=0;i<7;i++){
			userSpace.appendChild(new Element('div', {
				'class': 'appContainer',
				'id': 'app-cont-'+i
			}));
		};
		document.body.selectOne('div#mainContent').appendChild(userSpace);
	},

	/**
	 * Devuelve el contenedor de aplicaciones activo
	 */
	getNextContainerId: function(){
		var activeId = Hfos._activeContainerId;
		Hfos._activeContainerId++;
		return activeId;
	},

	/**
	 * Bootear una aplicación
	 */
	bootApp: function(name, code, uri, icon, runtimeLoaded){
		Hfos.initialize();
		if(Hfos._activeApplication!=null){
			if(Hfos._activeApplication.getCode()!=code){
				Hfos._activeApplication.sleep();
			}
		};
		if(typeof Hfos._applications[code] == "undefined"){
			if(Hfos._loaded==false){
				new Event.observe(document, 'dom:loaded', Hfos._delayedLoad.bind(this, name, code, uri, icon, runtimeLoaded));
			} else {
				Hfos._delayedLoad(name, code, uri, icon, runtimeLoaded);
			}
		} else {
			Hfos._applications[code].wakeup();
			Hfos._activeApplication = Hfos._applications[code];
		};
	},

	/**
	 * Cierra una aplicación y restaura una previamente abierta
	 */
	closeOpenedApp: function(application){
		var code = application.getCode();
		if(typeof Hfos._applications[code] != "undefined"){
			Hfos._applications[code].sleep();
			Hfos._numberApps--;
			if(Hfos._numberApps<=0){
				Hfos.closeApp();
			} else {
				var applications = Hfos._applications;
				$H(applications).each(function(application){
					application[1].wakeup();
					throw $break;
				})
			}
		}
	},

	/**
	 * Nombre del cliente que adquirió el sistema
	 */
	setConsumer: function(consumer){
		Hfos._consumer = consumer;
	},

	/**
	 * Devuelve el nombre del cliente que adquirió el sistema
	 */
	getConsumer: function(){
		return Hfos._consumer;
	},

	/**
	 * Establece la versión según la base de datos y si se debe actualizar
	 */
	setVersion: function(version, shouldUpgrade){
		Hfos._version = version;
		Hfos._shouldUpgrade = shouldUpgrade;
	},

	/**
	 * Indica si se debe actualizar la aplicación
	 */
	shouldToUpgrade: function(){
		return Hfos._shouldUpgrade;
	},

	/**
	 * Establece el entorno en el que se ejecuta la aplicación
	 */
	setMode: function(mode){
		Hfos._mode = mode;
	},

	/**
	 * Devuelve el entorno en el que se ejecuta la aplicación
	 */
	getMode: function(){
		return Hfos._mode;
	},

	/**
	 * Indica si el entorno de trabajo ya fue cargado
	 */
	getLoaded: function(){
		return Hfos._loaded;
	},

	/**
	 * Obtener la aplicación activa
	 */
	getApplication: function(){
		return Hfos._activeApplication;
	},

	/**
	 * Obtener las aplicaciones abiertas
	 */
	getApplications: function(){
		return Hfos._applications;
	},

	/**
	 * Cargar una aplicación hasta que se complete la carga del DOM
	 *
	 * @private
	 */
	_delayedLoad: function(name, code, uri, icon, runtimeLoaded){
		var application = new HfosApplication(name, code, uri, icon, runtimeLoaded);
		Hfos._applications[code] = application;
		Hfos._activeApplication = application;
		Hfos._numberApps++;
		if(Hfos._numberApps==1){
			Hfos._onFirstAppCreated(application);
		}
	},

	/**
	 * Se ejecuta cuando se carga la primera aplicación
	 *
	 * @private
	 */
	_onFirstAppCreated: function(){
		Hfos._communications.listen();
	},

	/**
	 * Indica si el navegador está soportado
	 */
	isSupportedNavigator: function(){
		var result = /Firefox\/([0-9]+)\.([0-5]+)/.exec(navigator.userAgent);
		if(result==null){
			var result = /Chrome\/([0-9]+)\.([0-5]+)/.exec(navigator.userAgent);
			if(result==null){
				return false;
			} else {
				return true;
			}
		} else {
			if(result[1]>3){
				return true;
			} else {
				return false;
			}
		}
	},

	/**
	 * Verificar si se está logueado en la aplicación
	 */
	checkForAuthToken: function(bootAppCode, onSuccess){
		Hfos.initialize();
		if (Hfos.isSupportedNavigator()) {
			if ($('mainContent').innerHTML == '') {
				new HfosAjax.JsonRequest('session/exists', {
					onSuccess: function(bootAppCode, onSuccess, authenticated){
						if(authenticated==false){
							new HfosLogin(bootAppCode, onSuccess);
						} else {
							onSuccess();
						}
					}.bind(this, bootAppCode, onSuccess)
				});
			} else {
				var className = Utils.upperCaseFirst($Kumbia.controller);
				if (eval('typeof ' + className + ' == "function"')) {
					eval('new ' + className + '($("mainContent"))');
				}
			}
		} else {
			Hfos._loadBaseComponents();
			new HfosModal.customDialog({
				icon: 'firefox48',
				title: 'Navegador Incompatible',
				message: 'Su navegador no es compatible con el sistema, por favor instale ó actualice a Mozilla Firefox 17.0',
				buttons: {
					'Descargar': {
						'action': function(){
							window.location = 'https://affiliates.mozilla.org/link/banner/1728/3/19';
						}
					}
				}
			});
		}
	},

	/**
	 * Activa los atajos de teclado globales
	 */
	activeShortcuts: function(){
		if(Hfos._shortCutsActived==false){
			Hfos._shortCutsActived = true;
			new Event.observe(window, 'keydown', HfosShortcuts.globalKeyUpHandler)
		}
	},

	/**
	 * Carga un recurso del sistema
	 */
	loadSource: function(source, procedure){
		var script = document.createElement('SCRIPT');
		script.type = 'text/javascript';
		script.src = $Kumbia.path+'javascript/hfos/'+source+'.js';
		if(typeof procedure != "undefined"){
			script.observe('load', procedure)
		};
		document.body.appendChild(script);
	},

	/**
	 * Muestra la pantalla de Bienvenida
	 */
	showWelcomeBox: function(){
		if(Hfos._welcomeBox==null){
			Hfos._welcomeBox = new HfosWelcomeBox();
		}
	},

	/**
	 * Muestra la pantalla de Bienvenida
	 */
	showUpgradeBox: function(){
		if(Hfos._upgradeBox==null){
			Hfos._upgradeBox = new HfosUpgradeBox();
		}
	},

	/**
	 * Indica si la pantalla de bienvenida está abierta
	 */
	isWelcoming: function(){
		if(Hfos._welcomeBox==null){
			return false;
		} else {
			return Hfos._welcomeBox.isOnCourse();
		}
	},

	/**
	 * Indica si la aplicación se está actualizando actualmente
	 */
	isUpgrading: function(){
		if(Hfos._upgradeBox==null){
			return false;
		} else {
			return Hfos._upgradeBox.isOnCourse();
		}
	},

	/**
	 * Muestra la pantalla de cambiar clave
	 */
	showAccountData: function(){
		Hfos.getApplication().run({
			id: 'win-useraccount',
			title: "Datos de la Cuenta",
			icon: 'key.png',
			height: 270,
			width: 500,
			externAction: "identity/usuarios/config"
		});
	},

	/**
	 * La función se llama al desconectar el equipo de la red
	 *
	 * @private
	 */
	_onOfflineMode: function(){
		new HfosModal.alert({
			title: 'Alerta',
			message: 'Su equipo está desconectado de la red y no puede acceder al servidor'
		});
	},

	/**
	 * Obtener el objeto de Interfaz de Usuario (UI)
	 */
	getUI: function(){
		return Hfos._ui;
	},

	/**
	 * Obtener el objeto estándar de animación
	 */
	getAnimation: function(){
		return Hfos._animation;
	},

	/**
	 * Obtener el objeto de comunicaciones
	 */
	getCommunications: function(){
		return Hfos._communications;
	},

	/**
	 * Obtener el objeto de productividad
	 */
	getProductivity: function(){
		return Hfos._productivity;
	},

	/**
	 * Cerrar la aplicación
	 */
	closeApp: function(){
		Hfos.getUI().showScreenShadow();
		Hfos.getCommunications().stop();
		for(var name in Hfos._applications){
			Hfos._applications[name].hibernate();
		};
		Hfos._waitForHibernate = window.setInterval(function(){
			var hibernatedComplete = true;
			for(var name in Hfos._applications){
				if(Hfos._applications[name].getState()!='hibernate'){
					hibernatedComplete = false;
				}
			};
			if(hibernatedComplete){
				window.clearInterval(Hfos._waitForHibernate);
				new HfosAjax.JsonRequest('session/end', {
					onSuccess: function(){
						window.location = Utils.getAppURL('index');
					}
				});
			}
		}, 200);
	}

};

