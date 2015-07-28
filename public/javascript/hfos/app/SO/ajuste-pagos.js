
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
var AjustePagos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ajustePagos
	 */
	initialize: function(container){
		this.setContainer(container);
		var ajustePagosButton = this.getElement('importButton');
		ajustePagosButton.observe('click', this._ajustePagos.bind(this, ajustePagosButton));
		var borrarPagosButton = this.getElement('deleteButton');
		borrarPagosButton.observe('click', this._borrarPagos.bind(this, borrarPagosButton));
		var formatoPagosButton = this.getElement('formatoButton');
		formatoPagosButton.observe('click', this._formatoPagos.bind(this, formatoPagosButton));
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

			this.getElement('headerSpinner').hide();
			this.getElement('importButton').enable();
	  		this.getElement('deleteButton').enable();
	  		this.getElement('formatoButton').enable();
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
	  	this.getElement('deleteButton').disable();
	  	this.getElement('formatoButton').disable();
	    
	    document.getElementById(div_id).innerHTML = "Uploading...";
	},
	
	/**
	 * Genera la ajustePagos
	 */
	_ajustePagos: function(ajustePagosButton){
		ajustePagosButton.disable();
		this.setIgnoreTermSignal(true);
		var ajustePagosForm = this.getElement('ajustePagosForm');
		this.fileUpload(ajustePagosForm, $Kumbia.path+'socios/ajuste_pagos/generar', 'archivo');
		this.getElement('headerSpinner').hide();
		ajustePagosForm.enable();
		ajustePagosButton.enable();
		this.setIgnoreTermSignal(false);	
	},

	/**
	 * Borra los ajustes del periodo
	 */
	_borrarPagos: function(borrarPagosButton){
		borrarPagosButton.disable();
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
		});
	},

	/**
	 * Descarga formato de ajuste pagos con sugerencias
	 */
	_formatoPagos: function(formatoPagosButton){
		formatoPagosButton.disable();
		new HfosAjax.JsonRequest('ajuste_pagos/formato', {
			parameters: { },
			onCreate: function(){
				formatoPagosButton.disable();
				this.getMessages().notice('Se esta generando formato de los ajustes de pagos, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				this.getElement('deleteButton').disable();
				this.getElement('importButton').disable();
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
				this.getElement('deleteButton').enable();
				this.getElement('importButton').enable();
				this.getElement('headerSpinner').hide();
				formatoPagosButton.enable();
			}.bind(this)
		});
	}

});

HfosBindings.late('win-ajuste-pagos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var ajustePagos = new AjustePagos(hfosWindow);
});

