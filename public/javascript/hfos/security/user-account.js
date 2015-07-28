
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

var HfosUserAccount = Class.create(HfosProcessContainer, {

	/**
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 * @this {HfosUserAccount}
	 */
	_setIndexCallbacks: function(){

		var submitElement = this.selectOne('input#submitButton');
		submitElement.observe('click', this._cambiarClaveUsuario.bind(this));

		this.selectOne('input#password_actual').activate();
	},

	/**
	 * Metodo que guarda la nueva clave del usuario
	 *
	 * @this {HfosUserAccount}
	 */
	_cambiarClaveUsuario: function(){

		var passwordActual = this.selectOne('input#password_actual');
		var passwordNuevo = this.selectOne('input#password_nuevo');
		var passwordNuevoConfirm = this.selectOne('input#password_nuevo_confirm');

		var valPasswordActual = "";
		if(passwordActual){
			valPasswordActual = passwordActual.getValue();
		}

		var valPasswordNuevo = "";
		if(passwordNuevo){
			valPasswordNuevo = passwordNuevo.getValue();
		}

		var valPasswordNuevoConfirm = "";
		if(passwordNuevoConfirm){
			valPasswordNuevoConfirm = passwordNuevoConfirm.getValue();
		}

		//Enviamos un ajax al action del controlador para que guarde en DB la nueva clave de usuario
		new HfosAjax.JsonApplicationRequest('identity/usuarios/cambioClave', {
			parameters: 'password_actual='+valPasswordActual+'&password_nuevo='+valPasswordNuevo+'&password_nuevo_confirm='+valPasswordNuevoConfirm,
			onLoading: function(){
				this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					window.setTimeout(function(){
						var application = Hfos.getApplication();
						var userOptions = application.getVirtualUser().getOptions();
						if(typeof userOptions['passwordExpired'] != "undefined"){
							new HfosModal.alert({
								title: application.getName(),
								message: 'Se va a reiniciar la sesión para refrescar sus credenciales',
								onAccept: function(){
									Hfos.closeApp();
								}
							});
						}
					}, 1500);
				} else {
					this.getMessages().error(response.message);
				}
			}.bind(this),
			onComplete: function(){
				this.getElement('headerSpinner').hide();
			}.bind(this)
		});
	}


});

HfosBindings.late('win-useraccount', 'afterCreate', function(hfosWindow){
	var hfosUserAccount = new HfosUserAccount(hfosWindow);
});