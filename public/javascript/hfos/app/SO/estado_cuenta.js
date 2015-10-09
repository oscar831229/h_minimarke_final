
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
 * Clase facturar
 *
 * Cada formulario de facturar en pantalla tiene asociado una instancia de esta clase
 */
var EstadoCuenta = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de facturar
	 */
	initialize: function(container){
		this.setContainer(container);
		var sendButton = this.getElement('sendButton');
		sendButton.observe('click', this._getSend.bind(this, sendButton));
		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._getGenerar.bind(this, saveButton));
		var printButton = this.getElement('printButton');
		printButton.observe('click', this._getReporte.bind(this, printButton));
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getGenerar: function(printButton){
		var sociosIdField = this.selectOne('#socios_id');
		var facturarForm = this.getElement('estadoCuentaForm');
		new HfosModal.confirm({
			title: 'Generar Esatdos de Cuenta',
			message: 'Desea generar los estados de cuenta?',
			onAccept: function() {

				facturarForm.setAttribute('action', $Kumbia.path+'socios/estado_cuenta/verificar');
				new HfosAjax.JsonFormRequest(facturarForm, {
					onCreate: function(facturarForm){
						this.setIgnoreTermSignal(true);
						this.getMessages().notice('Se está verificando estados de cuenta, esto tardará algunos minutos...');
						this.getElement('headerSpinner').show();
						facturarForm.disable();
						this.getElement('printButton').disable();
						this.getElement('sendButton').disable();
						this.getElement('saveButton').disable();
					}.bind(this, printButton),
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							if (response.count>0) {
								//Validamos si borra o deja anterior
								new HfosModal.confirm({
									title: 'Generar Esatdos de Cuenta',
									message: 'Parece que existe(n) un(os) estado de cuenta igual con la misma fecha. Desea reemplazarlo(s)?(Total: '+response.count+')',
									onAccept: function() {
										this._generarEstadosCuenta(facturarForm, response.count);
									}.bind(this),
									onRemove: function() {
										alert("salio");
									}
								});
							} else {
								this._generarEstadosCuenta(facturarForm, response.count);
							}	
						}
					}.bind(this),
					onComplete: function(facturarForm, printButton){
						this.getElement('headerSpinner').hide();
						facturarForm.enable();
						printButton.enable();
						this.getElement('sendButton').enable();
						this.getElement('printButton').enable();
						this.getElement('saveButton').enable();
						this.setIgnoreTermSignal(false);
					}.bind(this, facturarForm, printButton)
				});
			}.bind(this)
		});
	},

	/**
	* Genera Estado de ceunta segun action
	*/
	_generarEstadosCuenta: function(facturarForm, reemplazar){
		//Si existe un estado de cuenta este mes
		if (reemplazar==true) {
			var action = $Kumbia.path+'socios/estado_cuenta/generar/1';				
		} else {
			var action = $Kumbia.path+'socios/estado_cuenta/generar';
		}

		if (!facturarForm) {
			alert("Formulario no encontrado");
			return false;
		}
		
		facturarForm.setAttribute('action', action);

		//Generamos estados de cuenta con borrar o sin borrar anteriores
		new HfosAjax.JsonFormRequest(facturarForm, {
			onCreate: function(facturarForm){
				this.setIgnoreTermSignal(true);
				this.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				//facturarForm.disable();
				this.getElement('printButton').disable();
				this.getElement('sendButton').disable();
				this.getElement('saveButton').disable();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					this.getMessages().success(response.message);
				}
			}.bind(this),
			onComplete: function(facturarForm){
				this.getElement('headerSpinner').hide();
				//facturarForm.enable();
				this.getElement('sendButton').enable();
				this.getElement('printButton').enable();
				this.getElement('saveButton').enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, facturarForm)
		});
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getReporte: function(printButton){
		var sociosIdField = this.selectOne('#socios_id');
		var facturarForm = this.getElement('estadoCuentaForm');
		facturarForm.setAttribute('action', $Kumbia.path+'socios/estado_cuenta/reporte');
		new HfosAjax.JsonFormRequest(facturarForm, {
			onCreate: function(facturarForm){
				this.setIgnoreTermSignal(true);
				this.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
				this.getElement('headerSpinner').show();
				facturarForm.disable();
				this.getElement('printButton').disable();
				this.getElement('sendButton').disable();
				this.getElement('saveButton').disable();
			}.bind(this, printButton),
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
			onComplete: function(facturarForm, printButton){
				this.getElement('headerSpinner').hide();
				facturarForm.enable();
				printButton.enable();
				this.getElement('sendButton').enable();
				this.getElement('printButton').enable();
				this.getElement('saveButton').enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, facturarForm, printButton)
		});
	},

	/**
	* Se envia facturas a correos electronicos
	*/
	_getSend: function(sendButton){
		var facturarForm = this.getElement('facturarForm');
		new HfosModalForm(this, 'estado_cuenta/sendCorreo', {
			parameters: {},
			style: 'width: 50%;',
			beforeClose: function(form, canceled, response){
				if(canceled==false){
					if(response.status=='OK'){
						if(typeof response.message != "undefined"){
							//this._hyperForm.getMessages().success(response.message);
							this.getMessages().success(response.message);
						}
					}else{
						if(response.status=='FAILED'){
							//this._hyperForm.getMessages().error(response.message);
							this.getMessages().error(response.message);
						}
					}
				}
			}.bind(this)
		});
	},

});

HfosBindings.late('win-estado-cuenta-socios', 'afterCreateOrRestore', function(hfosWindow){
	var estadoCuenta = new EstadoCuenta(hfosWindow);
});
