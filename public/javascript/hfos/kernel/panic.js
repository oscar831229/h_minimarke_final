
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
 * HfosPanic
 *
 * Permite al usuario enviar un reporte de error cuando ocurre una excepción de Javascript o remota
 */
var HfosPanic = {

	_errorReport: null,

	_reported: {},

	/**
	 * Construye el cuadro de mensaje de error
	 */
	_buildCrashBox: function(html)
	{
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);
		var left = (pageSize.windowWidth - 600 - windowScroll.left) / 2;
		var numberReports = document.body.select('div.crash-report').length;
		if (numberReports > 5) {
			new HfosModal.alert({
				title: 'CrashReport',
				message: 'Se ha producido un problema en la aplicación. El asistente de envío de errores ha dejado de funcionar.'
			});
			return null;
		} else {
			var crashElement = document.createElement('DIV');
			crashElement.addClassName('crash-report');
			crashElement.update(html);
			crashElement.setStyle({
				'left': left+'px'
			});
			document.body.appendChild(crashElement);
			return crashElement;
		}
	},

	/**
	 * Muestra un cuadro de dialogo informando al usuario que se ha producido un error
	 */
	launchCrashReport: function(errorReport, wasReported)
	{
		HfosPanic._errorReport = errorReport;
		Hfos.getUI().showScreenShadow(0.2);
		if (wasReported === null) {
			new HfosAjax.Request('socorro/crash', {
				onSuccess: function(transport){
					if(transport.responseText!=''){
						var crashElement = HfosPanic._buildCrashBox(transport.responseText);
						if(crashElement!==null){
							var sendButton = crashElement.selectOne('input#sendButton');
							if(sendButton!==null){
								sendButton.observe('click', HfosPanic._sendReport);
							};
							var missButton = crashElement.selectOne('input#missButton');
							if(missButton!==null){
								missButton.observe('click', HfosPanic._closeCrashReport);
							}
						}
					}
				}
			});
		} else {
			new HfosAjax.Request('socorro/wasCrash', {
				onSuccess: function(transport) {
					if (transport.responseText != '') {
						var crashElement = HfosPanic._buildCrashBox(transport.responseText);
						if (crashElement!==null) {
							var missButton = crashElement.selectOne('input#missButton');
							missButton.observe('click', HfosPanic._closeCrashReport);
						}
					}
				}
			});
		}
	},

	/**
	 * Realiza el envio de un correo de manera silenciosa
	 */
	silentCrashReport: function(e){
		/*if(e !== null){
			var checksum = hex_md5(e).toLowerCase();
			if(typeof HfosPanic._reported[checksum] == "undefined"){
				new HfosAjax.JsonRequest('socorro/sendReport', {
					parameters: 'report='+Base64.encode(e),
				});
			}
		}*/
	},

	/**
	 * Realiza el envio de un correo cuando el problema se produce del lado del cliente
	 */
	_sendReport: function()
	{

		var crashElement = document.body.getElement('crash-report');
		crashElement.selectOne('div#sendMessage').show();
		crashElement.selectOne('div#buttonPanel').hide();

		if (HfosPanic._errorReport !== null) {
			var checksum = hex_md5(HfosPanic._errorReport).toLowerCase();
			if(typeof HfosPanic._reported[checksum] == "undefined"){
				new HfosAjax.JsonRequest('socorro/sendReport', {
					parameters: 'report=' + Base64.encode(HfosPanic._errorReport),
					onSuccess: function(response, transport){

						var reported = transport.getResponseHeader('X-Socorro-Reported');
						if (reported == 'yes') {
							crashElement.selectOne('div#sendMessage').update('Gracias!');
							window.setTimeout(function(){
								HfosPanic._closeCrashReport();
							}, 2000);
						} else {

							crashElement.selectOne('div#problemDescription').hide();
							crashElement.selectOne('div#sendMessage').hide();

							var reason = transport.getResponseHeader('X-Socorro-Reason');
							if(reason=='email-problem'){

								var file = transport.getResponseHeader('X-Socorro-File');
								crashElement.selectOne('a#logFile').href = file;

								crashElement.selectOne('div#emailDescription').show();
							} else {
								crashElement.selectOne('div#unknownDescription').show();
							};

							crashElement.selectOne('div#buttonPanel').show();
							var sendButton = crashElement.selectOne('input#sendButton');
							if(sendButton!==null){
								sendButton.hide();
							};

							var missButton = crashElement.selectOne('input#missButton');
							if(missButton!==null){
								missButton.setValue('Cerrar');
							}

						}
					}
				});
				HfosPanic._reported[checksum] = true;
			} else {
				crashElement.selectOne('div#sendMessage').update('Ya se había informado');
				window.setTimeout(function(){
					HfosPanic._closeCrashReport();
				}, 2000);
			}
		}
	},

	/**
	 * Cierra el cuadro de dialogo del error
	 */
	_closeCrashReport: function(){
		HfosPanic._errorReport = null;
		var crashElement = document.body.getElement('crash-report');
		crashElement.erase();
		Hfos.getUI().hideScreenShadow();
	}

};