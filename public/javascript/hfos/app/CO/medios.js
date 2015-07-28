
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

var Medios = Class.create(HfosProcessContainer, {

	_subirForm: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		this.getElement('saveButton').observe('click', this._generar.bind(this))
	},

	_generar: function(){
		var saveButton = this.getElement('saveButton');
		var mediosForm = this.getElement('mediosForm');
		new HfosAjax.JsonFormRequest(mediosForm, {
			onCreate: function(saveButton, mediosForm){
				saveButton.disable();
				mediosForm.disable();
				this.getElement('headerSpinner').show();
			}.bind(this, saveButton, mediosForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					this.getMessages().success(response.message);
				}
				if(typeof response.url != "undefined"){
					window.open(response.url)
				}
			}.bind(this),
			onComplete: function(saveButton, mediosForm){
				saveButton.enable();
				mediosForm.enable();
				this.getElement('headerSpinner').hide();
			}.bind(this, saveButton, mediosForm)
		});
	}

});

HfosBindings.late('win-medios', 'afterCreateOrRestore', function(hfosWindow){
	var medios = new Medios(hfosWindow);
});
