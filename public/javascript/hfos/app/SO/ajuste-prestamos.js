
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
 * Clase AjustePrestamos
 *
 * Cada formulario de ajustePrestamos en pantalla tiene asociado una instancia de esta clase
 */
var AjustePrestamos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ajustePrestamos
	 */
	initialize: function(container){
		this.setContainer(container);
		var ajustePrestamosButton = this.getElement('importButton');
		if (ajustePrestamosButton) {
			ajustePrestamosButton.observe('click', this._ajustePrestamos.bind(this, ajustePrestamosButton));
		} else {
			alert('.importButton not found');
		}
		var borrarPrestamosButton = this.getElement('deleteButton');
		borrarPrestamosButton.observe('click', this._borrarPrestamos.bind(this, borrarPrestamosButton));
		
	},
	
	fileUpload: function(form, action_url, div_id) {
		
		if (!form) return false;
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
	 * Genera la ajustePrestamos
	 */
	_ajustePrestamos: function(ajustePrestamosButton){
		if (ajustePrestamosButton) {
			ajustePrestamosButton.disable();
			this.setIgnoreTermSignal(true);
			var ajustePrestamosForm = this.getElement('ajustePrestamosForm');
			if (ajustePrestamosForm) {
				this.fileUpload(ajustePrestamosForm, $Kumbia.path+'socios/ajuste_prestamos/generar', 'archivo');
				
				this.getElement('headerSpinner').hide();
				ajustePrestamosForm.enable();
			} else {
				alert('.ajustePrestamosForm not found');
			}
			
			ajustePrestamosButton.enable();
			this.setIgnoreTermSignal(false);
		}
	},

	/**
	 * Borra los ajustes del periodo
	 */
	_borrarPrestamos: function(borrarPrestamosButton){
		borrarPrestamosButton.disable();
		new HfosModal.confirm({
			title: 'Borrar Ajustes de Convenios',
			message: 'Desea borrar todos los ajustes de consumos del periodo junto con su movimiento contable?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ajuste_prestamos/borrar', {
					parameters: { },
					onCreate: function(){
						borrarPrestamosButton.disable();
						this.getMessages().notice('Se estan borrando los ajustes de convenios del periodo actual, esto tardará algunos minutos...');
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
						borrarPrestamosButton.enable();
					}.bind(this)
				});
			}.bind(this)
		});
	}

});

HfosBindings.late('win-ajuste-prestamos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var ajustePrestamos = new AjustePrestamos(hfosWindow);
});

