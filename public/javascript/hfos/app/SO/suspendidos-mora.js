
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
 * Clase cambiarAccion
 *
 * Cada formulario de cambiarAccion en pantalla tiene asociado una instancia de esta clase
 */
var SuspendidosMora = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cambiarAccion
	 */
	initialize: function(container){
		this.setContainer(container);
		var suspendidosMoraButton = this.getElement('importButton');
		if (!suspendidosMoraButton) {
			alert('suspendidosMoraButton not found');
			return false;
		}
		suspendidosMoraButton.observe('click', this._consultaAccion.bind(this, suspendidosMoraButton));
	},

	_consultaAccion: function(suspendidosMoraButton){
	    suspendidosMoraButton.disable();
		this.setIgnoreTermSignal(true);
		var suspendidosMoraForm = this.getElement('suspendidosMoraForm');
		if (!suspendidosMoraForm) {
			alert('form ".suspendidosMoraForm" not found');
			return false;
		}
		new HfosAjax.JsonFormRequest(suspendidosMoraForm, {
			onCreate: function(suspendidosMoraForm){
				this.getMessages().notice('Se está realizando la consulta, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				suspendidosMoraForm.disable();
			}.bind(this, suspendidosMoraForm),
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
			onComplete: function(suspendidosMoraForm, suspendidosMoraButton){
				this.getElement('headerSpinner').hide();
				suspendidosMoraForm.enable();
				suspendidosMoraButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, suspendidosMoraForm, suspendidosMoraButton)
		});
	}

});

HfosBindings.late('win-suspendidos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var suspendidosMora = new SuspendidosMora(hfosWindow);
});

