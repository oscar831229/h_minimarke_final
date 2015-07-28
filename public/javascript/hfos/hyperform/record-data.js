
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
 * HyperRecordData
 *
 * Un registro del detalle de HyperForm
 */
var HyperRecordData = Class.create(Enumerable, {

	_data: null,

	/**
	 * @constructor
	 */
	initialize: function(data){
		this._data = data;
	},

	/**
	 * @this {HyperRecordData}
	 */
	getValueFromName: function(name){
		if(typeof this._data != "undefined"){
			for(var i=0;i<this._data.length;i++){
				if(this._data[i].name==name){
					return this._data[i].value;
				}
			};
		};
		return null;
	}


});