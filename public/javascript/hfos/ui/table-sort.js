
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
 * HfosTableSort
 *
 * Clase que hace las tablas ordenables
 */
var HfosTableSort = Class.create({

	_element: null,
	_thElements: [],
	_sortTableBody: null,

	/**
	 * @constructor
	 */
	initialize: function(element){
		this._element = $(element);
		var thElements = this._element.select('th');
		for(var i=0;i<thElements.length;i++){
			if(thElements[i].hasClassName('sortcol')){
				thElements[i].observe('click', this._columnSortHandler.bind(this, thElements[i], i));
				this._thElements.push(thElements[i]);
			}
		}
		this._sortTableBody = this._element.tBodies[0];
		for(var i=0;i<thElements.length;i++){
			if(thElements[i].hasClassName('sortcol')){
				var tdElements = this._sortTableBody.select('td:nth-of-type('+(i+1)+')');
				var typeOfData = '';
				for(var j=0;j<tdElements.length;j++){
					if(tdElements[j].innerHTML!=''){
						if(/^[0-9]+$/.test(tdElements[j].innerHTML)){
							typeOfData = 'number'
						} else {
							if(typeOfData=='number'){
								typeOfData = 'text';
								break;
							}
						}
					};
				};
				if(typeOfData==''){
					typeOfData = 'text';
				};
				thElements[i].store('sort-type', typeOfData);
			}
		}
	},

	/**
	 * Handler al dar click en una ventana ordenable
	 *
	 * @this {HfosTableSort}
	 */
	_columnSortHandler: function(thElement, number){
		var wasAscending = thElement.hasClassName('sortasc');
		for(var i=0;i<this._thElements.length;i++){
			this._thElements[i].removeClassName('sortasc');
			this._thElements[i].removeClassName('sortdesc');
		};
		if(wasAscending){
			this._sortColumn(number, thElement.retrieve('sort-type'), false);
			thElement.addClassName('sortdesc');
		} else {
			this._sortColumn(number, thElement.retrieve('sort-type'), true);
			thElement.addClassName('sortasc');
		}
	},

	/**
	 *
	 * @this {HfosTableSort}
	 */
	_sortColumn: function(number, type, ascending){
		var order = [];
		var tdElements = this._sortTableBody.select('td:nth-of-type('+(number+1)+')');
		for(var i=0;i<tdElements.length;i++){
			order.push(tdElements[i]);
		};
		if(ascending==true){
			if(type=='text'){
				order.sort(this._ascendingSortText);
			} else {
				order.sort(this._ascendingSortNumber);
			}
		} else {
			if(type=='text'){
				order.sort(this._descendingSortText);
			} else {
				order.sort(this._descendingSortNumber);
			}
		};
		for(var i=0;i<order.length;i++){
			this._sortTableBody.appendChild(order[i].parentNode);
		}
	},

	/**
	 * Ordena los datos de una columna texto ascendentemente
	 *
	 * @this {HfosTableSort}
	 */
	_ascendingSortText: function(a, b){
		if(a.innerHTML<b.innerHTML){
			return -1;
		} else {
			if(a.innerHTML>b.innerHTML){
				return 1;
			} else {
				return 0;
			}
		}
	},

	/**
	 * Ordena los datos de una columna texto descendentemente
	 *
	 * @this {HfosTableSort}
	 */
	_descendingSortText: function(a, b){
		if(a.innerHTML>b.innerHTML){
			return -1;
		} else {
			if(a.innerHTML<b.innerHTML){
				return 1;
			} else {
				return 0;
			}
		}
	},

	/**
	 * Ordena los datos de una columna texto ascendentemente
	 *
	 * @this {HfosTableSort}
	 */
	_ascendingSortNumber: function(a, b){
		var a = a.innerHTML.toString(), b = b.innerHTML.toString();
		if(a.length==b.length){
			if(a<b){
				return -1;
			} else {
				if(a>b){
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			if(a.length>b.length){
				return -1;
			} else {
				if(a.length<b.length){
					return 1;
				} else {
					return 0;
				}
			}
		}
	},

	/**
	 * Ordena los datos de una columna texto descendentemente
	 *
	 * @this {HfosTableSort}
	 */
	_descendingSortNumber: function(a, b){
		var a = a.innerHTML.toString(), b = b.innerHTML.toString();
		if(a.length==b.length){
			if(a>b){
				return -1;
			} else {
				if(a<b){
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			if(a<b){
				return -1;
			} else {
				if(a>b){
					return 1;
				} else {
					return 0;
				}
			}
		}
	}

});