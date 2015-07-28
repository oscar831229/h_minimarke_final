
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

var HfosUpgradeBox = Class.create({

	_position: 1,

	_upgradeBox: null,

	_onCourse: true,

	/**
	 *
	 * @constructor
	 */
	initialize: function(){
		if(this._onCourse==true){
			new HfosAjax.ApplicationRequest('identity/upgrade', {
				parameters: 'appCode='+Hfos.getApplication().getCode(),
				onSuccess: function(transport){

					Hfos.getUI().showScreenShadow(0.15);

					var workspace = Hfos.getApplication().getWorkspace();
					window.setTimeout(function(workspace){
						workspace.getToolbar().hide();
					}.bind(this, workspace), 1500);

					var upgradeBox = document.createElement('DIV');
					upgradeBox.setAttribute('id', 'upgradeBox');
					upgradeBox.update(transport.responseText);
					upgradeBox.hide();

					var spaceElement = workspace.getSpaceElement();
					spaceElement.appendChild(upgradeBox);
					Hfos.getUI().centerAtScreen(upgradeBox);

					upgradeBox.select('a.finishButton').each(function(element){
						element.observe('click', this.startUpgrade.bind(this));
					}.bind(this));

					this._upgradeBox = upgradeBox;
					new Effect.Appear(upgradeBox, {
						duration: 2.0
					});

				}.bind(this)
			});
		}
	},

	/**
	 * @this {HfosUpgradeBox}
	 */
	moveLeft: function(){
		new Effect.Move('upgradePages', {
			duration: 0.5,
			x: 870
		});
	},

	/**
	 * @this {HfosUpgradeBox}
	 */
	moveRight: function(){
		new Effect.Move('upgradePages', {
			duration: 0.5,
			x: -870
		});
	},

	/**
	 * @this {HfosUpgradeBox}
	 */
	startUpgrade: function(){
		this.moveRight();
		this.doUpgrade();
		window.setInterval(function(){
			var element = this._upgradeBox.selectOne('div.part'+this._position);
			new Effect.Appear(element, {
				duration: 1.2
			});
			this._position++;
			if(this._position==5){
				this._position = 1;
				var duration = 0.5;
				this._upgradeBox.select('div.part').reverse().each(function(element){
					new Effect.Fade(element, {
						duration: duration
					});
					duration+=0.1;
				})
			}
		}.bind(this), 1500);
	},

	/**
	 * @this {HfosUpgradeBox}
	 */
	doUpgrade: function(){
		new HfosAjax.JsonApplicationRequest('identity/upgrade/doUpgrade', {
			onSuccess: function(){
				this._upgradeBox.hide();
				this._upgradeBox.erase();
				var workspace = Hfos.getApplication().getWorkspace();
				workspace.getToolbar().show();
				Hfos.getUI().hideScreenShadow();
				this._onCourse = false;
			}.bind(this)
		});
	},

	/**
	 * @this {HfosUpgradeBox}
	 */
	isOnCourse: function(){
		return this._onCourse;
	}

});