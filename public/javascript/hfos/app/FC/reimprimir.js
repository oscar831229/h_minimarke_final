
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
 * Reimprimir
 *
 * Cada instancia de generar facturas
 */
var Reimprimir = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		var printButton = this.getElement('printButton');
		printButton.observe('click', this._reimprimirFactura.bind(this, printButton));
	},

	_reimprimirFactura: function(){
		var reimprimirForm = this.getElement('reimprimirForm')
		new HfosAjax.JsonFormRequest(reimprimirForm, {
			onLoading: function(reimprimirForm){
				this.getElement('headerSpinner').show();
				reimprimirForm.disable();
			}.bind(this, reimprimirForm),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					window.open($Kumbia.path+'temp/'+response.uri);
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}.bind(this),
			onComplete: function(reimprimirForm){
				this.getElement('headerSpinner').hide();
				reimprimirForm.enable();
			}.bind(this, reimprimirForm)
		});
	}

});

HfosBindings.late('win-reimprimir', 'afterCreate', function(hfosWindow){
	var reimprimir = new Reimprimir(hfosWindow);
});