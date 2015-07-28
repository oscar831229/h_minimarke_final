
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

var Salidas = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de salidas de compra
	 */
	onCreate: function(hyperGrid){
		new Salida(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de salidas
	 */
	onRestore: function(hyperGrid){
		new Salida(hyperGrid, true)
	}

};

/**
 * Clase Salida
 *
 * Cada salida del almacén en pantalla tiene asociada una instancia de esta clase
 */
var Salida = Class.create(TransaccionInve, {

	/**
	 * Constructor de Salida
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('C', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm salidas
HyperFormManager.lateGridBinding('salidas', 'afterInitialize', Salidas.onCreate);
HyperFormManager.lateGridBinding('salidas', 'afterRestore', Salidas.onRestore)
