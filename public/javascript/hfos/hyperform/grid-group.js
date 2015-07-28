
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
 * HyperGridGroup
 *
 * Representa un grupo de HyperGridRow
 */
var HyperGridGroup = Class.create(Enumerable, {

	//Filas del grupo
	_rows: [],

	//Indica si se deben iterar solo las filas modificadas
	_onlyChanged: false,

	/**
	 * Agrega una fila al grupo
	 *
	 * @this {HyperGridGroup}
	 */
	addRow: function(hyperGridRow){
		this._rows.push(hyperGridRow);
	},

	/**
	 * Método que permite iterar las filas del grupo
	 *
	 * @this {HyperGridGroup}
	 */
	_each: function(iterator){
		for(var i=0;i<this._rows.length;i++){
			if(this._onlyChanged==true){
				if(this._rows[i].wasChanged()==true){
					iterator(this._rows[i]);
				}
			} else {
				iterator(this._rows[i]);
			}
		}
	},

	/**
	 * Itera sobre las filas del grupo que han sido modificadas
	 *
	 * @this {HyperGridGroup}
	 */
	eachRowChanged: function(iterator){
		this._onlyChanged = true;
		this.each(iterator);
	},

	/**
	 * Busca un registro que cumpla la condición fieldName=value
	 *
	 * @this {HyperGridGroup}
	 */
	findFirst: function(fieldName, value){
		for(var i=0;i<this._rows.length;i++){
			if(this._rows[i].getValue(fieldName)==value){
				return this._rows[i];
			}
		};
		return false;
	},

	/**
	 * Altera el contenido de una fila existente
	 *
	 * @this {HyperGridGroup}
	 */
	alter: function(row){
		for(var i=0;i<this._rows.length;i++){
			if(this._rows[i].getValue('idGrid')==row.getValue('idGrid')){
				this._rows[i] = row;
				alert(i)
			}
		}
	}

});