
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

function queryDisponibility(element){
	if($F(element)=="all"){
		$$(".dis").each(function(element){
			element.setOpacity(1.0);
		});
	} else {
		$$(".dis").each(function(element){
			element.setOpacity(0.4);
		});
		var dis_selected = $$(".dis_"+$F(element));
		if(dis_selected.length>0){
			dis_selected.each(function(element){
				element.setOpacity(1.0);
			});
		} else {
			var windowScroll = WindowUtilities.getWindowScroll(document.body);
    		var pageSize = WindowUtilities.getPageSize(document.body);
			var d = document.createElement("DIV");
			d.className = "error_list";
			d.hide();
			d.innerHTML = "<strong>No hay disponibilidad para ese número de noches en la habitación "+$F('flid_numhab')+"</strong>";
			document.body.appendChild(d);
			new Effect.Appear(d, {duration: 0.4});
			d.style.top = (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px";
			window.setTimeout(function(element){
				new Effect.Fade(element, {duration: 0.4});
			}.bind(this, d), 4000);
			new Event.observe(window, "scroll", function(d){
				if(d.visible()==true){
					var windowScroll = WindowUtilities.getWindowScroll(document.body);
    				var pageSize = WindowUtilities.getPageSize(document.body);
    				d.setStyle({
    					top: (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px"
    				});
				}
			}.bind(this, d));
		}
		element.blur();
	}
};

var Walkin = {

	getAvailability: function(){
		new Ajax.Request('webServices/getAvailability.php', {
			parameters: {
				'fecsal': $('flid_fecsal').getValue(),
				'numhab': $('flid_numhab').getValue()
			},
			onSuccess: function(transport){
				var response = transport.responseText.evalJSON();
				if(response.status=="ERROR"){
					var windowScroll = WindowUtilities.getWindowScroll(document.body);
		    		var pageSize = WindowUtilities.getPageSize(document.body);
		    		if(!$('ava_error_list')){
						var d = document.createElement("DIV");
						d.id = 'ava_error_list';
						d.className = "error_list red_error_list";
						d.hide();
						document.body.appendChild(d);
		    		};
					d.update("No hay disponibilidad para ese número de noches en la habitación "+$F('flid_numhab')+". <strong>"+response.message+"</strong>");
					new Effect.Appear(d, {duration: 0.4});
					d.style.top = (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px";
					window.setTimeout(function(element){
						new Effect.Fade(element, {duration: 0.4});
						element.remove();
					}.bind(this, d), 10000);
					new Event.observe(window, "scroll", function(d){
						if(d.visible()==true){
							var windowScroll = WindowUtilities.getWindowScroll(document.body);
		    				var pageSize = WindowUtilities.getPageSize(document.body);
		    				d.setStyle({
		    					top: (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px"
		    				});
						}
					}.bind(this, d));
				} else {
					var avaList = $('ava_error_list');
					if(avaList){
						avaList.remove();
					}
				}
			}
		});
	}

};

new Event.observe(document, 'dom:loaded', function(){
	CalendarManager.observe('fl_fecsal', 'change', Walkin.getAvailability);
	$('flid_numhab').observe('change', Walkin.getAvailability);
	$('flid_locdir_text').observe('blur', function(){
		var flidLocPro = $F('flid_locpro');
		var flidLocDes = $F('flid_locdes');
		if(flidLocPro=="0"||flidLocPro==""){
			$('flid_locpro').setValue($F('flid_locdir'));
			$('flid_locpro_text').setValue(this.value);
		}
		if(flidLocDes=="0"||flidLocDes==""){
			$('flid_locdes').setValue($F('flid_locdir'));
			$('flid_locdes_text').setValue(this.value);
		}
	});
	$('flid_locpro_text').observe('blur', function(){
		var flidLocDes = $F('flid_locdes');
		if(flidLocDes=="0"||flidLocDes==""){
			$('flid_locdes').setValue($F('flid_locpro'));
			$('flid_locdes_text').setValue(this.value);
			$('flid_locdes_text').activate();
		}
	});
})