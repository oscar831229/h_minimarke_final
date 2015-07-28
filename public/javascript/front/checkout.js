
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

function doCheckout(url){
	new Modal.confirm({
		title: "Check Out",
		message: "¿Está seguro de hacer check-out a esta habitación?",
		pronunce: "¿Está seguro de hacer chekaut a esta habitación?",
		onAccept: function(){
			window.location = "?action=checkout&option=3&"+url
		}
	});
}

function undoCheckout(url){
	new Modal.confirm({
		title: "Check Out",
		message: "¿Está seguro de deshacer este check-out?",
		pronunce: "¿Está seguro de deshacer este chekaut?",
		onAccept: function(){
			window.location = "?action=checkout&option=3&"+url
		}
	});
}

new Event.observe(document, 'dom:loaded', function(){
	var q = $('q');
	q.observe('keyup', function(){
		if(this.value.strip()==""||this.value=='Número Habitación...'){
			this.setStyle({
				'color': '#969696'
			});
			this.value = 'Número Habitación...'
			this.activate();
		} else {
			this.setStyle({
				'color': '#000000'
			});
		}
	})
	q.observe('focus', function(){
		if(this.value.strip()==""||this.value=='Número Habitación...'){
			this.setStyle({
				'color': '#969696'
			});
			this.value = 'Número Habitación...'
			this.activate();
		} else {
			this.setStyle({
				'color': '#000000'
			});
		}
	});
	q.observe('blur', function(){
		if(this.value.strip()==""||this.value=='Número Habitación...'){
			this.setStyle({
				'color': '#969696'
			});
			this.value = 'Número Habitación...'
		}
	});
	q.activate();
});