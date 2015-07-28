
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

var Cuentas = Class.create(HfosProcessContainer, {

	_key: '',

	_hyperForm: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(hyperForm){

		this._hyperForm = hyperForm;

		this._hyperForm.observe('onNew', this._setNewCallbacks.bind(this));
		this._hyperForm.observe('onEdit', this._setEditCallbacks.bind(this));

		this.setContainer(hyperForm);
	},

	_queryByCodigo: function(inputCuenta, inputNombreCuenta){
		new HfosAjax.JsonRequest('cuentas/queryAllByCodigo', {
			checkAcl: true,
			parameters: 'codigo='+inputCuenta.getValue()+'&nombre='+inputNombreCuenta.getValue(),
			onSuccess: function(cuentas){
				this._renderCuentas(cuentas);
			}.bind(this)
		});
	},

	_queryByNombre: function(inputCuenta, inputNombreCuenta){
		if(inputNombreCuenta.getValue().length>2){
			new HfosAjax.JsonRequest('cuentas/queryAllByNombre', {
				checkAcl: true,
				parameters: 'codigo='+inputCuenta.getValue()+'&nombre='+inputNombreCuenta.getValue(),
				onSuccess: function(cuentas){
					this._renderCuentas(cuentas);
				}.bind(this)
			});
		}
	},

	_renderCuentas: function(cuentas){
		var resultados = this.getElement('resultadoCuentas');
		resultados.innerHTML = "";
		for(var i=0;i<cuentas.length;i++){
			resultados.innerHTML+='<tr>'+
			'<td>'+cuentas[i].codigo+'</td>'+
			'<td>'+cuentas[i].nombre+'</td>'+
			'<td><div class="hyDetails" title="'+cuentas[i].codigo+'"></div></td>'+
			'</tr>';
		};
		var hyDetails = resultados.select('div.hyDetails');
		for(var i=0;i<hyDetails.length;i++){
			hyDetails[i].observe('click', this._editCuenta.bind(this, hyDetails[i].title));
			hyDetails[i].title = '';
		};
		this._notifyContentChange();
	},

	_setIndexCallbacks: function(){
		var inputCuenta = this.selectOne('input#cuenta');
		var inputNombreCuenta = this.selectOne('input#nombreCuenta');

		inputCuenta.observe('keyup', this._queryByCodigo.bind(this, inputCuenta, inputNombreCuenta));
		inputNombreCuenta.observe('keyup', this._queryByNombre.bind(this, inputCuenta, inputNombreCuenta));

		var newButton = this.getElement('newButton');
		newButton.observe('click', this._newCuenta.bind(this, newButton));

	},

	_setNewCallbacks: function(){
		this._hyperForm.getMessages().notice('Defina los parámetros de la cuenta y haga click en "Guardar" para terminar');
		this._hyperForm.showControlButton('saveButton');
		this._hyperForm.setCurrentState('new');
		this._setSaveCallbacks();
	},

	_setEditCallbacks: function(){
		this._hyperForm.getMessages().notice('Defina los parámetros de la cuenta y haga click en "Guardar" para terminar');
		this._hyperForm.showControlButton('saveButton');
		this._hyperForm.setCurrentState('edit');
		this._setSaveCallbacks();
	},

	_setSaveCallbacks: function(){
		HfosCommon.addNiifCompleter('cuenta_niif');
		HfosCommon.addCuentaCompleter('contrapartida');
		HfosCommon.addCuentaCompleter('cta_retencion');
		HfosCommon.addCuentaCompleter('cta_iva');
	}

});

HyperFormManager.lateBinding('cuentas', 'afterInitialize', function(){
	var cuentas = new Cuentas(this);
})
