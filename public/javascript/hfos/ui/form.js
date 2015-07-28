
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
 * HfosForm
 *
 * Envía un formulario con AJAX y devuelve su salida en JSON
 */
var HfosForm = Class.create({

	/**
	 * Contenedor de Procesos donde está el formulario
	 */
	_processContainer: null,

	/**
	 * Elemento DOM base del formulario
	 */
	_formElement: null,

	/**
	 * Opciones del formulario
	 */
	_options: null,

	/**
	 * Referencia al objeto DOM que procesa el formulario
	 */
	_submitButton: null,

	/**
	 * @constructor
	 */
	initialize: function(processContainer, formName, options)
	{

		var submitHandler = this._submitForm.bind(this);
		this._formElement = processContainer.getContainer().getElement(formName);
		if (this._formElement) {

			this._formElement.observe('beforesubmit', function(event){
				new Event.stop(event);
			});

			this._submitButton = this._formElement.getInputs('submit')[0];
			if (this._submitButton) {
				this._submitButton.observe('click', submitHandler);
			}
		}

		if(typeof options.onLoading == "undefined"){
			options.onLoading = function(){
				this._processContainer.setIgnoreTermSignal(true);
				this._formElement.disable();
				this._formElement.getElement('formSpinner').show();
			}.bind(this);

			//ICA - Contab
			options.onCreate = function(){
				this._processContainer.setIgnoreTermSignal(true);
				this._formElement.disable();
				this._formElement.getElement('formSpinner').show();
			}.bind(this);
		};

		if(typeof options.onSuccess != "undefined"){
			options.onSuccess = function(onSuccess, response){
				this._submitButton.enable();
				this._formElement.getElement('formSpinner').hide();
				onSuccess.bind(this)(response);
			}.bind(this, options.onSuccess)
		};

		if(typeof options.onComplete == "undefined"){
			options.onComplete = function(){
				this._processContainer.setIgnoreTermSignal(false);
				this._formElement.enable();
				this._formElement.getElement('formSpinner').hide();
			}.bind(this)
		};

		//options.longProcess = true;
		this._options = options;
		this._processContainer = processContainer;
		if (this._formElement) {
			this._formElement.getInputs()[0].focus();
		}
	},

	/**
	 * @this {HfosForm}
	 */
	_submitForm: function(event){
		new HfosAjax.JsonFormRequest(this._formElement, this._options);
		new Event.stop(event);
	},

	/**
	 * @this {HfosForm}
	 */
	getFormElement: function(){
		return this._formElement;
	},

	/**
	 * @this {HfosForm}
	 */
	getMessages: function(){
		return this._processContainer.getMessages();
	},

	/**
	 * @this {HfosForm}
	 */
	getElement: function(selector){
		return this._processContainer.getElement(selector);
	},

	/**
	 * @this {HfosForm}
	 */
	getContainer: function(){
		return this._processContainer;
	}

});