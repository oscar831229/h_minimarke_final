
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
 * Clase NovedadActivos
 *
 * Cada formulario de Novedad Activos en pantalla tiene asociado una instancia de esta clase
 */
var NovedadActivos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de NovedadesActivos
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'novedadActivosForm', {
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

HfosBindings.late('win-novedad-activos', 'afterCreateOrRestore', function(hfosWindow){
	var novedadActivos = new NovedadActivos(hfosWindow);
});

