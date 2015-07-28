
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
 * HfosAcl (Access Control List)
 *
 * Comprueba remotamente si un usuario tiene permisos para
 * ejecutar una determinada acción
 *
 */
var HfosAcl = {

	/**
	 * Muestra el cuadro de dialógo de permisos insuficientes
	 * y ofrece el cuadro de elevación de permisos en caso de permitirse
	 *
	 * @this {HfosAcl}
	 */
	handleFailedCheck: function(accessInfo, resource, onSuccess){
		var application = Hfos.getApplication();
		if(accessInfo.elevation==false){
			new HfosModal.alert({
				title: application.getName(),
				message: 'No tiene permisos suficientes para: '+accessInfo.description
			});
		} else {
			new HfosModal.customDialog({
				icon: 'alert48',
				title: application.getName(),
				message: 'No tiene permisos suficientes para: '+accessInfo.description,
				buttons: {
					'Autenticar': {
						'action': function(resource, onSuccess, modal){
							modal.acceptDefault();
							HfosUac.requestElevation(resource, onSuccess);
						}.bind(this, resource, onSuccess)
					},
					'Cerrar': {
						'action': function(modal){
							modal.acceptDefault();
						}
					}
				}
			});
		}
	},

	/**
	 * Consulta si un usuario tiene un permiso y ejecuta onSuccess en caso de tenerlo
	 *
	 * @this {HfosAcl}
	 */
	checkPermission: function(resource, onSuccess, onLocalApp){
		if(typeof onLocalApp == "undefined"){
			var parameters = 'resource='+resource
		} else {
			var parameters = 'externResource='+resource
		};
		new HfosAjax.JsonRequest('gardien/check', {
			'parameters': parameters,
			'onSuccess': function(resource, onSuccess, response){
				if(response.status=="OK"){
					onSuccess();
				} else {
					if(response.status=='FAILED'){
						HfosAcl.handleFailedCheck(response.accessInfo, resource, onSuccess);
					}
				}
			}.bind(this, resource, onSuccess)
		});
	}

};