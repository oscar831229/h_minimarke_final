
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
 * Clase AjusteSaldos
 *
 * Cada formulario de ajusteSaldos en pantalla tiene asociado una instancia de esta clase
 */
var AjusteSaldos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de ajusteSaldos
	 */
	initialize: function(container){
		this.setContainer(container);
		var ajusteSaldosButton = this.getElement('importButton');
		ajusteSaldosButton.observe('click', this._ajusteSaldos.bind(this, ajusteSaldosButton));
		var borrarSaldosButton = this.getElement('deleteButton');
		borrarSaldosButton.observe('click', this._borrarSaldos.bind(this, borrarSaldosButton));
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
	    this.getElement('headerSpinner').show();
	  	this.getElement('importButton').disable();
	  	this.getElement('deleteButton').disable();
	},
	
	/**
	 * Genera la ajusteSaldos
	 */
	_ajusteSaldos: function(ajusteSaldosButton){
		ajusteSaldosButton.disable();
		this.setIgnoreTermSignal(true);
		var ajusteSaldosForm = this.getElement('ajusteSaldosForm');
		this.fileUpload(ajusteSaldosForm, $Kumbia.path+'socios/ajuste_saldos/generar', 'archivo');
		
		/*new HfosAjax.JsonFormRequest(ajusteSaldosForm, {
			//parametters: [ fileField ],			
			onLoading: function(ajusteSaldosForm){
				this.getMessages().notice('Se está ajustando los saldos...');
				this.getElement('headerSpinner').show();
				ajusteSaldosForm.disable();
			}.bind(this, ajusteSaldosForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el ajuste de saldos correctamente');
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(ajusteSaldosForm, ajusteSaldosButton){
				this.getElement('headerSpinner').hide();
				ajusteSaldosForm.enable();
				ajusteSaldosButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, ajusteSaldosForm, ajusteSaldosButton)
		});*/
		
		this.getElement('headerSpinner').hide();
		ajusteSaldosForm.enable();
		ajusteSaldosButton.enable();
		this.setIgnoreTermSignal(false);	
	},

	/**
	 * Borra los ajustes del periodo
	 */
	_borrarSaldos: function(borrarSaldosButton){
		borrarSaldosButton.disable();
		new HfosModal.confirm({
			title: 'Borrar Ajustes de Saldos',
			message: 'Desea borrar todos los ajustes de saldos del periodo junto con su movimiento contable?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ajuste_saldos/borrar', {
					parameters: { },
					onCreate: function(){
						borrarSaldosButton.disable();
						this.getMessages().notice('Se estan borrando los ajustes de saldos del periodo actual, esto tardará algunos minutos...');
						this.getElement('headerSpinner').show();
						this.getElement('importButton').disable();
						this.getElement('deleteButton').disable();
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
						this.getElement('deleteButton').enable();
						this.getElement('headerSpinner').hide();
						borrarSaldosButton.enable();
					}.bind(this)
				});
			}.bind(this)
		});
	}

});

HfosBindings.late('win-ajuste-saldos-socios', 'afterCreateOrRestore', function(hfosWindow){
	var ajusteSaldos = new AjusteSaldos(hfosWindow);
});

