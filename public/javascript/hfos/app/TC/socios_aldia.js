
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
 * Clase SociosAldia
 *
 * Cada formulario de informe socios al dia en pantalla tiene asociado una instancia de esta clase
 */
var SociosAldia = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de proyeccionCuentas
	 */
	initialize: function(container){
		this.setContainer(container);
		var generarButton = this.getElement('importButton');
		generarButton.observe('click', this._generar.bind(this, generarButton));
	},

	/**
	 * Genera el informe
	 */
	_generar: function(generarButton){
		generarButton.disable();
		this.setIgnoreTermSignal(true);
		var generarForm = this.getElement('sociosAldiaForm');
		new HfosAjax.JsonFormRequest(generarForm, {
			onLoading: function(generarForm){
				this.getMessages().notice('Se está generando el informe...');
				this.getElement('headerSpinner').show();
				generarForm.disable();
			}.bind(this, generarForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el informe correctamente');
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(generarForm, generarButton){
				this.getElement('headerSpinner').hide();
				generarForm.enable();
				generarButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, generarForm, generarButton)
		});
	}

});

HfosBindings.late('win-socios-aldia-tpc', 'afterCreateOrRestore', function(hfosWindow){
	var sociosAldia = new SociosAldia(hfosWindow);
});

