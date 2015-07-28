
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
 * Clase AjusteConsumos
 *
 * Cada formulario de ajusteConsumos en pantalla tiene asociado una instancia de esta clase
 */
var AjusteConsumos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ajusteConsumos
	 */
	initialize: function(container){
		this.setContainer(container);
		var ajusteConsumosButton = this.getElement('importButton');
		ajusteConsumosButton.observe('click', this._ajusteConsumos.bind(this, ajusteConsumosButton));
		var borrarConsumosButton = this.getElement('deleteButton');
		borrarConsumosButton.observe('click', this._borrarConsumos.bind(this, borrarConsumosButton));
	},
	
	fileUpload: function(form, action_url, div_id) {
		 // Create the iframe...
	    var iframe = document.createElement("iframe");
	    iframe.setAttribute("id", "upload_iframe");
	    iframe.setAttribute("name", "upload_iframe");
	    iframe.setAttribute("width", "0");
	    iframe.setAttribute("height", "0");
	    iframe.setAttribute("border", "0");
	    iframe.setAttribute("style", "width: 0; height: 0; border: none;");
	 
	    // Add to document...
	    form.parentNode.appendChild(iframe);
	    window.frames['upload_iframe'].name = "upload_iframe";
	 
	    iframeId = document.getElementById("upload_iframe");
	 
	    // Add event...
	    var eventHandler = function () {
	 
            if (iframeId.detachEvent) iframeId.detachEvent("onload", eventHandler);
            else iframeId.removeEventListener("load", eventHandler, false);
 
            // Message from server...
            if (iframeId.contentDocument) {
                content = iframeId.contentDocument.body.innerHTML;
            } else if (iframeId.contentWindow) {
                content = iframeId.contentWindow.document.body.innerHTML;
            } else if (iframeId.document) {
                content = iframeId.document.body.innerHTML;
            }
 
            document.getElementById(div_id).innerHTML = content;
 
            // Del the iframe...
            setTimeout('iframeId.parentNode.removeChild(iframeId)', 250);
            
            var response = content.evalJSON(true);
            
            if(response.status=='FAILED'){
				this.getMessages().error(response.message);
			} else {
				this.getMessages().success('Se realizó el ajuste de saldos correctamente');
			}
        }.bind(this);
	 
	    if (iframeId.addEventListener) iframeId.addEventListener("load", eventHandler, true);
	    if (iframeId.attachEvent) iframeId.attachEvent("onload", eventHandler);
	 
	    // Set properties of form...
	    form.setAttribute("target", "upload_iframe");
	    form.setAttribute("action", action_url);
	    form.setAttribute("method", "post");
	    form.setAttribute("enctype", "multipart/form-data");
	    form.setAttribute("encoding", "multipart/form-data");
	 
	    // Submit the form...
	    form.submit();
	 
	    document.getElementById(div_id).innerHTML = "Uploading...";
	},
	
	/**
	 * Genera la ajusteConsumos
	 */
	_ajusteConsumos: function(ajusteConsumosButton){
		ajusteConsumosButton.disable();
		this.setIgnoreTermSignal(true);
		var ajusteConsumosForm = this.getElement('ajusteConsumosForm');
		this.fileUpload(ajusteConsumosForm, $Kumbia.path+'socios/ajuste_consumos/generar', 'archivo');
		
		this.getElement('headerSpinner').hide();
		ajusteConsumosForm.enable();
		ajusteConsumosButton.enable();
		this.setIgnoreTermSignal(false);	
	},

	/**
	 * Borra los ajustes del periodo
	 */
	_borrarConsumos: function(borrarConsumosButton){
		borrarConsumosButton.disable();
		new HfosModal.confirm({
			title: 'Borrar Ajustes de Consumos',
			message: 'Desea borrar todos los ajustes de consumos del periodo junto con su movimiento contable?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ajuste_consumos/borrar', {
					parameters: { },
					onCreate: function(){
						borrarConsumosButton.disable();
						this.getMessages().notice('Se estan borrando los ajustes de consumos del periodo actual, esto tardará algunos minutos...');
						this.getElement('headerSpinner').show();
						this.getElement('importButton').disable();
					}.bind(this),
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
						}
					}.bind(this),
					onComplete: function() {
						this.getElement('importButton').enable();
						this.getElement('headerSpinner').hide();
						borrarConsumosButton.enable();
					}.bind(this)
				});
			}.bind(this)
		});
	}

});

HfosBindings.late('win-ajuste-consumos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var ajusteConsumos = new AjusteConsumos(hfosWindow);
});

