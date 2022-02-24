
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

var HfosHttpReadyState = {

	/**
	 * Maneja los estados de la petición
	 *
	 * @this {HfosHttpRequest}
	 */
	onReadyStateChange: function(xmlHttpRequest, options){
		try {
			switch(xmlHttpRequest.readyState){

				case 0:
					if(typeof options.onUninitialized != "undefined"){
						options.onUninitialized(xmlHttpRequest);
					};
					break;

				case 1:
					if(typeof options.onLoading != "undefined"){
						options.onLoading(xmlHttpRequest);
					};
					break;

				case 2:
				case 3:
					if(typeof options.onInteractive != "undefined"){
						options.onInteractive(xmlHttpRequest);
					};
					break;

				case 4:

					if (xmlHttpRequest.status == 200) {
						if(typeof options.onSuccess != "undefined"){
							options.onSuccess(xmlHttpRequest);
						}
					} else {
						if (xmlHttpRequest.status >= 400 && xmlHttpRequest.status <= 599) {
							if(typeof options.onFailure != "undefined"){
								options.onFailure(xmlHttpRequest);
							}
						}
					};

					if(typeof options.onComplete != "undefined"){
						options.onComplete(xmlHttpRequest);
					};

					delete this._xmlHttpRequest
					delete this._options;
					break;
			}
		}
		catch(e){
			if(typeof options.onException != "undefined"){
				options.onException(e, xmlHttpRequest);
			} else {
				HfosException.show(e, xmlHttpRequest);
			}
		}
	}

}

var HfosHttpRequest = Class.create({

	_xmlHttpRequest: null,

	_options: {},

	/**
	 * @constructor
	 */
	initialize: function(url, options, send){
		try {

			this._xmlHttpRequest = new XMLHttpRequest();

			if(typeof options == "undefined"){
				options = {};
			};

			if(typeof options.method == "undefined"){
				options.method = "POST";
			} else {
				options.method = options.method.toUpperCase();
			};

			if(typeof options.parameters != "undefined"){
				if(typeof options.parameters != "string"){
					options.parameters = $H(options.parameters).toQueryString();
				}
			};
			if (options.method == "GET") {
				if(typeof options.parameters != "undefined"){
					url+="?"+options.parameters;
				}
			};

			if(typeof send == "undefined"){
				send = true;
			};

			this._xmlHttpRequest.open(options.method, url, true);
			this._xmlHttpRequest.onreadystatechange = HfosHttpReadyState.onReadyStateChange.bind(this, this._xmlHttpRequest, options);
			this._options = options;

			if(typeof this._options.onCreate != "undefined"){
				this._options.onCreate(this._xmlHttpRequest);
			}

			this._xmlHttpRequest.setRequestHeader("Accept", "text/html, application/xml, text/xml, */*");
			this._xmlHttpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");

			if(options.contentTypeJson){
				this._xmlHttpRequest.setRequestHeader("X-Json-Accept", "text/json");
			}

			if(options.method=="POST"){
				this._xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			};
		}
		catch(e){
			HfosException.show(e);
		};
		if(send==true){
			this.send();
		}
	},

	/**
	 * Envía la petición Http
	 *
	 * @this {HfosHttpRequest}
	 */
	send: function(){
		try {
			if(typeof this._options.parameters == "undefined"){
				this._xmlHttpRequest.send();
			} else {
				if(this._options.method=="GET"){
					this._xmlHttpRequest.send();
				} else {
					this._xmlHttpRequest.send(this._options.parameters);
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Obtiene el objeto XMLHttpRequest Nativo
	 *
	 * @this {HfosHttpRequest}
	 */
	getHttpRequest: function(){
		return this._xmlHttpRequest;
	}

});


var HfosHttpRequestUpload = Class.create({

	_xmlHttpRequest: null,

	_options: {},

	/**
	 * @constructor
	 */
	initialize: function(url, options, send){

		try {

			this._xmlHttpRequest = new XMLHttpRequest();

			if(typeof options == "undefined"){
				options = {};
			};

			if(typeof options.method == "undefined"){
				options.method = "POST";
			} else {
				options.method = options.method.toUpperCase();
			};

			// if(typeof options.parameters != "undefined"){
			// 	if(typeof options.parameters != "string"){
			// 		options.parameters = $H(options.parameters).toQueryString();
			// 	}
			// };

			if (options.method == "GET") {
				if(typeof options.parameters != "undefined"){
					url+="?"+options.parameters;
				}
			};

			if(typeof send == "undefined"){
				send = true;
			};

			this._xmlHttpRequest.open(options.method, url, true);
			this._xmlHttpRequest.onreadystatechange = HfosHttpReadyState.onReadyStateChange.bind(this, this._xmlHttpRequest, options);
			this._options = options;

			if(typeof this._options.onCreate != "undefined"){
				this._options.onCreate(this._xmlHttpRequest);
			}

			// this._xmlHttpRequest.setRequestHeader("Accept", "text/html, application/xml, text/xml, */*");
			// this._xmlHttpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");

			// if(options.contentTypeJson){
			// 	this._xmlHttpRequest.setRequestHeader("X-Json-Accept", "text/json");
			// }

			// if(options.method=="POST"){
			// 	this._xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			// };

		}
		catch(e){
			HfosException.show(e);
		};
		if(send==true){
			this.send();
		}
	},

	/**
	 * Envía la petición Http
	 *
	 * @this {HfosHttpRequest}
	 */
	send: function(){
		try {
			if(typeof this._options.parameters == "undefined"){
				this._xmlHttpRequest.send();
			} else {
				if(this._options.method=="GET"){
					this._xmlHttpRequest.send();
				} else {
					this._xmlHttpRequest.send(this._options.parameters);
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Obtiene el objeto XMLHttpRequest Nativo
	 *
	 * @this {HfosHttpRequest}
	 */
	getHttpRequest: function(){
		return this._xmlHttpRequest;
	}

});

var HfosAjax = {

	setCallbacks: function(options){
		if(typeof options == "undefined"){
			options = {};
		};
		if(typeof options.onFailure == "undefined"){
			options.onFailure = HfosAjax.onFailure;
		};
		if(typeof options.onException == "undefined"){
			options.onException = HfosAjax.onException;
		};
		return options;
	},

	/**
	 *
	 * @this {HfosHttpRequest}
	 */
	bindHtml: function(callbackName, action, options){
		if(typeof options[callbackName] != "undefined"){
			if(typeof options[callbackName].wasBinded == "undefined"){
				var checkAcl = false;
				if(typeof options.checkAcl != "undefined"){
					checkAcl = options.checkAcl;
				};
				if(checkAcl==false){
					options[callbackName] = function(callbackFunction, transport){
						callbackFunction(transport);
					}.bind(this, options[callbackName]);
				} else {
					if(!HfosWallet.has(action)){
						options[callbackName] = function(action, callbackFunction, transport){
							var applicationState = transport.getResponseHeader('X-Application-State');
							if(typeof applicationState == "null"){
								applicationState = 'OK';
							};
							if(applicationState=='OK'){
								HfosWallet.store(action, true);
								callbackFunction(transport);
							} else {
								if(applicationState && applicationState.split(', ')[0]=='Unauthorized'){
									var aclDescription = transport.getResponseHeader('X-Acl-Description');
									var aclElevation = transport.getResponseHeader('X-Acl-Elevation');
									var aclResource = transport.getResponseHeader('X-Resource');
									var aclAction = transport.getResponseHeader('X-Action');
									var aclAccessInfo = {
										elevation: aclElevation,
										description: aclDescription
									};
									HfosAcl.handleFailedCheck(aclAccessInfo, aclResource+'/'+aclAction, callbackFunction.shift(transport));
								} else {
									callbackFunction(transport);
								}
							}
						}.bind(this, action, options[callbackName]);
					} else {
						options[callbackName] = function(callbackFunction, transport){
							callbackFunction(transport);
						}.bind(this, options[callbackName]);
					}
				};
				options[callbackName].wasBinded = true;
			}
		};
		return options;
	},

	/**
	 *
	 * @this {HfosHttpRequest}
	 */
	bindJson: function(callbackName, action, options){
		options.contentTypeJson = true;
		if(typeof options[callbackName] != "undefined"){
			if(typeof options[callbackName].wasBinded == "undefined"){
				var checkAcl = false;
				if(typeof options.checkAcl != "undefined"){
					checkAcl = options.checkAcl;
				};
				if(checkAcl==false){
					options[callbackName] = function(callbackFunction, transport){
						callbackFunction(Json.decode(transport.responseText), transport);
					}.bind(this, options[callbackName]);
				} else {
					if(!HfosWallet.has(action)){
						options[callbackName] = function(action, callbackFunction, transport){
							if (!transport.responseText.trim()) {
								alert("Error: responseText is empty result");
								return false;
							}

							var response = Json.decode(transport.responseText);
							if(typeof response.status == "undefined"){
								callbackFunction(response, transport);
							} else {
								if(response.status=='SECURITY'){
									HfosAcl.handleFailedCheck(response.accessInfo, response.resource+'/'+response.action, callbackFunction.shift(response));
								} else {
									HfosWallet.store(action, true);
									callbackFunction(response, transport);
								}
							}
						}.bind(this, action, options[callbackName]);
					} else {
						options[callbackName] = function(callbackFunction, transport){
							callbackFunction(Json.decode(transport.responseText), transport);
						}.bind(this, options[callbackName]);
					}
				};
				options[callbackName].wasBinded = true;
			}
		};
		return options;
	},

	onFailure: function(transport){
		HfosException.showRemote(transport);
	},

	onException: function(e, transport){
		HfosException.show(e, transport);
	}

};

/**
 * Hacer una petición Ajax
 *
 * @this {HfosAjax}
 */
HfosAjax.Request = function(action, options){
	options = HfosAjax.setCallbacks(options);
	options = HfosAjax.bindHtml('onSuccess', action, options);
	return new HfosHttpRequest(Utils.getKumbiaURL(action), options);
};

/**
 * Hacer una petición Ajax a una aplicación
 *
 * @this {HfosAjax}
 */
HfosAjax.ApplicationRequest = function(action, options){
	if(action!==null){
		options = HfosAjax.setCallbacks(options);
		return new HfosHttpRequest(Utils.getURL(action), options);
	}
};

/**
 * Hacer una petición JSON-Ajax a una aplicación
 *
 * @this {HfosAjax}
 */
HfosAjax.JsonApplicationRequest = function(action, options){
	if(action!==null){
		options = HfosAjax.setCallbacks(options);
		options = HfosAjax.bindJson('onSuccess', action, options);
		return new HfosHttpRequest(Utils.getURL(action), options);
	}
};

/**
 * Hacer una petición Ajax que devuelve un JSON
 *
 * @constructor
 */
HfosAjax.JsonRequest = function(action, options){
	if (action !== null) {
		options = HfosAjax.setCallbacks(options);
		options = HfosAjax.bindJson('onSuccess', action, options);
		return new HfosHttpRequest(Utils.getKumbiaURL(action), options);
	}
};

/**
 * Hacer una petición Ajax que actualiza un contenedor
 *
 * @constructor
 */
HfosAjax.Updater = function(container, action, options){
	options = HfosAjax.setCallbacks(options);
	if(typeof options.onSuccess == "undefined"){
		options.onSuccess = function(container, transport){
			container.update(transport.responseText);
		}.bind(this, container);
	} else {
		options.onSuccess = function(container, onSuccess, transport){
			container.update(transport.responseText);
			onSuccess();
		}.bind(this, container, options.onSuccess);
	};
	new HfosAjax.Request(action, options);
};

/**
 * Enviar un formulario via AJAX
 *
 * @constructor
 */
HfosAjax.FormRequest = function(form, options){
	options = HfosAjax.setCallbacks(options);
	options.parameters = form.serialize();
	options.method = form.method;
	return new HfosHttpRequest(form.getAttribute('action'), options);
};

/**
 * Enviar un formulario que devuelve una salida JSON
 *
 * @constructor
 */
HfosAjax.JsonFormRequest = function(form, options){
	options = HfosAjax.setCallbacks(options);
	options = HfosAjax.bindJson('onSuccess', form.getAttribute('action'), options);
	options.parameters = form.serialize();
	options.method = form.getAttribute('method');
	if(typeof options.longProcess == "undefined"){
		return new HfosHttpRequest(form.getAttribute('action'), options);
	} else {
		var bgProcess = new HfosBgProcess();
		bgProcess.request(form.getAttribute('action'), options);
	}
};



/**
 * Enviar un formulario con archivos adjuntos que devuelve una salida JSON
 *
 * @constructor
 */
 HfosAjax.JsonFormFileRequest = function(form, options){

	// Parametros en  FormData
	var formData = new FormData(form);
	options = HfosAjax.setCallbacks(options);
	options = HfosAjax.bindJson('onSuccess', form.getAttribute('action'), options);
	options.parameters = formData;
	options.method = form.getAttribute('method');
	if(typeof options.longProcess == "undefined"){
		return new HfosHttpRequestUpload(form.getAttribute('action'), options);
	} else {
		var bgProcess = new HfosBgProcess();
		bgProcess.request(form.getAttribute('action'), options);
	}

};

/**
 * Genera un autocompleter basado en Ajax
 *
 * @constructor
 */
HfosAjax.Autocompleter = function(element, action, options){

	var completerChoices = $('completerChoices');
	if(!completerChoices){
		var completerChoices = document.createElement('DIV');
		completerChoices.addClassName('autocomplete');
		element.insert({
			after: completerChoices
		});
	};

	if(typeof options.minChars == "undefined"){
		options.minChars = 3;
	};

	options = HfosAjax.setCallbacks(options);
	return new Ajax.Autocompleter(element, completerChoices, Utils.getKumbiaURL(action), options);
};

/**
 * Sube un archivo por Ajax
 *
 * @constructor
 */
HfosAjax.UploadFile = function(fileInput, options){
	fileInput.disable();
	/*options = HfosAjax.setCallbacks(options);
	options = HfosAjax.bindJson('onSuccess', options);
	options.method = 'POST';
	var hfosHttpRequest = new HfosHttpRequest(fileInput.form.getAttribute('action'), options, false);
	var httpRequest = hfosHttpRequest.getHttpRequest();
	httpRequest.upload.addEventListener("progress", function(e){
		if(e.lengthComputable){
			var percentage = Math.round((e.loaded*100)/e.total);
			//document.title = percentage;
		} else {
			//document.title = ':(';
		}
	}, false);
	if(typeof options.onLoad != "undefined"){
		httpRequest.upload.addEventListener("load", options.onLoad, false);
	};
	httpRequest.setRequestHeader("Content-Type", "multipart/form-data");
  	httpRequest.setRequestHeader("Content-Length", fileInput.files[0].size);
  	httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  	httpRequest.setRequestHeader("X-Json-Accept", "text/json");
  	alert(typeof FormData)
 	var fd = new FormData();
  	fd.append(archivo, fileInput.files[0]);
  	httpRequest.send(body);
  	var reader = new FileReader();
	reader.onload = function(httpRequest, fileInput, event){
		httpRequest.overrideMimeType('text/plain; charset=x-user-defined-binary');
		httpRequest.sendAsBinary(event.target.result);
		fileInput.enable();
	}.bind(this, httpRequest, fileInput);
	reader.readAsBinaryString(fileInput.files[0]);*/
};