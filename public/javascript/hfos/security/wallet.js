
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
 * HfosWallet
 *
 * Cachea la comprobación de privilegios para mejorar el rendimiento
 * y reducir la latencia en la red
 *
 */
var HfosWallet = {

	_secureWallet: {},

	has: function(action){
		if(typeof HfosWallet._secureWallet[action] == "undefined"){
			return false;
		} else {
			return HfosWallet._secureWallet[action];
		}
	},

	store: function(action, value){
		HfosWallet._secureWallet[action] = value;
	}

};