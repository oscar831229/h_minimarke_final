
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
 * Clase ComprobanteDiario
 *
 * Cada formulario de comprobante diario en pantalla tiene asociado una instancia de esta clase
 */
var ComprobanteDiario = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ComprobanteDiario
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'comprobanteDiarioForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se generó el reporte correctamente');
					window.open(Utils.getURL(response.file));
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});
	}

});

HfosBindings.late('win-comprobante-diario', 'afterCreateOrRestore', function(hfosWindow){
	var comprobanteDiario = new ComprobanteDiario(hfosWindow);
});

