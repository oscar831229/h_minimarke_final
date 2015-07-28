
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
var ConsultaCausacion = Class.create(HfosProcessContainer, {

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		var diferidosId = this.selectOne('input#diferidosId');
		var diferidosIdDet = this.selectOne('input#diferidosId_det');

		diferidosId.observe('blur', this._showDiferidosCalcs.bind(this, diferidosId, diferidosIdDet));
		diferidosIdDet.observe('blur', this._showDiferidosCalcs.bind(this, diferidosId, diferidosIdDet));
	},

	/**
	 *
	 * Muestra/Oculta campos segun seleccion de diferidos
	 */
	_showDiferidosCalcs: function(diferidosId, diferidosIdDet){
		if(diferidosId.getValue() && diferidosIdDet.getValue().indexOf("NO EXISTE")<0){
			this.getElement('chooseDiferidos').hide();
			this.getElement('CausacionContent').show();
			this._calcularCausacion();
		} else {
			this.getElement('chooseDiferidos').show();
			this.getElement('CausacionContent').hide();
		};
	},

	/**
	 *
	 * Calculas las causaciones
	 */
	_calcularCausacion: function(){
		// consultamos al action de neustro controlador los perfiles guardados al usuario selecionado
		new HfosAjax.Request('consulta_causacion/calcularCausacion', {
			parameters: 'diferidosId='+diferidosId.getValue(),
			onCreate: function(){
			  this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(transport){
			  this.selectOne("#cauContent").update(transport.responseText);
				this._notifyContentChange();
				this.getElement('headerSpinner').hide();
			}.bind(this)
		});
	}


});

HfosBindings.late('win-consulta-causacion', 'afterCreate', function(hfosWindow){
	var consultaCausacion = new ConsultaCausacion(hfosWindow);
});

