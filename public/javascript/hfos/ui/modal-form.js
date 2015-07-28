
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
 * HfosModalForm
 *
 * Clase para crear formularios modales
 */
var HfosModalForm = Class.create(HfosProcessContainer, {

	/**
	 * Elemento base del formulario modal
	 */
	_element: null,

	/**
	 * Opciones de la ventana modal
	 */
	_options: {},

	/**
	 * Constructor de HfosModalForm
	 *
	 * @constructor
	 */
	initialize: function(processContainer, action, options){
		this.setContainer(this);
		Hfos.getUI().showScreenShadow();
		this._options = options;
		if(typeof options.parameters == "undefined"){
			options.parameters = "";
		};
		if(typeof options.checkAcl == "undefined"){
			options.checkAcl = false;
		};
		new HfosAjax.Request(action, {
			checkAcl: options.checkAcl,
			parameters: options.parameters,
			onSuccess: function(transport){
				var windowManager = Hfos.getApplication().getWorkspace().getWindowManager();
				if(windowManager.hasModalWindow()==true){
					return;
				} else {
					windowManager.setModalWindow(this, function(){
						this._element = document.createElement('DIV');
						this._element.addClassName("window-modal");
						this._element.addClassName("formExternalContainer");
						this._element.update(transport.responseText);
						document.body.appendChild(this._element);
						if(typeof this._options.style != "undefined"){
							var formExternal = this._element.getElement('formExternal');
							formExternal.setStyle(this._options.style);
						};
						if(typeof this._options.defaults != "undefined"){
							for(var name in this._options.defaults){
								var inputElement = this._element.selectOne('#'+name);
								if(inputElement!==null){
									inputElement.setValue(this._options.defaults[name]);
								}
							}
						};
						this._messages = new HfosMessages(this);
						var notSubmit = false;
						if(typeof this._options.notSubmit != "undefined"){
							notSubmit = this._options.notSubmit;
						};
						if(notSubmit==false){
							var submitElement = this._element.selectOne('input[type="submit"]');
							if(submitElement!==null){
								submitElement.observe('click', function(submitElement, event){
									new HfosAjax.JsonFormRequest(submitElement.form, {
										onCreate: function(response) {
											if(typeof this._options.onSubmit != "undefined"){
												this._options.onSubmit(this);
											}
										}.bind(this),
										onSuccess: function(response){
											if(response.status=='FAILED'){
												this._messages.error(response.message);
												if(typeof response.fields != "undefined"){
													var focusElement = this._element.selectOne('#'+response.fields[0]);
													if(focusElement){
														focusElement.activate();
													}
												}
											} else {
												this.closeForm(false, response);
											}
										}.bind(this)
									});
									new Event.stop(event);
								}.bind(this, submitElement));
								submitElement.form.observe('beforesubmit', function(event){
									new Event.stop(event);
								});
								window.setTimeout(function(submitElement){
									submitElement.form.getInputs()[0].activate();
								}.bind(this, submitElement), 100);
							};
						}
						if (this._options.messageDefault) {
							this._messages.success(this._options.messageDefault);								
						}
						var windowClose = this._element.getElement('window-close');
						windowClose.observe('click', this.closeForm.bind(this, true));
						new Effect.Move(this._element, {
							duration: 0.5,
							y: 500
						});
						if(typeof this._options.afterShow != "undefined"){
							this._options.afterShow(this);
						}
					}.bind(this));
				};
			}.bind(this)
		});
	},

	/**
	 * Cierra el formulario y lo quita de la pantalla
	 *
	 * @this {HfosModalForm}
	 */
	closeForm: function(canceled, response){
		if(this._onCloseProcess()==true){
			if(typeof this._options.beforeClose != "undefined"){
				this._options.beforeClose(this, canceled, response);
			};
			Hfos.getApplication().getWorkspace().getWindowManager().removeModalWindow();
			this._element.erase();
			Hfos.getUI().hideScreenShadow();
		}
	},

	/**
	 * Obtiene un elemento del formulario
	 *
	 * @this {HfosModalForm}
	 */
	getElement: function(selector){
		return this._element.getElement(selector);
	},

	/**
	 * Obtiene un elemento
	 *
	 * @this {HfosModalForm}
	 */
	getContentElement: function(){
		return this._element;
	},

	/**
	 * Obtiene un grupo de elementos desde un selector en la ventana
	 *
	 * @this {HfosModalForm}
	 */
	select: function(selector){
		return this._element.select(selector);
	},

	/**
	 * Obtiene un elemento desde un selector en la ventana
	 *
	 * @this {HfosModalForm}
	 */
	selectOne: function(selector){
		return this._element.selectOne(selector);
	},

	/**
	 * Obtiene el elemento DOM de la ventana
	 *
	 * @this {HfosModalForm}
	 */
	getWindowElement: function(){
		return this._element;
	},

	/**
	 * Obtiene el título de la ventana
	 *
	 * @this {HfosModalForm}
	 */
	getTitle: function(){
		return this._element.selectOne('h1').innerHTML;
	},

	/**
	 * Se vuelve la misma ventana modal como contenedor del proceso
	 *
	 * @this {HfosModalForm}
	 */
	getContainer: function(){
		return this;
	},

	/**
	 * Notifica que hubo un cambio en el contenido de la ventana modal
	 *
	 * @this {HfosModalForm}
	 */
	notifyContentChange: function(){

	},

	/**
	 * Alias de closeForm
	 *
	 * @this {HfosModalForm}
	 */
	close: function(){
		this.closeForm();
	},

	/**
	 * Envia el evento keyup
	 *
	 * @this {HfosModalForm}
	 */
	sendKeyEvent: function(event){
		//this.fire('onKeyPress', event);
	}

});
