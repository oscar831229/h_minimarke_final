
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
 * Clase Depreciacion
 *
 * Cada formulario de Depreciacion de Activos en pantalla tiene asociado una instancia de esta clase
 */
var ConsultaDepreciacion = Class.create(HfosProcessContainer, {

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		var activosId = this.selectOne('input#activosId');
		var activosIdDet = this.selectOne('input#activosId_det');

		activosId.observe('blur', this._showActivosCalcs.bind(this, activosId, activosIdDet));
		activosIdDet.observe('blur', this._showActivosCalcs.bind(this, activosId, activosIdDet));
	},

	/**
	 *
	 * Muestra/Oculta campos segun seleccion de activos
	 */
	_showActivosCalcs: function(activosId, activosIdDet, submitButton){
		if(activosId.getValue() && activosIdDet.getValue()){
			this.getElement('chooseActivo').hide();
			this.getElement('DepreciacionContent').show();
			this._calcularDepreciacion();
		} else {
			this.getElement('chooseActivo').show();
			this.getElement('DepreciacionContent').hide();
		};
	},

	/**
	 *
	 * Calculas las depreciaciones desde la ultima fecha de referencia
	 */
	_calcularDepreciacion: function(){
		// consultamos al action de neustro controlador los perfiles guardados al usuario selecionado
		new HfosAjax.Request('consulta_depreciacion/calcularDepreciacion', {
			parameters: 'activosId='+activosId.getValue(),
			onCreate: function(){
			  this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(transport){
				this.selectOne("#depContent").update(transport.responseText);
				this._notifyContentChange();
				this.getElement('headerSpinner').hide();
			}.bind(this)
		});
	}


});

HfosBindings.late('win-consulta-depreciacion', 'afterCreate', function(hfosWindow){
	var consultaDepreciacion = new ConsultaDepreciacion(hfosWindow);
});

