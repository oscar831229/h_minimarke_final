
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

var Pedidos = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de pedidos
	 */
	onCreate: function(hyperGrid){
		new Pedido(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de pedidos
	 */
	onRestore: function(hyperGrid){
		new Pedido(hyperGrid, true);
	}

};

/**
 * Clase Pedido
 *
 * Cada pedido en pantalla tiene asociada una instancia de esta clase
 */
var Pedido = Class.create(TransaccionInve, {

	/**
	 * Constructor de Salida
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('P', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm pedidos
HyperFormManager.lateGridBinding('pedidos', 'afterInitialize', Pedidos.onCreate);
HyperFormManager.lateGridBinding('pedidos', 'afterRestore', Pedidos.onRestore);
