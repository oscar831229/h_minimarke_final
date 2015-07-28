
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
 * HfosToolbar
 *
 * Administra la barra de menus de la aplicación activa
 */
var HfosToolbar = Class.create({

	//Referencia al HfosWorkspace
	_workspace: null,

	//Referencia al HfosStartMenu
	_startMenu: null,

	//Elemento DOM de la barra de menús y de inicio
	_headerElement: null,

	//Elemento DOM del botón de inicio
	_applicationSharp: null,

	//Número de menús visibles
	_numberVisible: 0,

	//Menú visible actualmente
	_menu: null,

	//Submenús activos en la aplicación
	_subMenus: [],

	/**
	 * Constructor de HfosToolbar
	 *
	 * @constructor
	 */
	initialize: function(workspace){
		this._workspace = workspace;
	},

	/**
	 * Oculta todos los menus
	 *
	 * @this {HfosToolbar}
	 */
	hideMenus: function(){
		for(var i=0;i<this._subMenus.length;i++){
			this._subMenus[i].hide();
		}
	},

	/**
	 * Despliega el primer menú
	 *
	 * @this {HfosToolbar}
	 */
	deployFirst: function(){
		this.hideMenus();
		if(this._subMenus.length>0){
			this._subMenus[0].show();
		}
	},

	/**
	 * Despliega el último menú
	 *
	 * @this {HfosToolbar}
	 */
	deployLast: function(){
		this.hideMenus();
		if(this._subMenus.length>0){
			this._subMenus[this._subMenus.length-1].show();
		}
	},

	/**
	 * Despliega el siguiente menu
	 *
	 * @this {HfosToolbar}
	 */
	deployNext: function(){
		var submenu = this.getActiveMenu();
		var activeOption = submenu.getActiveOption();
		if(activeOption!==null){
			if(activeOption.retrieve('hasSubOptions')){
				var optionAction = activeOption.retrieve('action');
				if(typeof optionAction != "undefined"){
					optionAction();
					return;
				}
			}
		};
		var hasVisible = false;
		for(var i=0;i<this._subMenus.length;i++){
			if(hasVisible==false){
				if(this._subMenus[i].visible()){
					hasVisible = true;
				}
			} else {
				this.hideMenus();
				this._subMenus[i].show();
				break;
			}
		}
	},

	/**
	 * Despliega el anterior menu
	 *
	 * @this {HfosToolbar}
	 */
	deployPrev: function(){
		var hasVisible = false;
		for(var i=this._subMenus.length-1;i>=0;i--){
			if(hasVisible==false){
				if(this._subMenus[i].visible()){
					hasVisible = true;
				}
			} else {
				this.hideMenus();
				this._subMenus[i].show();
				break;
			}
		}
	},

	/**
	 * Indica si algún submenu está visible
	 *
	 * @this {HfosToolbar}
	 */
	isSomeVisible: function(){
		for(var i=0;i<this._subMenus.length;i++){
			if(this._subMenus[i].visible()){
				return true;
			}
		};
		return false;
	},

	/**
	 * Indica si algún submenu está visible
	 *
	 * @this {HfosToolbar}
	 */
	getActiveMenu: function(){
		for(var i=0;i<this._subMenus.length;i++){
			if(this._subMenus[i].visible()){
				return this._subMenus[i];
			}
		};
		return false;
	},

	/**
	 * Establece el menú activo de la aplicación
	 *
	 * @this {HfosToolbar}
	 */
	setMenu: function(menu){

		//Quita menu
		new Event.observe(window, 'click', this.hideMenus.bind(this));

		//Agregar barra principal
		this._headerElement = document.createElement('DIV');
		var menuElement = document.createElement('DIV');
		this._headerElement.addClassName('header');
		menuElement.addClassName('menu');

		//Agregar opciones al menu
		var options = menu.getOptions();
		var fragment = document.createDocumentFragment();
		for(var i=0;i<options.length;i++){
			var elementParent = document.createElement('DIV');
			elementParent.addClassName('item-menu-parent');
			var elementItem = document.createElement('DIV');
			elementItem.addClassName('item-menu');
			elementItem.update(options[i]['title']);
			elementItem.setStyle({
				'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/'+options[i]['icon']+')'
			});
			var subMenu = new HfosSubmenu(this, options[i], elementItem);
			elementItem.observe('mouseover', subMenu.show.bind(subMenu));
			elementItem.observe('click', subMenu.showbyClick.bind(subMenu));
			elementParent.appendChild(elementItem);
			fragment.appendChild(elementParent);
			this.addSubmenu(subMenu);
		};
		menuElement.appendChild(fragment);
		this._headerElement.appendChild(menuElement);

		//System Tray
		var systemTray = this._workspace.getSystemTray();
		this._headerElement.appendChild(systemTray.getElement());
		systemTray.loadWidgets();

		document.body.appendChild(this._headerElement);
		new Effect.Move(this._headerElement, {
			duration: 1.0,
			y: 50
		});

		var application = this._workspace.getApplication();

		//Botón de la aplicación
		this._applicationSharp = document.createElement('DIV');
		this._applicationSharp.addClassName('header-button-main');
		this._applicationSharp.innerHTML = '<div id="appName">'+application.getName()+'</div>';
		document.body.appendChild(this._applicationSharp);

		var appName = this._applicationSharp.selectOne('div#appName');
		appName.setStyle({
			'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/hover/'+application.getIcon()+')'
		})

		//Crear Menú de Inicio
		this._startMenu = new HfosStartMenu(this);

		this._menu = menu;
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	addSubmenu: function(subMenu){
		this._subMenus[this._subMenus.length] = subMenu;
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	getSubmenus: function(){
		return this._subMenus;
	},

	/**
	 * Devuelve el elemento DOM del botón de menú inicio
	 *
	 * @this {HfosToolbar}
	 */
	getApplicationSharp: function(){
		return this._applicationSharp;
	},

	/**
	 * Notifica al toolbar que se solicitó mostrar un submenú
	 *
	 * @this {HfosToolbar}
	 */
	notifyShow: function(){
		if(this._numberVisible>=1){
			for(var i=0;i<this._subMenus.length;i++){
				if(this._subMenus[i].visible()){
					this._subMenus[i].hide();
				}
			}
		};
		this._numberVisible++;
		if(this._startMenu!==null){
			this._startMenu.hide();
		};
	},

	/**
	 * Notifica al toolbar que se ocultó un submenú
	 *
	 * @this {HfosToolbar}
	 */
	notifyHide: function(){
		this._numberVisible--;
		if(this._numberVisible<=0){
			window.setTimeout(function(){
				document.body.select('div.submenu-options').map(Element.hide);
			}, 250);
		};
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	hide: function(){
		this._startMenu.hide();
		new Effect.Move(this._headerElement, {
			duration: 0.5,
			y: -50
		});
		new Effect.Fade(this._applicationSharp);
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	show: function(){
		new Effect.Move(this._headerElement, {
			duration: 0.5,
			y: 50
		});
		new Effect.Appear(this._applicationSharp);
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	sleep: function(){
		this.hide();
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	wakeup: function(){
		this.show();
	},

	/**
	 *
	 * @this {HfosToolbar}
	 */
	getWorkspace: function(){
		return this._workspace;
	}

});
