
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
 * Clase TrasladoActivos
 *
 * Cada formulario de Traslado de Activos en pantalla tiene asociado una instancia de esta clase
 */
var TrasladoActivos = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de Balance
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._trasladar.bind(this));

		new HfosTabs(this, 'tabbed');

		/*new HfosForm(this, 'depreciacionForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					window.open(Utils.getURL(response.file));
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});*/
	},

	_trasladar: function(){
		new HfosModal.confirm({
			title: 'Traslado Activos',
			message: 'Seguro desea realizar el traslado?',
			onAccept: function(){
				this.getElement('headerSpinner').show();
				var trasladoForm = this.getElement('trasladoActivosForm');
				new HfosAjax.JsonFormRequest(trasladoForm, {
					onSuccess: function(response){
						if(response.status=='OK'){
							this.getMessages().success('Se realizó el traslado correctamente');
						} else {
							this.getMessages().error(response.message);
						}
					}.bind(this),
					onComplete: function(){
						this.getElement('headerSpinner').hide();
					}.bind(this)
				});
			}.bind(this)
		});
	}

});

HfosBindings.late('win-traslado-activos', 'afterCreateOrRestore', function(hfosWindow){
	var trasladoActivos = new TrasladoActivos(hfosWindow);
});

