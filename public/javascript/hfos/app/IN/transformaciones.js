
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

var Transformaciones = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de transformaciones
	 */
	onCreate: function(hyperGrid)
	{
		new Transformacion(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de transformaciones
	 */
	onRestore: function(hyperGrid)
	{
		new Transformacion(hyperGrid, true);
	}

};

/**
 * Clase Transformacion
 *
 * Cada transformacion en pantalla tiene asociado una instancia de esta clase
 */
var Transformacion = Class.create(TransaccionInve, {

	/**
	 * Constructor de Ajuste
	 */
	initialize: function(hyperGrid, restored){
		this._initializeTransaction('R', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm transformaciones
HyperFormManager.lateGridBinding('transformaciones', 'afterInitialize', Transformaciones.onCreate);
HyperFormManager.lateGridBinding('transformaciones', 'afterRestore', Transformaciones.onRestore);
