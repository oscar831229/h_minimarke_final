
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
 * HfosTabs
 *
 * Clase para convertir en Tabs un grupo de fieldsets
 */
var HfosTabs = Class.create({

	_container: null,

	_tabsContents: [],

	_tabsTexts: [],

	_tabs: [],

	/**
	 * @constructor
	 */
	initialize: function(container, tabbedClassName, options){
		try {
			this._tabsContents = container.select('fieldset.'+tabbedClassName);
			this._tabsTexts = [];
			for(var i=0;i<this._tabsContents.length;i++){
				var legendElement = this._tabsContents[i].selectOne('legend');
				legendElement.hide();
				this._tabsTexts.push(legendElement.innerHTML);
				if(i>0){
					this._tabsContents[i].hide();
				}
			};
			this._tabs = [];
			var tabsUl = document.createElement('UL');
			var fragment = document.createDocumentFragment();
			tabsUl.addClassName('tabs');
			for(var i=0;i<this._tabsTexts.length;i++){
				this._tabs[i] = document.createElement('LI');
				if(i==0){
					this._tabs[i].update('<a class="active">'+this._tabsTexts[i]+'</a>');
				} else {
					this._tabs[i].update('<a>'+this._tabsTexts[i]+'</a>');
				};
				this._tabs[i].observe('click', this._changeTab.bind(this, i));
				fragment.appendChild(this._tabs[i]);
			};
			tabsUl.appendChild(fragment);
			this._tabsContents[0].insert({
				'before': tabsUl
			});
			this._options = options || {};
			this._container = container;
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 *
	 * @this {HfosTabs}
	 */
	_changeTab: function(number){
		for(var i=0;i<this._tabsContents.length;i++){
			if(i==number){
				this._tabsContents[i].show();
			} else {
				this._tabsContents[i].hide();
			}
		};
		for(var i=0;i<this._tabs.length;i++){
			if(i==number){
				this._tabs[i].selectOne('a').addClassName('active');
			} else {
				this._tabs[i].selectOne('a').removeClassName('active');
			}
		};
		if(typeof this._options.onChange != "undefined"){
			this._options.onChange(this._tabsTexts[number]);
		};
		this._container._notifyContentChange();
	}

});