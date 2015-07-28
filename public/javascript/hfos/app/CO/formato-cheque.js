
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
 * Clase FormatoCheque
 *
 * Permite configurar los formatos de cheques
 */
var FormatoCheque = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de FormatoCheque
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._addIndexCallbacks();
	},

	_addIndexCallbacks: function(){

		var chequerasSelect = this.selectOne('select#chequeraId');
		chequerasSelect.observe('change', this._onChangeChequera.bind(this, chequerasSelect));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._saveFormato.bind(this));

		var copyButton = this.getElement('copyButton');
		copyButton.observe('click', this._selectFormato.bind(this));
	},

	_onChangeChequera: function(chequerasSelect){
		if(chequerasSelect.getValue()=='@'){
			this.getMessages().notice('Seleccione una chequera para configurar su formato de impresión');
			this.getElement('saveButton').hide();
			this.getElement('chooseChequera').show();
			this.getElement('chequeraFormato').update('');
		} else {
			this.getMessages().notice('Indique las posiciones horizontales y verticales de los items del cheque');
			this.getElement('chooseChequera').hide();
			new HfosAjax.Request('formato_cheque/getFormato', {
				parameters: {
					'chequeraId': this.selectOne('select#chequeraId').getValue()
				},
				onCreate: function(){
					this.getElement('headerSpinner').show();
				}.bind(this),
				onSuccess: function(transport){
					this.getElement('chequeraFormato').update(transport.responseText);
					this._notifyContentChange();
				}.bind(this),
				onComplete: function(){
					this.getElement('headerSpinner').hide();
					this.getElement('saveButton').show();
				}.bind(this)
			})
		}
	},

	_saveFormato: function(){
		var chequeraId = this.selectOne('select#chequeraId').getValue();
		if(chequeraId!='@'){
			var parameters = {
				'chequeraId': chequeraId,
				'medida': this.selectOne('select#medida').getValue()
			};
			var items = [
				'ano', 'mes', 'dia', 'valor', 'tercero', 'suma',
				'numero', 'nota', 'cuenta', 'detalle', 'debito',
				'credito', 'valor_movi', 'empresa', 'num_cheque',
				'cuenta_bancaria'
			];
			for(var i=0;i<items.length;i++){
				var el = this.selectOne('input#'+items[i]+'X');
				if(el){
					parameters[items[i]+'X'] = this.selectOne('input#'+items[i]+'X').getValue();
					parameters[items[i]+'Y'] = this.selectOne('input#'+items[i]+'Y').getValue();
				}
			};
			new HfosAjax.JsonRequest('formato_cheque/guardar', {
				onCreate: function(){
					this.getElement('headerSpinner').show();
				}.bind(this),
				parameters: parameters,
				onSuccess: function(response){
					if(response.status=='OK'){
						this.getMessages().success('Se actualizó correctamente el formato')
					} else {
						this.getMessages().error(response.message)
					}
				}.bind(this),
				onComplete: function(){
					this.getElement('headerSpinner').hide();
					this.getElement('saveButton').show();
				}.bind(this)
			});
		}
	},

	/**
	 * Imprime el comprobante en pantalla
	 */
	_selectFormato: function(){
		new HfosModalForm(this, 'formato_cheque/selectFormato', {
			//checkAcl: true,
			parameters: this._key,
			beforeClose: function(form, canceled, response){
				if(canceled==false){
					if (response.status == 'OK') {
						this.getMessages().success(response.message, true);
					} else {
						this.getMessages().error(response.message, true);
					}
					
				}
			}.bind(this)
		});
	},

});

HfosBindings.late('win-formato-cheque', 'afterCreate', function(hfosWindow){
	var formatoCheque = new FormatoCheque(hfosWindow);
});