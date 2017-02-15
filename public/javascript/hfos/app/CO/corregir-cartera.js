
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
 * Clase CorregirCartera
 *
 * Cada formulario de corregciónde cartera de tercero
 */
var CorregirCartera = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de CarteraEdades
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'corregirCarteraForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se generó el reporte correctamente');
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});
	}

});

HfosBindings.late('win-corregir-cartera', 'afterCreateOrRestore', function(hfosWindow){
	var corregirCartera = new CorregirCartera(hfosWindow);
});
