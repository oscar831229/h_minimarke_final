
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

var HfosScreenSaver = Class.create({

	_startTimeout: null,
	_started: false,
	_waitTime: 900000,

	/**
	 *
	 * @constructor
	 */
	initialize: function(){
		new Event.observe(window, 'click', this.cancelStart.bind(this));
		this._startTimeout = window.setTimeout(this.start.bind(this), this._waitTime)
	},

	/**
	 *
	 * @this {HfosScreenSaver}
	 */
	start: function(){
		var d = document.createElement('DIV');
		d.id = 'screen-saver';
		d.update('<div id="screen-saver-container"></div>');
		document.body.appendChild(d);
		swfobject.embedSWF($Kumbia.path+'files/backoffice/aurora.swf', "screen-saver-container", "1280", "700", "9.0.0", "expressInstall.swf", null, {wmode: "transparent"});
		this._started = true;
	},

	/**
	 *
	 * @this {HfosScreenSaver}
	 */
	stop: function(){
		$('screen-saver').erase();
		this._started = false;
	},

	/**
	 *
	 * @this {HfosScreenSaver}
	 */
	cancelStart: function(){
		if(this._started==true){
			this.stop();
		};
		if(this._startTimeout!=null){
			window.clearTimeout(this._startTimeout)
			this._startTimeout = null;
		}
		this._startTimeout = window.setTimeout(this.start.bind(this), this._waitTime)
	}

})
