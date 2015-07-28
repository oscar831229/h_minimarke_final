
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Front-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var Revest = {

	dropSearch: function(){
		if(!$('printIcon').visible()){
			$('printIcon').show();
			$('orderList').show();
			$('searchBox').hide();
			new Event.stopObserving(window, 'click', Revest.dropSearch);
		}
	},

	prepareSearch: function(event){
		if($('printIcon').visible()){
			$('printIcon').hide();
			$('orderList').hide();
			$('searchBox').show();
			$('q').activate();
			window.setTimeout(function(){
				new Event.observe(window, 'click', Revest.dropSearch);
			}, 100);
		};
		new Event.stop(event);
	},

	doSearch: function(){
		if(this.value.length>1){
			$('normalView').hide();
			$('searchView').show();
			new Ajax.Request('dispatch.php?action=revbus&q='+this.value, {
				onSuccess: function(transport){
					$('searchView').update(transport.responseText);
				}
			});
		} else {
			$('normalView').show();
			$('searchView').hide();
		}
	},

	initialize: function(){
		$('tipOrd').observe('change', function(){
			window.location = "?action=revest&option=1&orderBy="+$F('tipOrd');
		});
		$('mainTitle').update("Estado de Habitaciones");
		$$('.habi').each(function(element){
			element.observe('mouseover', function(){
				this.setOpacity(0.75);
			});
			element.observe('mouseout', function(){
				this.setOpacity(1.0);
			})
		});
		$('findIcon').observe('click', Revest.prepareSearch)
		$('q').observe('keyup', Revest.doSearch);
	}

};

new Event.observe(document, "dom:loaded", Revest.initialize);