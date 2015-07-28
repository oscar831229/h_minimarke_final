
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

var HfosCuentasSelectores = {

	_selectores: {},

	add: function(name, selector){
		HfosCuentasSelectores._selectores[name] = selector;
	},

	removeAll: function(){
		for(var name in HfosCuentasSelectores._selectores){
			HfosCuentasSelectores._selectores[name].destroy();
			delete HfosCuentasSelectores._selectores[name];
		}
	}

};

var HfosCuentasSelector = Class.create({

	_element: null,
	_elementDetalle: null,
	_elements: {},
	_cuentaElement: null,

	_elementDiv: null,

	_cuenta: '',
	_oldCuenta: '',
	_blockQuery: false,

	_selectionFirst: false,
	_selectionLast: false,
	_resultsPointer: 0,
	_liResults: [],

	_windowManager: null,

	/**
	 * @constructor
	 */
	initialize: function(element, elementDetalle){

		HfosCuentasSelectores.removeAll();

		this._element = $(element);
		this._elementDetalle = $(elementDetalle);

		var html = '<table cellspacing="0" cellpadding="0" class="cuentaSelector">';
		html+='<tr><td><input type="text" class="tipo cuentaSelectorPart" size="1" maxlength="1"/></td>';
		html+='<td><input type="text" class="mayor cuentaSelectorPart" size="1" maxlength="1"/></td>';
		html+='<td><input type="text" class="clase cuentaSelectorPart" size="2" maxlength="2"/></td>';
		html+='<td><input type="text" class="subclase cuentaSelectorPart" size="2" maxlength="2"/></td>';
		html+='<td><input type="text" class="auxiliar cuentaSelectorPart" size="3" maxlength="3"/></td>';
		html+='<td><input type="text" class="subaux cuentaSelectorPart" size="3" maxlength="3"/></td>';
		html+='</tr></table>';

		var originalPosition = this._element.selectionStart;
		this._element.hide();
		this._element.insert({ after: html });

		this._cuentaElement = this._element.parentNode.getElement('cuentaSelector');

		var fields = {
			'tipo': {'length': 1, 'next': 'mayor', 'prev': ''},
			'mayor': {'length': 1, 'next': 'clase', 'prev': 'tipo'},
			'clase': {'length': 2, 'next': 'subclase', 'prev': 'mayor'},
			'subclase': {'length': 2, 'next': 'auxiliar', 'prev': 'clase'},
			'auxiliar': {'length': 3, 'next': 'subaux', 'prev': 'subclase'},
			'subaux': {'length': 3, 'next': '', 'prev': 'auxiliar'}
		};
		for(var field in fields){
			var descriptor = fields[field];
			this._elements[field] = this._cuentaElement.getElement(field);
			this._autoNextOn(field, descriptor);
		};
		this._oldCuenta = this._element.value;
		this._setCuenta(this._element.value, false);
		this.focus(originalPosition);

		for(var field in fields){
			this._elements[field].observe('blur', this._blurCuentaElement.bind(this));
			this._elements[field].observe('focus', this._focusCuentaElement.bind(this));
		};

		this._windowManager = Hfos.getApplication().getWorkspace().getWindowManager();

		HfosCuentasSelectores.add(this._element.id, this);
	},

	_getSelectedCuenta: function(){
		var cuenta = '';
		for(var elementName in this._elements){
			if(typeof this._elements[elementName].value != "undefined"){
				cuenta+=this._elements[elementName].value;
			}
		};
		return cuenta;
	},

	_setCuenta: function(cuenta, jump){
		var focusTo = "";
		this._blockQuery = true;
		this._cuenta = cuenta;
		this._customFocus();
		this._blockQuery = false;
		this._queryByCuenta();
		if(jump==true){
			if(cuenta.length>9){
				if(this._elementDetalle){
					this._elementDetalle.focus();
					this.fire('leave', this._element);
				};
			}
		}
	},

	_customFocus: function(element){
		var focusTo = 'tipo';
		if(this._cuenta.length>0){
			this._elements['tipo'].setValue(this._cuenta.substr(0,1));
			focusTo = 'mayor';
		} else {
			this._elements['tipo'].setValue("");
		};
		if(this._cuenta.length>1){
			this._elements['mayor'].setValue(this._cuenta.substr(1,1));
			focusTo = 'clase';
		} else {
			this._elements['mayor'].setValue("");
		};
		if(this._cuenta.length>2){
			this._elements['clase'].setValue(this._cuenta.substr(2,2));
			focusTo = 'subclase';
		} else {
			this._elements['clase'].setValue("");
		};
		if(this._cuenta.length>4){
			this._elements['subclase'].setValue(this._cuenta.substr(4,2));
			focusTo = 'auxiliar';
		} else {
			this._elements['subclase'].setValue("");
		};
		if(this._cuenta.length>6){
			this._elements['auxiliar'].setValue(this._cuenta.substr(6,3));
			focusTo = 'subaux';
		} else {
			this._elements['auxiliar'].setValue("");
		};
		if(this._cuenta.length>9){
			this._elements['subaux'].setValue(this._cuenta.substr(9,3));
			focusTo = 'detalle';
		} else {
			this._elements['subaux'].setValue("");
		};
		if(typeof this._elements[focusTo] != "undefined"){
			this._elements[focusTo].focus();
		};
	},

	_setCuentaTag: function(element, text, color){
		var element = this._elements[element];
		var elementTag = $(element.id+'_tag');
		var position = element.up().positionedOffset();
		if(!elementTag){
			var spaceElement = this._windowManager.getSpaceElement();
			var elementTag = document.createElement('DIV');
			elementTag.addClassName('auxiliarTag');
			elementTag.id = element.id+'_tag';
			elementTag.hide();
			elementTag.update(text);
			spaceElement.appendChild(elementTag);
		} else {
			elementTag.update(text);
		};

		var container = this._windowManager.getActiveWindow();
		var contentElement = container.getContentElement();
		var contentOffset = contentElement.positionedOffset();
		var top =  position[1]-contentElement.scrollTop+container.getTop()+element.getHeight();
		var left = position[0]-contentElement.scrollLeft+container.getLeft()+element.getHeight();

		elementTag.setStyle({
			'top': top+'px',
			'left': left+'px',
			'height': element.getWidth()+'px',
			'backgroundColor': color
		});

		if(elementTag.visible()==false){
			new Effect.Appear(elementTag, { duration: 0.2 });
		}
	},

	_removeCuentaTag: function(element){
		/*var element = this._elements[element];
		var elementTag = $(element.id+'_tag');
		if(elementTag){
			new Effect.Fade(elementTag, { duration: 0.3 });
		};*/
	},

	_removeCuentaTags: function(){
		var duration = 0.4;
		$$('.auxiliarTag').each(function(element){
			new Effect.Fade(element, { duration: duration });
			duration-=0.1;
			if(duration<0){
				duration = 0.1;
			}
		});
	},

	/**
	 *
	 */
	_autoNextOn: function(onElement, toElement){
		onElement = this._elements[onElement];
		onElement.observe('keyup', function(onElement, toElement, event){
			var ev = parseInt(event.keyCode, 10);
			var cancelBubble = false;
			if(((ev>47&&ev<58)||(ev>95&&ev<110))&&onElement.selectionStart==onElement.value.length){
				if(onElement.value.length>=toElement.length){
					if(typeof this._elements[toElement.next] != "undefined"){
						this._elements[toElement.next].activate();
						cancelBubble = true;
					} else {
						this._setCuenta(this._getSelectedCuenta(), true);
						if(this._elementDetalle){
							this._elementDetalle.focus();
						}
						this.fire('leave', this._element);
					}
				}
			} else {
				if(ev==Event.KEY_RETURN){
					var defaultValue = onElement.value;
					if(defaultValue!=""){
						var paddedValue = "";
						if(onElement.value.length<toElement.length){
							for(var i=0;i<(toElement.length-onElement.value.length);i++){
								paddedValue+="0";
							};
							defaultValue = paddedValue+defaultValue;
						};
						onElement.value = defaultValue;
						if(typeof this._elements[toElement.next] != "undefined"){
							this._elements[toElement.next].activate();
							cancelBubble = true;
						} else {
							if(this._elementDetalle){
								this._elementDetalle.focus();
							}
							this.fire('leave', this._element);
							return;
						}
					};
					if(this._liResults.length>0){
						this._setCuenta(this._liResults[this._resultsPointer].id, true);
						this._resetResults();
						return;
					}
				} else {
					if((ev==8&&onElement.value=="")||(ev==Event.KEY_LEFT&&onElement.selectionStart==0)){
						if(onElement.value.length>0){
							if(this._selectionFirst==false){
								this._selectionFirst = true;
								return;
							} else {
								if(typeof this._elements[toElement.prev] != "undefined"){
									this._elements[toElement.prev].focus();
									cancelBubble = true;
								}
							}
						} else {
							if(typeof this._elements[toElement.prev] != "undefined"){
								this._elements[toElement.prev].focus();
								cancelBubble = true;
							}
						}
					} else {
						if(ev==Event.KEY_RIGHT&&onElement.selectionStart==onElement.value.length){
							if(toElement.next!==''){
								if(onElement.value.length>0){
									if(this._selectionLast==false){
										this._selectionLast = true;
										return;
									} else {
										if(typeof this._elements[toElement.next] != "undefined"){
											this._elements[toElement.next].focus();
										} else {
											this._elementDetalle.focus();
											this.fire('leave', this._element);
											return;
										}
									}
								} else {
									if(typeof this._elements[toElement.next] != "undefined"){
										this._elements[toElement.next].focus();
									} else {
										if(this._elementDetalle){
											this._elementDetalle.focus();
										}
										this.fire('leave', this._element);
										return;
									}
								}
							} else {
								this._elementDetalle.focus();
								this.fire('leave', this._element);
								return;
							}
						} else {
							if(ev==46&&onElement.selectionStart==onElement.value.length){
								if(this._selectionLast==false){
									this._selectionLast = true;
									return;
								} else {
									if(typeof this._elements[toElement.next] != "undefined"){
										this._elements[toElement.next].focus();
										this._elements[toElement.next].selectionStart = 0;
										this._elements[toElement.next].selectionEnd = 0;
									}
								}
							}
						}
					}
				}
			};
			if(ev==Event.KEY_DOWN){
				if(this._liResults.length>0){
					this._moveCursorNext();
				} else {
					this.fire('keyup', this._element, event);
				};
				return;
			} else {
				if(ev==Event.KEY_UP){
					if(this._liResults.length>0){
						this._moveCursorPrev();
					} else {
						this.fire('keyup', this._element, event);
					};
					return;
				}
			};
			this._selectionFirst = false;
			this._selectionLast = false;
			if(cancelBubble==false){
				this.fire('keyup', this._element, event);
			}
		}.bind(this, onElement, toElement));

		onElement.observe('keydown', function(onElement, toElement, event){
			var ev = parseInt(event.keyCode, 10);
			if(event.shiftKey==false&&ev==9){
				var defaultValue = onElement.value;
				if(defaultValue!=""){
					var paddedValue = "";
					if(onElement.value.length<toElement.length){
						for(var i=0;i<(toElement.length-onElement.value.length);i++){
							paddedValue+="0";
						};
						defaultValue = paddedValue+defaultValue;
					};
					onElement.value = defaultValue;
					if(typeof this._elements[toElement.next] != "undefined"){
						this._elements[toElement.next].activate();
					} else {
						if(this._elementDetalle){
							this._elementDetalle.focus();
						}
						this.fire('leave', this._element);
					}
					new Event.stop(event);
					return;
				}
			} else {
				if(ev>=65&&ev<=90){
					new Event.stop(event);
					return;
				};
			}
		}.bind(this, onElement, toElement));
	},

	_resetResults: function(){
		if(this._elementDiv!==null){
			this._elementDiv.hide();
			this._elementDiv.innerHTML = "";
		};
		this._liResults = [];
	},

	_selectFirstAuxiliar: function(){
		if(this._liResults.length>0){
			this._liResults[0].addClassName('selected');
		};
		this._resultsPointer = 0;
	},

	_moveCursorNext: function(){
		this._resultsPointer++;
		if(this._resultsPointer>=this._liResults.length){
			this._resultsPointer = 0;
		};
		this._moveCursor();
	},

	_moveCursorPrev: function(){
		this._resultsPointer--;
		if(this._resultsPointer<0){
			this._resultsPointer = this._liResults.length-1;
		};
		this._moveCursor();
	},

	_moveCursor: function(){
		if(this._liResults.length>0){
			this._liResults.each(function(liElement){
				liElement.removeClassName('selected');
			});
			this._liResults[this._resultsPointer].addClassName('selected');
		}
	},

	_blurCuentaElement: function(){
		this._queryByCuenta();
	},

	_focusCuentaElement: function(event){
		var activeElement = document.activeElement;
		if(activeElement.getValue()==''){
			var fieldName = activeElement.className.split(' ')[0];
			for(var name in this._elements){
				if(name==fieldName){
					break;
				} else {
					if(this._elements[name].getValue()==''){
						this._elements[name].focus();
						break;
					}
				}
			};
		}
		this._queryByCuenta();
	},

	_queryByCuenta: function(){
		if(this._blockQuery==false){
			var cuenta = this._getSelectedCuenta();
			if(cuenta!=""){
				if(cuenta!=this._oldCuenta){
					new HfosAjax.Request('cuentas/queryCuenta', {
						method: 'GET',
						parameters: {
							'cuenta': cuenta
						},
						onSuccess: this._successQueryCuenta.bind(this)
					});
					this._oldCuenta = cuenta;
					this.fire('change', this._element, null);
				}
			} else {
				this._removeCuentaTags();
				this._resetResults();
				this._oldCuenta = cuenta;
			}
		};
		window.setTimeout(this._checkForBlurSelector.bind(this), 100);
	},

	_checkForBlurSelector: function(){
		var activeElement = document.activeElement;
		if(activeElement.tagName=='INPUT'){
			var insideSelector = false;
			for(var name in this._elements){
				if(this._elements[name]==activeElement){
					insideSelector = true;
					break;
				}
			};
			if(insideSelector==false){
				this.fire('leave', this._element);
			}
		}
	},

	_successQueryCuenta: function(transport){
		var cuenta = Json.decode(transport.responseText);
		if(cuenta.existe=="S"){
			if(typeof this._elementDetalle != "undefined"){
				this._elementDetalle.setValue(cuenta.nombre);
				if(cuenta.esAuxiliar=="N"){
					if(cuenta.cuentasAuxiliares.length>0){
						var html = "<table cellspacing='0' cellpadding='0'><tr>"
						var n = 0;
						var colors = ['#aeaeae', '#b0b0b0', '#cfcfcf', '#d0d0d0', '#ececec'];
						var components = ['tipo', 'mayor', 'clase', 'subclase', 'auxiliar'];
						for(var i=0;i<components.length;i++){
							var nombreElement = components[i];
							if(typeof cuenta[nombreElement+'Detalle'] != "undefined"){
								if(cuenta[nombreElement+'Detalle']!=''){
									var element = this._elements[nombreElement];
									var detalleElement = cuenta[nombreElement+'Detalle'];
									var height = parseInt(detalleElement.length*9.4, 10);
									html+="<td class='auxiliarTag' style='background-color:"+colors[n]+";height:"+height+"px' valign='top'><div style='width:"+element.getWidth()+"px'>"+detalleElement.replace(/ /g, '&nbsp;')+"</div></td>";
								};
								n++;
							}
						};
						html+="<td valign='top'>";
						var activeElement = this._elements['tipo'];
						var position = activeElement.positionedOffset();
						var container = this._windowManager.getActiveWindow();
						if(container){
							var contentElement = container.getContentElement();
							var contentOffset = contentElement.positionedOffset();
							var top =  position[1]-contentElement.scrollTop+container.getTop()+activeElement.getHeight();
							var left = position[0]-contentElement.scrollLeft+container.getLeft();
							this._elementDiv = $('cuentasDiv');
							if(this._elementDiv===null){
								this._elementDiv = document.createElement('DIV');
								this._elementDiv.id = 'cuentasDiv';
								this._elementDiv.hide();
								var spaceElement = this._windowManager.getSpaceElement();
								spaceElement.appendChild(this._elementDiv);
							} else {
								this._elementDiv.innerHTML = "";
							};
							this._elementDiv.setStyle({
								'top': top+'px',
								'left': left+'px'
							});
							html+="<ul>";
							for(var i=0;i<cuenta.cuentasAuxiliares.length;i++){
								html+="<li id='"+cuenta.cuentasAuxiliares[i].cuenta;
								html+="'><span class='resAuxiliar'>"+cuenta.cuentasAuxiliares[i].auxiliar+"</span>";
								html+=cuenta.cuentasAuxiliares[i].nombre+'</li>';
							};
							html+="</ul></td></tr></table>";
							this._elementDiv.innerHTML = html;
							this._elementDiv.show();
							this._liResults = this._elementDiv.select('li');
							/*this._liResults.each(function(element){
								element.observe('click', function(){

								})
							});*/
							this._selectFirstAuxiliar();
						}
					} else {
						this._resetResults();
					}
				} else {
					this._removeCuentaTags();
					this._resetResults();
					var activeElement = document.activeElement;
					if(activeElement.value==""){
						if(this._elementDetalle){
							this._elementDetalle.focus();
						}
						this.fire('leave', this._element);
						return;
					}
				}
			}
		} else {
			this._removeCuentaTags();
			this._resetResults();
			this._elementDetalle.setValue('NO EXISTE LA CUENTA');
		}
	},

	/**
	 * Evento focus
	 */
	focus: function(originalPosition){
		if(typeof originalPosition == "undefined"){
			originalPosition = this._cuenta.length;
		};
		var focusPositions = {
			'12': 'subaux',
			'9': 'auxiliar',
			'6': 'subclase',
			'4': 'clase',
			'2': 'mayor',
			'1': 'tipo',
			'0': 'tipo'
		};
		if(typeof focusPositions[originalPosition] != "undefined"){
			var focusElement = this._elements[focusPositions[originalPosition]];
			focusElement.selectionStart = focusElement.value.length;
			focusElement.focus();
		};
	},

	/**
	 * Devuelve la cuenta capturada
	 */
	getCuenta: function(){
		return this._cuenta;
	},

	/**
	 * Destruye el selector de cuentas
	 */
	destroy: function(){
		this._removeCuentaTags();
		this._resetResults();
		if(this._cuentaElement!==null){
			this._cuentaElement.erase();
			this._element.value = this._getSelectedCuenta();
			this._element.show();
			this._cuentaElement = null;
		}
	},

	/**
	 * Agrega un procedimiento a un determinado evento
	 */
	observe: function(eventName, procedure){
		if(Object.isUndefined(this['_'+eventName])){
			this['_'+eventName] = [];
		};
		this['_'+eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento en el componente
	 */
	fire: function(eventName, elementParam, observerParam){
		if(!Object.isUndefined(this['_'+eventName])){
			for(var i=0;i<this['_'+eventName].length;i++){
				if(this['_'+eventName][i](this, elementParam, observerParam)===false){
					return false;
				}
			};
			return true;
		} else {
			return true;
		}
	}

});
