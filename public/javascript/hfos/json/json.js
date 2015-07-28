
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

var Json = {

	encode: function(value){
		if(typeof JSON == "undefined"){
			return value.toJSON();
		} else {
			return JSON.stringify(value);
		}
	},

	decode: function(value){
		if(typeof JSON == "undefined"){
			return value.evalJSON();
		} else {
			return JSON.parse(value);
		}
	}

};
