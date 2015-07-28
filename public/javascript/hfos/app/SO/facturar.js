
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
 * Selecciona todos los correos en la lista a enviar
 * @param a
 */
function checkAll(a){var b=!1;!0==a.checked&&(b=!0);a=document.getElementsByClassName("mailCheck");for(check in a)a[check].checked=b};
/**
 * Realiza Ajax que actuaiza lista a mostrar facturas a enviar por correo
 * @param a
 */
function showListToSend(periodo){
    HfosAjax.Updater(document.getElementById("listToSend"), "facturar/showFacturasToSend/" + $(periodo).getValue());
};


/**
 * Clase facturar
 *
 * Cada formulario de facturar en pantalla tiene asociado una instancia de esta clase
 */
var Facturar = {

	// Referencia al hyperGrid asociado a la Orden de Compra
	_hyperGrid: null,

	// Referencia al HyperForm
	_hyperForm: null,

	/**
	 * Se llama cada vez que se cree/edite una grilla de ordenes de compra
	 */
	onCreate: function(hyperGrid) {
		var hyperForm = hyperGrid.getHyperForm();
		this._hyperForm = hyperForm;
		this._hyperGrid = hyperGrid;
		new Factura(hyperGrid, false);
	},

	/**
	 * Se llama cada vez que se restaure una grilla de ordenes de compra
	 */
	onRestore: function(hyperGrid) {
		var hyperForm = hyperGrid.getHyperForm();
		this._hyperForm = hyperForm;
		this._hyperGrid = hyperGrid;
		new Factura(hyperGrid, true)
	}

};

var Factura = Class.create(HfosProcessContainer, {

	// Referencia al hyperGrid asociado a la Orden de Compra
	_hyperGrid: null,

	// Referencia al HyperForm
	_hyperForm: null,

	/**
	 * Constructor de Orden
	 */
	initialize: function(hyperGrid, restored){
		var hyperForm = hyperGrid.getHyperForm();
		this._hyperForm = hyperForm;
		this._hyperGrid = hyperGrid;

		this._setIndexCallbacks();
		hyperForm.observe('afterBack', this._setIndexCallbacks.bind(this));
	},

	/**
	* Callback on index action
	*/
	_setIndexCallbacks: function() {
		this._hyperForm.observe('beforeInput', this._prepareForInput.bind(this));
		var currentState = this._hyperForm.getCurrentState();
		if (currentState=='new' || currentState=='edit') {

		} else {
			this._hyperForm.getElement("newButton").hide();

			this._hyperForm.addControlButton({
				className: "import2Button",
				value: "Generar Facturas",
				onClick: this._facturar.bind(this)
			});

			this._hyperForm.addControlButton({
				className: "delete2Button",
				value: "Borrar Facturas",
				onClick: this._delete.bind(this)
			});

			this._hyperForm.addControlButton({
				className: "print2Button",
				value: "Imprimir Facturas",
				onClick: this._getReporteFacturacion.bind(this)
			});

			this._hyperForm.addControlButton({
				className: "sendButton",
				value: "Enviar Facturas",
				onClick: this._getSendFacturacion.bind(this)
			});
		}
		
	},

	_prepareForInput: function()
	{
		window.setTimeout(function(){
			var fields = ['item', 'descripcion', 'valor', 'iva', 'total'];
			this._hyperGrid.loadBaseData('facturar', fields);
		}.bind(this), 80);
	},

	/**
	 * Genera las facturas
	 */
	_facturar: function() {
		var facturarButton = this._hyperForm.getElement('import2Button');
		new HfosModal.confirm({
			title: 'Facturas del Periodo',
			message: 'Desea generar todas las facturas del periodo junto con su movimiento contable?',
			onAccept: function() {

				var hfosWindow = HfosCommon.findWindow(facturarButton);
				new HfosModalForm(hfosWindow, 'facturar/selectperiodo', {
					notSubmit: true,
					style: {
						'width': '650px'
					},
					afterShow: function(hfosWindow, form){
						var consultarButton = form.getElement('selectPeriodoButton');
						consultarButton.observe('click', function(form){
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

							new HfosAjax.JsonRequest('facturar/generar', {
								parameters: {
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
									this._hyperForm.getMessages().notice('Se estan realizando las facturas del periodo actual, esto tardará algunos minutos...');
									this._hyperForm.getElement('hyToolbarSpinner').show();
									facturarButton.disable();
									consultarButton.disable();
									dateIni.disable();
									dateFin.disable();
									this._hyperForm.getElement('print2Button').disable();
									this._hyperForm.getElement('delete2Button').disable();
								}.bind(this),
								onSuccess: function(response){
									if(response.status=='FAILED'){
										this._hyperForm.getMessages().error(response.message);
									} else {
										if(response.status=='OK'){
											this._hyperForm.getMessages().notice(response.message);
										}
									}
								}.bind(this),
								onComplete: function(facturarButton){
									form.getElement('formSpinner').hide();
									this._hyperForm.getElement('hyToolbarSpinner').hide();
									facturarButton.enable();
									consultarButton.enable();
									dateIni.enable();
									dateFin.enable();
									this._hyperForm.getElement('print2Button').enable();
									this._hyperForm.getElement('delete2Button').enable();
									this.setIgnoreTermSignal(false);
									form.close();
								}.bind(this, facturarButton)
							});
						}.bind(this, form));
					}.bind(this, hfosWindow)
				});
			}.bind(this)
		});
	},

	/**
	 * Borrar las facturas del periodo
	 */
	_delete: function(){
		var deleteButton = this._hyperForm.getElement('delete2Button');
		new HfosModal.confirm({
			title: 'Facturas del Periodo',
			message: 'Desea anular todas las facturas del periodo junto con su movimiento contable?',
			onAccept: function(){
				var hfosWindow = HfosCommon.findWindow(deleteButton);
				new HfosModalForm(hfosWindow, 'facturar/selectfecha/D', {
					notSubmit: true,
					style: {
						'width': '550px'
					},
					afterShow: function(hfosWindow, form){
						var consultarButton = form.getElement('selectFechaButton');
						consultarButton.observe('click', function(form){
							var dateIni = form.selectOne('#dateIni').getValue();
							var selectFechaButton = form.getElement('selectFechaButton');
							
							new HfosAjax.JsonRequest('facturar/borrar', {
								parameters: {
									'dateIni': dateIni
								},
								onCreate: function(){
									this.setIgnoreTermSignal(true);
				    				this._hyperForm.getMessages().notice('Se está realizando el borrado de facturas, esto tardará algunos minutos...');
									this._hyperForm.getElement('hyToolbarSpinner').show();
									form.getElement('formSpinner').show();
									deleteButton.disable();
									selectFechaButton.disable();
									this._hyperForm.getElement('import2Button').disable();
									this._hyperForm.getElement('print2Button').disable();
								}.bind(this),
								onSuccess: function(response){
									if(response.status=='FAILED'){
										this._hyperForm.getMessages().error(response.message);
									} else {
										this._hyperForm.getMessages().notice(response.message);
									}
								}.bind(this),
								onComplete: function(deleteButton){
									this._hyperForm.getElement('hyToolbarSpinner').hide();
									form.getElement('formSpinner').hide();
									deleteButton.enable();
									selectFechaButton.enable();
									this._hyperForm.getElement('import2Button').enable();
									this._hyperForm.getElement('print2Button').enable();
									this.setIgnoreTermSignal(false);
									form.close();
								}.bind(this, deleteButton)
							});
						}.bind(this, form));
					}.bind(this, hfosWindow)
				});
			}.bind(this)
		});
	},

	/**
	* Se envia facturas a correos electronicos
	*/
	_getSendFacturacion: function() {
		var sendButton = this._hyperForm.getElement('sendButton');
		new HfosModalForm(this, 'facturar/sendCorreo', {
			parameters: {},
			style: 'width: 70%;',
			beforeClose: function(form, canceled, response){
				if (canceled==false) {
					if (response.status=='OK') {
						if (typeof response.message != "undefined") {
							this._hyperForm.getMessages().success(response.message);
						}
						this.close();
					} else {
						if (response.status=='FAILED') {
							this._hyperForm.getMessages().error(response.message);
						}
					}
				}
			}.bind(this)
		});
	},

	_getReporteFacturacion: function(){
		var printButton = this._hyperForm.getElement('print2Button');
		var hfosWindow = HfosCommon.findWindow(printButton);
		new HfosModalForm(hfosWindow, 'facturar/selectfecha/P', {
			notSubmit: true,
			style: {
				'width': '550px'
			},
			afterShow: function(hfosWindow, form){
				var consultarButton = form.getElement('selectFechaButton');
				consultarButton.observe('click', function(form){
					var dateIni = form.selectOne('#dateIni').getValue();
					var selectFechaButton = form.getElement('selectFechaButton');
					new HfosAjax.JsonRequest('facturar/reporteFactura', {
						parameters: {
							'dateIni': dateIni
						},
						onCreate: function(){
							this.setIgnoreTermSignal(true);
							this._hyperForm.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
							this._hyperForm.getElement('hyToolbarSpinner').show();
							form.getElement('formSpinner').show();
							printButton.disable();
							selectFechaButton.disable();
							this._hyperForm.getElement('import2Button').disable();
							this._hyperForm.getElement('delete2Button').disable();
						}.bind(this, printButton),
						onSuccess: function(response){
							if(response.status=='FAILED'){
								this._hyperForm.getMessages().error(response.message);
							} else {
								if(typeof response.file != "undefined"){
									window.open($Kumbia.path+response.file);
								}
								this._hyperForm.getMessages().success(response.message);
							}
						}.bind(this),
						onComplete: function(printButton){
							this._hyperForm.getElement('hyToolbarSpinner').hide();
							form.getElement('formSpinner').hide();
							printButton.enable();
							selectFechaButton.enable();
							this._hyperForm.getElement('import2Button').enable();
							this._hyperForm.getElement('delete2Button').enable();
							this.setIgnoreTermSignal(false);
							form.close();
						}.bind(this, printButton)
					});
				}.bind(this, form));
			}.bind(this, hfosWindow)
		});
	},

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm ordenes
HyperFormManager.lateGridBinding('facturar', 'afterInitialize', Facturar.onCreate);
HyperFormManager.lateGridBinding('facturar', 'afterRestore', Facturar.onRestore);
HyperFormManager.lateGridBinding('facturar', 'afterDetails', Facturar.onRestore);
