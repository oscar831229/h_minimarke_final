/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Back-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var Contratos = {

	/**
	 * Attributo que almacena flag si genera reporte de amortización
	 */
	_showAmortizacion: null,

	/**
	 * Attributo que almacena flag si esta cambiando de contrato
	 */
	_cambioContrato: null,

	/**
	 * Attributo que alamcena flag si esta refinanciando
	 */
	refinanciar: null,

	/**
	* Attributo que almacena la información del hyperForm actual
	*/ 
	_active: null,

	/**
	 * Cambia el attributo _active con un nuevo hyperForm
	 */
	setActive: function(hyperForm){
		Contratos._active = new Contrato(hyperForm);
	},

	/**
	 * Valida si existe o no información en attributo _active
	 */
	hasActive: function(){
		return Contratos._active!==null;
	},

	/**
	 * Obtiene el hyperForm almacenado en attributo _active
	 */
	getActive: function(){
		return Contratos._active
	},

	/**
	 * Borra valor en attributo _active
	 */
	unsetActive: function(){
		Contratos._active = null;
	},
	
	/**
	 * Cambia el valor de el attributo _refinanciar
	 */
	setRefinanciar: function(refinanciar){
		Contratos._refinanciar = refinanciar;
	},

	/**
	 * Valida si hay una refinanciación en curso
	 */
	hasRefinanciar: function(){
		return Contratos._refinanciar!==null;
	},

	/**
	 * Obteien el valor del attributo _refinanciar
	 */
	getRefinanciar: function(){
		return Contratos._refinanciar
	},

	/**
	 * Borra el contenido del attributo _refinanciar
	 */
	unsetRefinanciar: function(){
		Contratos._refinanciar = null;
	},

	/**
	 * Cambia el attributo _cambioContrato a un estado booleano
	 */
	setCambioContrato: function(status){
		Contratos._cambioContrato = status;
	},

	/**
	 * Valida si existe o no información en attributo _cambioContrato
	 */
	hasCambioContrato: function(){
		return Contratos._cambioContrato!==null;
	},

	/**
	 * Obtiene el hyperForm almacenado en attributo_cambioContrato
	 */
	getCambioContrato: function(){
		return Contratos._cambioContrato
	},

	/**
	 * Borra valor en attributo _active
	 */
	unsetCambioContrato: function(){
		Contratos._cambioContrato = null;
	},
	
	/**
	 * Cambia el attributo _showAmortizacion a un estado booleano
	 */
	setShowAmortizacion: function(status){
		Contratos._showAmortizacion = status;
	},

	/**
	 * Valida si existe o no información en attributo _showAmortizacion
	 */
	hasShowAmortizacion: function(){
		return Contratos._showAmortizacion!==null;
	},

	/**
	 * Obtiene el hyperForm almacenado en attributo _showAmortizacion
	 */
	getShowAmortizacion: function(){
		return Contratos._showAmortizacion
	},

	/**
	 * Borra valor en attributo _showAmortizacion
	 */
	unsetShowAmortizacion: function(){
		Contratos._showAmortizacion = null;
	}
};

/**
 * Clase controladora de HyperForm de Contratos
 */
var Contrato = Class.create({

	_hyperForm: null,
	
	_refinanciar: null,

	/**
	 * Constructor de Contrato
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		hyperForm.observe('beforeInput', this._addValidations.bind(this));
		hyperForm.observe('beforeRecordPreview', this._addCambiarContratoButton.bind(this));  
		hyperForm.observe('beforeRecordPreview', this._addDesistirButton.bind(this));  
		hyperForm.observe('beforeRecordPreview', this._addRetomaButton.bind(this));  
		hyperForm.observe('beforeRecordPreview', this._addRefinanciarButton.bind(this));
		hyperForm.observe('afterSendForm', this._mostrarAmortizacion.bind(this)); 
		//Validamos si existe activar reserva en curso
		if(Reservas.hasActive()==true){
			hyperForm.observe('beforeInput', function(){
				if(Reservas.hasActive()==true){
					Reservas.getActive().loadData(this);
					Reservas.unsetActive();
				}
			}.bind(hyperForm));
			hyperForm.externalProcedureCall('new');
		}
	},

	/**
	 * Muestra el reporte de amortización despues de guardar un contrato
	 * soloc uando permita editar un contrato o uno nuevo
	 */
	_mostrarAmortizacion: function(hyperForm, response){
		var state = this._hyperForm.getCurrentState();
		if(response.status=='OK' && (state=='edit'||state=='new')){
			Contratos.setShowAmortizacion(true);
			this._hyperForm.observe('beforeRecordPreview',this._getReporteAmortizacion.bind(this));
		}
	},

	/**
	* Calcula las cuotas iniciales al crear un contrato en memebresia
	*/
	_getCuotaInicial: function(){
		var hyperForm = this._hyperForm
		var activeSection = this._hyperForm.getActiveSection();
		var valorTotal = activeSection.selectOne('#valor_total');
		var cuotaInicial = activeSection.selectOne('#cuota_inicial');
		if(valorTotal.getValue()){
			new HfosAjax.JsonRequest('contratos/getCuotaInicial', {
				parameters: {
					'valorTotal'	: valorTotal.getValue(),
					'cuotaInicial'	: cuotaInicial.getValue()
				},
				onSuccess: function(response){
					if(response.status=='FAILED'){
						hyperForm.getMessages().error(response.message);
						var valorTotal 		= activeSection.selectOne('#valor_total');
						var cuotaInicial 	= activeSection.selectOne('#cuota_inicial');
						var saldoPagar		= activeSection.selectOne('#saldo_pagar');
						cuotaInicial.setValue(0);
						saldoPagar.setValue(valorTotal.getValue());
					}else{
						var cuotaInicial 	= activeSection.selectOne('#cuota_inicial');
						if(cuotaInicial){
							cuotaInicial.setValue(response.cuotaInicial)
						}
						var saldoPagar= activeSection.selectOne('#saldo_pagar');
						if(saldoPagar){
							saldoPagar.setValue(response.saldoPagar)
						}
					}
					var hoy = activeSection.selectOne('#hoy');
					if(hoy){
						hoy.setValue(cuotaInicial.getValue());
					}
				}
			});
		}
	},

	/**
	* Metodo en crear contrato para calcular punto s de membresia
	*/
	_getPuntos: function(){
		var hyperForm 		= this._hyperForm
		var activeSection 	= this._hyperForm.getActiveSection();
		var puntosAno 		= activeSection.selectOne('#puntos_ano');
		var numeroAno 		= activeSection.selectOne('#numero_anos');
		if(puntosAno.getValue() && numeroAno.getValue()){
			new HfosAjax.JsonRequest('contratos/getPuntos', {
				parameters: {
					'puntosAno': puntosAno.getValue(),
					'numeroAno': numeroAno.getValue()
				},
				onSuccess: function(response){
					if(response.status=='FAILED'){
						hyperForm.getMessages().error(response.message);
					}else{
						var totalPuntos = activeSection.selectOne('#total_puntos');
						if(totalPuntos){
							totalPuntos.setValue(response.totalPuntos)
						}
					}
				}
			});
		}
	},
	
	/**
	* Abre el dialogo de tipo de reporte de amortización
	*/
	_getReporteAmortizacion: function(hyperForm, response){
		var hyperForm 		= this._hyperForm
		var activeSection 	= this._hyperForm.getActiveSection();
		var sociosIdField 	= activeSection.selectOne('#id');
		var sociosId = 0;
		if(!sociosIdField && response && response.data){
			//si existe un valor en contrato asociado es un cambio de contrato cogemos ese para la amortización
			var contratoAsociado = response.data.last().value;
			if(contratoAsociado){
				contratoAsociadoArray = contratoAsociado.split('/');
				contratoAsociadoId = parseInt(contratoAsociadoArray[0]);
				sociosId = contratoAsociadoId;
			}else{
				//alert($H(response.data.last()).inspect());
				sociosId = response.data[0].value;
			}
		}else{
			sociosId = sociosIdField.getValue();
		}
		if(sociosId>0 && Contratos.getShowAmortizacion()==true){
			new HfosModalForm(this, 'contratos/getFormato', {
				parameters: {
					'sociosId': sociosId,
				},
				beforeClose: function(sociosId, form, canceled, response){
					if(canceled==false){
						if(response.status=='OK'){
							if(typeof response.file != "undefined"){
								Contratos.setShowAmortizacion(false);
								window.open($Kumbia.path+response.file);
							}
						}else{
							if(response.status=='FAILED'){
								this._hyperForm.getMessages().error(response.message);
							}
						}
					}
				}.bind(this, sociosId)
			});
			Contratos.unsetShowAmortizacion();
		}
	},

	/**
	 * Add validations on sheets
	 */
	_addValidations: function(){
		alert('validando');
		var activeSection = this._hyperForm.getActiveSection();
		//Puntos
		var puntosAno = activeSection.selectOne('#puntos_ano');
		var numeroAno = activeSection.selectOne('#numero_anos');
		puntosAno.observe('change', this._getPuntos.bind(this));
		numeroAno.observe('change', this._getPuntos.bind(this));
		//Memebresia
		var valorTotal = activeSection.selectOne('#valor_total');
		var cuotaInicial = activeSection.selectOne('#cuota_inicial');
		valorTotal.observe('change', this._getCuotaInicial.bind(this));
		cuotaInicial.observe('change', this._getCuotaInicial.bind(this));
		//agregamos observer al imprimir amortizacion
		var activeSection = this._hyperForm.getActiveSection();	   
		var amortizacionButton = activeSection.selectOne('#amortizacionButton');
		if(amortizacionButton){
			amortizacionButton.observe('click', this._amortizacionShow.bind(this));
		}
	},
	
	/**
	 * Metodo que imprime la amortizacion en la pestaña amortizacion
	 */
	_amortizacionShow: function(){
		Contratos.setShowAmortizacion(true);
		this._getReporteAmortizacion(false);
	},
	
	/**
	* Abre el dialogo de cambio de contrato
	*/ 
	_abrirCambioContrato: function(record){
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var sociosId = record.getValueFromName("id");
		if(sociosId){
			new HfosModalForm(this, 'contratos/getCambioContrato', {
				parameters: {
					'sociosId': sociosId,
				},
				beforeClose: function(sociosId, form, canceled, response){
					if(canceled==false){
						if(response.status=='OK'){
							if(typeof response.message != "undefined"){
								Contratos.setCambioContrato(true);
								this._hyperForm.getMessages().success(response.message);
								this._editCambioContrato(response);
							}
						}else{
							if(response.status=='FAILED'){
								this._hyperForm.getMessages().error(response.message);
							}
						}
					}
				}.bind(this, sociosId)
			});
		}else{
			alert('No encontro codigo');
		}
	},
	/**
	 * Metodo que calcula la cuota de interes recomendada para un cambio de contrato
	 */
	_getCuotaInicialCambioContrato: function(){
		var hyperFormContratos = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var sociosIdField = activeSection.selectOne('#id');
		if(sociosIdField && sociosIdField.getValue()){
			new HfosAjax.JsonRequest('contratos/getCuotaInicialCambioContrato', {
				parameters: 'id='+sociosIdField.getValue(),
				onSuccess: function(response){
					if(response.status=='FAILED'){
						hyperFormContratos.getMessages().error(response.message);
					}else{
						var cuotaInicial = activeSection.selectOne('#cuota_inicial');
						if(cuotaInicial){
							cuotaInicial.setValue(response.valor)
						}
						var saldoPagar = activeSection.selectOne('#saldo_pagar');
						if(saldoPagar){
							saldoPagar.setValue(response.saldoPagar)
						}
						var hoy = activeSection.selectOne('#hoy');
						if(hoy){
							hoy.setValue(response.valor)
						}
						var cuota2 = activeSection.selectOne('#cuota2');
						if(cuota2){
							cuota2.setValue(0)
						}
						var cuota3 = activeSection.selectOne('#cuota3');
						if(cuota3){
							cuota3.setValue(0)
						}
					}
				}
			});
		}
	},
	/**
	 * Al editar un contrato por medio de cambio de contrato añade campo hidden y label nuevo
	 */
	_editCambioContrato: function(response){
		var hyperFormContratos = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var editButton = this._hyperForm.getElement('editButton');
		if(editButton!==null){
			this._hyperForm.observe('beforeInput', function(response,event){
				//Validamos que si esta en cambio de contrato
				if(Contratos.getCambioContrato()==true){
					hyperFormContratos.getMessages().notice('Cambiando Contrato');
					hyperFormContratos.addFieldLabelInput('nuevo_contrato', 'Nuevo Contrato', response.tipoContratoId, 'before', response.tipoContratoNombre);
					var tipoContratoIdField = hyperFormContratos.getActiveSection().selectOne('#tipo_contrato_id');
					if(tipoContratoIdField){
						tipoContratoIdField.setAttribute('disabled','true');
						tipoContratoIdField.disable();
					}
					var fechaCompraMField = hyperFormContratos.getActiveSection().selectOne('#fecha_compraMonth');
					var fechaCompraYField = hyperFormContratos.getActiveSection().selectOne('#fecha_compraYear');
					var fechaCompraDField = hyperFormContratos.getActiveSection().selectOne('#fecha_compraDay');
					if(fechaCompraMField){
						fechaCompraMField.setAttribute('disabled','true');
						fechaCompraMField.disable();
						fechaCompraYField.setAttribute('disabled','true');
						fechaCompraYField.disable();
						fechaCompraDField.setAttribute('disabled','true');
						fechaCompraDField.disable();
					}
					this._getCuotaInicialCambioContrato();
					hyperFormContratos.observe('beforeRecordPreview', function(){
						Contratos.unsetCambioContrato();
					}.bind(this));
				}
			}.bind(this,response));
			this._hyperForm.fire('onEdit');
			editButton.click();
		}else{
			alert('No encontro editButton')
		}
	},
	
	/**
	 * Agrega el botton de cambio de contrato
	 */
	_addCambiarContratoButton: function(eventName, response){
		this._hyperForm.removeControlButton("reopenButton");
		var activeSection = this._hyperForm.getActiveSection();
		var record = new HyperRecordData(response.data);
		var estadoContrato = record.getValueFromName("estado_contrato");
		if(estadoContrato=="Activo"){
			this._hyperForm.addControlButton({
				className: "copyButton",
				value: "Cambio de Contrato",
				onClick: this._abrirCambioContrato.bind(this, record)
			});
		}
	},
	
	/**
	 * Agrega el boton Desistir
	 */
	_addDesistirButton: function(eventName, response){
		this._hyperForm.removeControlButton("deleteButton");
		this._hyperForm.removeControlButton("desistirButton");
		var record = new HyperRecordData(response.data);
		var estadoContrato = record.getValueFromName("estado_contrato");
		if(estadoContrato=="Activo"){
			this._hyperForm.addControlButton({
				className: "desistirButton",
				value: "Desistir",
				onClick: this._abrirDesistir.bind(this, record)
			});
		}
	},

	/**
	* Abre la ventana con la vista para desistir a un contrato
	*/
	_abrirDesistir: function(record){
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var sociosId = record.getValueFromName("id");
		if(sociosId){
			new HfosModalForm(this, 'contratos/getDesistir', {
				parameters: {
					'sociosId': sociosId,
				},
				beforeClose: function(sociosId, form, canceled, response){
					if(canceled==false){
						if(response.status=='OK'){
							if(typeof response.message != "undefined"){
								this._hyperForm.getMessages().success(response.message);
							}
					}else{
							if(response.status=='FAILED'){
								this._hyperForm.getMessages().error(response.message);
							}
						}
					}
				}.bind(this, sociosId)
			});
		}else{
			alert('No encontro codigo');
		}  
	},

	/**
	 * Agrega el boton Retoma
	 */
	_addRetomaButton: function(eventName, response){
		this._hyperForm.removeControlButton("retomaButton");
		var record = new HyperRecordData(response.data);
		var estadoContrato = record.getValueFromName("estado_contrato");
		if(estadoContrato=="Anulado"){
			this._hyperForm.addControlButton({
				className: "retomaButton",
				value: "Retoma de Contrato",
				onClick: this._abrirRetoma.bind(this, record)
			});
		}
	},

	/**
	* Realiza una retoma a un contrato anulado
	*/
	_abrirRetoma: function(record){
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var sociosId = record.getValueFromName("id");
		if(sociosId){
			new HfosModal.confirm({
				title: 'Retoma',
				message: 'Esta seguro de retomar este contrato?',
				onAccept: function(){
					new HfosAjax.JsonRequest('contratos/retoma', {
						parameters: {
							'sociosId': sociosId
						},
						onSuccess: function(response){
							if(response.status=='FAILED'){
								hyperForm.getMessages().error(response.message);
							}else{
								hyperForm.getMessages().success(response.message);
								Contratos.setShowAmortizacion(true);
							}
						}
					});
				}.bind(this)
			});
		}else{
			alert('No encontro codigo');
		}  
	},

	/**
	 * Agrega el boton Refinanciar
	 */
	_addRefinanciarButton: function(eventName, response){
	    this._hyperForm.removeControlButton("refinanciarButton");
		var record = new HyperRecordData(response.data);
		var estadoContrato = record.getValueFromName("estado_contrato");
		if(estadoContrato=="Activo"){
			this._hyperForm.addControlButton({
				className: "refinanciarButton",
				value: "Refinanciar Contrato",
				onClick: this._addRefinanciacion.bind(this, record)
			});
		}
	},

	/**
	* Permite refinanciar contrato
	*/
	_addRefinanciacion: function(record){
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var sociosId = record.getValueFromName("id");
		if(sociosId){
			new HfosAjax.JsonRequest('contratos/validarRefinanciar', {
				parameters: {
					'sociosId': sociosId
				},
				onSuccess: function(response){
					if(response.status=='FAILED'){
						hyperForm.getMessages().error(response.message);
					}else{
						new HfosModalForm(this, 'contratos/getRefinanciar', {
							parameters: {
								'sociosId': sociosId,
							},
							beforeClose: function(sociosId, form, canceled, response){
								if(canceled==false){
									if(response.status=='OK'){
										if(typeof response.message != "undefined"){
											this._hyperForm.getMessages().success(response.message);
										}
									}else{
										if(response.status=='FAILED'){
											this._hyperForm.getMessages().error(response.message);
										}
									}
								}
							}.bind(this, sociosId)
						});
					}
				}.bind(this)
			});
		}else{
			alert('No encontro codigo');
		}  
	}
}); 

HyperFormManager.lateBinding('contratos', 'afterInitialize', function(){
	Contratos.setActive(this);
	var contrato = Contratos.getActive();
});
