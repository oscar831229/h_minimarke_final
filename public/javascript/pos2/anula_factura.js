
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

var Tabs = {

	setActiveTab: function(element, number){
		if(element.hasClassName("active_tab")){
			return;
		} else {
			element.removeClassName("inactive_tab");
		}
		$$(".active_tab").each(function(tab_element){
			tab_element.removeClassName("active_tab");
		});
		$$(".tab_basic").each(function(tab_element){
			if(tab_element==element){
				tab_element.addClassName("active_tab");
			} else {
				tab_element.addClassName("inactive_tab");
			}
		});
		$$(".tab_content").each(function(tab_content){
			if(tab_content.id!="tab"+number){
				tab_content.hide();
			}
		});
		$("tab"+number).show();
	}

};


window.cancelNumber = function(){
	new Utils.redirectToAction('appmenu')
}

try {
	window.acceptNumber = function(){
		if($F("number").strip()!=''){
			new Ajax.Request("anula_factura/exists/"+$F("number")+"/"+$F("salon_id")+"/"+$F("tipo_venta"), {
				onSuccess: function(transport){
					var response = transport.responseText.evalJSON();
					if(response=='yes'){
						if(confirm("¿Seguro desea anular la orden/factura No. "+$F("number")+"?")){
							new Utils.redirectToAction('anula_factura/anula/'+hex_sha1($("number").value)+"/"+$F("salon_id")+"/"+$F("tipo_venta"))
						}
					} else {
						alert("No existe la factura/orden "+$("number").value+" en el ambiente indicado");
					}
				}
			});
		}
	}
}
catch(e){
	alert(e.stack)
}


new Event.observe(window, "load", function(){
	$('number').activate();
});