
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
 * HfosVirtualUser (Usuario Virtual)
 *
 * Comprueba remotamente si un usuario tiene permisos para
 * ejecutar una determinada acción
 *
 */
var HfosVirtualUser = Class.create({

	_token: {},

	_options: {},

	/**
	 * @constructor
	 */
	initialize: function(token, options){
		this._token = token;
		this._options = options;
	},

	/**
	 *
	 * @this {HfosVirtualUser}
	 */
	getToken: function(){
		return this._token;
	},

	/**
	 *
	 * @this {HfosVirtualUser}
	 */
	getOptions: function(){
		return this._options;
	}

});