
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
 * HyperTabs
 *
 * Administración de Pestañas en HyperForm
 */
var HyperTabs = Class.create(Enumerable, {

	//Referencia al HyperForm
	_hyperForm: null,

	//Elemento DOM donde se encuentra el grupo de pestañas
	_activeSection: null,

	//Referencias a botones de cambio de pestaña
	_tabs: [],

	//Referencias a los contenidos de las pestañas
	_contents: [],

	//Pestaña activa en el grupo
	_tabPointer: 0,

	/**
	 *
	 * @constructor
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		this._activeSection = hyperForm.getActiveSection();
		this._tabs = this._activeSection.select('a.tab');
		for(var i=0;i<this._tabs.length;i++){
			this._tabs[i].observe('click', this.changeTab.bind(this, i));
		};
		this._contents = this._activeSection.select('div.content')
	},

	/**
	 * Cambia a un tab apartir de su Label
	 *
   	 * @this {HyperTabs}
	 */
	changeTabByLabel: function(name){
		for(var i=0;i<this._tabs.length;i++){
			if(this._tabs[i].innerHTML==name){
				this.changeTab(i);
			}
		};
	},

	/**
	 * Cambia a un Tab a partir de su posición
	 *
   	 * @this {HyperTabs}
	 */
	changeTab: function(position){
		var selectedTab = null;
		for(var i=0;i<this._tabs.length;i++){
			if(position==i){
				selectedTab = this._tabs[i];
			} else {
				this._tabs[i].removeClassName('active');
			}
		};
		if(selectedTab!==null){
			selectedTab.addClassName('active');
			for(var i=0;i<this._contents.length;i++){
				if(position==i){
					this._contents[i].show();
				} else {
					this._contents[i].hide();
				}
			};
			var contentName = selectedTab.getAttribute('title');
			this._hyperForm.notifyContentChange();
			this._tabPointer = position;
			this.fire('tabChanged', contentName, this._contents[position], position);
		};
	},

	/**
	 * Se mueve a la siguiente pestaña a la derecha
	 *
   	 * @this {HyperTabs}
	 */
	movePrev: function(){
		if(this._tabPointer>0){
			this._tabPointer--;
		} else {
			this._tabPointer = this._tabs.length-1;
		};
		this.changeTab(this._tabPointer);
	},

	/**
	 * Se mueve a la siguiente pestaña a la derecha
	 *
   	 * @this {HyperTabs}
	 */
	moveNext: function(){
		if(this._tabPointer<(this._tabs.length-1)){
			this._tabPointer++;
		} else {
			this._tabPointer = 0;
		};
		this.changeTab(this._tabPointer);
	},

	/**
	 * Agrega un callback a un determinado evento
	 *
   	 * @this {HyperTabs}
	 */
	observe: function(eventName, procedure){
		if(Object.isUndefined(this['_'+eventName])){
			this['_'+eventName] = [];
		};
		this['_'+eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento del grupo de pestañas
	 *
   	 * @this {HyperTabs}
	 */
	fire: function(eventName){
		try {
			if(!Object.isUndefined(this['_'+eventName])){
				for(var i=0;i<this['_'+eventName].length;i++){
					if(this['_'+eventName][i].apply(this, arguments)===false){
						return false;
					}
				};
				return true;
			} else {
				return true;
			}
		}
		catch(e){
			HfosException.show(e);
		}
	}

});