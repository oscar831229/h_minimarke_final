
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
 * HfosSubmenu
 *
 * Controla el menú superior ocultándolo y mostrándolo
 */
var HfosSubmenu = Class.create({

	_element: null,

	_option: null,

	_toolbar: null,

	_subMenuOptions: [],

	_hideTimeout: null,

	_subMenuElement: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(toolbar, option, element){
		this._toolbar = toolbar;
		this._option = option;
		this._element = element;
	},

	/**
	 * Indica si el submenu está visible
	 *
	 * @this {HfosSubmenu}
	 */
	visible: function(){
		return this._subMenuElement!==null;
	},

	/**
	 * Muestra el submenu
	 *
	 * @this {HfosSubmenu}
	 */
	show: function(){

		this.hide();
		this._toolbar.notifyShow();

		this._element.addClassName('item-menu-over');
		this._element.parentNode.addClassName('item-menu-parent-over');

		var subMenuElement = document.createElement('DIV');
		subMenuElement.addClassName('submenu');

		var fragment = document.createDocumentFragment();

		var position = this._element.cumulativeOffset();
		var options = this._option.options;
		for(var i=0;i<options.length;i++){
			var hasSubOptions = false;
			var itemElement = document.createElement('DIV');
			itemElement.setAttribute('id', options[i]['title']+i);
			itemElement.update(options[i]['title']);
			itemElement.addClassName('item-submenu');
			if(typeof options[i]['options'] == "undefined"){
				itemElement.setStyle({
					'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/hover/'+options[i]['icon']+')'
				});
			} else {
				itemElement.setStyle({
					'background': 'url('+$Kumbia.path+'img/backoffice/hover/'+options[i]['icon']+') 7px 9px no-repeat, url('+$Kumbia.path+'img/nextw.gif) 97% center no-repeat'
				});
			};
			if(typeof options[i]['click'] != "undefined"){
				itemElement.store('action', options[i]['click']);
				itemElement.observe('click', itemElement.retrieve('action'));
				itemElement.observe('mouseenter', this._hideSubOptions.bind(this));
			} else {
				if(typeof options[i]['options'] != "undefined"){
					var showSubOptions = this._showSubOptions.bind(this, subMenuElement, itemElement, options[i]['options']);
					itemElement.addClassName('item-suboptions');
					itemElement.store('action', showSubOptions);
					itemElement.observe('click', function(itemElement, event){
						itemElement.retrieve('action')();
						this.focusOption.bind(this, itemElement)();
						new Event.stop(event);
					}.bind(this, itemElement));
					itemElement.observe('mouseenter', showSubOptions);
					hasSubOptions = true;
				}
			};
			itemElement.store('icon', options[i]['icon']);
			itemElement.store('hasSubOptions', hasSubOptions);
			itemElement.observe('mouseenter', this.focusOption.bind(this, itemElement));
			fragment.appendChild(itemElement);
			this._subMenuOptions[this._subMenuOptions.length] = $(itemElement);
		};
		subMenuElement.appendChild(fragment);

		var spaceElement = this._toolbar.getWorkspace().getSpaceElement();
		document.body.appendChild(subMenuElement);
		subMenuElement.hide();

		if(position[0]>0){
			if(position[0]<(spaceElement.getWidth()-subMenuElement.getWidth())){
				subMenuElement.setStyle({
					'top': '35px',
					'left': (position[0]+1)+'px'
				});
			} else {
				subMenuElement.setStyle({
					'top': '35px',
					'left': (position[0]+this._element.getWidth()-subMenuElement.getWidth())+'px'
				});
			}
		} else {
			subMenuElement.setStyle({
				'top': '35px',
				'right': '15px'
			});
		};

		new Effect.Appear(subMenuElement, {duration: 0.2});
		this._subMenuElement = subMenuElement;

		/*if(hasSubOptions==false){
			subMenuElement.observe('mouseleave', this._prepareForRemove.bind(this));
			this._element.observe('mouseleave', this._prepareForRemove.bind(this));
		};*/

		subMenuElement.observe('mouseenter', this._cancelRemove.bind(this));
		this._element.observe('mouseenter', this._cancelRemove.bind(this));

	},

	/**
	 * Muestra el submenú haciendo click sobre él
	 *
	 * @this {HfosSubmenu}
	 */
	showbyClick: function(event){
		this.show();
		new Event.stop(event);
	},

	/**
	 * Oculta los sub-menus
	 *
	 * @this {HfosSubmenu}
	 */
	_hideSubOptions: function(){
		var subOptionMenuElement = document.body.selectOne('div.submenu-options');
		if(subOptionMenuElement!==null){
			if(typeof subOptionMenuElement.parentNode != "undefined"){
				if(subOptionMenuElement.parentNode != null){
					subOptionMenuElement.erase();
				}
			}
		};
	},

	/**
	 * Genera el sub-menu y sus opciones
	 *
	 * @this {HfosSubmenu}
	 */
	_showSubOptions: function(subMenuElement, itemElement, options){

		try {

			this._hideSubOptions();

			var subOptionMenuElement = document.createElement('DIV');
			subOptionMenuElement.addClassName('submenu');
			subOptionMenuElement.addClassName('submenu-options');
			subOptionMenuElement.hide();

			var fragment = document.createDocumentFragment();

			for(var i=0;i<options.length;i++){
				var subItemElement = document.createElement('DIV');
				subItemElement.setAttribute('id', options[i]['title']+i);
				subItemElement.update(options[i]['title']);
				subItemElement.addClassName('item-submenu');
				subItemElement.setStyle({
					'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/hover/'+options[i]['icon']+')'
				});
				if(typeof options[i]['click'] != "undefined"){
					subItemElement.store('action', options[i]['click']);
					subItemElement.observe('click', subItemElement.retrieve('action'));
				};
				subItemElement.store('icon', options[i]['icon']);
				subItemElement.store('hasSubOptions', false);
				subItemElement.observe('mouseenter', this.focusSubOption.bind(this, subItemElement));
				fragment.appendChild(subItemElement);
			};

			subOptionMenuElement.appendChild(fragment);

			var position = itemElement.cumulativeOffset();
			var spaceElement = this._toolbar.getWorkspace().getSpaceElement();

			if((position[0]+190)<(spaceElement.getWidth()-itemElement.getWidth())){
				subOptionMenuElement.setStyle({
					'top': position[1]+'px',
					'left': (position[0]+190)+'px'
				});
			} else {
				subOptionMenuElement.setStyle({
					'top': position[1]+'px',
					'left': (position[0]-200)+'px'
				});
			};

			//subOptionMenuElement.observe('mouseleave', this._prepareForRemove.bind(this));
			subMenuElement.store('subOptions', subOptionMenuElement);

			document.body.appendChild(subOptionMenuElement);
			new Effect.Appear(subOptionMenuElement, {duration: 0.2});

		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 *
	 * @this {HfosSubmenu}
	 */
	_cancelRemove: function(){
		this.removeTimeout();
	},

	/**
	 *
	 * @this {HfosSubmenu}
	 */
	_prepareForRemove: function(timeout){
		this.removeTimeout();
		this._hideTimeout = window.setInterval(this.hide.bind(this), 300);
	},

	/**
	 *
	 * @this {HfosSubmenu}
	 */
	removeTimeout: function(){
		if(this._hideTimeout!=null){
			window.clearTimeout(this._hideTimeout);
			this._hideTimeout = null;
		}
	},

	/**
	 * Quita el sub-menu de la pantalla
	 *
	 * @this {HfosSubmenu}
	 */
	hide: function(){
		if(this._subMenuElement!==null){
			this._toolbar.notifyHide();
			var subOptions = this._subMenuElement.retrieve('subOptions');
			if(typeof subOptions != "undefined"){
				new Effect.Fade(subOptions, {
					duration: 0.2,
					afterFinish: function(){
						if(this.parentNode!==null){
							if(typeof this.parentNode != "undefined"){
								this.erase();
							}
						}
					}.bind(subOptions)
				});
			};
			new Effect.Fade(this._subMenuElement, {
				duration: 0.2,
				afterFinish: function(){
					if(this.parentNode!==null){
						if(typeof this.parentNode != "undefined"){
							this.erase();
						}
					}
				}.bind(this._subMenuElement)
			});
			this._subMenuElement = null;
			this._subMenuOptions = [];
			this._element.removeClassName('item-menu-over');
			this._element.parentNode.removeClassName('item-menu-parent-over');
			this.removeTimeout();
		};
	},

	/**
	 * Resalta una opción del menu selecionada
	 *
	 * @this {HfosSubmenu}
	 */
	focusSubOption: function(subItemElement){
		var subMenuOptions = subItemElement.parentNode.select('div.item-submenu-over');
		for(var i=0;i<subMenuOptions.length;i++){
			this._applyIcon(subMenuOptions[i], 'hover/', false);
			subMenuOptions[i].removeClassName('item-submenu-over');
			subMenuOptions[i].parentNode.removeClassName('item-menu-parent-over');
		};
		subItemElement.addClassName('item-submenu-over');
		subItemElement.parentNode.addClassName('item-menu-parent-over');
		this._applyIcon(subItemElement, 'hover/', true);
	},

	/**
	 * Resalta una opción del menu selecionada
	 *
	 * @this {HfosSubmenu}
	 */
	focusOption: function(itemElement){
		for(var i=0;i<this._subMenuOptions.length;i++){
			this._applyIcon(this._subMenuOptions[i], 'hover/', false);
			this._subMenuOptions[i].removeClassName('item-submenu-over');
		};
		if(typeof itemElement != "undefined"){
			itemElement.addClassName('item-submenu-over');
			this._applyIcon(itemElement, 'hover/', true);
		};
	},

	/**
	 * Aplica el icono a la opción dependiendo del estado
	 *
	 * @this {HfosSubmenu}
	 */
	_applyIcon: function(itemElement, state, selected){
		var icon = itemElement.retrieve('icon');
		if(typeof icon != "undefined"){
			var hasSubOptions = itemElement.retrieve('hasSubOptions');
			if(hasSubOptions==false){
				itemElement.setStyle({
					'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/'+state+icon+')'
				});
			} else {
				if(selected==false){
					itemElement.setStyle({
						'background': 'url('+$Kumbia.path+'img/backoffice/'+state+icon+') 7px 9px no-repeat, url('+$Kumbia.path+'img/nextw.gif) 97% center no-repeat'
					});
				} else {
					itemElement.setStyle({
						'background': 'url('+$Kumbia.path+'img/backoffice/'+state+icon+') 7px 9px no-repeat, url('+$Kumbia.path+'img/nextw.gif) 97% center no-repeat, rgba(40, 60, 92, 0.9)'
					});
				}
			};
		}
	},

	/**
	 * Hace foco en la siguiente opción del submenu de la que esté seleccionada
	 *
	 * @this {HfosSubmenu}
	 */
	focusNextOption: function(){
		if(this._subMenuOptions.length>0){
			var hasFocus = false;
			for(var i=0;i<this._subMenuOptions.length;i++){
				if(hasFocus==false){
					if(this._subMenuOptions[i].hasClassName('item-submenu-over')){
						hasFocus = true;
					}
				} else {
					this.focusOption(this._subMenuOptions[i]);
					return;
				}
			};
			this.focusOption(this._subMenuOptions[0]);
		}
	},

	/**
	 * Hace foco en la anterior opción del submenu de la que esté seleccionada
	 *
	 * @this {HfosSubmenu}
	 */
	focusPrevOption: function(){
		if(this._subMenuOptions.length>0){
			var hasFocus = false;
			for(var i=this._subMenuOptions.length-1;i>=0;i--){
				if(hasFocus==false){
					if(this._subMenuOptions[i].hasClassName('item-submenu-over')){
						hasFocus = true;
					}
				} else {
					this.focusOption(this._subMenuOptions[i]);
					return;
				}
			};
			this.focusOption(this._subMenuOptions[this._subMenuOptions-1]);
		}
	},

	/**
	 * Ejecuta la acción activa en el submenu
	 *
	 * @this {HfosSubmenu}
	 */
	runActiveOption: function(){
		if(this._subMenuOptions.length>0){
			for(var i=this._subMenuOptions.length-1;i>=0;i--){
				if(this._subMenuOptions[i].hasClassName('item-submenu-over')){
					var optionAction = this._subMenuOptions[i].retrieve('action');
					if(typeof optionAction != "undefined"){
						optionAction();
						break;
					}
				}
			};
			this.hide();
		}
	},

	/**
	 * Devuelve la acción activa en el menú
	 *
	 * @this {HfosSubmenu}
	 */
	getActiveOption: function(){
		if(this._subMenuOptions.length>0){
			for(var i=this._subMenuOptions.length-1;i>=0;i--){
				if(this._subMenuOptions[i].hasClassName('item-submenu-over')){
					return this._subMenuOptions[i];
				}
			}
		};
		return null;
	}

});