
(function(){

	var eventMatchers = {
		'HTMLEvents': /^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/,
		'MouseEvents': /^(?:dblclick|click|mouse(?:down|up|over|move|out))$/,
		'KeyboardEvent': /^(?:keypress|keyup|keydown)$/
	};

	var defaultOptions = {
		pointerX: 0,
		pointerY: 0,
		button: 0,
		ctrlKey: false,
		altKey: false,
		shiftKey: false,
		metaKey: false,
		bubbles: true,
		cancelable: true,
		charCode: 0
	};

	Event.simulate = function(element, eventName) {
		var options = Object.extend(Object.clone(defaultOptions), arguments[2] || { });
		var eventType = null;

		element = $(element);

		for(var name in eventMatchers){
			if(eventMatchers[name].test(eventName)) {
				eventType = name; break;
			}
		}

		if(!eventType){
			throw new SyntaxError('Interface not supported');
		}

		var	oEvent = document.createEvent(eventType);
		if(eventType=='HTMLEvents'){
			oEvent.initEvent(eventName, options.bubbles, options.cancelable);
		} else {
			if(eventType=='MouseEvents'){
				oEvent.initMouseEvent(eventName, options.bubbles, options.cancelable, document.defaultView,
					options.button, options.pointerX, options.pointerY, options.pointerX, options.pointerY,
					options.ctrlKey, options.altKey, options.shiftKey, options.metaKey, options.button, element);
			} else {
				oEvent.initKeyEvent(eventName, options.bubbles, options.cancelable, null,
					options.ctrlKey, options.altKey, options.shiftKey, options.metaKey,
					options.keyCode, options.charCode);
			}
		}
		element.dispatchEvent(oEvent);
		return element;
	}

	Element.addMethods({ simulate: Event.simulate });
})();

var UnitTest = Class.create({

	_application: null,

	_windowManager: null,

	_suite: {},

	_activeTest: '',

	initialize: function(){

	},

	//Obtener elementos en la pantalla
	/**
	 * Obtiene un menu por su nombre
	 */
	getMenu: function(text){
		var elements = $$('div.item-menu');
		for(var i=0;i<elements.length;i++){
			if(elements[i].innerHTML==text){
				return elements[i];
			}
		}
	},

	/**
	 * Obtiene un sub-menu por su nombre
	 */
	getSubMenu: function(text){
		var elements = $$('div.item-submenu');
		for(var i=0;i<elements.length;i++){
			if(elements[i].innerHTML==text){
				return elements[i];
			}
		}
	},

	/**
	 * Obtiene una opción de un sub-menu de acuerdo a su posición
	 */
	getSubOption: function(number){
		return $$('div.submenu-options > div.item-submenu:eq('+number+')')[0];
	},

	/**
	 * Obtiene un campo a partir de su texto
	 */
	getButton: function(text){
		var activeWindow = this._windowManager.getActiveWindow();
		return activeWindow.selectOne('input[value="'+text+'"]');
	},

	/**
	 * Obtiene un campo en el formulario en pantalla
	 */
	getField: function(fieldName){
		var activeWindow = this._windowManager.getActiveWindow();
		var process = activeWindow.getSubprocess();
		if(typeof process.getActiveSection == "function"){
			return process.getActiveSection().selectOne('#'+fieldName);
		} else {
			return activeWindow.selectOne('#'+fieldName);
		}
	},

	/**
	 * Obtiene el primer elemento que tenga una clase CSS
	 */
	getElement: function(selector){
		var activeWindow = this._windowManager.getActiveWindow();
		var process = activeWindow.getSubprocess();
		if(typeof process.getActiveSection == "function"){
			return process.getActiveSection().getElement(selector);
		} else {
			return activeWindow.getElement(selector);
		}
	},

	//Operaciones sobre ventanas
	/**
	 * Cierra la ventana según el titulo que se le dé
	 */
	closeWindow: function(title){
		var windows = this._windowManager.getWindows();
		for(var windowId in windows){
			if(windows[windowId].getTitle()==title){
				windows[windowId].close();
				break;
			}
		}
	},

	/**
	 * Obtiene el proceso que se está corriendo actualmente
	 */
	getProcess: function(){
		var activeWindow = this._windowManager.getActiveWindow();
		return activeWindow.getSubprocess();
	},

	//Simular eventos de mouse y teclado
	/**
	 * OK
	 */
	simulateKeyEntry: function(element, text){
		element.activate();
		text = text.toString();
		for(var i=0;i<text.length;i++){
			var chr = text.charCodeAt(i);
			element.simulate("keypress", {
				keyCode: chr,
				charCode: chr
			});
		}
	},

	simulateKey: function(keyCode){
		document.body.simulate("keydown", {keyCode: keyCode})
	},

	/**
	 * Coloca un valor en un combo apartir de su texto
	 */
	setComboValue: function(element, text){
		for(var i=0;i<element.options.length;i++){
			if(element.options[i].text==text){
				element.options[i].selected = true;
				break;
			}
		}
	},

	//Datos aleatorios
	/**
	 * Genera un numero entero aleatorio entre un rango
	 */
	getRandomInteger: function(min, max){
		return parseInt(Math.random()*(max-min))+min;
	},

	/**
	 * Genera un numero entero aleatorio entre un rango
	 */
	getRandomString: function(length){
		var str = '';
		for(var i=0;i<length;i++){
			str+=String.fromCharCode(this.getRandomInteger(50, 90));
		};
		return str;
	},

	//Aserciones
	/**
	 * Aumenta el conteo de aserciones fallidas
	 */
	_assertFailed: function(type, value){
		throw 'Aserción Falló: ['+type+'] '+value+' ('+this._activeTest+')';
	},

	/**
	 * Realiza una aserción sobre si la ventana contiene un mensaje de error con un determinado mensaje
	 */
	assertErrorMessage: function(message){
		var activeWindow = this._windowManager.getActiveWindow();
		if(activeWindow===null){
			this._assertFailed('assertErrorMessage', message);
		};
		var messages = activeWindow.getElement('messages');
		if(messages===null){
			this._assertFailed('assertErrorMessage', message);
		};
		var content = messages.getElement('error').innerHTML;
		if(content!=message){
			this._assertFailed('assertErrorMessage', message);
		}
	},

	/**
	 * Realiza una aserción sobre si la ventana contiene un mensaje de información con un determinado mensaje
	 */
	assertNoticeMessage: function(message){
		var activeWindow = this._windowManager.getActiveWindow();
		if(activeWindow===null){
			this._assertFailed('assertNoticeMessage', message);
		};
		var messages = activeWindow.getElement('messages');
		if(messages===null){
			this._assertFailed('assertNoticeMessage', message);
		};
		var content = messages.getElement('notice').innerHTML;
		if(content!=message){
			this._assertFailed('assertNoticeMessage', message);
		}
	},

	/**
	 * Realiza una aserción sobre el valor de un campo
	 */
	assertFieldValue: function(fieldName, value){
		var field = this.getField(fieldName);
		if(field.tagName=='INPUT'||field.tagName=='TEXTAREA'){
			if(field.getValue()!=value){
				this._assertFailed('assertFieldValue', field.getValue()+'!='+value);
			}
		} else {
			if(field.tagName=='SELECT'){
				if(field.options[field.selectedIndex].text!=value){
					this._assertFailed('assertFieldValue', field.options[field.selectedIndex].text+'!='+value);
				}
			}
		}
	},

	/**
	 * Indica el set de pruebas a realizar
	 */
	setSuite: function(suite){
		this._suite = $H(suite);
	},

	/**
	 * Corre el set de pruebas
	 */
	runTestSuite: function(){
		this._application = Hfos.getApplication();
		this._windowManager = this._application.getWorkspace().getWindowManager();
		this._execute(this._suite, this._suite.keys(), 0);
	},

	/**
	 * Ejecuta una tarea dentro del set
	 */
	_execute: function(suite, keys, position){
		if(typeof keys[position] != "undefined"){
			this._activeTest = keys[position];
			var test = suite.get(keys[position]);
			if(typeof test.action == "function"){
				test.action.bind(this)();
			};
			if(typeof test.delay == "undefined"){
				this._execute(suite, keys, position+1);
			} else {
				window.setTimeout(function(suite, keys, position){
					this._execute(suite, keys, position+1);
				}.bind(this, suite, keys, position), test.delay);
			}
		}
	}

});
