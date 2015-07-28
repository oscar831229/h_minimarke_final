
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

var HfosMenu = Class.create({

	_options: {},
	_application: null,

	/**
	 * @constructor
	 */
	initialize: function(application){
		var script = document.createElement('SCRIPT');
		script.type = 'text/javascript';
		script.src = $Kumbia.path+'javascript/hfos/menus/'+application.getCode()+'.js?r='+parseInt(Math.random()*1000);
		document.body.appendChild(script);
		script.observe('load', this._refreshToolbar.bind(this));
		this._application = application;
	},

	/**
	 * @this {HfosMenu}
	 */
	_refreshToolbar: function(){
		this._application.getWorkspace().getToolbar().setMenu(this);
	},

	/**
	 * @this {HfosMenu}
	 */
	setOptions: function(options){
		this._options[this._application.getCode()] = $A(options);
	},

	/**
	 * @this {HfosMenu}
	 */
	getOptions: function(){
		return this._options[this._application.getCode()];
	},

});