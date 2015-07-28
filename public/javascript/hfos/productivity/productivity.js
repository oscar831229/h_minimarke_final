
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

var HfosProductivity = Class.create({

	_leftPanel: null,

	_rightPanel: null,

	_element: null,

	_mail: null,

	/**
	 * @constructor
	 */
	initialize: function(){

	},

	/**
	 * @this {HfosProductivity}
	 */
	_canGetMail: function(){
		if(this._mail==null){
			new HfosAjax.JsonApplicationRequest('identity/productivity/canGetMail', {
				checkAcl: true,
				onSuccess: function(response){
					if(response.status=='OK'){
						if(typeof response.notMailUser != "undefined"){
							if(response.notMailUser == true){
								this._requestFrontCredentials();
							} else {
								this._mail = new HfosMail(this);
							}
						} else {
							this._mail = new HfosMail(this);
						}
					}
				}.bind(this)
			});
		};
	},

	/**
	 * @this {HfosProductivity}
	 */
	_addCallbacks: function(){
		this._panelItems = document.body.select('div.left-panel-item');
		for(var i=0;i<this._panelItems.length;i++){
			this._panelItems[i].observe('click', this._selectPanelItem.bind(this, this._panelItems[i]));
		}
	},

	/**
	 * @this {HfosProductivity}
	 */
	selectNone: function(){
		for(var i=0;i<this._panelItems.length;i++){
			this._panelItems[i].removeClassName('active');
		};
	},

	/**
	 * @this {HfosProductivity}
	 */
	_selectPanelItem: function(element){
		this.selectNone();
		element.addClassName('active');
	},

	/**
	 * @this {HfosProductivity}
	 */
	show: function(){
		if(this._leftPanel==null){
			new HfosAjax.ApplicationRequest('identity/productivity/getPanel', {
				checkAcl: false,
				onSuccess: function(transport){

					var d = document.createElement('DIV');
					d.setAttribute('id', 'left-panel');
					d.addClassName('left-panel');
					d.update(transport.responseText);
					document.body.appendChild(d);
					this._leftPanel = d;

					var d = document.createElement('DIV');
					d.setAttribute('id', 'right-panel');
					d.addClassName('right-panel');
					document.body.appendChild(d);
					this._rightPanel = d;

					this._addCallbacks();
					this.adjustPanels();

					this._canGetMail();
				}.bind(this)
			});
		};
	},

	/**
	 * @this {HfosProductivity}
	 */
	_requestFrontCredentials: function(resource, onSuccess){
		new HfosAjax.ApplicationRequest('identity/productivity/requestFrontCredentials', {
			onSuccess: function(transport){

		    	var d = document.createElement('DIV');
		    	d.addClassName('uacLayout');
		    	d.id = 'uacLayout';
		    	d.innerHTML = transport.responseText;
		    	document.body.appendChild(d);
		    	Hfos.getUI().centerAtScreen(d);

		    	Hfos.getUI().showScreenShadow();

		    	var acceptButton = d.selectOne('input#acceptButton');
			    acceptButton.observe('click', this._closeRequest.bind(this));

			    var authButton = d.selectOne('input#authButton');
			    authButton.observe('click', this._validateCredentials.bind(this));

				var noHaveFrontAccount = d.selectOne('input#noHaveFrontAccount');
				noHaveFrontAccount.observe('click', this._changeUserType.bind(this));

				var haveFrontAccount = d.selectOne('input#haveFrontAccount');
				haveFrontAccount.observe('click', this._changeUserType.bind(this));

			    var passwordBox = d.selectOne('input#password');
			    passwordBox.activate();

		    	new Draggable(d);

		    	this._element = d;

		    	if(Hfos.isUpgrading()==true||Hfos.isWelcoming()==true){
					this._closeRequest();
				}

			}.bind(this)
		});
	},

	/**
	 * @this {HfosProductivity}
	 */
	_changeUserType: function(){
		var haveUserForm = this._element.selectOne('#haveUserForm');
		var noHaveFrontAccount = this._element.selectOne('input#noHaveFrontAccount');
		if(noHaveFrontAccount.checked){
			haveUserForm.hide();
		} else {
			haveUserForm.show();
		}
	},

	/**
	 * @this {HfosProductivity}
	 */
	_removeDialog: function(){
		this._element.erase();
		this._element = null;
		Hfos.getUI().hideScreenShadow();
	},

	/**
	 * @this {HfosProductivity}
	 */
	_closeRequest: function(){
		this._removeDialog();
		new HfosAjax.ApplicationRequest('identity/productivity/noMailUser', {
			checkAcl: false,
			onSuccess: function(transport){
				$('right-panel').update(transport.responseText);
			}
		});
	},

	/**
	 * @this {HfosProductivity}
	 */
	_validateCredentials: function(){
		var frontAccount = this._element.selectOne('input#haveFrontAccount').checked ? 'Y' : 'N';
		new HfosAjax.JsonApplicationRequest('identity/productivity/linkAccount', {
			parameters: {
				'frontAccount': frontAccount,
				'usuarioFrontId': this._element.selectOne('select#usuarioFrontId').getValue(),
				'password': this._element.selectOne('input#password').getValue()
			},
			onSuccess: function(frontAccount, response){
				if(response.status=='FAILED'){
					if(frontAccount=='Y'){
						new Effect.Shake(this._element, {
							afterFinish: function(response){
								this._element.selectOne('#'+response.field).activate()
							}.bind(this, response)
						});
					} else {
						new HfosModal.alert({
							title: 'Enlace de cuentas',
							message: response.message
						});
					}
				} else {
					this._removeDialog();
					this._canGetMail();
				}
			}.bind(this, frontAccount)
		});
	},

	/**
	 * @this {HfosProductivity}
	 */
	adjustPanels: function(){
		var pageSize = WindowUtilities.getPageSize(document.body);
		this._rightPanel.setStyle({
			'width': (pageSize.windowWidth-220)+'px'
		});
	},

	/**
	 * @this {HfosProductivity}
	 */
	getLeftPanel: function(){
		return this._leftPanel;
	},

	/**
	 * @this {HfosProductivity}
	 */
	getRightPanel: function(){
		return this._rightPanel;
	}

});