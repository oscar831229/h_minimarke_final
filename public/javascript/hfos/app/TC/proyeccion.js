
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
var Proyeccion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de proyeccionCuentas
	 */
	initialize: function(container){
	    this.setContainer(container);
	    var proyeccionButton = this.getElement('importButton');
	    proyeccionButton.observe('click', this._proyeccion.bind(this, proyeccionButton));
	    var valorTotal = this.getElement('valorTotal');
	    if(valorTotal){
	        valorTotal.observe('change', this._cuotaInicial.bind(this, valorTotal));
	        var cuotaInicial = this.getElement('cuotaInicial');
	        if(cuotaInicial){
	            cuotaInicial.observe('change', this._cuotaInicial.bind(this, valorTotal));
	        }
	    };
	    valorTotal.activate();
	},

	/**
	 * Asigna el 33% del valor total por defecto
	 */
	_cuotaInicial: function(valorTotal){
	    var valorTotal = this.getElement('valorTotal');
        var cuotaInicial = this.getElement('cuotaInicial');
        //alert('cuotaInicial: '+cuotaInicial.getValue());
        var saldoPagar= this.getElement('saldoPagar');
	    if(valorTotal.getValue()){
    		new HfosAjax.JsonRequest('contratos/getCuotaInicial', {
    		    parameters: {
    				'valorTotal': valorTotal.getValue(),
    				'cuotaInicial': cuotaInicial.getValue()
    			},
    			onSuccess: function(response){
    				if(response.status=='FAILED'){
                        this.getMessages().error(response.message);
                        cuotaInicial.setValue(0);
                        saldoPagar.setValue(valorTotal.getValue());
    				} else {
                        //var cuotaInicial = this.getElement('cuotaInicial');
                        if(cuotaInicial){
                            cuotaInicial.setValue(response.cuotaInicial)
                        }
                        //var saldoPagar= this.getElement('saldoPagar');
                        if(saldoPagar){
                            saldoPagar.setValue(response.saldoPagar)
                        }
    				}
    			}.bind(this)
    		});
	    }
	},

	/**
	 * Genera la proyeccion
	 */
	_proyeccion: function(proyeccionButton){
		proyeccionButton.disable();
		this.setIgnoreTermSignal(true);
		var proyeccionForm = this.getElement('proyeccionForm');
		new HfosAjax.JsonFormRequest(proyeccionForm, {
			onLoading: function(proyeccionForm){
				this.getMessages().notice('Se está proyectando la amortización...');
				this.getElement('headerSpinner').show();
				proyeccionForm.disable();
			}.bind(this, proyeccionForm),
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
					if(typeof response.url != "undefined"){
						window.open($Kumbia.path+response.url);
					}
				} else {
					this.getMessages().success('Se realizó el proyección correctamente');
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this),
			onComplete: function(proyeccionForm, proyeccionButton){
				this.getElement('headerSpinner').hide();
				proyeccionForm.enable();
				proyeccionButton.enable();
				this.setIgnoreTermSignal(false);
			}.bind(this, proyeccionForm, proyeccionButton)
		});
	}

});

HfosBindings.late('win-proyeccion-tpc', 'afterCreateOrRestore', function(hfosWindow){
	var proyeccion = new Proyeccion(hfosWindow);
});

