
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
 * HyperGridRow
 *
 * Cada fila de un HyperGridRow
 */
var HyperGridRow = Class.create(Enumerable, {

	//Datos de la fila
	_data: {},

	//Indica si alguna columna de la fila fue modificada
	_changed: false,

	//Indica si se deben iterar solo las filas modificadas
	_onlyChanged: false,

	/**
	 * Establece un valor dentro de la fila
	 *
	 * @this {HyperGridRow}
	 */
	setValue: function(index, value, changed){
		if(typeof changed == "undefined"){
			changed = true;
		};
		if(changed==true){
			this._changed = true;
		};
		this._data[index] = {
			'value': value,
			'changed': changed
		};
	},

	/**
	 * Devuelve el valor de un campo dentro de la fila
	 *
	 * @this {HyperGridRow}
	 */
	getValue: function(index){
		if(typeof this._data[index] != "undefined"){
			return this._data[index].value
		} else {
			return null;
		}
	},

	/**
	 * Indica si alguna columna de la fila fue modificada
	 *
	 * @this {HyperGridRow}
	 */
	wasChanged: function(){
		return this._changed;
	},

	/**
	 * Iterador sobre las columnas de la fila
	 *
	 * @this {HyperGridRow}
	 */
	_each: function(iterator){
		$H(this._data).each(function(column){
			if(this._onlyChanged==true){
				if(column.changed==true){
					iterator(column);
				}
			} else {
				iterator(column);
			}
		}.bind(this));
	},

	/**
	 * Itera sobre las columnas que han sido modificadas
	 *
	 * @this {HyperGridRow}
	 */
	eachColumnChanged: function(iterator){
		this._onlyChanged = true;
		this.each(iterator);
	}

});