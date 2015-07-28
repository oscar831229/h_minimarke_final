
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

var HfosProgress = Class.create({

	/**
	 * @constructor
	 */
	initialize: function(container){
		this._element = document.createElement('DIV');
		this._element.update('<div class="progressContainer"></div>');
		container.appendChild(container);
	}

})