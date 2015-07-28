
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
 * Clase facturar
 *
 * Cada formulario de facturar en pantalla tiene asociado una instancia de esta clase
 */
var InformePagosPeriodo = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de facturar
	 */
	initialize: function(container) {
		this.setContainer(container);
		var printButton = this.getElement('printButton');
		printButton.observe('click', this._getReporte.bind(this, printButton));
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getReporte: function(printButton) {
		var periodo = this.selectOne('#periodo');
		var reportType = this.selectOne('#reportType');
		new HfosAjax.JsonRequest('pagos_periodo/reporte', {
			parameters: {
				'periodo': periodo.getValue(),
				'reportType': reportType.getValue()
			},
			onCreate: function(){
				this.setIgnoreTermSignal(true);
				this.getMessages().notice('Se está realizando el informe, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				this.getElement('printButton').disable();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
					this.getMessages().success(response.message);
				}
			}.bind(this),
			onComplete: function() {
				this.getElement('headerSpinner').hide();
				this.getElement('printButton').enable();
				this.setIgnoreTermSignal(false);
			}.bind(this)
		});
	}

});

HfosBindings.late('win-informe-pagos-periodo-socios', 'afterCreateOrRestore', function(hfosWindow){
	var informePagosPeriodo = new InformePagosPeriodo(hfosWindow);
});
