
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
 * Clase Depreciacion
 *
 * Cada formulario de Depreciacion de Activos en pantalla tiene asociado una instancia de esta clase
 */
var Depreciacion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Depreciacion
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'depreciacionForm', {
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

HfosBindings.late('win-depreciacion', 'afterCreateOrRestore', function(hfosWindow){
	var depreciacion = new Depreciacion(hfosWindow);
});

