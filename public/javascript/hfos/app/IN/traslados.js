
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

var Traslados = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de ordenes de compra
	 */
	onCreate: function(hyperGrid)
	{
		new Traslado(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de ordenes de compra
	 */
	onRestore: function(hyperGrid)
	{
		new Traslado(hyperGrid, true);
	}

};

var Traslado = Class.create(TransaccionInve, {

	/**
	 * Constructor de Ajuste
	 */
	initialize: function(hyperGrid, restored)
	{
		this._initializeTransaction('T', hyperGrid, restored);
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm traslados
HyperFormManager.lateGridBinding('traslados', 'afterInitialize', Traslados.onCreate);
HyperFormManager.lateGridBinding('traslados', 'afterRestore', Traslados.onRestore);
