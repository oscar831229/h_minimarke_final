
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

var Entradas = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de entradas de compra
	 */
	onCreate: function(hyperGrid){
		new Entrada(hyperGrid);
	},

	onRestore: function(hyperGrid){
		new Entrada(hyperGrid, true)
	}

};

/**
 * Clase Entrada
 *
 * Cada entrada de compra en pantalla tiene asociada una instancia de esta clase
 */
var Entrada = Class.create(TransaccionInve, {

	/**
	 * Constructor de Entrada
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('E', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm entradas
HyperFormManager.lateGridBinding('entradas', 'afterInitialize', Entradas.onCreate);
HyperFormManager.lateGridBinding('entradas', 'afterRestore', Entradas.onRestore)
