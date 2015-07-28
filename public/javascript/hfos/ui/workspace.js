
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
 * Enum para limites de tamaño de ventanas
 *
 * @enum {number}
 */
var HfosWindowDefinition = {

	WINDOW_MIN_TOP: 5,
	WINDOW_MIN_LEFT: 230

};

/**
 * Administra el Workspace de Trabajo
 */
var HfosWorkspace = Class.create({

	/**
	 * Referencia a HfosWindowManager
	 */
	_windowManager: null,

	/**
	 * Referencia a HfosToolbar
	 */
	_toolbar: null,

	/**
	 * Referencia a HfosTaskbar
	 */
	_taskbar: null,

	/**
	 * Referencia a HfosSystemTray
	 */
	_systemTray: null,

	//Referencia a HfosApplication
	_application: null,

	/**
	 * Inicializa el espacio de trabajo
	 *
	 * @constructor
	 */
	initialize: function(application){

		//Enlaza aplicación al workspace
		this._application = application;

		//Crear elementos del workspace
		this._windowManager = new HfosWindowManager(this);
		this._toolbar = new HfosToolbar(this);
		this._taskbar = new HfosTaskbar(this);
		this._systemTray = new HfosSystemTray(this);

		//Crear espacio de trabajo
		this._createSpace();

		//Agregar atajos de teclado
		this._application.activeShortcuts();

	},

	/**
	 * Cambia el tamaño de $('space') al agrandar la ventana
	 *
	 * @this {HfosWorkspace}
	 */
	_modifySpace: function(){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);

		//Modifica $('space')
		var height = (pageSize.windowHeight-windowScroll.top-34)+"px";
		this._windowManager.getSpaceElement().style.height = height;

		//Modifica posición barra de tareas
		if(this._windowManager.winToolbarElement!=null){
			var windowScroll = WindowUtilities.getWindowScroll(document.body);
			var pageSize = WindowUtilities.getPageSize(document.body);
			var toolbarElement = this._windowManager.winToolbarElement;
			toolbarElement.setStyle({
				'top': (pageSize.windowHeight-toolbarElement.getHeight())+'px'
			});
		}

	},

	/**
	 * Crea un DIV $('space') donde conviviran las ventanas del espacio de trabajo
	 *
	 * @this {HfosWorkspace}
	 */
	_createSpace: function(){

		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);
		var height = (pageSize.windowHeight-windowScroll.top-34)+"px";

		var workspace = document.createElement('DIV');
		workspace.addClassName('workspace');
		workspace.style.height = height;

		var appContainer = document.body.selectOne('div#app-cont-'+this._application.getContainerId());
		appContainer.appendChild(workspace);

		this._windowManager.setSpaceElement(workspace);

		var productivity = Hfos.getProductivity();
		this._application.observe('onHasAccess', productivity.show.bind(productivity));

		new Event.observe(window, 'resize', this._modifySpace.bind(this));
	},

	/**
	 * Consulta los elementos DOM que estén bajo el workspace
	 *
	 * @this {HfosWorkspace}
	 */
	select: function(selector){
		return this._windowManager.select(selector);
	},

	/**
	 * Devuelve el toolbar del workspace
	 *
	 * @this {HfosWorkspace}
	 */
	getToolbar: function(){
		return this._toolbar;
	},

	/**
	 * Devuelve el taskbar del workspace
	 *
	 * @this {HfosWorkspace}
	 */
	getTaskbar: function(){
		return this._taskbar;
	},

	/**
	 * Devuelve el system-tray del workspace
	 *
	 * @this {HfosWorkspace}
	 */
	getSystemTray: function(){
		return this._systemTray;
	},

	/**
	 * Obtiene el manejador de ventanas
	 *
	 * @this {HfosWorkspace}
	 */
	getWindowManager: function(){
		return this._windowManager;
	},

	/**
	 * Devuelve el espacio
	 *
	 * @this {HfosWorkspace}
	 */
	getSpaceElement: function(){
		return this._windowManager.getSpaceElement();
	},

	/**
	 * Devuelve la aplicación a la que pertenece el workspace
	 *
	 * @this {HfosWorkspace}
	 */
	getApplication: function(){
		return this._application;
	},

	/**
	 * Recibe notificaciones de eventos
	 *
	 * @this {HfosWorkspace}
	 */
	notify: function(eventName){
		switch(eventName){
			case 'hibernateCompleted':
				this._application.notify('hibernateCompleted');
				break;
		}
	},

	/**
	 * Duerme el workspace
	 *
	 * @this {HfosWorkspace}
	 */
	sleep: function(){
		this._toolbar.sleep();
		this._windowManager.sleep();
	},

	/**
	 * Despierta la aplicación
	 *
	 * @this {HfosWorkspace}
	 */
	wakeup: function(){
		this._toolbar.wakeup();
		this._windowManager.wakeup();
	},

	/**
	 * Hibernate
	 *
	 * @this {HfosWorkspace}
	 */
	hibernate: function(){
		this._windowManager.hibernate();
	}

});
