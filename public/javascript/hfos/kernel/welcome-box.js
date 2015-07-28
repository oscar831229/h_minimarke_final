
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

var HfosWelcomeBox = Class.create(HfosProcessContainer, {

	_onCourse: true,

	/**
	 * @constructor
	 */
	initialize: function(){
		if(this._onCourse==true){
			new HfosAjax.ApplicationRequest('identity/welcome', {
				onSuccess: function(transport){
					var windowManager = Hfos.getApplication().getWorkspace().getWindowManager();
					if(windowManager.hasModalWindow()==true){
						return;
					} else {
						windowManager.setModalWindow(this, function(transport){

							var welcomeBox = document.createElement('DIV');
							welcomeBox.setAttribute('id', 'welcomeBox');
							welcomeBox.update(transport.responseText);

							var spaceElement = Hfos.getApplication().getWorkspace().getSpaceElement();
							spaceElement.appendChild(welcomeBox);
							Hfos.getUI().centerAtScreen(welcomeBox);
							welcomeBox.hide();

							welcomeBox.select('a.prevButton').each(function(element){
								element.observe('click', this.moveLeft.bind(this));
							}.bind(this))

							welcomeBox.select('a.nextButton').each(function(element){
								element.observe('click', this.moveRight.bind(this));
							}.bind(this));

							welcomeBox.select('a.finishButton').each(function(element){
								element.observe('click', this.finishTour.bind(this));
							}.bind(this));

							Hfos.getUI().showScreenShadow();

							new Effect.Appear(welcomeBox, {
								duration: 0.3
							});

						}.bind(this, transport));
					}
				}.bind(this)
			});
		}
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	moveLeft: function(){
		new Effect.Move('welcomePages', {
			duration: 0.5,
			x: 870
		});
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	moveRight: function(){
		new Effect.Move('welcomePages', {
			duration: 0.5,
			x: -870
		});
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	finishTour: function(){
		if(this._onCloseProcess()==true){
			var welcomeBox = $('welcomeBox');
			if(welcomeBox){
				new Effect.Fade(welcomeBox, {
					duration: 0.3,
					afterFinish: function(welcomeBox){
						Hfos.getUI().hideScreenShadow();
						Hfos.getApplication().getWorkspace().getWindowManager().removeModalWindow();
						welcomeBox.erase();
						new HfosAjax.ApplicationRequest('identity/workspace/setPageCheck', {
							parameters: 'pageName=welcome'
						});
						this._onCourse = false;
					}.bind(this, welcomeBox)
				});
			}
		}
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	close: function(){
		this.finishTour();
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	sendKeyEvent: function(event){
		if(event.keyCode==Event.KEY_LEFT){
			this.moveLeft();
		} else {
			if(event.keyCode==Event.KEY_RIGHT){
				this.moveRight();
			}
		}
	},

	/**
	 * @this {HfosWelcomeBox}
	 */
	isOnCourse: function(){
		return this._onCourse;
	}

});