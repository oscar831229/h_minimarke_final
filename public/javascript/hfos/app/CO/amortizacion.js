
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
 * Clase Amortizacion
 *
 * Cada formulario de Amortizacion de Activos en pantalla tiene asociado una instancia de esta clase
 */
var Amortizacion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Balance
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'amortizacionForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					if(typeof response.file != "undefined"){
						window.open(Utils.getURL(response.file));
					}
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});
	}

});

HfosBindings.late('win-amortizacion', 'afterCreate', function(hfosWindow){
	var amortizacion = new Amortizacion(hfosWindow);
});

