
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

var Conversiones = {

	/**
	 * Se llama cada vez que se cree/edite una grilla de entradas de compra
	 */
	onCreate: function(){
		new Conversion(this);
	}

};

/**
 * Clase Entrada
 *
 * Cada entrada de compra en pantalla tiene asociada una instancia de esta clase
 */
var Conversion = Class.create({

	//Referencia al HyperForm
	_hyperForm: {},

	_unidadBase: '@',

	/**
	 * Constructor de Entrada
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		hyperForm.observe('beforeInput', this._prepareForInput.bind(this));
	},

	_getUnidadBase: function(unidadElement, unidadBaseElement, factorElement){
		new HfosAjax.JsonRequest('conversion/getUnidadBase', {
			parameters: {unidad: unidadElement.value},
			onSuccess: function(response){
				if(response.status=='OK'){
					this._unidadBase = response.unidadBase;
					unidadBaseElement.setValue(response.unidadBase);
				} else {
					unidadBaseElement.setValue('@');
				};
				factorElement.focus();
			}.bind(this)
		});
	},

	/**
	 * Agrega los callbacks
	 */
	_prepareForInput: function(){

		/*var formName = 'hySaveForm';
		var unidadElement = this._hyperForm.getElement(formName).selectOne('#unidad');
		var unidadBaseElement = this._hyperForm.getElement(formName).selectOne('#unidad_base');
		var factorElement = this._hyperForm.getElement(formName).selectOne('#factor_conversion');
		unidadElement.focus();
		unidadElement.observe('change', this._getUnidadBase.bind(this, unidadElement, unidadBaseElement, factorElement));
		unidadBaseElement.observe('change', function(){
			unidadBaseElement.setValue(this._unidadBase);
			factorElement.focus();
		}.bind(this));
		if(this._hyperForm.getCurrentState() == 'edit'){
			this._unidadBase = unidadBaseElement.value;
		}*/

		return true;
	}

});

//Agregar un evento cada vez que se cree una grilla en el hyperForm conversion
HyperFormManager.lateBinding('conversion', 'afterInitialize', Conversiones.onCreate)
