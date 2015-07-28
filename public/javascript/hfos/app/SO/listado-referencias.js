
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
 * Clase ListadoReferencias
 *
 * Cada formulario de Listado de Referencias en pantalla tiene asociado una instancia de esta clase
 */
var ListadoReferencias = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ListadoReferencias
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'listadoReferenciasForm', {
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

HfosBindings.late('win-listado-referencias', 'afterCreateOrRestore', function(hfosWindow){
	var listado_referencias = new ListadoReferencias(hfosWindow);
});

