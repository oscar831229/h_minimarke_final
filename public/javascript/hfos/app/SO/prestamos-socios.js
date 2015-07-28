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

var prestamosSocios = {

	/**
	* Attributo que almacena la información del hyperForm actual
	*/ 
	_active: null,

	/**
	 * Cambia el attributo _active con un nuevo hyperForm
	 */
	setActive: function(hyperForm){
		prestamosSocios._active = new prestamosSocio(hyperForm);
	},

	/**
	 * Valida si existe o no información en attributo _active
	 */
	hasActive: function(){
		return prestamosSocios._active!==null;
	},

	/**
	 * Obtiene el hyperForm almacenado en attributo _active
	 */
	getActive: function(){
		return prestamosSocios._active
	},

	/**
	 * Borra valor en attributo _active
	 */
	unsetActive: function(){
		prestamosSocios._active = null;
	}
};

/**
 * Clase controladora de HyperForm de Contratos
 */
var prestamosSocio = Class.create({

	_hyperForm: null,
	
	/**
	 * Constructor de Contrato
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		hyperForm.observe('beforeRecordPreview', this._addEstadoCuentaButton.bind(this)); 
	},

	/**
	 * Agrega el botton de cambio de contrato
	 */
	_addEstadoCuentaButton: function(eventName, response){
		this._hyperForm.removeControlButton("printButton");
		var activeSection = this._hyperForm.getActiveSection();
		var record = new HyperRecordData(response.data);
		var estadoContrato = record.getValueFromName("cuenta");
		if(estadoContrato){
			this._hyperForm.addControlButton({
				className: "printButton",
				value: "Estado de Cuenta",
				onClick: this._abrirEstadoCuenta.bind(this, record)
			});
		}
	},
	
	/**
	* Abre la ventana con la vista para desistir a un contrato
	*/
	_abrirEstadoCuenta: function(record){
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		var prestamoId = record.getValueFromName("id");
		if(prestamoId){
			new HfosModalForm(this, 'prestamos_socios/estadoCuenta', {
				parameters: {
					'id': prestamoId
				},
				style: 'width: 50%;',
				beforeClose: function(prestamoId, form, canceled, response){
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
				}.bind(this, prestamoId)
			});
		}else{
			alert('No encontro prestamo');
		}  
	}
}); 

HyperFormManager.lateBinding('prestamos_socios', 'afterInitialize', function(){
	prestamosSocios.setActive(this);
	var prestamosSocio = prestamosSocios.getActive();
});
