
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

var HfosCuentasSelectores2 = {

	_selectores: {},

	add: function(name, selector){
	},

	removeAll: function(){
	}

};

var HfosCuentasSelector2 = Class.create({

	_element: null,
	_elementDetalle: null,
	_elements: {},
	_cuentaElement: null,

	_elementDiv: null,

	_cuenta: '',
	_oldCuenta: '',
	_blockQuery: false,

	_selectionFirst: false,
	_selectionLast: false,
	_resultsPointer: 0,
	_liResults: [],

	_windowManager: null,

	/**
	 * @constructor
	 */
	initialize: function(element, elementDetalle){

		this._element = $(element);
		this._elementDetalle = $(elementDetalle);

		this.addCuentaCompleter();
    },

	/**
	 * CUENTAS
	 */

	/**
	 * Agrega un autocompleter para cuentas contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	_queryByCuenta: function(){
		var value = this._element.getValue();
		if(value!=''){
			new HfosAjax.JsonRequest('cuentas/queryByCuenta', {
				method: 'GET',
				parameters: 'cuenta='+value,
				onSuccess: function(cuentaElement, cuentaNombreElement, response){
					if(response.status=='OK'){
						cuentaNombreElement.setValue(response.nombre);
					} else {
						cuentaNombreElement.setValue(response.message);
					}
				}.bind(this, this._element, this._elementDetalle)
			});
		} else {
			this._elementDetalle.setValue('');
		}
	},

	_updateCodigoCuenta: function(elementCuenta, option){
		console.log("_updateCodigoCuenta");
		console.log(option);
		elementCuenta.setValue(option.value);
		elementCuenta.fire("blur");
	},

	/**
	 * Agrega un autocompleter para cuentas contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addCuentaCompleter: function(name, nameDetail){
		try {
			context = HfosCommon.getContext();
			if(this._element){
				console.log("cuentaElement");
				console.log(this._element);

				new HfosAutocompleter(this._elementDetalle, 'cuentas/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: this._updateCodigoCuenta.bind(this, this._element)
				});
				this._element.observe('blur', this._queryByCuenta.bind(this));
				if(this._element.getValue()!='' && this._elementDetalle.getValue()==''){
					this._queryByCuenta.bind(this)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	_searchCuenta: function(element) {
		new HfosAjax.Request('cuentas/queryCuenta', {
			method: 'GET',
			parameters: {
				'cuenta': element.getValue()
			},
			onSuccess: this._successQueryCuenta.bind(this)
		});
	},

	_successQueryCuenta: function(transport){
		var cuenta = Json.decode(transport.responseText);
		console.log(cuenta);
	},


	/**
	 * Devuelve la cuenta capturada
	 */
	getCuenta: function(){
		return this._cuenta;
	},

	/**
	 * Destruye el selector de cuentas
	 */
	destroy: function(){
		this._removeCuentaTags();
		this._resetResults();
		if(this._cuentaElement!==null){
			this._cuentaElement.erase();
			this._element.value = this._getSelectedCuenta();
			this._element.show();
			this._cuentaElement = null;
		}
	},

	/**
	 * Agrega un procedimiento a un determinado evento
	 */
	observe: function(eventName, procedure){
		if(Object.isUndefined(this['_'+eventName])){
			this['_'+eventName] = [];
		};
		this['_'+eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento en el componente
	 */
	fire: function(eventName, elementParam, observerParam){
		if(!Object.isUndefined(this['_'+eventName])){
			for(var i=0;i<this['_'+eventName].length;i++){
				if(this['_'+eventName][i](this, elementParam, observerParam)===false){
					return false;
				}
			};
			return true;
		} else {
			return true;
		}
	}

});
