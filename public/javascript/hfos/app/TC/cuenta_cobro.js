
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
 * Clase Proyeccion
 *
 * Cada formulario de proyeccion en pantalla tiene asociado una instancia de esta clase
 */
var CuentaCobro = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de proyeccionCuentas
	 */
	initialize: function(container){
		this.setContainer(container);
		var imprimirButton = this.getElement('printButton');
		imprimirButton.observe('click', this._imprimir.bind(this, imprimirButton));
		var cuentaCobroButton = this.getElement('importButton');
		cuentaCobroButton.observe('click', this._cuentaCobro.bind(this, cuentaCobroButton));
	},

	/**
	 * Genera las cuentas de cobro
	 */
	_cuentaCobro: function(cuentaCobroButton){
		cuentaCobroButton.disable();
		this.setIgnoreTermSignal(true);
		var cuentaCobroForm = this.getElement('cuentaCobroForm');
		cuentaCobroForm.setAttribute('action', $Kumbia.path+'tpc/cuenta_cobro/generar');		
		new HfosAjax.JsonFormRequest(cuentaCobroForm, {
			onLoading: function(cuentaCobroForm){
				this.getMessages().notice('Se está generando las cuentas de cobro por favor esperar...');
				this.getElement('headerSpinner').show();
				cuentaCobroForm.disable();
			}.bind(this, cuentaCobroForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					this.getMessages().success('Se realizaron la(s) cuenta(s) de cobro correctamente');
				}
			}.bind(this),
			onComplete: function(cuentaCobroForm, cuentaCobroButton){
				this.getElement('headerSpinner').hide();
				cuentaCobroForm.enable();
				cuentaCobroButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cuentaCobroForm, cuentaCobroButton)
		});
	},

	/**
	 * Genera el impresion de cuentas de cobro
	 */
	_imprimir: function(imprimirButton){
		imprimirButton.disable();
		this.setIgnoreTermSignal(true);
		var cuentaCobroForm = this.getElement('cuentaCobroForm');
		cuentaCobroForm.setAttribute('action', $Kumbia.path+'tpc/cuenta_cobro/imprimir');		
		new HfosAjax.JsonFormRequest(cuentaCobroForm, {
			onLoading: function(cuentaCobroForm){
				this.getMessages().notice('Se está generando la impresión de las cuentas de cobro por favor esperar...');
				this.getElement('headerSpinner').show();
				cuentaCobroForm.disable();
			}.bind(this, cuentaCobroForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					if(response.status=='OK'){
						this.getMessages().success('Se realizaró la impresión de la(s) cuenta(s) de cobro correctamente');
						if(typeof response.file != "undefined"){
							window.open($Kumbia.path+response.file);
						}
					}
				}
			}.bind(this),
			onComplete: function(cuentaCobroForm, imprimirButton){
				this.getElement('headerSpinner').hide();
				cuentaCobroForm.enable();
				imprimirButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, cuentaCobroForm, imprimirButton)
		});
	}

});

HfosBindings.late('win-cuenta-cobro-tpc', 'afterCreateOrRestore', function(hfosWindow){
	var cuentaCobro = new CuentaCobro(hfosWindow);
});
