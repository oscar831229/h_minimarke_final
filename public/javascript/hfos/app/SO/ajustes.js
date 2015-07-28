
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

var Ajustes = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de ajustes
	 */
	onCreate: function(hyperGrid){
		new Ajuste(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se cree/edite una grilla de ajustes
	 */
	onRestore: function(hyperGrid){
		new Ajuste(hyperGrid, true)
	}

};

/**
 * Clase Ajuste
 *
 * Cada ajuste en pantalla tiene asociado una instancia de esta clase
 */
var Ajuste = Class.create(TransaccionInve, {

	/**
	 * Constructor de Ajuste
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('A', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm ajustes
HyperFormManager.lateGridBinding('ajustes', 'afterInitialize', Ajustes.onCreate);
HyperFormManager.lateGridBinding('ordenes', 'afterRestore', Ajustes.onRestore)

