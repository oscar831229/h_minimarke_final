
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
 * Enum para tipos de mensajes
 *
 * @enum {number}
 */
var HfosMessagesTypes = {

	M_NOTICE: 1,
	M_ERROR: 2,
	M_SUCCESS: 3

};

/**
 * HfosMessages
 *
 * Permite generar mensajes contextuales en un contenedor listo para ello
 */
var HfosMessages = Class.create({

	_container: null,

	_default: null,

	_notify: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this._container = container;
		this._default = this._container.getElement('messages').innerHTML;
	},

	/**
	 * Agrega un mensaje por su tipo
	 *
	 * @this {HfosMessages}
	 */
	addMessage: function(message, type, highlight){
		if(typeof highlight == "undefined"){
			highlight = false;
		};
		var className = '';
		if(type==HfosMessagesTypes.M_NOTICE){
			className = 'notice';
		} else {
			if(type==HfosMessagesTypes.M_SUCCESS){
				className = 'success';
			} else {
				if(type==HfosMessagesTypes.M_ERROR){
					className = 'error';
				}
			}
		};
		var messagesElement = this._container.getElement('messages');
		var alreadyError = messagesElement.getElement('error');
		var alreadyNotice = messagesElement.getElement('notice');
		messagesElement.show();
		messagesElement.update('<div class="'+className+'">'+message+'</div>');

		var contentElement = this._container.getContentElement();
		if(contentElement.scrollTop<(messagesElement.offsetTop)){
			if(Hfos.getMode()!='test'){
				if(type==HfosMessagesTypes.M_ERROR||highlight==true){
					if(!alreadyError){
						new Effect.Highlight(messagesElement.descendants()[0], {
							startcolor: '#DDF4FF',
							keepBackgroundImage: true,
							duration: 1.5
						});
					}
				} else {
					if(type==HfosMessagesTypes.M_NOTICE||highlight==true){
						if(!alreadyError){
							new Effect.Highlight(messagesElement.descendants()[0], {
								startcolor: '#F0F6FF',
								keepBackgroundImage: true,
								duration: 1.5
							});
						}
					}
				}
			};
		} else {
			if(this._notify==null){
				HfosNotifierServer.get(function(message, notify){
					this._notify = notify;
					this._notify.update(message);
					this._notify.show(false);
				}.bind(this, message));
			} else {
				this._notify.update(message);
			}
		}
	},

	/**
	 * Actualiza la barra de mensajes con el mensaje predeterminado
	 *
	 * @this {HfosMessages}
	 */
	setDefault: function(){
		var messagesElement = this._container.getElement('messages');
		messagesElement.update(this._default);
	},

	/**
	 * Coloca el mensaje actual en la barra de mensajes como el predeterminado
	 *
	 * @this {HfosMessages}
	 */
	setActiveToDefault: function(){
		this._default = this._container.getElement('messages').innerHTML;
		this.setDefault();
	},

	/**
	 * Muestra un mensaje de exito en la barra de mensajes
	 *
	 * @this {HfosMessages}
	 */
	success: function(message, highlight){
		return this.addMessage(message, HfosMessagesTypes.M_SUCCESS, highlight);
	},

	/**
	 * Muestra un mensaje de información en la barra de mensajes
	 *
	 * @this {HfosMessages}
	 */
	notice: function(message, highlight){
		return this.addMessage(message, HfosMessagesTypes.M_NOTICE, highlight);
	},

	/**
	 * Muestra un mensaje de error en la barra de mensajes
	 *
	 * @this {HfosMessages}
	 */
	error: function(message, highlight){
		return this.addMessage(message, HfosMessagesTypes.M_ERROR, highlight);
	},

	/**
	 * Limpia la barra de mensajes
	 *
	 * @this {HfosMessages}
	 */
	clear: function(){
		this._container.getElement('messages').update('');
	},

	/**
	 * Oculta la barra de mensajes
	 *
	 * @this {HfosMessages}
	 */
	hide: function(){
		this._container.getElement('messages').hide();
	}

});
