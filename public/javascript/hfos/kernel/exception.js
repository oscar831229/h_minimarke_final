
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

var HfosException = {

	/**
	 * Muestra en pantalla una excepción remota
	 */
	showRemote: function(transport)
	{
		var exception = transport.responseText;
		if (exception) {
			if (exception.substring(0, 1) == '{') {
				var coreException = Json.decode(exception);
				exception = '<b>JsonRemoteException > '+coreException.type+':</b> ';
				exception += coreException.message+'<br>';
				if (typeof coreException.trace != "undefined"){
					for(var i = 0, length=coreException.trace.length; i<length; i++) {
						if (typeof coreException.trace[i].file != "undefined") {
							exception+= coreException.trace[i].file + '(' + coreException.trace[i].line + ')<br>'
						}
					}
				}
			} else {
				if (exception == '') {
					if (typeof transport.getAllHeaders == "function") {
						exception = '<pre>' + transport.getAllHeaders() + '</pre>' + exception;
					} else {
						exception = 'No hay información del error';
					}
				}
			};
			try {
				if (Hfos.getMode() == 'production') {
					if (typeof exception != 'undefined') {
						if (exception) {
							var reported = transport.getResponseHeader('X-Socorro-Reported');
							var reason = transport.getResponseHeader('X-Socorro-Reason');
							HfosPanic.launchCrashReport(exception, reported, reason);
						}
					}
				} else {
					var debug = $('debugFailure');
					if (debug.innerHTML == '') {
						debug.update('<b>Kernel Panic</b><br/>' + exception);
					} else {
						debug.innerHTML += exception;
					};
					debug.show();
				}
			}
			catch(e){
				alert(e)
			}
		}
	},

	/**
	 * Muestra en pantalla una excepción Javascript
	 *
	 * @param {Object} e
	 * @param {Object=} transport
	 */
	show: function(e, transport)
	{
		var callstack = [];
		if (e.stack) {
			var lines = e.stack.split("\n");
			for (var i = 0, length = lines.length; i < length; i++) {
				callstack.push(lines[i]);
			}
		};
		if (Hfos.getMode() == 'production') {
			if (typeof transport != 'undefined') {
				if (transport.responseText.include('Allowed memory')) {
					new HfosModal.alert({
						title: 'Memoria Insuficiente',
						message: 'El servidor no tiene memoria suficiente para completar el proceso requerido. '+
							'Si es un reporte intente generarlo a otro formato. Si es un proceso intente ejecutarlo cuando no hayan '+
							'otros usuarios trabajando'
					});
					return;
				};
			}
			var extra = '';
			if (typeof Hfos.toSource == "function") {
				extra = Hfos.toSource();
			};
			if (typeof transport != "undefined") {
				var reported = transport.getResponseHeader('X-Socorro-Reported');
				var reason = transport.getResponseHeader('X-Socorro-Reason');
				HfosPanic.launchCrashReport(e + ' ' + callstack.join("\n") + ' ' + transport.responseText+"\n"+extra, reported, reason);
			} else {
				HfosPanic.launchCrashReport(e + ' ' + callstack.join("\n") + "\n" + extra, null, null);
			}
		} else {
			var message = '<pre>Exception: ' + e + ' ' + callstack.join("\n") + '</pre>';
			$('debugException').show();
			$('debugException').innerHTML += message;
			if (typeof transport != "undefined") {
				$('debugException').innerHTML += transport.responseText;
			};
			$('debugException').innerHTML += '<div align="right"><a href="#" onclick="$(\'debugException\').update(\'\')">[Clear]</a></div>';
		}
	},

	/**
	 * Reporta un error de manera silenciosa
	 *
	 * @param {Object} e
	 */
	showSilent: function(e) {
		if (Hfos.getMode() == 'production') {
			HfosPanic.silentCrashReport(e);
		} else {
			var callstack = [];
			if (e.stack) {
				var lines = e.stack.split("\n");
				for (var i = 0, length = lines.length; i < length; i++) {
					callstack.push(lines[i]);
				}
			};
			var message = '<pre>SilentException: ' + e + ' ' + callstack.join("\n") + '</pre>';
			$('debugException').show();
			$('debugException').innerHTML+=message;
			if (typeof transport != "undefined") {
				$('debugException').innerHTML += transport.responseText;
			};
			$('debugException').innerHTML += '<div align="right"><a href="#" onclick="$(\'debugException\').update(\'\')">[Clear]</a></div>';
		}
	},

	/**
	 * Muestra en pantalla un error ó excepción que no fue capturada
	 */
	showToDebug: function(message, url, line){
		if(typeof url != "undefined"){
			if(Hfos.getMode()=='production'){
				if(url.include('cloudfront')){
					return false;
				};
				if(message.include("too much recursion")){
					return false;
				};
				if(message.include("Script error.")){
					return false;
				};
				var extra = '';
				if(typeof Hfos.toSource == "function"){
					extra = Hfos.toSource();
				};
				HfosPanic.launchCrashReport('UncaughtException: '+message+' '+url+'@'+line+"\n"+extra, null, null);
			} else {
				var message = '<pre>UncaughtException: '+message+' '+url+'@'+line+'</pre>';
				$('debugException').show();
				$('debugException').innerHTML+=message;
				$('debugException').innerHTML+='<a href="#" onclick="$(\'debugException\').update(\'\')">[Clear]</a>';
			}
		};
	},

	/**
	 * Muestra en pantalla un error ó excepción que no fue capturada
	 */
	showWorkerError: function(error)
	{
		var stack = '';
		var callstack = [];
		if (error.stack) {
			var lines = error.stack.split("\n");
			for (var i=0, length=lines.length; i<length; i++) {
				callstack.push(lines[i]);
			};
			stack = callstack.join("\n");
		};
		if(Hfos.getMode()=='production'){
			HfosPanic.launchCrashReport('UncaughtException: '+error.message+' '+error.filename+'@'+error.line+' '+stack, null, null);
		} else {
			var message = '<pre>UncaughtException: '+error.message+' '+error.filename+'@'+error.line+' '+stack+'</pre>';
			$('debugException').show();
			$('debugException').innerHTML+=error.message;
			$('debugException').innerHTML+='<a href="#" onclick="$(\'debugException\').update(\'\')">[Clear]</a>';
		}
	}

};