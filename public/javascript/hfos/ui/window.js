
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
 * HfosWindow
 *
 * Clase base para crear ventanas dentro del WindowManager
 */
var HfosWindow = Class.create({

	//Elemento DOM de la ventana
	_element: null,

	//Elemento DOM de la barra de titulo de la ventana
	_headerElement: null,

	//Elemento DOM del botón de cerrar
	_closeElement: null,

	//Elemento DOM del botón de minimizar
	_minimizeElement: null,

	//Elemento DOM del botón de redimensionar
	_resizeElement: null,

	//Elemento DOM del contenido de la ventana
	_contentElement: null,

	//Elemento DOM del título de la ventana
	_titleElement: null,

	//Elemento DOM de la barra de titulo
	_titleBarElement: null,

	//Elemento DOM de la barra de estado
	_statusBarElement: null,

	//Referencia al WindowManager
	_windowManager: null,

	//Referencia a un HfosProcessContainer (o interface)
	_subprocess: null,

	/**
	 * Estado de la ventana
	 *
	 * @define {string}
	 */
	_status: 'normal',

	//Un key que le dirá a la aplicación como restaurarse
	_state: null,

	//Posición de la ventana en el stack del WindowManager
	_position: -1,

	//Indica si la ventana fue restaurada
	_wasRestored: false,

	//Parámetros con los que se cargo la URI actual
	_parameters: '',

	//Titulo de la ventana
	_title: '',

	//Referencia la función anónima del dragAttempt
	_handlerDragCache: null,

	//Offset del inicio del redimensionado de la ventana
	_resizeOffset: [],

	/**
	 * Constructor de HfosWindow
	 *
	 * @constructor
	 * @param {Object} windowManager
	 * @param {Object} options
	 */
	initialize: function(windowManager, options){

		try {

			var spaceElement = windowManager.getSpaceElement();
			if(spaceElement.selectOne('div#'+options.id)){
				windowManager.setActiveWindow(this, true);
				return false;
			};

			//Opciones por defecto
			options.width = parseInt(options.width, 10) || 900;
			options.height = parseInt(options.height, 10) || 560;

			var windowScroll = WindowUtilities.getWindowScroll(spaceElement);
	    	var pageSize = WindowUtilities.getPageSize(spaceElement);
	    	var left = (pageSize.windowWidth-options.width-windowScroll.left)/2;
	    	var top = (pageSize.windowHeight-options.height-windowScroll.top-35)/2;

			options.y = options.y || top;
			options.x = options.x || left;
			options.parameters = options.parameters || '';

			if(options.y<13){
				options.y = 13;
			} else {
				options.y+=windowManager.getOffsetTop();
			};
			if(options.x<10){
				options.x = 10;
			} else {
				options.x+=windowManager.getOffsetLeft();
			};

			if(top+options.height>(spaceElement.getHeight()-50)){
				options.height = spaceElement.getHeight()-60;
			}

			//Crear la ventana
			this._element = document.createElement('DIV');
			this._element.addClassName('window-main');
			this._element.setStyle({
				top: options.y+"px",
				left: options.x+"px",
				width: options.width+"px",
				height: options.height+"px"
			});

			this._element.id = options.id;
			this._element.update(this.getSkeleton());
			this._element.observe('click', windowManager.setActiveWindow.bind(windowManager, this, false));

			//Referencia al titulo
			this._titleElement = this._element.getElement('window-title-bar');
			if(typeof options.icon != "undefined"){
				this._titleElement.addClassName('window-icon');
				this._titleElement.setStyle({
					'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/hover/'+options.icon+')'
				});
				this._icon = options.icon;
			};
			this._titleElement.update(options.title);

			//Referencia a la barra de titulo
			this._titleBarElement = this._element.getElement('window-header-se');
			this._titleBarElement.observe('dblclick', this._maximizeWindow.bind(this));

			//Al colocar el mouse sobre la barra de titulo se hace arrastrable
			this._handlerDragCache = this._attemptToDrag.bind(this);
			this._titleBarElement.observe('mouseenter', this._handlerDragCache);
			this._title = options.title;

			//Crear una referencia a elementos de la ventana
			this._headerElement = $(this._element.getElement('window-header-se'));
			this._closeElement = $(this._element.getElement('window-close'));
			this._minimizeElement = $(this._element.getElement('window-minimize'));
			this._resizerElement = $(this._element.getElement('window-resizer'));
			this._contentElement = $(this._element.getElement('window-content'));

			//Colocar estado normal a la ventana (normal, minimized, maximized)
			this._status = 'normal';

			//Mantener una referencia al WindowManager
			this._windowManager = windowManager;

			//Obtener y aplicar late bindings
			var lateBindings = windowManager.getLateBindings(this._element.id);
			if(lateBindings!==null){
				for(var eventName in lateBindings){
					for(var i=0;i<lateBindings[eventName].length;i++){
						this.observe(eventName, lateBindings[eventName][i]);
					}
				}
			};

			//Agregar bindings de las opciones del constructor
			if(typeof options.bindings != "undefined"){
				for(var eventName in options.bindings){
					this.observe(eventName, options.bindings[eventName]);
				}
			};

			//Obtener contenido de la ventana
			if(typeof options.action != "undefined" || typeof options.externAction != "undefined"){
				this._element.hide();
				if(typeof options.action == "undefined"){
					var url = options.externAction;
					new HfosAjax.ApplicationRequest(url, {
						parameters: options.parameters,
						onSuccess: this._onReceiveContent.bind(this)
					});
				} else {
					var url = options.action;
					new HfosAjax.Request(url, {
						parameters: options.parameters,
						onSuccess: this._onReceiveContent.bind(this)
					});
				};
				spaceElement.appendChild(this._element);
			} else {
				if(typeof options.content != "undefined"){
					this._contentElement.update(options.content);
				};
				spaceElement.appendChild(this._element);
				if(this._element.visible()==true){
					if(typeof options.content != "undefined"){
						this._state = options.state;
						this._wasRestored = true;
						delete options.state;
						delete options.content;
						this.fire('afterRestore');
					} else {
						this.fire('afterCreate');
						this._storePosition();
					};
					this.fire('afterCreateOrRestore');
				};
			};

			//Posicionar elementos
			this._relocateElements();

			//Agregar handlers a los elementos
			this._addHandlers();

		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Recibe la respuesta del contenido inicial de la ventana
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_onReceiveContent: function(transport){
		this._contentElement.update(transport.responseText);
		this._element.show();
		this.fire('afterCreate');
		this.fire('afterCreateOrRestore');
		this._storePosition();
		this._onResize();
	},

	/**
	 * Almacena la posición de la ventana
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_storePosition: function(onSuccess){
		var storage = this._windowManager.getWorkspace().getApplication().getStorage();
		if(storage){
			if(typeof onSuccess == "function"){
				storage.save("Objects", {
					'id': this._element.id,
					'state': this._state,
					'position': this._position,
					'title': this._title,
					'icon': this._icon,
					'parameters': this._parameters,
					'y': parseInt(this._element.style.top, 10),
					'x': parseInt(this._element.style.left, 10),
					'width': this._element.getWidth(),
					'height': this._element.getHeight(),
					'content': this._contentElement.innerHTML
				}, onSuccess);
			};
		} else {
			if(typeof onSuccess == "function"){
				onSuccess();
			}
		}
	},

	/**
	 * Esta función siempre debe llamarse al cambiar el tamaño de una ventana
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_onResize: function(){
		var viewPortHeight = this._contentElement.getHeight();
		var contentHeight = this._contentElement.scrollHeight;
		if(contentHeight>viewPortHeight){
			this._contentElement.style.overflowY = 'scroll';
		} else {
			this._contentElement.style.overflowY = 'hidden';
		};
		this._storePosition();
	},

	/**
	 * Administra el comportamiento de las ventanas al ser arrastradas
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_dragBehavior: function(drag){
		if(drag){
			var left = drag.element.style.left;
			if(parseInt(left, 10)<0){
				this._splitLeftWindow();
			} else {
				var windowScroll = WindowUtilities.getWindowScroll(document.body);
				var pageSize = WindowUtilities.getPageSize(document.body);
				var width = drag.element.getWidth();
				if((parseInt(left, 10)+width)>(pageSize.windowWidth-windowScroll.left)){
					this._splitRightWindow();
				} else {
					var top = drag.element.style.top;
					if(parseInt(top, 10)<10){
						new Effect.Morph(drag.element, {
							duration: 0.2,
							style: {
								"top": "10px"
							},
							afterFinish: this._storePosition.bind(this)
						});
					} else {
						this._storePosition();
					}
				}
			}
		}
	},

	/**
	 * Si el mouse es ubicado sobre la barra de titulo al menos una vez
	 * agrega el handler de arrastrar
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_attemptToDrag: function(){
		new HfosDraggable(this._element, {
			handle: this._titleElement,
			starteffect: false,
			endeffect: false,
			onEnd: this._dragBehavior.bind(this)
		});
		if(this._handlerDragCache){
			this._titleBarElement.stopObserving('mouseenter', this._handlerDragCache);
		}
	},

	/**
	 * Al cambiar la posicion de la ventana es necesario reposicionar los elementos
	 *
	 * @this {HfosWindow}
	 * @private
	 */
	_relocateElements: function(){
		this._contentElement.setStyle({
			'height': (this._element.getHeight()-32)+'px'
		});
		if(this._statusBarElement!==null){
			var viewPortHeight = this._contentElement.getHeight();
			var contentHeight = this._contentElement.scrollHeight;
			if(contentHeight>viewPortHeight){
				this._statusBarElement.setStyle({
					'width': (this._element.getWidth()-25)+'px',
					'top': (this._element.getHeight()-32)+'px'
				});
			} else {
				this._statusBarElement.setStyle({
					'width': (this._element.getWidth()-10)+'px',
					'top': (this._element.getHeight()-32)+'px'
				});
			};
		};
	},

	/**
	 * Agrega handlers a los elementos de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_addHandlers: function(){
		this._closeElement.observe('click', this._closeWindow.bind(this));
		this._minimizeElement.observe('click', this._minimizeWindow.bind(this));
		return;
		/*var mouseDownEvent = this._startResize.bind(this);
		var mouseMoveEvent = this._updateResize.bind(this);
		var mouseUpEvent = this._stopResize.bind(this);
		var mouseLeaveEvent = this._leaveResize.bind(this);
		this._resizerElement.observe('mousedown', mouseDownEvent);
		this._resizerElement.observe('mousemove', mouseMoveEvent);
		this._resizerElement.observe('mouseup', mouseUpEvent);
		this._resizerElement.observe('mouseleave', mouseLeaveEvent);*/
	},

	/**
	 * Inicia el proceso de redimensionado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_startResize: function(event){
		if(Event.isLeftClick(event)){
			var pointer = [Event.pointerX(event), Event.pointerY(event)];
			var pos = this._element.cumulativeOffset();
			this._resizeOffset = [];
			this._resizeOffset[0] = pos.left;
			this._resizeOffset[1] = pos.top;
			this._resizeActive = true;
		}
	},

	/**
	 * Termina el proceso de redimensionado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_leaveResize: function(event){
		try {
			if(this._resizeActive==true){
				var pointer = [Event.pointerX(event), Event.pointerY(event)];
				var pos = this._resizerElement.cumulativeOffset();
				var isOut = pointer[0]<(pos.left-20);
				if(isOut){
					delete this._resizeOffset;
					this._resizeActive = false;
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Termina el proceso de redimensionado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_stopResize: function(){
		delete this._resizeOffset;
		this._resizeActive = false;
	},

	/**
	 * Actualiza las dimensiones de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_updateResize: function(event){
		if(this._resizeActive==true){
			var pointer = [Event.pointerX(event), Event.pointerY(event)];
			if(pointer[0]>this._resizeOffset[0]){
				this._element.setStyle({
					width: (pointer[0]-this._resizeOffset[0])+'px'
				});
			} else {
				this._element.setStyle({
					width: (pointer[0]+this._resizeOffset[0])+'px'
				});
			}
		}
	},

	/**
	 * Cierra una ventana externo
	 *
	 * @this {HfosWindow}
	 */
	close: function(force){
		if(typeof force == "undefined"){
			if(this.isObserved('beforeClose')){
				if(this.fire('beforeClose')===false){
					return false;
				} else {
					this._closeInternal();
				}
			} else {
				this._closeInternal();
			}
		} else {
			this._closeInternal();
		}
	},

	/**
	 * Interno para cerrar ventana
	 *
	 * @this {HfosWindow}
	 */
	_closeInternal: function(){
		var contentLength = this._contentElement.select('*').length;
		if(contentLength<750){
			if(this._windowManager.getNumberWindows()<3){
				new Hfos.getAnimation().scale(this._element, {
					to: 0.95,
					duration: 0.2,
					afterFinish: this._removeWindow.bind(this)
				});
			} else {
				this._removeWindow();
			}
		} else {
			this._removeWindow();
		};
		this.fire('afterClose')
	},

	/**
	 * Cierra una ventana (interno)
	 *
	 * @this {HfosWindow}
	 */
	_closeWindow: function(event){
		this.close();
		new Event.stop(event);
	},

	/**
	 * Quita la ventana
	 *
	 * @this {HfosWindow}
	 */
	_removeWindow: function(){
		var objectId = this._element.id;
		this._element.hide();
		if(this._element.parentNode!==null){
			this._element.erase();
		};
		var storage = this._windowManager.getWorkspace().getApplication().getStorage();
		if(storage!==null){
			storage.remove("Objects", objectId, this._windowManager.notify.bind(this._windowManager, 'closed', this));
		} else {
			this._windowManager.notify('closed', this);
		};
	},

	/**
	 * Convierte una ventana a Thumbnail
	 *
	 * @this {HfosWindow}
	 */
	showThumbnail: function(refElement){
		if(this._status=='minimized'){
			if(Prototype.Browser.Gecko){
				var position = refElement.cumulativeOffset();
				this._element.setOpacity(1.0);
				this._element.setStyle({
					MozTransformOrigin: 'top left',
					MozTransform: 'scale(0.25)',
					top: (position[1]-150)+'px',
					left: (position[0]+7)+'px',
					zIndex: 155
				});
			}
			this._element.show();
		}
	},

	/**
	 * Convierte una ventana a Thumbnail
	 *
	 * @this {HfosWindow}
	 */
	hideThumbnail: function(refElement){
		if(this._status=='minimized'){
			new Effect.Fade(this._element, {
				duration: 0.5,
				afterFinish: function(){
					if(this.getStatus()!='minimized'){
						this.show();
					}
				}.bind(this)
			});
		}
	},

	/**
	 * Muestra la ventana
	 *
	 * @this {HfosWindow}
	 */
	show: function(){
		this._element.setOpacity(1.0);
		this._element.style.MozTransform = 'scale(1.0)';
		this._element.show();
	},

	/**
	 * Cambia el tamaño de la ventana
	 *
	 * @this {HfosWindow}
	 */
	_resizeTo: function(width, height){
		this._element.setStyle({
			width: width,
			height: height
		});
		this._relocateElements();
	},

	/**
	 * Minimiza una ventana
	 *
	 * @this {HfosWindow}
	 */
	_minimizeWindow: function(event){
		if(this._windowManager.getNumberWindows()<3){
			new Effect.Parallel([
				new Effect.Move(this._element, { sync: true, y: 5, x: -5 }),
	  			new Effect.Fade(this._element, { sync: true, to: 0.5 })
			], {
	  			duration: 0.3,
	  			afterFinish: this._minimizeInternal.bind(this)
	  		});
		} else {
			this._minimizeInternal();
		};
		new Event.stop(event);
	},

	/**
	 *
	 * @this {HfosWindow}
	 */
	_minimizeInternal: function(){
		var dimensions = this._windowManager.getDimensionsFor('minimized', this);
		this._element.hide();
		this._element.setOpacity(1.0);
		this._resizeTo(dimensions.width, dimensions.height);
		this._windowManager.setFirstNormalToActive();
		this.fire('onInactive');
	},

	/**
	 * Restaura una ventana cuando está minimizada
	 *
	 * @this {HfosWindow}
	 */
	restoreWindow: function(){

		var dimensions = this._windowManager.getDimensionsFor('normal', this);
		this._element.setStyle({
			top: dimensions.top,
			left: dimensions.left,
			width: dimensions.width,
			height: dimensions.height
		});

		this.show();
		this._relocateElements();

		//Colocar ventana como activa en el stack
		this._windowManager.setActiveWindow(this);

	},

	/**
	 * Maximiza la ventana
	 *
	 * @this {HfosWindow}
	 */
	maximize: function(){
		this._maximizeWindow();
	},

	/**
	 * Maximiza la ventana (internal)
	 *
	 * @this {HfosWindow}
	 */
	_maximizeWindow: function(){
		var dimensions;
		if(this._status=='normal'){
			dimensions = this._windowManager.getDimensionsFor('maximized', this);
		} else {
			dimensions = this._windowManager.getDimensionsFor('normal', this);
		};
		new Hfos.getAnimation().morph(this._element, {
			duration: 0.2,
			style: {
				top: dimensions.top,
				left: dimensions.left,
				width: dimensions.width,
				height: dimensions.height
			},
			afterFinish: function(){
				this._relocateElements();
				this._onResize();
				this._storePosition();
			}.bind(this)
		});
	},

	/**
	 * Ajusta el tamaño de la ventana a la mitad del tamaño de la pantalla hacia la izq
	 * cuando se supera el limite izq de la pantalla
	 *
	 * @this {HfosWindow}
	 */
	_splitLeftWindow: function(){
		var spaceElement = this._windowManager.getSpaceElement();
		var windowScroll = WindowUtilities.getWindowScroll(spaceElement);
		var pageSize = WindowUtilities.getPageSize(spaceElement);
		var width = (parseInt((pageSize.windowWidth-windowScroll.left)/2, 10)-15)+"px";
		var height = (pageSize.windowHeight-windowScroll.top-50)+"px";
		new Effect.Morph(this._element, {
			duration: 0.2,
			style: {
				left: "0px",
				top: "10px",
				width: width,
				height: height
			},
			afterFinish: function(){
				this._relocateElements();
				this._onResize();
			}.bind(this)
		});
	},

	/**
	 * Ajusta el tamaño de la ventana a la mitad del tamaño de la pantalla hacia la derecha
	 * cuando se supera el limite derecho de la pantalla
	 *
	 * @this {HfosWindow}
	 */
	_splitRightWindow: function(){
		var spaceElement = this._windowManager.getSpaceElement();
		var windowScroll = WindowUtilities.getWindowScroll(spaceElement);
		var pageSize = WindowUtilities.getPageSize(spaceElement);
		var width = (parseInt((pageSize.windowWidth-windowScroll.left)/2, 10)-10)+"px";
		var height = (pageSize.windowHeight-windowScroll.top-50)+"px";
		new Effect.Morph(this._element, {
			duration: 0.2,
			style: {
				left: width,
				top: "10px",
				width: width,
				height: height
			},
			afterFinish: function(){
				this._relocateElements();
				this._onResize();
			}.bind(this)
		});
	},

	/**
	 *
	 * @this {HfosWindow}
	 */
	skewLeft: function(){
		this.setStyle("-moz-transform: scale(0.7) rotate(5deg)");
	},

	/**
	 * Carga el contenido de la ventana usando AJAX
	 *
	 * @this {HfosWindow}
	 */
	go: function(url, options){
		if(typeof options == "undefined"){
			options = {};
		};
		if(options.onComplete == "undefined"){
			options.onComplete = function(){
				this.notify('contentChanged');
			}.bind(this)
		} else {
			options.onComplete = function(onComplete){
				if(typeof onComplete == "function"){
					onComplete();
				};
				this.notify('contentChanged');
			}.bind(this, options.onComplete)
		}
		if(typeof options.parameters != "undefined"){
			this._parameters = options.parameters;
		};
		return this._contentElement.load(url, options);
	},

	/**
	 * Establece un indicador que permita restaurar los callbacks al restaurar la ventana
	 *
	 * @this {HfosWindow}
	 */
	setState: function(state){
		this._state = state;
		this._storePosition();
	},

	/**
	 * Obtiene el indicador de restauración
	 *
	 * @this {HfosWindow}
	 */
	getState: function(){
		return this._state;
	},

	/**
	 * Indica si la ventana fue restaurada
	 *
	 * @this {HfosWindow}
	 */
	wasRestored: function(){
		return this._wasRestored;
	},

	/**
	 * Sube el scroll hasta lo más alto del contenido de la ventana
	 *
	 * @this {HfosWindow}
	 */
	scrollToTop: function(){
		this._contentElement.scrollTop = 0;
	},

	/**
	 * Baja el scroll hasta lo más bajo del contenido de la ventana
	 *
	 * @this {HfosWindow}
	 */
	scrollToBottom: function(){
		this._contentElement.scrollTop = this._contentElement.scrollHeight;
	},

	/**
	 * Obtiene el ancho de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getWidth: function(){
		return this._element.getWidth();
	},

	/**
	 * Obtiene el alto de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getHeight: function(){
		return this._element.getHeight();
	},

	/**
	 * Notifica sobre un evento ocurrido en la ventana
	 *
	 * @this {HfosWindow}
	 */
	notify: function(eventName){
		switch(eventName){
			case 'contentChanged':
				this._onResize();
				break;
		}
		this.fire(eventName);
	},

	/**
	 * Notifica que el contenido de la ventana fue cambiado
	 *
	 * @this {HfosWindow}
	 */
	notifyContentChange: function(){
		this.notify('contentChanged');
	},

	/**
	 * Indica si un determinado evento está siendo observado
	 *
	 * @this {HfosWindow}
	 */
	isObserved: function(eventName){
		return !Object.isUndefined(this['_'+eventName]);
	},

	/**
	 * Agrega un proceso a un determinado evento
	 *
	 * @this {HfosWindow}
	 */
	observe: function(eventName, procedure){
		if(Object.isUndefined(this['_'+eventName])){
			this['_'+eventName] = [];
		};
		this['_'+eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento de la ventana
	 *
	 * @this {HfosWindow}
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
	 * Muestra la barra de estado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	showStatusBar: function(message){
		if(this._statusBarElement===null){
			this._statusBarElement = document.createElement('DIV');
			this._statusBarElement.addClassName('window-status-bar');
			this._element.appendChild(this._statusBarElement);
		};
		this._statusBarElement.update(message);
		this._statusBarElement.show();
		this._relocateElements();
	},

	/**
	 * Oculta la barra de estado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	hideStatusBar: function(){
		if(this._statusBarElement!==null){
			this._statusBarElement.hide();
		}
	},

	/**
	 * Devuelve el elemento de la barra de estado
	 *
	 * @this {HfosWindow}
	 * @private
	 * @return {Object}
	 */
	getStatusBar: function(){
		return this._statusBarElement;
	},

	/**
	 * Obtiene el esqueleto vacio para crear una ventana
	 *
	 * @this {HfosWindow}
	 * @private
	 * @return {string}
	 */
	getSkeleton: function(){
		var html ='<div class="window-header-se"><table cellspacing="0" cellpadding="0" width="100%"><tr><td width="95%"><div class="window-title-bar"></div></td>';
		html+='<td align="right"><div class="window-header-buttons"><table cellspacing="0" cellpadding="0"><tr><td><div class="window-minimize">-</div></td>';
		html+='</td><td><div class="window-close"></div></td></tr></table></td></tr></table></div>';
		html+='<div class="window-content"></div><div class="window-resizer"></div>';
		return html;
	},

	/**
	 * Indica si la ventana está activa
	 *
	 * @this {HfosWindow}
	 * @private
	 * @return {boolean}
	 */
	isActive: function(){
		return this._element.style.zIndex==150;
	},

	/**
	 * Coloca la ventana con apariencia activa
	 *
	 * @this {HfosWindow}
	 * @public
	 * @param {number} position
	 */
	setActive: function(position){
		if(this.isActive()==false){
			this._headerElement.removeClassName('window-header-se-un');
			this._contentElement.removeClassName('window-content-un');
			this._element.setOpacity(1.0);
			this._element.setStyle({
				'zIndex': 150
			});
		};
		this._position = position;
	},

	/**
	 * Coloca la ventana con apariencia inactiva
	 *
	 * @this {HfosWindow}
	 * @param {number} position
	 * @public
	 */
	setInactive: function(position){
		if(this.isActive()==true){
			this._headerElement.addClassName('window-header-se-un');
			this._contentElement.addClassName('window-content-un');
			this._element.setOpacity(0.95);
			this._element.setStyle({
				'zIndex': 100
			});
			this.fire('onInactive');
		};
		this._position = position;
	},

	/**
	 * Envia el evento keyup a los observers
	 *
	 * @this {HfosWindow}
	 */
	sendKeyEvent: function(event){
		return this.fire('onKeyPress', event);
	},

	/**
	 * Obtiene las dimensiones de la ventana
	 *
	 * @this {HfosWindow}
	 * @public
	 * @return {Object}
	 */
	getDimensions: function(){
		return {
			'top': this._element.style.top,
			'left': this._element.style.left,
			'width': this._element.style.width,
			'height': this._element.style.height
		};
	},

	/**
	 * Obtiene la coordenada "y" de la ventana en el espacio de trabajo
	 *
	 * @this {HfosWindow}
	 * @public
	 * @return {number}
	 */
	getTop: function(){
		return this._element.offsetTop;
	},

	/**
	 * Obtiene la coordenada "x" de la ventana en el espacio de trabajo
	 *
	 * @this {HfosWindow}
	 * @public
	 * @return {number}
	 */
	getLeft: function(){
		return this._element.offsetLeft;
	},

	/**
	 * Devuelve un elemento dentro del contenido de la ventana usando un selector
	 *
	 * @this {HfosWindow}
	 */
	getElement: function(selector){
		return this._contentElement.getElement(selector);
	},

	/**
	 * Devuelve el elemento DOM que contiene todos los subelementos de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getWindowElement: function(){
		return this._element;
	},

	/**
	 * Devuelve el elemento DOM espacio de trabajo del WindowManager asociado a la ventana
	 *
	 * @this {HfosWindow}
	 */
	getSpaceElement: function(){
		return this._windowManager.getSpaceElement();
	},

	/**
	 * Devuelve el elemento DOM que tiene el contenido de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getContentElement: function(){
		return this._contentElement;
	},

	/**
	 * Devuelve un conjunto de elementos dentro del contenido de la ventana
	 *
	 * @this {HfosWindow}
	 */
	select: function(selector){
		return this._contentElement.select(selector);
	},

	/**
	 * Devuelve un elemento de acuero a un selector en el contenido de la ventana
	 *
	 * @this {HfosWindow}
	 */
	selectOne: function(selector){
		return this._contentElement.selectOne(selector);
	},

	/**
	 * Devuelve el id de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getId: function(){
		return this._element.id;
	},

	/**
	 * Devuelve el estado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getStatus: function(){
		return this._status;
	},

	/**
	 * Devuelve el titulo de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getTitle: function(){
		return this._title;
	},

	/**
	 * Devuelve el windowManager de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getWindowManager: function(){
		return this._windowManager;
	},

	/**
	 * Establece el estado de la ventana
	 *
	 * @this {HfosWindow}
	 */
	setStatus: function(status){
		this._status = status;
	},

	/**
	 * Establece el subproceso asociado a la ventana
	 *
	 * @this {HfosWindow}
	 */
	setSubprocess: function(subprocess){
		this._subprocess = subprocess;
	},

	/**
	 * Obtiene el subproceso que corre dentro de la ventana
	 *
	 * @this {HfosWindow}
	 */
	getSubprocess: function(){
		return this._subprocess;
	},

	/**
	 * Hiberna una ventana
	 *
	 * @this {HfosWindow}
	 */
	hibernate: function(){
		this._storePosition(this.setStatus.bind(this, 'hibernate'));
	}

});
