
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
 * HfosNotifierServer
 *
 * Servidor de notificaciones
 */
var HfosNotifierServer = {

	_notifications: {},

	_number: 0,

	//Indica si el servidor de notificaciones está bloquedo
	_lock: false,

	/**
	 * Genera una notificación sin obtener una referencia a la misma
	 */
	add: function(message, options){
		if(HfosNotifierServer._lock==false){
			HfosNotifierServer._lock = true;
			var notify = new HfosNotifier(message);
			HfosNotifierServer._notifications[notify.getId()] = notify;
			HfosNotifierServer._number++;
			notify.show();
			HfosNotifierServer._lock = false;
		} else {
			window.setTimeout(HfosNotifierServer.add.bind(window, options), 100);
		}
	},

	/**
	 * Obtiene asincrónicamente una notificación
	 */
	get: function(procedure){
		if(HfosNotifierServer._lock==false){
			HfosNotifierServer._lock = true;
			var notify = new HfosNotifier();
			HfosNotifierServer._notifications[notify.getId()] = notify;
			HfosNotifierServer._number++;
			HfosNotifierServer._lock = false;
			procedure(notify);
		} else {
			window.setTimeout(HfosNotifierServer.add.bind(window, '', procedure), 100);
		}
	},

	/**
	 * Elimina una notificación del servidor
	 */
	drop: function(notify){
		if(typeof HfosNotifierServer._notifications[notify.getId()] != "undefined"){
			delete HfosNotifierServer._notifications[notify.getId()];
			HfosNotifierServer._number--;
		}
	},

	/**
	 * Obtiene una posición libre para mostrar la notificación
	 */
	getPosition: function(){
		var position = 1;
		for(var notifyId in HfosNotifierServer._notifications){
			if(HfosNotifierServer._notifications[notifyId].getState()=='visible'){
				position++;
			}
		};
		return position;
	}

};

/**
 * HfosNotifier
 *
 * Clase que instancia una notificación HfosNotifier
 */
var HfosNotifier = Class.create({

	_element: null,

	_id: null,

	_state: null,

	/**
	 * @constructor
	 */
	initialize: function(message, options){
		this._state = 'hidden';
		if(typeof message != "undefined"){
			this.update(message, options);
			this.show();
		}
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	update: function(message, options){
		try {
			if(this._element==null){
				this._element = document.createElement('DIV');
				document.body.appendChild(this._element);
			};
			if(typeof options != "undefined"){
				if(typeof options.classNames == "undefined"){
					this._element.className = 'notify';
				} else {
					this._element.className = 'notify '+options.classNames;
				};
			} else {
				this._element.className = 'notify';
			};
			var position = HfosNotifierServer.getPosition();
			this._element.update("<table><tr><td></td><td>"+message+"</td></tr></table>");
			this._element.setStyle({
				'bottom': (10+position*50)-5
			});
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	show: function(autoDestroy){
		if(this._element!=null){

			/*if(typeof navigator.mozNotification != "undefined"){
				navigator.mozNotification.createNotification(this._element.textContent).show();
			};*/

			this._element.show();
			new Effect.Move(this._element, {
				duration: 0.5,
				y: -5
			});
		};
		this._state = 'visible';
		this.hide(5, autoDestroy);
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	getId: function(){
		if(this._id===null){
			this._id = (new Date()).getTime();
		};
		return this._id;
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	hide: function(delay, autoDestroy){
		window.setTimeout(function(autoDestroy){
			new Effect.Fade(this._element, {
				afterFinish: function(autoDestroy){
					this._state = 'hidden';
					if(typeof autoDestroy == "undefined"){
						this.destroy();
					}
				}.bind(this, autoDestroy)
			});
		}.bind(this, autoDestroy), delay*1000);
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	getState: function(){
		return this._state;
	},

	/**
	 *
	 * @this {HfosNotifier}
	 */
	destroy: function(){
		this._element.erase();
		HfosNotifierServer.drop(this);
	}

});