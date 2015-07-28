
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
 * HfosBgProcess
 *
 * Ejecuta tareas largas en segundo plano usando Threads (Workers) para no bloquear la aplicación
 *
 */
var HfosBgProcess = Class.create({

	_worker: null,

	_options: {},

	/**
	 * @constructor
	 */
	initialize: function(){
		this._worker = new Worker($Kumbia.path+'javascript/hfos/kernel/smp/worker.js');
		this._worker.onmessage = this._onMessage.bind(this);
		this._worker.onerror = HfosException.show;
		this._postMessage({
			action: 'initialize',
			path: $Kumbia.path
		});
	},

	/**
	 *
	 * @this {HfosBgProcess}
	 */
	request: function(url, options){
		HfosBgProcess._options = options;
		this._postMessage({
			'action': 'request',
			'url': url,
			'options': options
		});
	},

	/**
	 *
	 * @this {HfosBgProcess}
	 */
	_postMessage: function(message){
		var message = Json.encode(message);
		this._worker.postMessage(message);
	},

	/**
	 *
	 * @this {HfosBgProcess}
	 */
	_onMessage: function(event){
		var message = Json.decode(event.data);
		switch(message.action){
			case 'readyState':
				HfosHttpReadyState.onReadyStateChange(message, HfosBgProcess._options);
				if(message.readyState==4){
					this._worker.terminate();
					delete this._worker;
				}
				break;
			case 'debug':
				if(typeof window.console != "undefined"){
					window.console.log(message.value);
				};
				break;
		};
	}

});
