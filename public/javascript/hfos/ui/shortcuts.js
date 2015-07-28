
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
 * Ampliación del mapa de códigos de teclas
 */

//Teclas alfanumericas
/** @const */
Event.KEY_C = 67;

/** @const */
Event.KEY_E = 69;

/** @const */
Event.KEY_I = 73;

/** @const */
Event.KEY_M = 77;

/** @const */
Event.KEY_N = 78;

/** @const */
Event.KEY_P = 80;

/** @const */
Event.KEY_V = 86;

/** @const */
Event.KEY_X = 88;

//Teclas de Función
/** @const */
Event.KEY_F1 = 112;

/** @const */
Event.KEY_F2 = 113;

/** @const */
Event.KEY_F3 = 114;

/** @const */
Event.KEY_F4 = 115;

/** @const */
Event.KEY_F5 = 116;

/** @const */
Event.KEY_F6 = 117;

/** @const */
Event.KEY_F7 = 118;

/** @const */
Event.KEY_F8 = 119;

/** @const */
Event.KEY_F9 = 120;

/** @const */
Event.KEY_F10 = 121;

//Otras teclas
/** @const */
Event.KEY_SUPR = 46;

/** @const */
Event.KEY_COMMAND = 224;

/**
 * HfosShortcuts
 *
 * Administra el input de teclas y las enruta a los respectivos objetos activos en pantalla
 */
var HfosShortcuts = {

	//Indica si los shortcuts están activos ó no
	_inactive: false,

	/**
	 * Desactiva los shortcuts
	 */
	disable: function(){
		HfosShortcuts._inactive = true;
	},

	/**
	 * Activa los shortcuts
	 */
	enable: function(){
		HfosShortcuts._inactive = false;
	},

	/**
	 * Handler para evento keyup global
	 */
	globalKeyUpHandler: function(event){
		//Evitar que se haga atrás con DELETE
		if(event.keyCode==Event.KEY_BACKSPACE){
			switch(document.activeElement.tagName){
				case 'INPUT':
				case 'TEXTAREA':
					return true;
				default:
					new Event.stop(event);
					new Event.cancelBubble(event);
					return false;
			};
		};
		//No tener en cuenta shortcuts cuando está en false
		if(HfosShortcuts._inactive==false){
			//Siempre dejar pasar Ctrl-V, Ctrl-C, y Ctrl-X
			if(event.ctrlKey==true||event.metaKey){
				if(event.keyCode==Event.KEY_V||event.keyCode==Event.KEY_C||event.keyCode==Event.KEY_X){
					return true;
				};
			};
			var observableKey = event.keyCode==Event.KEY_UP||
				event.keyCode==Event.KEY_DOWN||
				event.keyCode==Event.KEY_LEFT||
				event.keyCode==Event.KEY_RIGHT||
				event.keyCode==Event.KEY_RETURN||
				event.keyCode==Event.KEY_F2||
				event.keyCode==Event.KEY_F4||
				event.keyCode==Event.KEY_F7||
				event.keyCode==Event.KEY_F8||
				event.keyCode==Event.KEY_F10||
				event.keyCode==Event.KEY_ESC||
				event.keyCode==Event.KEY_SUPR||
				event.ctrlKey==true;
			if(observableKey){
				var windowManager = Hfos.getApplication().getWorkspace().getWindowManager();
				var numberWindows = windowManager.getNumberWindows();
				if(numberWindows==0){
					//Navegar por menus
					var toolbar = Hfos.getApplication().getWorkspace().getToolbar();
					if(toolbar.isSomeVisible()==false){
						if(event.keyCode==Event.KEY_UP){
							toolbar.deployLast();
						} else {
							if(event.keyCode==Event.KEY_DOWN){
								toolbar.deployFirst();
							}
						}
					} else {
						if(event.keyCode==Event.KEY_RIGHT){
							toolbar.deployNext();
						} else {
							if(event.keyCode==Event.KEY_LEFT){
								toolbar.deployPrev();
							} else {
								var submenu = toolbar.getActiveMenu();
								if(event.keyCode==Event.KEY_UP){
									submenu.focusPrevOption();
								} else {
									if(event.keyCode==Event.KEY_DOWN){
										submenu.focusNextOption();
									} else {
										if(event.keyCode==Event.KEY_RETURN){
											submenu.runActiveOption();
										} else {
											submenu.hide();
										}
									}
								}
							}
						}
					};
					new Event.stop(event);
					return false;
				};
				if(event.keyCode==Event.KEY_ESC){
					windowManager.closeActiveWindow();
					new Event.stop(event);
					return false;
				};
				var winActive = windowManager.getActiveWindow();
				if(event.ctrlKey==true&&event.keyCode==Event.KEY_M){
					winActive.maximize();
					new Event.stop(event);
					return false;
				};
				if(winActive){
					return winActive.sendKeyEvent(event);
				};
			};
			//Hfos._screenSaver.cancelStart();
		};
	}

};
