
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
 * Clase cargosSocios
 *
 * Cada formulario de cargosSocios en pantalla tiene asociado una instancia de esta clase
 */
var CargosSocios = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de cargosSocios
	 */
	initialize: function(container){
		this.setContainer(container);
		var cargosSociosButton = this.getElement('importButton');
		if (cargosSociosButton) {
			cargosSociosButton.observe('click', this._cargosSocios.bind(this, cargosSociosButton));
		}
		var printButton = this.getElement('printButton');
		if (printButton) {
			printButton.observe('click', this._getReporteFacturacion.bind(this));
		}
	},

	/**
	* Abre el dialogo de tipo de reporte de facturación
	*/
	_getReporteFacturacion: function(cargosSociosForm){
		var hyperForm = this._hyperForm;
		new HfosModalForm(this, 'cargos_socios/getFormato', {
			parameters: {},
			onSubmit: function(cargosSociosForm) {
				cargosSociosForm.getElement('printButton2').disable();
				cargosSociosForm.getElement('formSpinner').show();
			}.bind(this),
			beforeClose: function(form, canceled, response){
				if(canceled==false){
					if(response.status=='OK'){
						form.getMessages().notice(response.message);
						if(typeof response.file != "undefined"){
							window.open($Kumbia.path+response.file);
						}
					}else{
						if(response.status=='FAILED'){
							form.getMessages().error(response.message);
						}
					}
				}
				form.getElement('formSpinner').hide();
				form.getElement('printButton2').enable();				
			}.bind(this)
		});
	},

	/**
	 * Genera cargos de todos los socios
 	 * @param {Object} cargosSociosButton
	*/
	_cargosSocios: function(cargosSociosButton){
		var periodo = this.selectOne('#periodo');
		
		new HfosModal.confirm({
			title: 'Generar Cargos de periodo',
			message: 'Desea recalcular todos los cargos de los socios en el periodo ('+periodo.getValue()+')?',
			onAccept: function() {
				//Seleccionamos fechas
				var hfosWindow = HfosCommon.findWindow(cargosSociosButton);
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

							new HfosAjax.JsonRequest('cargos_socios/generar', {
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
								onCreate: function() {
									this.setIgnoreTermSignal(true);
									this.getMessages().notice('Se está realizando la generacion, esto tardará algunos minutos...');
									this.getElement('headerSpinner').show();
									form.getElement('formSpinner').show();
									cargosSociosButton.disable();
								}.bind(this),
								onSuccess: function(response){
									if(response.status=='FAILED') {
										this.getMessages().error(response.message);
										if(typeof response.url != "undefined"){
											window.open($Kumbia.path+response.url);
										}
									} else {
										this.getMessages().success(response.message);
									}
								}.bind(this),
								onComplete: function(){
									this.getElement('headerSpinner').hide();
									form.getElement('formSpinner').hide();
									cargosSociosButton.enable();
									this.setIgnoreTermSignal(false);
									form.close();
								}.bind(this)
							});
						}.bind(this, form));
					}.bind(this, hfosWindow)
				});

				
			}.bind(this)
		});
	}
});

HfosBindings.late('win-cargos-socios-socios', 'afterCreateOrRestore', function(hfosWindow){
	var cargosSocios = new CargosSocios(hfosWindow);
});

