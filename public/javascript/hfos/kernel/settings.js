
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

var HfosSettings = Class.create(HfosProcessContainer, {

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 *
	 * @this {HfosSettings}
	 */
	_setIndexCallbacks: function(){
		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._saveSettings.bind(this));
		var tabs = this.getElement('formPannel');
		if (tabs) {
			new HfosTabs(this, 'tabbed');	
		}
	},

	/**
	 *
	 * @this {HfosSettings}
	 */
	_saveSettings: function(){
		var settingsForm = this.getElement('settingsForm');
		new HfosAjax.JsonFormRequest(settingsForm, {
			onLoading: function(){
				this.getElement('headerSpinner').show()
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se actualizó correctamente la configuración');
				} else {
					this.getMessages().error(response.message);
				}
			}.bind(this),
			onComplete: function(){
				this.getElement('headerSpinner').hide()
			}.bind(this)
		});
	}

});

var settingsWindows = ['win-settings-contab', 'win-settings-inve', 'win-settings-nomina', 'win-settings-tpc', 'win-settings-socios'];
for(var i=0;i<settingsWindows.length;i++){
	HfosBindings.late(settingsWindows[i], 'afterCreate', function(hfosWindow){
		var hfosSettings = new HfosSettings(hfosWindow);
	});
}
