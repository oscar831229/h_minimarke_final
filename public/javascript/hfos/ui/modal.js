
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
 * HfosModal
 *
 * Administrador de cuadros de dialogo en pantalla
 */
var HfosModal = {

	//Contador del z-Index de los cuadros
	zIndex: 5002,

	//Dialogos creados
	_modals: [],

	/**
	 * Quita un dialogo en especial
	 */
	remove: function(hfosModal){
		for(var i=HfosModal._modals.length-1;i>=0;i--){
			if(typeof HfosModal._modals[i] != "undefined"){
				if(HfosModal._modals[i]==hfosModal){
					HfosModal._modals[i].getElement().erase();
					delete HfosModal._modals[i];
					break;
				}
			}
		};
		HfosModal._onRemove();
	},

	/**
	 * Quita el top-dialog de la pantalla y del administrador
	 */
	_remove: function(){
		if(HfosModal.getLength()>0){
			for(var i=HfosModal._modals.length-1;i>=0;i--){
				if(typeof HfosModal._modals[i] != "undefined"){
					HfosModal._modals[i].getElement().erase();
					delete HfosModal._modals[i];
					break;
				}
			};
			HfosModal._onRemove();
		};
	},

	/**
	 * Finaliza el proceso de eliminación de un dialogo
	 */
	_onRemove: function(){
		if(HfosModal.getLength()==0){
			Hfos.getUI().hideScreenShadow();
			HfosShortcuts.enable();
			if(HfosModal._onWindowKey){
				new Event.stopObserving(window, 'keydown', HfosModal._onWindowKey);
			};
			/*if(this._notUserResponse!=null){
				window.clearInterval(this._notUserResponse);
				this._notUserResponse = null;
			};*/
		}
	},

	/**
	 * Handler global para cuadros de dialogo. Recibe las teclas y las enruta al top-dialog
	 */
	_onWindowKey: function(event){
		if(event.keyCode==Event.KEY_RETURN){
			HfosModal.getTopDialog().getFocusButton().click();
		} else {
			if(event.keyCode==Event.KEY_ESC){
				HfosModal._remove();
			} else {
				if(event.keyCode==Event.KEY_LEFT){
					HfosModal.getTopDialog().moveLeft();
				} else {
					if(event.keyCode==Event.KEY_RIGHT){
						HfosModal.getTopDialog().moveRight();
					}
				}
			}
		};
		new Event.stop(event);
		new Event.cancelBubble(event);
		return false;
	},

	/**
	 * Devuelve el último dialogo creado
	 */
	getTopDialog: function(){
		for(var i=HfosModal._modals.length-1;i>=0;i--){
			if(typeof HfosModal._modals[i] != "undefined"){
				return HfosModal._modals[i];
			}
		};
	},

	/**
	 * Devuelve el número de dialogos activos en pantalla
	 */
	getLength: function(){
		var length = 0;
		for(var i=HfosModal._modals.length-1;i>=0;i--){
			if(typeof HfosModal._modals[i] != "undefined"){
				length++;
			}
		};
		return length;
	},

	/**
	 * Agrega un dialogo al administrador de dialogos
	 */
	add: function(hfosModal){
		HfosModal._modals.push(hfosModal);
		if(HfosModal.getLength()==1){
			HfosShortcuts.disable();
			Hfos.getUI().showScreenShadow();
			new Event.observe(window, 'keydown', HfosModal._onWindowKey);
		}
	}

};

/**
 * Tipos de cuadro de dialogo
 * @enum {number}
 */
HfosModal.Types = {

	ALERT: 1,
	CONFIRM: 2,
	ERROR: 3,
	EVENT: 4,
	CUSTOM: 5

};

/**
 * HfosModal.Dialog
 *
 * Clase para crear cuadros de dialogo
 */
HfosModal.Dialog = Class.create({

	//Opciones del cuadro de dialogo
	_options: {},

	//Elemento DOM que contiene el cuadro
	_element: null,

	//Referencia al botón de aceptar
	_acceptButton: null,

	//Referencias a todos los botones del cuadro de dialogo
	_buttons: null,

	//Indica el botón que tiene el foco
	_focusIndex: 0,

	/**
	 * @constructor
	 */
	initialize: function(options, type){
    	var element = $(document.createElement('DIV'));
    	element.addClassName('modalLayout');
    	element.id = 'modal-'+HfosModal.zIndex;
    	element.style.zIndex = HfosModal.zIndex;
    	var html = "<table width='100%'>"+
    	"<tr><td id='imgCon'><img src='"+$Kumbia.path+"img/backoffice/blank.gif' class='"
    	if(type==HfosModal.Types.ALERT){
    		html+='alert48';
    	} else {
    		if(type==HfosModal.Types.CONFIRM){
    			html+='info48';
    		} else {
    			if(type==HfosModal.Types.EVENT){
    				html+='event48';
    			} else {
    				if(type==HfosModal.Types.CUSTOM){
    					if(typeof options.icon != "undefined"){
    						html+=options.icon;
    					}
    				}
    			}
    		}
    	};
    	html+="'/></td><td>"+
    	"<h1>"+options.title+"</h1><h2>"+options.message+"</h2></td>"+
    	"</tr><tr><td align='right' colspan='2'>"
    	if(type==HfosModal.Types.ALERT){
    		html+="<div class='buttonContainer'><input type='button' value='Aceptar' class='controlButton controlButtonShadow' id='acceptButton'></div>";
    	} else {
	    	if(type==HfosModal.Types.CONFIRM){
	    		html+="<div class='buttonContainer'><input type='button' value='Cancelar' class='controlButtonCancel' id='cancelButton'></div>"
	    		html+="<div class='buttonContainer'><input type='button' value='Aceptar' class='controlButton controlButtonShadow' id='acceptButton'></div>"
	    	} else {
	    		if(type==HfosModal.Types.EVENT){
	    			html+="<div class='buttonContainer'><input type='button' value='Cerrar' class='controlButton controlButtonShadow' id='acceptButton'></div>";
	    		} else {
	    			if(type==HfosModal.Types.CUSTOM){
	    				if(typeof options.showAccept != "undefined"){
	    					if(options.showAccept){
	    						html+="<div class='buttonContainer'><input type='button' value='Aceptar' class='controlButton controlButtonShadow' id='acceptButton'></div>"
	    					}
	    				};
	    				if(typeof options.buttons != "undefined"){
	    					$H(options.buttons).each(function(option){
								html+="<div class='buttonContainer'><input type='button' value='"+option[0]+"' "
								if(typeof option[1].className){
									html+="class='"+option[1].className+"'";
								}
								html+=" id='btn"+option[0]+"'></div>";
	    					});
	    				}
	    			}
	    		}
	    	}
    	}
    	html+="</td></tr></table>";
    	element.innerHTML = html;
    	document.body.appendChild(element);
    	new Draggable(element);
    	this._element = element;
    	HfosModal.zIndex++;

    	Hfos.getUI().centerAtScreen(element);

    	if(type==HfosModal.Types.CUSTOM){
	    	if(typeof options.buttons != "undefined"){
	    		$H(options.buttons).each(function(option){
	    			element.selectOne('input#btn'+option[0]).observe('click', option[1].action.bind(this, this));
	    		}.bind(this));
	    	}
    	};

    	var acceptButton = element.selectOne('input#acceptButton');
    	if(acceptButton){
	    	acceptButton.observe('click', HfosModal.remove.bind(this, this));
	    	if(typeof options.onAccept != "undefined"){
	    		acceptButton.observe('click', options.onAccept);
	    	};
	    	this._acceptButton = acceptButton;
    	};
    	if(type==HfosModal.Types.CONFIRM){
    		var cancelButton = element.selectOne('input#cancelButton');
	    	cancelButton.observe('click', HfosModal.remove.bind(this, this));
	    	cancelButton.addClassName('default');
	    };
		this._options = options;
		this._focusIndex = 0;
		this._buttons = this._element.select('input[type="button"]');

		HfosModal.add(this);
	},

	/**
	 * Coloca el foco sobre uno de los botones del cuadro de dialogo
	 *
	 * @this {HfosModal.Dialog}
	 */
	focusButton: function(position){
		for(var i=0;i<this._buttons.length;i++){
			if(i==position){
				this._buttons[i].addClassName('default');
			} else {
				this._buttons[i].removeClassName('default');
			}
		}
	},

	/**
	 * Cambia el botón seleccionado al de la izquierda
	 *
	 * @this {HfosModal.Dialog}
	 */
	moveLeft: function(){
		if(this._focusIndex>0){
			this._focusIndex--;
			this.focusButton(this._focusIndex);
		}
	},

	/**
	 * Cambia el botón seleccionado al de la derecha
	 *
	 * @this {HfosModal.Dialog}
	 */
	moveRight: function(){
		if(this._focusIndex<(this._buttons.length-1)){
			this._focusIndex++;
			this.focusButton(this._focusIndex);
		}
	},

	/**
	 * Ejecuta la opción por defecto del dialogo (cerrar)
	 *
	 * @this {HfosModal.Dialog}
	 */
	acceptDefault: function(){
		HfosModal.remove.bind(this, this)();
	},

	/**
	 * Devuelve el botón que tiene el foco
	 *
	 * @this {HfosModal.Dialog}
	 */
	getFocusButton: function(){
		return this._buttons[this._focusIndex];
	},

	/**
	 * Obtiene el elemento DOM padre del cuadro dialogo
	 *
	 * @this {HfosModal.Dialog}
	 */
	getElement: function(){
		return this._element;
	}

});

/**
 * Crea un cuadro de dialogo de Aceptar/Cancelar
 */
HfosModal.confirm = function(options){
	new HfosModal.Dialog(options, HfosModal.Types.CONFIRM);
};

/**
 * Crea un cuadro de dialogo de Aceptar
 */
HfosModal.alert = function(options){
	new HfosModal.Dialog(options, HfosModal.Types.ALERT);
};

/**
 * Crea un cuadro de dialogo personalizado
 */
HfosModal.customDialog = function(options){
	new HfosModal.Dialog(options, HfosModal.Types.CUSTOM);
};

/**
 * Crea un cuadro de dialogo de notificación de evento
 */
HfosModal.showEvent = function(options){
	new HfosModal.Dialog(options, HfosModal.Types.EVENT);
};
