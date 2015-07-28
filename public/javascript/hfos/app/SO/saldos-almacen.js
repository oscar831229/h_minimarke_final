
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
 * Clase SaldosAlmacen
 *
 * Cada formulario de Saldos por Almacenes en pantalla tiene asociado una instancia de esta clase
 */
var SaldosAlmacen = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de SaldosAlmacen
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'saldosAlmacenForm', {
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

HfosBindings.late('win-saldos-almacen', 'afterCreateOrRestore', function(hfosWindow){
	var saldosAlmacen = new SaldosAlmacen(hfosWindow);
});

