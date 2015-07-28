
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
 * Clase ListadoMovimiento
 *
 * Cada formulario de Listado de Movimiento en pantalla tiene asociado una instancia de esta clase
 */
var ListadoMovimiento = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ListadoMovimiento
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'listadoMovimientoForm', {
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

HfosBindings.late('win-listado-movimiento', 'afterCreateOrRestore', function(hfosWindow){
	var listadoMovimiento = new ListadoMovimiento(hfosWindow);
});

