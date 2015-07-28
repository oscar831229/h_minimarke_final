
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
 * Clase CertificadoRetencion
 *
 * Cada formulario de Certificado de Retencion en pantalla tiene asociado una instancia de esta clase
 */
var CertificadoRetencion = Class.create(HfosProcessContainer, {

	/**
	 * Constructor de CertificadoRetencion
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		new HfosForm(this, 'certificadoRetencionForm', {
			update: 'resultados',
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

HfosBindings.late('win-certificado-retencion', 'afterCreateOrRestore', function(hfosWindow){
	var certificadoRetencion = new CertificadoRetencion(hfosWindow);
});

