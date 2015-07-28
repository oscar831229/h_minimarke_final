
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
 * HfosWindowManager
 *
 * Administrador de Ventanas
 */
var HfosWindowManager = Class.create({

	//Referencia al Workspace
	_workspace: null,

	//Coordenadas originales de las ventanas
	_originalState: {},

	//Elemento DOM del space
	_spaceElement: null,

	//Todas las ventanas registradas
	_windows: {},

	//Referencia a la ventana modal dentro del workspace
	_modalWindow: null,

	//Número de ventanas registradas
	_numberWindows: 0,

	//Eventos para las ventanas con enlazamiento tardío
	_lateBindings: {},

	//Establece la posición de las ventanas en la cascada
	_windowOrder: [],

	_offsetTop: 0,

	_offsetLeft: 0,

	/**
	 * @constructor
	 */
	initialize: function(workspace){
		this._offsetTop = 0;
		this._offsetLeft = 0;
		this._workspace = workspace;
	},

	/**
	 * Función interna para activar una ventana
	 *
	 * @this {HfosWindowManager}
	 */
	_setActiveInternal: function(selectedWindow){
		if(selectedWindow.isActive()==false){
			var position = 0;
			this._windowOrder = [];
			$H(this._windows).each(function(_window){
				if(_window[1]!=selectedWindow){
					_window[1].setInactive(position);
					this._windowOrder.push(_window[1].getId());
				};
				position++;
			}.bind(this));
			this._windowOrder.push(selectedWindow.getId());
			selectedWindow.setActive(position);
		};
		switch(selectedWindow.getStatus()){
			case 'minimized':
			selectedWindow.restoreWindow();
			break;
		};
	},

	/**
	 * Establece una ventana como activa
	 *
	 * @this {HfosWindowManager}
	 */
	setActiveWindow: function(selectedWindow, withEffect){
		if(typeof withEffect == "undefined"){
			withEffect = false;
		};
		if(withEffect==false){
			this._setActiveInternal(selectedWindow);
		} else {
			var leftSignal = 1;
			var activeWindow = this.getActiveWindow();
			if(activeWindow!==null){
				if(activeWindow.getWindowElement().offsetLeft>selectedWindow.getWindowElement().offsetLeft){
					leftSignal = -1;
				};
				new Effect.Move(selectedWindow.getWindowElement(), {
					x: leftSignal*20,
					duration: 0.2,
					afterFinish: function(selectedWindow, leftSignal){
						this._setActiveInternal(selectedWindow);
						new Effect.Move(selectedWindow.getWindowElement(), {
							x: 20*-leftSignal,
							duration: 0.2
						});
					}.bind(this, selectedWindow, leftSignal)
				});
			};
		};
		this._workspace.getTaskbar().setActive(selectedWindow);
	},

	/**
	 * Registra una ventana en modo modal
	 *
	 * @this {HfosWindowManager}
	 */
	setModalWindow: function(modalWindow, onSuccess){
		if(this._modalWindow===null){
			this._modalWindow = modalWindow;
			onSuccess();
		} else {
			new HfosModal.alert({
				title: Hfos.getApplication().getName(),
				message: 'Ya hay otra ventana modal abierta'
			});
		}
	},

	/**
	 * Indica si hay una ventana en modo modal
	 *
	 * @this {HfosWindowManager}
	 */
	hasModalWindow: function(){
		return this._modalWindow!==null;
	},

	/**
	 * Quita la ventana modal activa
	 *
	 * @this {HfosWindowManager}
	 */
	removeModalWindow: function(){
		delete this._modalWindow;
		this._modalWindow = null;
	},

	/**
	 * Devuelve la ventana activa en el window manager
	 *
	 * @this {HfosWindowManager}
	 */
	getActiveWindow: function(){
		if(this._modalWindow===null){
			var selectedWindow = null;
			$H(this._windows).each(function(_window){
				if(_window[1].isActive()){
					selectedWindow = _window[1];
				}
			});
			return selectedWindow;
		} else {
			return this._modalWindow;
		}
	},

	/**
	 * Crea una nueva ventana
	 *
	 * @this {HfosWindowManager}
	 */
	create: function(options){
		if(this.exists(options)==false){
			this._numberWindows++;
			return this._addWindow(options);
		} else {
			this.setActiveWindow(this.getWindow(options.id), true);
			return false;
		}
	},

	/**
	 * Crea una nueva ventana
	 *
	 * @this {HfosWindowManager}
	 */
	createAndRun: function(options){
		if(this.exists(options)==false){
			if(typeof options.onStartup != "undefined"){
				if(typeof options.bindings == "undefined"){
					options.bindings = { };
				};
				options.bindings.onReady = options.onStartup;
			};
			this._numberWindows++;
			return this._addWindow(options);
		} else {
			var _window = this.getWindow(options.id);
			this.setActiveWindow(_window, false);
			if(typeof options.onStartup != "undefined"){
				options.onStartup(_window);
			};
			return false;
		}
	},

	/**
	 * Agrega una ventana al windowManager
	 *
	 * @this {HfosWindowManager}
	 */
	_addWindow: function(options){

		//Registra la ventana en el WindowManager
		this._windows[options.id] = new HfosWindow(this, options);

		//Agregar botón en barra de tareas
		this._workspace.getTaskbar().add(options);

		//Colocar la ventana como activa
		this.setActiveWindow(this._windows[options.id]);

		this._offsetTop+=10;
		this._offsetLeft+=10;

		return this._windows[options.id];

	},

	/**
	 * Desregistra una ventana al cerrarse
	 *
	 * @this {HfosWindowManager}
	 */
	_closedWindow: function(hfosWindow){
		this._workspace.getTaskbar().remove(hfosWindow);
		delete this._windows[hfosWindow.getId()];
		if(typeof this._originalState[hfosWindow.getId()] != "undefined"){
			delete this._originalState[hfosWindow.getId()];
		}
		this._numberWindows--;
		this._offsetTop = (this._numberWindows*10);
		this._offsetLeft = (this._numberWindows*10);
		if(this._numberWindows>0){
			this.setFirstNormalToActive();
		}
	},

	/**
	 * Consulta si una ventana existe
	 *
	 * @this {HfosWindowManager}
	 */
	exists: function(options){
		if(typeof this._windows[options.id] == "undefined"){
			return false;
		} else {
			return true;
		}
	},

	/**
	 * Obtiene un objeto de ventana apartir de su ID
	 *
	 * @this {HfosWindowManager}
	 */
	getWindow: function(windowId){
		if(typeof this._windows[windowId] != "undefined"){
			return this._windows[windowId];
		} else {
			return null;
		}
	},

	/**
	 * Obtiene todas las ventanas disponibles en el window manager
	 *
	 * @this {HfosWindowManager}
	 */
	getWindows: function(){
		return this._windows;
	},

	/**
	 * Almacena las dimensiones originales de la ventana cuando su estado es = normal
	 *
	 * @this {HfosWindowManager}
	 */
	getDimensionsFor: function(status, hfosWindow){
		var dimensions = {};
		switch(status){
			case 'normal':
				if(typeof this._originalState[hfosWindow.getId()] != "undefined"){
					var originalState = this._originalState[hfosWindow.getId()];
					delete this._originalState[hfosWindow.getId()];
					dimensions.left = originalState.left;
					dimensions.top = originalState.top;
					dimensions.width = originalState.width;
					dimensions.height = originalState.height;
				};
				break;
			case 'maximized':
				var windowScroll = WindowUtilities.getWindowScroll(this._spaceElement);
				var pageSize = WindowUtilities.getPageSize(this._spaceElement);
				this._originalState[hfosWindow.getId()] = hfosWindow.getDimensions();
				dimensions.width = (pageSize.windowWidth-windowScroll.left-20)+"px";
				dimensions.height = (pageSize.windowHeight-windowScroll.top-50)+"px";
				dimensions.left = '10px';
				dimensions.top = '10px';
				break;
			case 'minimized':
				this._originalState[hfosWindow.getId()] = hfosWindow.getDimensions();
				dimensions.width = "680px";
				dimensions.height = "400px";
				break;
		};
		hfosWindow.setStatus(status);
		return dimensions;
	},

	/**
	 * Coloca la primera ventana no-minimizada como activa
	 *
	 * @this {HfosWindowManager}
	 */
	setFirstNormalToActive: function(){
		this._workspace.getTaskbar().setNoneActive();
		for(var i=this._windowOrder.length-1;i>=0;i--){
			var _window = this.getWindow(this._windowOrder[i]);
			if(_window!==null){
				if(_window.getStatus()!='minimized'){
					this.setActiveWindow(_window);
					break;
				}
			};
		};
	},

	/**
	 * Cierra la ventana activa actualmente
	 *
	 * @this {HfosWindowManager}
	 */
	closeActiveWindow: function(){
		if(this._modalWindow===null){
			$H(this._windows).each(function(_window){
				if(_window[1].isActive()){
					_window[1].close();
				}
			});
		} else {
			this._modalWindow.close();
		}
	},

	/**
	 * Establece el elemento $('space') donde se agregan las ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	setSpaceElement: function(spaceElement){
		this._spaceElement = spaceElement;
	},

	/**
	 * Establece el elemento $('space') donde se agregan las ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	getSpaceElement: function(){
		return this._spaceElement;
	},

	/**
	 * Devuelve el número de ventanas activas en el WindowManager
	 *
	 * @this {HfosWindowManager}
	 */
	getNumberWindows: function(){
		if(this._modalWindow===null){
			return this._numberWindows;
		} else {
			return this._numberWindows+1;
		}
	},

	/**
	 * Obtiene el espacio adicional en "y" que se debe agregar al crear una ventana
	 *
	 * @this {HfosWindowManager}
	 */
	getOffsetTop: function(){
		return this._offsetTop;
	},

	/**
	 * Obtiene el espacio adicional en "x" que se debe agregar al crear una ventana
	 *
	 * @this {HfosWindowManager}
	 */
	getOffsetLeft: function(){
		return this._offsetLeft;
	},

	/**
	 * Devuelve la referencia a HfosWorkspace
	 *
	 * @this {HfosWindowManager}
	 */
	getWorkspace: function(){
		return this._workspace;
	},

	/**
	 * Consulta los elementos DOM que estén bajo el workspace
	 *
	 * @this {HfosWindowManager}
	 */
	select: function(selector){
		return this._spaceElement.select(selector);
	},

	/**
	 * Duerme el administrador de ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	sleep: function(){
		this._spaceElement.hide();
	},

	/**
	 * Despierta el administrador de ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	wakeup: function(){
		this._spaceElement.show();
		this.setFirstNormalToActive();
	},

	/**
	 * Suspende el administrador de ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	hibernate: function(){
		var windows = $H(this._windows);
		windows.each(function(_window){
			_window[1].hibernate();
		});
		this._interval = window.setInterval(function(windows){
			var hibernatedComplete = true;
			windows.each(function(_window){
				if(_window[1].getStatus()!='hibernate'){
					hibernatedComplete = false;
				}
			});
			if(hibernatedComplete){
				window.clearInterval(this._interval);
				this._workspace.notify('hibernateCompleted');
			}
		}.bind(this, windows), 200);
	},

	/**
	 * Notifica al administrador de ventanas sobre un evento ocurrido en alguna ventana
	 *
	 * @this {HfosWindowManager}
	 */
	notify: function(eventName, eventWindow){
		switch(eventName){
			case 'closed':
				this._closedWindow(eventWindow);
				break;
		}
	},

	/**
	 * Agrega un evento a una ventana con enlazamiento tardío
	 *
	 * @this {HfosWindowManager}
	 */
	lateBinding: function(windowId, eventName, procedure){
		HfosBindings.late(windowId, eventName, procedure);
	},

	/**
	 * Devuelve los eventos de enlazamiento tardío de las ventanas
	 *
	 * @this {HfosWindowManager}
	 */
	getLateBindings: function(windowId){
		return HfosBindings.get(windowId);
	}

});
