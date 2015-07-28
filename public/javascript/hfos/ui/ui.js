
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

//UI
var HfosUI = Class.create({

	_shadow: null,

	_blockInput: null,

	_numberShadows: 0,

	/**
	 * Muestra una capa oscura para hacer un mensaje tipo modal
	 *
	 * @this {HfosUI}
	 */
	showScreenShadow: function(opacity){
		if(this._shadow==null){
			this._shadow = document.createElement('DIV');
			this._shadow.addClassName('screen-shadow');
			this._shadow.observe('click', function(event){
				new Event.stop(event);
			});
			document.body.appendChild(this._shadow);
		};
		if(typeof opacity != "undefined"){
			this._shadow.setOpacity(opacity);
		};
		this._shadow.show();
		this._numberShadows++;
	},

	/**
	 * Elimina la capa oscura para mensajes modales
	 *
	 * @this {HfosUI}
	 */
	hideScreenShadow: function(){
		this._numberShadows--;
		if(this._shadow!==null){
			if(this._numberShadows<=0){
				this._shadow.hide();
				this._numberShadows = 0;
			}
		}
	},

	/**
	 * Bloquea la entrada agregando una capa a la pantalla
	 *
	 * @this {HfosUI}
	 */
	blockInput: function(){
		if(this._blockInput==null){
			this._blockInput = document.createElement('DIV');
			this._blockInput.addClassName('block-input');
			document.body.appendChild(this._blockInput);
		};
		this._blockInput.show();
	},

	/**
	 * Quita el bloqueo de entrada
	 *
	 * @this {HfosUI}
	 */
	unblockInput: function(){
		if(this._blockInput!==null){
			this._blockInput.hide();
		}
	},

	/**
	 * Cambia el fondo de escritorio por una imagen
	 *
	 * @this {HfosUI}
	 */
	setBackground: function(image){
		document.body.style.background = 'url('+$Kumbia.path+'img/backoffice/wall/'+image+')';
	},

	/**
	 * Centra un objeto con posición absoluta en el centro de la pantalla
	 *
	 * @this {HfosUI}
	 */
	centerAtScreen: function(element){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);
		var left = (pageSize.windowWidth-element.getWidth()-windowScroll.left)/2;
		var top = (pageSize.windowHeight-element.getHeight()-windowScroll.top)/3;
		element.style.left = left+'px';
		element.style.top  = top+'px';
	}

});
