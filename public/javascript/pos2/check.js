
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

var Screens = {

	_lastOrders: 0,

	_notWhistle: false,

	getContent: function(){
		new Ajax.Request(Utils.getKumbiaURL('check/getContent'), {
			parameters: {
				'screenId': $('screenId').getValue()
			},
			onSuccess: function(transport){
				$('content').update(transport.responseText);
				var opacity = 1.0;
				var rOpacity = 0.2;
				var orders = $$('tr.order_row');
				orders.each(function(element){
					if(opacity>0.1){
						element.style.background = "rgba(138, 189, 92, "+opacity+")";
						opacity-=0.1;
					} else {
						element.style.background = "rgba(153, 51, 0, "+rOpacity+")";
						rOpacity+=0.1;
					}
				});
				if(Screens._lastOrders!=orders.length){
					if(orders.length!=0){
						if(Screens._notWhistle==false){
							$('whistle').play();
						} else {
							Screens._notWhistle = false;
						}
					};
					Screens._lastOrders = orders.length;
				};
				$$('input.ready').each(function(element){
					element.observe('click', function(){
						new Ajax.Request(Utils.getKumbiaURL('check/status/'+this.lang), {
							onSuccess: function(){
								Screens._notWhistle = true;
								Screens.getContent();
							}
						});
					});
				})
			}
		});
	}

};

new Event.observe(document, "dom:loaded", function(){
	$('screenId').observe('change', Screens.getContent);
});

new Event.observe(window, "load", function(){
	Screens.getContent();
	window.setInterval(Screens.getContent, 10000);
});
