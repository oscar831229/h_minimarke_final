
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
 * Clase AnularDepreciacion
 *
 * Cada formulario de Anular Depreciacion de Activos en pantalla tiene asociado una instancia de esta clase
 */
var AnularDepreciacion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de AnularDepreciacion
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		var anularDepreciacionForm = this.getElement('anularDepreciacionForm');
		if(anularDepreciacionForm!==null){
			new HfosForm(this, 'anularDepreciacionForm', {
				update: 'resultados',
				onSuccess: function(response){
					if(response.status=='OK'){
						this.getMessages().success(response.message);
					} else {
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						}
					}
				}
			});
		}
	}

});

HfosBindings.late('win-anular-depreciacion', 'afterCreate', function(hfosWindow){
	var anularDepreciacion = new AnularDepreciacion(hfosWindow);
});

