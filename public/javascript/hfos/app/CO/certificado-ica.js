
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
 * Clase CertificadoIca
 *
 * Cada formulario de Certificado de Ica en pantalla tiene asociado una instancia de esta clase
 */
var CertificadoIca = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de CertificadoIca
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		new HfosForm(this, 'certificadoIcaForm', {
			update: 'resultados',
			onCreate: function() {
				alert("aaaaa");
			},
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se generó el certificado correctamente');
					window.open(Utils.getURL(response.file));
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});
	}

});

HfosBindings.late('win-certificado-ica', 'afterCreateOrRestore', function(hfosWindow){
	var certificadoIca = new CertificadoIca(hfosWindow);
});

