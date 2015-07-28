
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
var FacturarPersonal = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de facturar
	 */
	initialize: function(container){
		this.setContainer(container);
		var facturarButton = this.getElement('importButton');
		facturarButton.observe('click', this._facturar.bind(this, facturarButton));
		var printButton = this.getElement('printButton');
		printButton.observe('click', this._getReporteFacturacion.bind(this, printButton));
		var deleteButton = this.getElement('deleteButton');
		deleteButton.observe('click', this._delete.bind(this, deleteButton));
		
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getReporteFacturacion: function(printButton){
		var hfosWindow = HfosCommon.findWindow(printButton);
		new HfosModalForm(hfosWindow, 'facturar/selectfecha/P/P', {
			notSubmit: true,
			style: {
				'width': '550px'
			},
			afterShow: function(hfosWindow, form){
				var consultarButton = form.getElement('selectFechaButton');
				consultarButton.observe('click', function(form){
					//Socio
					var sociosId = form.selectOne('#sociosId');
					if (!sociosId.getValue()) {
						alert("Es necesario dar el socio a imprimir la factura");
						return false;
					}
					//Fechas
					var dateIni = form.selectOne('#dateIni').getValue();

					var selectFechaButton = form.getElement('selectFechaButton');
					new HfosAjax.JsonRequest('facturar_personal/reporteFactura', {
						parameters: {
							'sociosId': sociosId.getValue(),
							'dateIni': dateIni
						},
						onCreate: function(){
							this.setIgnoreTermSignal(true);
							this.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
							this.getElement('headerSpinner').show();
							form.getElement('formSpinner').show();
							printButton.disable();
							selectFechaButton.disable();
							this.getElement('importButton').disable();
							this.getElement('deleteButton').disable();
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
						onComplete: function(printButton){
							this.getElement('headerSpinner').hide();
							form.getElement('formSpinner').hide();
							printButton.enable();
							selectFechaButton.enable();
							this.getElement('importButton').enable();
							this.getElement('deleteButton').enable();
							this.setIgnoreTermSignal(false);
							form.close();
						}.bind(this, printButton)
					});
				}.bind(this, form));
			}.bind(this, hfosWindow)
		});
	},

	/**
	 * genera la factura
 	 * @param {Object} facturarButton
	 */
	_facturar: function(facturarButton){
	    var facturarForm = this.getElement('facturarPersonalForm');
	    new HfosModal.confirm({
			title: 'Facturas del Periodo',
			message: 'Desea generar la factura de este socio del actual periodo junto con su movimiento contable? Si acepta y hay movimiento antiguo esto se acumulará.',
			onAccept: function(facturarForm,facturarButton) {
				//Validamso si hay cargos por suspencion
				this.generarFactura(facturarButton,facturarForm,false)
			}.bind(this,facturarForm,facturarButton),
			onError: function() {
				this.getMessages().error('Error 1');
			}.bind(this)	
		});
	},

	/** 
	* Ejecuta el Ajax para generar la factura del socio
	*/
	generarFactura: function (facturarButton,facturarForm,suspencion){
		//Si genera cargos por suspencion
		if (!suspencion) {
			suspencion = false;
		}

		//Seleccionamos fechas
		var hfosWindow = HfosCommon.findWindow(facturarButton);
		new HfosModalForm(hfosWindow, 'facturar/selectperiodo/P', {
			notSubmit: true,
			style: {
				'width': '650px'
			},
			afterShow: function(hfosWindow, form){
				var consultarButton = form.getElement('selectPeriodoButton');
				consultarButton.observe('click', function(form){
					//Socio
					var sociosId = form.selectOne('#sociosId');
					if (!sociosId.getValue()) {
						alert("Es necesario dar el socio a imprimir la factura");
						return false;
					}
					//Fechas
					var dateIni = form.selectOne('#dateIni');
					var dateFin = form.selectOne('#dateFin');
					//Que facturar
					var sostenimiento = form.selectOne('#sostenimiento').getValue();
					var administracion = form.selectOne('#administracion').getValue();
					var novedades = form.selectOne('#novedades').getValue();
					var consumoMinimo = form.selectOne('#consumoMinimo').getValue();
					var interesesMora = form.selectOne('#interesesMora').getValue();
					var ajusteSostenimiento = form.selectOne('#ajusteSostenimiento').getValue();

					new HfosAjax.JsonRequest('facturar_personal/generar', {
						parameters: {
							'sociosId': sociosId.getValue(),
							'dateIni': dateIni.getValue(),
							'dateFin': dateFin.getValue(),
							'sostenimiento': sostenimiento,
							'administracion': administracion,
							'novedades': novedades,
							'consumoMinimo': consumoMinimo,
							'interesesMora': interesesMora,
							'ajusteSostenimiento': ajusteSostenimiento
						},
						onCreate: function(){
							form.getElement('formSpinner').show();
							this.setIgnoreTermSignal(true);
							this.getMessages().notice('Se estan realizando las facturas del periodo actual, esto tardará algunos minutos...');
							this.getElement('headerSpinner').show();
							facturarButton.disable();
							consultarButton.disable();
							dateIni.disable();
							dateFin.disable();
							this.getElement('printButton').disable();
							this.getElement('deleteButton').disable();
						}.bind(this),
						onSuccess: function(response){
							if(response.status=='FAILED'){
								this.getMessages().error(response.message);
							} else {
								if(response.status=='OK'){
									this.getMessages().notice(response.message);
								}
							}
						}.bind(this),
						onComplete: function(facturarButton){
							form.getElement('formSpinner').hide();
							this.getElement('headerSpinner').hide();
							facturarButton.enable();
							consultarButton.enable();
							dateIni.enable();
							dateFin.enable();
							this.getElement('printButton').enable();
							this.getElement('deleteButton').enable();
							this.setIgnoreTermSignal(false);
							form.close();
						}.bind(this, facturarButton)
					});
				}.bind(this, form));
			}.bind(this, hfosWindow)
		});
	},
	
	/**
	 * Borrar las facturas del periodo
	 */
	_delete: function(deleteButton){
		var facturarForm = this.getElement('facturarPersonalForm');
		new HfosModal.confirm({
			title: 'Facturas del Periodo',
			message: 'Desea anular todas las facturas del periodo junto con su movimiento contable?',
			onAccept: function(facturarForm){
				var hfosWindow = HfosCommon.findWindow(deleteButton);
				new HfosModalForm(hfosWindow, 'facturar/selectfecha/D/P', {
					notSubmit: true,
					style: {
						'width': '550px'
					},
					afterShow: function(hfosWindow, form){
						var consultarButton = form.getElement('selectFechaButton');
						consultarButton.observe('click', function(form){
							//Socio
							var sociosId = form.selectOne('#sociosId');
							if (!sociosId.getValue()) {
								alert("Es necesario dar el socio a borrar la factura");
								return false;
							}

							var dateIni = form.selectOne('#dateIni').getValue();
							var selectFechaButton = form.getElement('selectFechaButton');
							
							new HfosAjax.JsonRequest('facturar_personal/borrar', {
								parameters: {
									'sociosId': sociosId.getValue(),
									'dateIni': dateIni
								},
								onCreate: function(){
									this.setIgnoreTermSignal(true);
				    				this.getMessages().notice('Se está realizando el borrado de facturas, esto tardará algunos minutos...');
									this.getElement('headerSpinner').show();
									form.getElement('formSpinner').show();
									deleteButton.disable();
									selectFechaButton.disable();
									this.getElement('importButton').disable();
									this.getElement('printButton').disable();
								}.bind(this),
								onSuccess: function(response){
									if(response.status=='FAILED'){
										this.getMessages().error(response.message);
									} else {
										this.getMessages().notice(response.message);
									}
								}.bind(this),
								onComplete: function(deleteButton){
									this.getElement('headerSpinner').hide();
									form.getElement('formSpinner').hide();
									deleteButton.enable();
									selectFechaButton.enable();
									this.getElement('importButton').enable();
									this.getElement('printButton').enable();
									this.setIgnoreTermSignal(false);
									form.close();
								}.bind(this, deleteButton)
							});
						}.bind(this, form));
					}.bind(this, hfosWindow)
				});

				/*facturarForm.setAttribute('action', $Kumbia.path+'socios/facturar_personal/borrar');
				new HfosAjax.JsonFormRequest(facturarForm, {
					onCreate: function(facturarForm){
						this.setIgnoreTermSignal(true);
						this.getMessages().notice('Se está realizando el borrado de facturas, esto tardará algunos minutos...');
						this.getElement('headerSpinner').show();
						deleteButton.disable();
						this.getElement('importButton').disable();
						this.getElement('printButton').disable();
					}.bind(this, facturarForm),
					onSuccess: function(facturarForm, response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().notice(response.message);
						}
					}.bind(this, facturarForm),
					onComplete: function(facturarForm, deleteButton){
						this.getElement('headerSpinner').hide();
						deleteButton.enable();
						this.getElement('importButton').enable();
						this.getElement('printButton').enable();
						this.setIgnoreTermSignal(false);
					}.bind(this, facturarForm, deleteButton)
				});*/
			}.bind(this, facturarForm)
		});
	    
	},

});

HfosBindings.late('win-facturar-personal-socios', 'afterCreateOrRestore', function(hfosWindow){
	var facturarPersonal = new FacturarPersonal(hfosWindow);
});
