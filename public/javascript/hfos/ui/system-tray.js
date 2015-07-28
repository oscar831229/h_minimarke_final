
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

var HfosSystemTray = Class.create({

	_element: null,
	_workspace: null,

	/**
	 * @constructor
	 */
	initialize: function(workspace){
		this._workspace = workspace;
		this._element = document.createElement('DIV');
		this._element.addClassName('system-tray');
	},

	/**
	 * @this {HfosSystemTray}
	 */
	getElement: function(){
		return this._element;
	},

	/**
	 * @this {HfosSystemTray}
	 */
	loadWidgets: function(){
		for(var i=0;i<HfosSystemTrayWidgets.length;i++){
			this._addWidget(HfosSystemTrayWidgets[i]);
		}
	},

	/**
	 * @this {HfosSystemTray}
	 */
	_addWidget: function(widgetOptions){
		var widgetParent = document.createElement('DIV');
		widgetParent.addClassName('system-tray-parent-div');
		var widgetElement = document.createElement('DIV');
		widgetElement.addClassName('system-tray-div');
		var widgetContent = document.createElement('IMG');
		widgetContent.addClassName('system-tray-widget');
		widgetContent.title = widgetOptions['title'];
		widgetContent.src = $Kumbia.path+'img/backoffice/'+widgetOptions['icon'];
		widgetElement.appendChild(widgetContent);
		widgetParent.appendChild(widgetElement);
		this._element.appendChild(widgetParent);
		widgetOptions.initialize(this, widgetOptions, widgetElement);
	},

	/**
	 * @this {HfosSystemTray}
	 */
	getWorkspace: function(){
		return this._workspace;
	}

});