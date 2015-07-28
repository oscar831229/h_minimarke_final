
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
 * Clase AjustePagos
 *
 * Cada formulario de ajustePagos en pantalla tiene asociado una instancia de esta clase
 */
var ImportarPagos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ajustePagos
	 */
	initialize: function(container){
		this.setContainer(container);
		var importarPagosButton = this.getElement('importButton');
		importarPagosButton.observe('click', this._importarPagos.bind(this, importarPagosButton));
		//var borrarPagosButton = this.getElement('deleteButton');
		//borrarPagosButton.observe('click', this._borrarPagos.bind(this, borrarPagosButton));
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
            setTimeout('iframeId.parentNode.removeChild(iframeId)', 2150);
            var response = {"status": "FAILED", "message": "Error en el parseo del JSON"};
            content = content.replace(/<(?:.|\n)*?>/gm, '');
            console.log(content);
            try {
            	var response = content.evalJSON(true);
            } catch (e) {
            	this.getMessages().error(e.message + " " + content);
            }
            
            if(response.status=='FAILED'){
				this.getMessages().error(response.message);
			} else {
				this.getMessages().success('Se realizó la importación de pagos correctamente. Por favor revise los recibos de caja generados en contabilidad.');
			}

			this.getElement('headerSpinner').hide();
			this.getElement('importButton').enable();
	  		//this.getElement('deleteButton').enable();
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

	 	this.getElement('headerSpinner').show();
	  	this.getElement('importButton').disable();
	  	//this.getElement('deleteButton').disable();
	    
	    document.getElementById(div_id).innerHTML = "Uploading...";

	    return true;
	},
	
	/**
	 * importa los pagos
	 */
	_importarPagos: function(importarPagosButton) {

		try {
			importarPagosButton.disable();
			this.setIgnoreTermSignal(true);
			var fileField = this.getElement("archivo");
			if (!fileField.getValue()) {
				alert("Por favor ingrese el archivo para poder importar pagos");
				importarPagosButton.enable();
				this.setIgnoreTermSignal(false);	
				return false;
			}
			new HfosModal.confirm({
				title: 'Importar Pagos',
				message: 'Al importar pagos no se podrán borrar por socios, solo por contabilidad uno a uno. Desea importar pagos?',
				onAccept: function(){
					var importarPagosForm = this.getElement('importarPagosForm');
					this.fileUpload(importarPagosForm, $Kumbia.path+'socios/importar_pagos/generar', 'archivo');
					importarPagosForm.enable();
				}.bind(this)
			});
			this.getElement('headerSpinner').hide();
			importarPagosButton.enable();
			this.setIgnoreTermSignal(false);
		} catch(e) {
			this.getMessages().error(e.message);
		}
	},

	/**
	 * Borra los ajustes del periodo
	 */
	_borrarPagos: function(borrarPagosButton){
		/*borrarPagosButton.disable();
		new HfosModal.confirm({
			title: 'Borrar Ajustes de Pagos',
			message: 'Desea borrar todos los ajustes de pagos del periodo junto con su movimiento contable?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ajuste_pagos/borrar', {
					parameters: { },
					onCreate: function(){
						borrarPagosButton.disable();
						this.getMessages().notice('Se estan borrando los ajustes de pagos del periodo actual, esto tardará algunos minutos...');
						this.getElement('headerSpinner').show();
						this.getElement('importButton').disable();
						this.getElement('formatoButton').disable();
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
						this.getElement('formatoButton').enable();
						this.getElement('headerSpinner').hide();
						borrarPagosButton.enable();
					}.bind(this)
				});
			}.bind(this)
		});*/
	}
});

HfosBindings.late('win-importar-pagos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var importarPagos = new ImportarPagos(hfosWindow);
});

