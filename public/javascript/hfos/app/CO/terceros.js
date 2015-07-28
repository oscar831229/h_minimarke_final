
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

var Terceros = {

	/**
	 * Se llama cada vez que se crea un formulario de terceros
	 */
	onCreate: function(){
		new Tercero(this);
	}

};

/**
 * Clase Tercero
 *
 * Cada formulario de terceros en pantalla tiene asociado una instancia de esta clase
 */
var Tercero = Class.create({

	/**
	 * Constructor de Tercero
	 *
	 * @constructor
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm pedidos
HyperFormManager.lateBinding('terceros', 'afterInitialize', Terceros.onCreate)

