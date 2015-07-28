
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

var CambioNit = Class.create(HfosProcessContainer, {

	_subirForm: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container)
	{
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 * @this {CambioNit}
	 */
	_setIndexCallbacks: function()
	{

		HfosCommon.addTerceroCompleter('nitErrado');
		HfosCommon.addTerceroCompleter('nitCorrecto');

		this.getElement('importButton').observe('click', this._changeTercero.bind(this))
	},

	/**
	 * @this {CambioNit}
	 */
	_changeTercero: function(){
		this.setIgnoreTermSignal(true);
		var cambiarForm = this.getElement('cambiarForm');
		new HfosAjax.JsonFormRequest(cambiarForm, {
			onCreate: function(){
				this.getElement('importButton').disable();
				this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.go('cambio_nit/index', {
						onSuccess: function(){
							this._setIndexCallbacks();
							this.getMessages().success(response.message);
						}.bind(this)
					});
				} else {
					this.getMessages().error(response.message);
				}
			}.bind(this),
			onComplete: function(){
				this.getElement('importButton').enable();
				this.getElement('headerSpinner').hide();
				this.setIgnoreTermSignal(false);
			}.bind(this)
		});
	}

});

HfosBindings.late('win-cambio-nit', 'afterCreateOrRestore', function(hfosWindow){
	var cambioNit = new CambioNit(hfosWindow);
});
