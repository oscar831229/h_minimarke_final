
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
 * HfosAutocompleter
 *
 * Clase para autocompletar campos
 *
 */
var HfosAutocompleter = Class.create({

	/**
	 * Elemento del Auto-Completer
	 * @private
	 */
	_element: null,

	/**
	 * Acción
	 * @private
	 */
	_action: null,

	//Opciones del autocompleter
	_options: [],

	//Opciones devueltas por el servidor
	_results: [],

	//Cache para funcion anónima al hacer un keyup
	_cachedKeyUp: null,

	//Cache para funcion anónima al hacer un keydown
	_cachedKeyDown: null,

	//Cache para funcion anónima al hacer un blur
	_cachedOnBlur: null,

	//Cache para el id aleatorio
	_cachedId: null,

	/**
	 * Constructor de HfosAutocompleter
	 *
	 * @constructor
	 * @param {string} action
	 * @param {Object} options
	 */
	initialize: function(element, action, options){
		this._element = $(element);
		if(this._element!==null){
			var application = Hfos.getApplication();
			if(application!==null){
				var activeWindow = application.getWorkspace().getWindowManager().getActiveWindow();
				if(activeWindow){
					this._parentNode = activeWindow.getContentElement();
				} else {
					this._parentNode = this._element.up(3);
				}
			} else {
				this._parentNode = this._element.up(3);
			};
			if(!this._parentNode){
				this._element = null;
				return false;
			};
			this._action = action;
			this._options = options;
			this._cachedKeyUp = this._onKeyUp.bind(this);
			this._element.observe('keyup', this._cachedKeyUp);
			this._cachedKeyDown = this._onKeyDown.bind(this);
			this._element.observe('keydown', this._cachedKeyDown);
			//this._cachedOnBlur = this._onBlur.bind(this);
			//this._element.observe('blur', this._cachedOnBlur);
			this._cachedOnclick = this._hideAutocompleter.bind(this);
			this._parentNode.observe('click', this._cachedOnclick);
			this._cachedId = 'completerChoices'+Math.ceil(Math.random()*100);
		}
	},

	/**
	 * Evento al bajar
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_moveCursorDown: function(){
		var completerChoices = $(this._cachedId);
		var selected = -1;
		var length = this._results.length;
		for(var i=0;i<length;i++){
			if(this._results[i].selected==true){
				selected = i;
				break;
			}
		};
		if(selected<(length-1)){
			selected++;
		};
		this._showChoices(completerChoices, selected);
	},

	/**
	 * Evento al bajar
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_moveCursorUp: function(){
		var completerChoices = $(this._cachedId);
		var selected = -1;
		var length = this._results.length;
		for(var i=0;i<length;i++){
			if(this._results[i].selected==true){
				selected = i;
				break;
			}
		};
		if(selected>0){
			selected--;
		};
		this._showChoices(completerChoices, selected);
	},

	/**
	 * Evento al salir de un campo
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_onBlur: function(){
		/*if(this._results.length>0){
			this._hideResults();
		}*/
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_hideAutocompleter: function(){
		if(this._results.length>0){
			this._hideResults();
		}
	},

	/**
	 * Evento al oprimir una tecla
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_onKeyUp: function(event){
		if(event.keyCode!=Event.KEY_TAB&&event.keyCode!=Event.KEY_LEFT&&event.keyCode!=Event.KEY_RIGHT&&
			event.keyCode!=Event.KEY_DOWN&&event.keyCode!=Event.KEY_ESC&&
			event.keyCode!=Event.KEY_RETURN&&event.keyCode!=Event.KEY_PAGEUP&&event.keyCode!=Event.KEY_PAGEDOWN
		){
			var value = this._element.getValue();
			if(value.length>2){
                var paramString = this._options.paramName+'='+value;
                if (this._options.paramNames) {
                    for (var i=0;i<this._options.paramNames.length;i++) {
                        var paramName = this._options.paramNames[i];
                        var paramValue = '';
                        var paramElement = $(paramName);
                        if (paramElement) {
                            paramValue = paramElement.getValue();
                        } else {
                            alert("not found element #"+paramName);
                        }
                        paramString = paramString + '&' + this._options.paramName+'_tipo=' + paramValue;
                    }
                }
                new HfosAjax.JsonRequest(this._action, {
					parameters: paramString,
					onSuccess: function(response){
						this._results = response;
						this._showResults();
					}.bind(this)
				});
			} else {
				this._hideResults();
			}
		}
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_onKeyDown: function(event){
		if(event.keyCode==Event.KEY_UP||event.keyCode==Event.KEY_DOWN||
			event.keyCode==Event.KEY_TAB||event.keyCode==Event.KEY_RETURN||event.keyCode==Event.KEY_ESC
		){
			if(this._results.length>0){
				if(event.keyCode==Event.KEY_UP||event.keyCode==Event.KEY_DOWN){
					if(event.keyCode==Event.KEY_DOWN){
						this._moveCursorDown();
					} else {
						this._moveCursorUp();
					};
				} else {
					if(event.keyCode==Event.KEY_TAB||event.keyCode==Event.KEY_RETURN||event.keyCode==Event.KEY_ESC){
						if(event.keyCode==Event.KEY_RETURN||event.keyCode==Event.KEY_TAB){
							this._takeResult();
						} else {
							this._hideResults();
						}
					}
				};
				if(event.keyCode!=Event.KEY_TAB){
					new Event.stop(event);
				}
			}
		}
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_takeResult: function(){
		var length = this._results.length;
		for(var i=0;i<length;i++){
			if(this._results[i].selected==true){
				this._element.value = this._results[i].selectText;
				this._hideResults();
				if(typeof this._options.afterUpdateElement != "undefined"){
					this._options.afterUpdateElement(this._results[i]);
					this._element.fire('external:changed');
				};
				return;
			}
		};
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_mouseOverChoice: function(position, element){
		var completerChoices = $(this._cachedId);
		if(completerChoices){
			var length = this._results.length;
			for(var i=0;i<length;i++){
				this._results[i].selected = false;
			};
			var selectedOption = completerChoices.getElement('selected')
			if(selectedOption!==null){
				selectedOption.removeClassName('selected');
			};
			this._results[position].selected = true;
			element.addClassName('selected');
		}
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_selectThisChoice: function(position, event){
		if(typeof this._results[position] != "undefined"){
			this._element.value = this._results[position].selectText;
			if(typeof this._options.afterUpdateElement != "undefined"){
				this._options.afterUpdateElement(this._results[position]);
				this._element.fire('external:changed');
			};
			this._hideResults();
		};
		new Event.stop(event);
		return;
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_hideResults: function(){
		var completerChoices = $(this._cachedId);
		if(completerChoices){
			completerChoices.hide();
		}
	},

	/**
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_showResults: function(){
		try {
			var completerChoices = $(this._cachedId);
			if(!completerChoices){
				var completerChoices = document.createElement('DIV');
				completerChoices.id = this._cachedId;
				completerChoices.addClassName('autocomplete');
				this._element.insert({
					after: completerChoices
				});
				completerChoices.hide();
			};
			if(completerChoices.visible()==false){
				Position.clone(this._element, completerChoices, {
		            setHeight: false,
		            offsetTop: this._element.offsetHeight
          		});
			};
			this._showChoices(completerChoices, 0);
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Mostrar lista de resutados
	 *
	 * @this {HfosAutocompleter}
	 * @private
	 */
	_showChoices: function(completerChoices, selected){
		var length = this._results.length;
		if(length>0){
			var ul = document.createElement('UL');
			var fragment = document.createDocumentFragment();
			for(var i=0;i<length;i++){
				var li = document.createElement('LI');
				if(i==selected){
					li.addClassName('selected');
					this._results[i].selected = true;
				} else {
					this._results[i].selected = false;
				};
				li.setAttribute('id', this._results[i].value);
				li.update(this._results[i].optionText);
				li.observe('mouseenter', this._mouseOverChoice.bind(this, i, li));
				li.observe('click', this._selectThisChoice.bind(this, i));
				fragment.appendChild(li);
			};
			ul.appendChild(fragment);
			completerChoices.innerHTML = '';
			completerChoices.appendChild(ul);
			completerChoices.show();
		} else {
			completerChoices.hide();
		}
	},

	/**
	 * Destruir el autocompleter
	 *
	 * @this {HfosAutocompleter}
	 * @destructor
	 */
	destroy: function(){
		this._hideResults();
		//new Event.stopObserving(this._element, 'blur', this._cachedOnBlur);
		if(this._cachedKeyDown){
			new Event.stopObserving(this._element, 'keydown', this._cachedKeyDown);
		};
		if(this._cachedKeyUp){
			new Event.stopObserving(this._element, 'keyup', this._cachedKeyUp);
		};
		if(this._cachedOnclick){
			new Event.stopObserving(this._parentNode, 'click', this._cachedOnclick);
		}
		//delete this;
	}

});
