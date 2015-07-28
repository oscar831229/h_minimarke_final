
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
 * Clase Kardex
 *
 * Cada formulario de Kardex en pantalla tiene asociado una instancia de esta clase
 */
var Kardex = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Kardex
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'buscarForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status == 'OK'){
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

HfosBindings.late('win-kardex', 'afterCreateOrRestore', function(hfosWindow){
	var kardex = new Kardex(hfosWindow);
});
