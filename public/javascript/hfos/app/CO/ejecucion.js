
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
 * Clase Ejecucion
 *
 * Ejecucion del Presupuesto Comparativo
 */
var Ejecucion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Ejecucion
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		if(typeof container.tagName == "undefined"){
			var contentElement = container.getContentElement();
			contentElement.addClassName('window-content-dark');
		};
		Hfos.loadSource('canvas/chart');
	}

});

HfosBindings.late('win-ejecucion', 'afterCreateOrRestore', function(hfosWindow){
	var ejecucion = new Ejecucion(hfosWindow);
});