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

var Reservas = {

	_active: null,

	setActive: function(hyperForm){
		Reservas._active = new Reserva(hyperForm);
	},

	hasActive: function(){
		return Reservas._active!==null;
	},

	getActive: function(){
		return Reservas._active
	},

	unsetActive: function(){
		Reservas._active = null;
	}

};

/**
 * Clase Reserva
 *
 * Cada reserva en pantalla tiene asociada una instancia de esta clase
 */
var Reserva = Class.create({

	_hyperForm: null,

	_data: null,

	/**
	 * Constructor de Orden
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		hyperForm.observe('beforeRecordPreview', this._addActivarButton.bind(this));
		hyperForm.observe('beforeRecordPreview', this._addDesistirButton.bind(this));
	},

	/**
	 * Agrega el de re-abrir orden
	 */
	_addActivarButton: function(eventName, response){
		this._hyperForm.removeControlButton("copyButton");
		var estadoContrato = '';
		if(response!=null){
			var record = new HyperRecordData(response.data);
			estadoContrato = record.getValueFromName("estado_contrato");
		}
		if(estadoContrato=="Activo"){
			this._hyperForm.addControlButton({
				className: "copyButton",
				value: "Activar Contrato",
				onClick: this._abrirActivarContrato.bind(this, record)
			});
		}
	},
	
	/**
	 * Intenta reabrir la orden de compra o el pedido en pantalla
	 */
	_abrirActivarContrato: function(record){
	    var codigo = record.getValueFromName("id");
		new HfosAjax.JsonRequest('reservas/getDatos', {
			parameters: {
				'codigo': codigo
			},
			onSuccess: function(response){
				if(response.status=='OK'){
					this._data = response.data;
					Hfos.getApplication().run({
						id: 'win-contratos-tpc',
        				icon: 'hire-me.png',
        				title: "Contratos",
        				width: '900px',
        				height: '600px',
        				action: 'contratos'
					});
				} else {
					this._hyperForm.getMessages().error(response.message);
				}
			}.bind(this)
		});
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
	* Abre la ventana con la vista para desistir una reserva
	*/
	_abrirDesistir: function(record){
	    var hyperForm = this._hyperForm;
	    var activeSection = this._hyperForm.getActiveSection();
	    var reservaId = record.getValueFromName("id");
	    if(reservaId){
    		new HfosModalForm(this, 'reservas/getDesistir', {
    			parameters: {
    			     'reservaId': reservaId,
    			},
    			beforeClose: function(sociosId, form, canceled, response){
    				if(canceled==false){
    					if(response.status=='OK'){
    						if(typeof response.message != "undefined"){
    						    this._hyperForm.getMessages().success(response.message);
    							form.close();
    						}
    					} else {
    						if(response.status=='FAILED'){
    							this._hyperForm.getMessages().error(response.message);
    						}
    					}
    				}
    			}.bind(this, reservaId)
    		});
	    } else {
	        alert('No encontro codigo');
	    }  
	},

	/**
	 * Carga los datos de la reserva
	 */
	loadData: function(hyperFormContratos){
		if(this._data!==null){
			hyperFormContratos.setFieldValues(this._data);
			hyperFormContratos.getMessages().notice('Activando la reserva '+this._data.numero_contrato);
			hyperFormContratos.addFieldLabelInput('reservas_contrato', 'Reserva', this._data.id, 'before', this._data.numero_contrato);
			//hyperFormContratos.addFieldInput('reservas_contrato', 'Reserva', this._data.numero_contrato, 'before');
		};
	}
});

//Agregar un evento cada vez que se cree una grilla en el hyperForm ordenes
HyperFormManager.lateBinding('reservas', 'afterInitialize', function(){
	Reservas.setActive(this);
});

