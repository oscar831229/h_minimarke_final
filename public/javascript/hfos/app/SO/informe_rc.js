
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Clase facturar
 *
 * Cada formulario de facturar en pantalla tiene asociado una instancia de esta clase
 */
var InformeRc = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de facturar
	 */
	initialize: function(container){
		this.setContainer(container);
		var printButton = this.getElement('printButton');
		printButton.observe('click', this._getReporte.bind(this, printButton));
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getReporte: function(printButton){
		var sociosIdField = this.selectOne('#socios_id');
		var facturarForm = this.getElement('informeRcForm');
		facturarForm.setAttribute('action', $Kumbia.path+'socios/informe_rc/reporte');
		new HfosAjax.JsonFormRequest(facturarForm, {
			onCreate: function(facturarForm){
				this.setIgnoreTermSignal(true);
				this.getMessages().notice('Se está realizando la generación, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				facturarForm.disable();
				this.getElement('printButton').disable();
			}.bind(this, printButton),
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
			onComplete: function(facturarForm, printButton){
				this.getElement('headerSpinner').hide();
				facturarForm.enable();
				printButton.enable();
				this.getElement('printButton').enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, facturarForm, printButton)
		});
	},

});

HfosBindings.late('win-informe-rc-socios', 'afterCreateOrRestore', function(hfosWindow){
	var informeRc = new InformeRc(hfosWindow);
});
