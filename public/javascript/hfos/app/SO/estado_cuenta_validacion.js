
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
 * Clase EstadoCuentaValidacion
 *
 * Cada formulario de facturar en pantalla tiene asociado una instancia de esta clase
 */
var EstadoCuentaValidacion = Class.create(HfosProcessContainer, {

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
		var fecha = this.selectOne('#fecha');
		var reportType = this.selectOne('#reportType');
		var sociosId = this.selectOne('#sociosId');
		
		new HfosAjax.JsonRequest('estado_cuenta_validacion/reporte', {
			parameters: {
				'fecha': fecha.getValue(),
				'sociosId': sociosId.getValue(),
				'reportType': reportType.getValue()
			},
			onCreate: function(){
				this.setIgnoreTermSignal(true);
				this.getMessages().notice('Se está realizando la generación, esto tardará algunos minutos...');
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
	},

});

HfosBindings.late('win-estado-cuenta-validacion-socios', 'afterCreateOrRestore', function(hfosWindow){
	var estadoCuentaValidacion = new EstadoCuentaValidacion(hfosWindow);
});
