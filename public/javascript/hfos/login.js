
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
 * HfosLogin
 *
 * Administra el inicio de sesión del usuario
 */
var HfosLogin = Class.create({

	_onSuccess: null,

	_cachedKeyDown: null,

	/**
	 * @constructor
	 */
	initialize: function(loginAppCode, onSuccess){
		this._onSuccess = onSuccess;
		new HfosAjax.ApplicationRequest('identity/login', {
			parameters: 'appCode='+loginAppCode,
			onSuccess: function(transport){
				var loginBox = document.createElement('DIV');
				loginBox.update(transport.responseText);
				document.body.appendChild(loginBox);
				this._addCallbacks();
			}.bind(this)
		});
	},

	/**
	 * @this {HfosLogin}
	 */
	_addCallbacks: function(){
		$('login').observe('focus', function(){
			if(this.value=='Nombre de Usuario'){
				this.removeClassName('dummy');
				this.value = '';
			}
		});
		$('login').observe('blur', function(){
			if(this.value==''){
				this.addClassName('dummy');
				this.value = 'Nombre de Usuario';
			}
		});
		$('passwordDummy').observe('focus', function(){
			this.hide();
			$('password').show();
			$('password').activate();
		});
		$('password').observe('blur', function(){
			if(this.value==''){
				this.hide();
				$('passwordDummy').show();
			}
		});
		$('loginButton').observe('click', this._onLogin.bind(this));
		this._cachedKeyDown = function(event){
			if(event.keyCode==Event.KEY_RETURN){
				this._onLogin();
			}
		}.bind(this);
		new Event.observe(window, 'keydown', this._cachedKeyDown);
	},

	/**
	 * @this {HfosLogin}
	 */
	_onLogin: function(){
		new HfosAjax.JsonRequest('session/start/'+$F('login')+'/'+$F('password'), {
			onLoading: function(){
				$('login').form.disable();
			},
			onSuccess: function(authenticated){
				if(authenticated==true){
					new Effect.Fade('login-view', {
						duration: 0.5,
						afterFinish: function(){
							this._onSuccess();
							if(this._cachedKeyDown){
								new Event.stopObserving(window, 'keydown', this._cachedKeyDown);
							};
							this._cachedKeyDown = null;
							var loginView = $('login-view');
							if(loginView){
								loginView.erase();
							}
						}.bind(this)
					});
				} else {
					new Effect.Shake('login-box', {
						duration: 0.5
					});
					window.setTimeout(function(){
						if($F('login')==''){
							$('login').activate();
						} else {
							$('password').activate();
						}
					}, 550);
				}
			}.bind(this),
			onComplete: function(){
				$('login').form.enable();
			}
		});
	}

});