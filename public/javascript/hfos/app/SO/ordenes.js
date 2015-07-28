
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

var Ordenes = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de ordenes de compra
	 */
	onCreate: function(hyperGrid){
		new Orden(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de ordenes de compra
	 */
	onRestore: function(hyperGrid){
		new Orden(hyperGrid, true)
	}

};

/**
 * Clase Orden
 *
 * Cada orden de compra en pantalla tiene asociada una instancia de esta clase
 */
var Orden = Class.create(TransaccionInve, {

	/**
	 * Constructor de Orden
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('O', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm ordenes
HyperFormManager.lateGridBinding('ordenes', 'afterInitialize', Ordenes.onCreate);
HyperFormManager.lateGridBinding('ordenes', 'afterRestore', Ordenes.onRestore)
