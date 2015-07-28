
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
 * HfosProcessContainer
 *
 * Ofrece un contenedor de procesos para aquellos modulos que no usan HyperForm
 */
var HfosProcessContainer = Class.create({

	/**
	 * Contenedor donde se ejecuta el proceso
	 */
	_container: null,

	/**
	 * Indica si se debe omitir la señal de cerrar la ventana
	 */
	_ignoreTermSignal: false,

	/**
	 * Referencia al HfosMessages asociado al proceso
	 */
	_messages: null,

	/**
	 * Referencia al HfosWindow donde se ejecuta el proceso
	 */
	_window: null,

	/**
	 * Establece el contenedor donde se ejecuta el proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	setContainer: function(container){
		this._container = container;
		if(typeof this._container.tagName != "undefined"){
			this._setWindow(container);
		} else {
			this._window = container;
		};
		if(this._container!=this){
			this._container.observe('beforeClose', this._onCloseProcess.bind(this));
		}
	},

	/**
	 * Obtiene el contenedor donde se ejecuta el proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	getContainer: function(){
		return this._container;
	},

	/**
	 * Establece la condición actual del contenedor
	 *
	 * @this {HfosProcessContainer}
	 */
	setState: function(state){
		if(typeof this._container.setState != "undefined"){
			return this._container.setState(state);
		}
	},

	/**
	 * Devuelve la condición actual del contenedor
	 *
	 * @this {HfosProcessContainer}
	 */
	getState: function(){
		if(typeof this._container.getState != "undefined"){
			return this._container.getState();
		} else {
			return null;
		}
	},

	/**
	 * Cambia la ubicación de la ventana
	 *
	 * @this {HfosProcessContainer}
	 */
	go: function(url, options){
		if(typeof this._container.go != "undefined"){
			return this._container.go(url, options);
		} else {
			return this._container.load(url, options);
		}
	},

	/**
	 * Oculta la barra de estado
	 *
	 * @this {HfosProcessContainer}
	 */
	hideStatusBar: function(){
		if(typeof this._container.hideStatusBar != "undefined"){
			this._container.hideStatusBar();
		} else {
			var statusBar = $('processStatusBar');
			if(statusBar){
				statusBar.hide();
			}
		}
	},

	/**
	 * Muestra la barra de estado con un un mensaje
	 *
	 * @this {HfosProcessContainer}
	 */
	showStatusBar: function(message){
		if(typeof this._container.showStatusBar != "undefined"){
			this._container.showStatusBar(message);
		} else {
			var statusBar = $('processStatusBar');
			if(!statusBar){
				statusBar = document.createElement('DIV');
				statusBar.setAttribute('id', 'processStatusBar');
				document.body.appendChild(statusBar);
			};
			statusBar.update(message);
		}
	},

	/**
	 * Obtiene un elemento en la barra de mensajes usando un selector de clase
	 *
	 * @this {HfosProcessContainer}
	 */
	getStatusBarElement: function(selector){
		if(typeof this._container.showStatusBar != "undefined"){
			return this._container.getStatusBar().getElement(selector);
		} else {
			var statusBar = $('processStatusBar');
			if(statusBar){
				return statusBar.getElement(selector);
			} else {
				return null;
			}
		}
	},

	/**
	 * Mueve el scroll a lo más alto del contenedor
	 *
	 * @this {HfosProcessContainer}
	 */
	scrollToTop: function(){
		if(typeof this._container.scrollToTop != "undefined"){
			this._container.scrollToTop();
		} else {
			document.body.scrollTop = 0;
		}
	},

	/**
	 * Mueve el scroll a lo más bajo del contenedor
	 *
	 * @this {HfosProcessContainer}
	 */
	scrollToBottom: function(){
		if(typeof this._container.scrollToTop != "undefined"){
			this._container.scrollToBottom();
		} else {
			document.body.scrollTop = document.body.offsetHeight;
		}
	},

	/**
	 *
	 * @this {HfosProcessContainer}
	 */
	select: function(selector){
		return this._container.select(selector);
	},

	/**
	 *
	 * @this {HfosProcessContainer}
	 */
	selectOne: function(elementID){
		return this._container.selectOne(elementID);
	},

	/**
	 * Obtiene el identificador único del proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	getId: function(){
		return this._container.id;
	},

	/**
	 *
	 * @this {HfosProcessContainer}
	 */
	getElement: function(className){
		return this._container.getElement(className);
	},

	/**
	 *
	 * @this {HfosProcessContainer}
	 */
	getContentElement: function(){
		if(this._container !== null){
			if(typeof this._container.getContentElement != "undefined"){
				return this._container.getContentElement();
			} else {
				return document.body;
			}
		} else {
			return document.body;
		}
	},

	/**
	 * Devuelve el espacio DOM asociado al proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	getSpaceElement: function(){
		if(typeof this._container.getSpaceElement != "undefined"){
			return this._container.getSpaceElement();
		} else {
			return document.body;
		}
	},

	/**
	 * Devuelve el objeto de mensajes del proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	getMessages: function(){
		if(this._messages==null){
			this._messages = new HfosMessages(this);
		};
		return this._messages;
	},

	/**
	 * Observa un evento en el contenedor
	 *
	 * @this {HfosProcessContainer}
	 */
	observe: function(eventName, procedure){
		if(this._window!==null){
			this._window.observe(eventName, procedure);
		}
	},

	/**
	 * Busca la ventana donde se está ejecutando el proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	_setWindow: function(container){
		var ascestors = container.ancestors();
		for(var i=0;i<ascestors.length;i++){
			if(ascestors[i].hasClassName('window-main')){
				this._window = Hfos.getApplication().getWorkspace().getWindowManager().getWindow(ascestors[i].id);
				break;
			}
		}
	},

	/**
	 * Notifica la ventana sobre un cambio en el contenido de la misma
	 *
	 * @this {HfosProcessContainer}
	 */
	_notifyContentChange: function(){
		if(this._window!==null){
			this._window.notify('contentChanged')
		}
	},

	/**
	 * Establece si se debe ignorar el cerrado de la ventana
	 *
	 * @this {HfosProcessContainer}
	 */
	setIgnoreTermSignal: function(ignoreTermSignal){
		this._ignoreTermSignal = ignoreTermSignal;
	},

	/**
	 * Evento al cerrar el proceso
	 *
	 * @this {HfosProcessContainer}
	 */
	_onCloseProcess: function(){
		if(this._ignoreTermSignal==true){
			new HfosModal.alert({
				title: this._container.getTitle(),
				message: 'No se puede cerrar la ventana porque está ocupada realizando un proceso'
			});
			return false;
		} else {
			return true;
		}
	}

});