
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
 * HfosCommunications
 *
 * Clase que permite obtener notificaciones y mensajes del servidor periodicamente
 */
var HfosCommunications = Class.create({

	_enabled: null,

	_communicationInterval: null,

	initialize: function()
	{

	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	listen: function()
	{
		var time = 20000;
		if (window.location.host.include('bhteck.com')) {
			time *= 15;
		} else {
			if (window.location.host.include('wepax.com.co')) {
				time *= 5;
			} else {
				if (window.location.host.include('localhost')) {
					time *= 10;
				}
			}
		};
		this._communicationInterval = window.setInterval(this._pollMessages.bind(this), time);
	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	_pollMessages: function()
	{
		if (navigator.onLine) {
			new HfosAjax.JsonApplicationRequest('identity/workspace/getMessages', {
				onSuccess: this._onMessage.bind(this)
			});
		};
	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	_onMessage: function(response)
	{
		if (response.status == 'OK') {
			for (var i=0; i < response.messages.length; i++) {
				this.fire(response.messages[i]);
			}
		}
	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	stop: function()
	{
		window.clearInterval(this._communicationInterval);
	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	observe: function(eventType, procedure)
	{
		if (Object.isUndefined(this['_' + eventType])) {
			this['_' + eventType] = [];
		};
		this['_' + eventType].push(procedure);
	},

	/**
	 *
   	 * @this {HfosCommunications}
	 */
	fire: function(message){
		try {
			var eventType = message.type;
			if(!Object.isUndefined(this['_'+eventType])){
				for(var i=0;i<this['_'+eventType].length;i++){
					if(this['_'+eventType][i](message)===false){
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
	}

})