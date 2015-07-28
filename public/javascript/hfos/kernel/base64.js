
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

var Base64 = {

	encode: function(data) {
	    return window.btoa(data);
	},

	decode: function(data) {
	    return window.atob(data);
	}
}
