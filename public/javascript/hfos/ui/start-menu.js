
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
 * HfosStartMenu
 *
 * Controla el menú de inicio superior a la izquierda
 */
var HfosStartMenu = Class.create({

	//Referencia a HfosToolbar
	_toolbar: null,

	//Elemento DOM del botón de menú inicio
	_element: null,

	//Elemento DOM del menú de inicio
	_startMenuElement: null,

	/**
	 * Constructor de HfosStartMenu
	 *
	 * @constructor
	 */
	initialize: function(toolbar){
		this._toolbar = toolbar;
		this._element = toolbar.getApplicationSharp();
		this._element.observe('click', this.show.bind(this));
	},

	/**
	 * Muestra el start menu
	 *
	 * @this {HfosStartMenu}
	 */
	show: function(event){
		if(!this._element.hasClassName('header-button-main-se')){
			new HfosAjax.JsonRequest('workspace/getStartItems', {
				onLoading: function(){
					this._element.selectOne('#appName').update('<div align="center"><div class="load-bar"></div></div>');
				}.bind(this),
				onSuccess: function(response){
					this._element.addClassName('header-button-main-se');
					if(this._startMenuElement===null){
						this._startMenuElement = document.createElement('DIV');
						this._startMenuElement.addClassName('start-menu');

						var appContainer = this._toolbar.getWorkspace().getApplication().getAppContainer();
						appContainer.appendChild(this._startMenuElement);

						var spaceElement = this._toolbar.getWorkspace().getSpaceElement();
						spaceElement.observe('click', this.hide.bind(this));

					};
					if(response.status=='OK'){
						this._startMenuElement.update('');
						response.items.each(function(group){
							this._startMenuElement.appendChild(new Element('LABEL').update(group.name));
							var ul = document.createElement('UL');
							var fragment = document.createDocumentFragment();
							group.items.each(function(item){
								var li = document.createElement('LI');
								if(typeof Movimientos != "undefined"){
									li.observe('click', Movimientos.abrir.bind(window, item.key));
								};
								if(typeof group.icon != "undefined"){
									li.style.backgroundImage = "url("+$Kumbia.path+'img/backoffice/hover/'+group.icon+')';
								};
								li.observe('click', this.hide.bind(this));
								li.update(item.text);
								fragment.appendChild(li);
							}.bind(this));
							ul.appendChild(fragment);
							this._startMenuElement.appendChild(ul);
						}.bind(this));
					}
					this._startMenuElement.show();
				}.bind(this),
				onComplete: function(){
					var application = this._toolbar.getWorkspace().getApplication();
					this._element.selectOne('#appName').update(application.getName());
				}.bind(this)
			});
		} else {
			this._element.removeClassName('header-button-main-se');
			this._startMenuElement.hide();
		}
	},

	/**
	 * Oculta el menú inicio al hacer click por fuera de él
	 *
	 * @this {HfosStartMenu}
	 */
	hide: function(){
		if(this._startMenuElement!==null){
			this._element.removeClassName('header-button-main-se');
			this._startMenuElement.hide();
		}
	}

});