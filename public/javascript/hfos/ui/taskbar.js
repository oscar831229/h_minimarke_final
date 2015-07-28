
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

var HfosTaskbar = Class.create({

	_workspace: null,
	_winToolbarElement: null,

	/**
	 * @constructor
	 */
	initialize: function(workspace){
		this._workspace = workspace;
	},

	/**
	 * Revisa si existe la barra de tareas, la crea si no existe
	 *
	 * @this {HfosTaskBar}
	 */
	_initWindowToolbar: function(){
		if(this._winToolbarElement==null){
			var spaceElement = this._workspace.getWindowManager().getSpaceElement();
			var windowScroll = WindowUtilities.getWindowScroll(spaceElement);
			var pageSize = WindowUtilities.getPageSize(spaceElement);
			var windowToolbar = document.createElement('DIV');
			windowToolbar.addClassName('window-toolbar');
			spaceElement.appendChild(windowToolbar);
			this._winToolbarElement = windowToolbar;
		}
	},

	/**
	 * Vuelve inactivos todos los elementos
	 *
	 * @this {HfosTaskBar}
	 */
	setNoneActive: function(){
		if(this._winToolbarElement!==null){
			this._winToolbarElement.select('.window-thumb').each(function(element){
				element.removeClassName('window-thumb-active');
			});
		}
	},

	/**
	 * Coloca el botón de una ventana como activo
	 *
	 * @this {HfosTaskBar}
	 */
	setActive: function(selectedWindow){
		this.setNoneActive();
		$(selectedWindow.getId()+'win-thumb').addClassName('window-thumb-active');
	},

	/**
	 * Agrega un botón a la barra de ventanas apartir de la ventana
	 *
	 * @this {HfosTaskBar}
	 */
	add: function(options){

		var hfosWindow = this._workspace.getWindowManager().getWindow(options.id);

		//Si hay más de una ventana abierta crea la barra de ventanas
		this._initWindowToolbar();
		var windowButton = document.createElement('DIV');
		windowButton.addClassName('window-thumb');
		windowButton.setAttribute('id', hfosWindow.getId()+'win-thumb');
		if(typeof options.icon != "undefined"){
			windowButton.update('<table><tr><td><div class="window-icon" style="background-image:url('+$Kumbia.path+'img/backoffice/hover/'+options.icon+')"/></div></td><td><div class="window-caption">'+hfosWindow.getTitle()+'</div></td></tr></table>');
		} else {
			windowButton.update('<table><tr><td><div class="window-icon"></div></td><td><div class="window-caption">'+hfosWindow.getTitle()+'</div></td></tr></table>');
		};
		this._winToolbarElement.appendChild(windowButton);

		//Eventos del botón
		windowButton.observe('click', this._handleButtonClick.bind(this, hfosWindow));
		windowButton.observe('mouseenter', this._showThumbnail.bind(this, hfosWindow, windowButton));
		windowButton.observe('mouseleave', this._hideThumbnail.bind(this, hfosWindow, windowButton));
	},

	/**
	 * Quita un botón de la barra de tareas
	 *
	 * @this {HfosTaskBar}
	 */
	remove: function(hfosWindow){
		var windowButton = $(hfosWindow.getId()+'win-thumb');
		if(windowButton){
			this._winToolbarElement.removeChild(windowButton);
		};
		if(this._winToolbarElement!==null){
			if(this._winToolbarElement.childNodes.length==0){
				var spaceElement = this._workspace.getWindowManager().getSpaceElement();
				spaceElement.removeChild(this._winToolbarElement);
				this._winToolbarElement = null;
			};
		};
	},

	/**
	 * Evento al dar click sobre un botón de la barra de tareas
	 *
	 * @this {HfosTaskBar}
	 */
	_handleButtonClick: function(hfosWindow){
		var windowManager = hfosWindow.getWindowManager();
		if(windowManager.hasModalWindow()==false){
			if(hfosWindow.getStatus()=='normal'){
				hfosWindow.getWindowManager().setActiveWindow(hfosWindow, true);
			} else {
				hfosWindow.getWindowManager().setActiveWindow(hfosWindow, false);
			};
		}
	},

	/**
	 * Muestra la miniatura de una ventana
	 *
	 * @this {HfosTaskBar}
	 */
	_showThumbnail: function(hfosWindow, windowButton){
		hfosWindow.showThumbnail(windowButton);
	},

	/**
	 * Oculta la miniatura de una ventana
	 *
	 * @this {HfosTaskBar}
	 */
	_hideThumbnail: function(hfosWindow, windowButton){
		hfosWindow.hideThumbnail(windowButton);
	},

	/**
	 * Oculta una miniatura de una ventana
	 *
	 * @this {HfosTaskBar}
	 */
	hideThumbnail: function(refElement){
		/*var hideTimeout = this.retrieve('hideTimeout');
		if(typeof hideTimeout != "undefined"){
			window.clearTimeout(hideTimeout);
			this.store('hideTimeout', null);
		};
		this.store('hideTimeout', window.setTimeout(function(){*/
		if(this._status=='minimized'){
			var ca = new CoreAnimation();
			ca.opaque(this, {
				to: 0.2,
				duration: 0.3,
				afterFinish: function(){
					this.hide();
				}
			});
		}
		//}.bind(this), 500));
	}

});