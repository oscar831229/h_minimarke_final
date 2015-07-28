
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

var Modal = {

	confirm: function(message, yesCallback){
		document.body.scrollTop = 0;
		new WINDOW.open({
			url: "context",
			title: "Confirmación",
			width: "500px",
			height: "200px",
			afterRender: function(message){
				$('contextMessage').update(message);
				//$('okModal').activate();
				$('okModal').observe('click', function(yesCallback){
					$('myWindow').close();
					yesCallback();
				}.bind(this, yesCallback));
				$('noModal').observe('click', function(){
					$('myWindow').close();
				});
			}.bind(this, message, yesCallback)
		});
	}

};

var Growler = {

	timeout: null,

	addTimeout: function(d){
		if(Growler.timeout!=null){
			window.clearTimeout(Growler.timeout);
			Growler.timeout = null;
		};
		Growler.timeout = window.setTimeout(function(d){
			document.body.removeChild(d);
			Growler.timeout = null;
		}.bind(this, d), 3500)
	},

	show: function(msg){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
	    var pageSize = WindowUtilities.getPageSize(document.body);
	    var d = $('growler');
	    if(!d){
			var d = document.createElement("DIV");
			d.id = "growler";
			d.innerHTML = msg;
			d.hide();
			document.body.appendChild(d);
			d.setStyle({
				top: (pageSize.windowHeight-(d.getHeight()+20)+windowScroll.top)+"px",
				left: (pageSize.windowWidth-270+windowScroll.left)+"px"
			});
			d.show();
			Growler.addTimeout(d);
	    } else {
	    	d.innerHTML = msg;
	    	Growler.addTimeout(d);
	    	new Effect.Shake(d, {duration:0.5});
	    }
	}
};
