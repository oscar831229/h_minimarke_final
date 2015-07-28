
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

function trans(obj1, obj2){

}

function select_all(){
	$$('input[type="checkbox"]').each(function(element){
		$(element).checked = true;
	})
}

function select_none(){
	$$('input[type="checkbox"]').each(function(element){
		$(element).checked = false;
	})
}

new Event.observe(document, 'dom:loaded', function(){
	$$('input[type="checkbox"]').each(function(element){
		element.observe('click', function(){
			element.up(4).toggleClassName('hab_pendent_sel');
		})
	});
	$$('img.arrow_asig').each(function(element){
		element.lang = element.title;
		element.title = 'Presione Para Mostrar/Ocultar Habitaciones Asignadas';
		element.observe('click', function(){
			new Effect.toggle(this.lang, "slide", {
				duration: 0.4
			});
		});
	});
	$$('div.cam_asig').each(function(element){
		Droppables.add(element, {
			hoverclass: 'hoverclass123',
			onDrop: function(obj1, obj2){
				window.location = '?action=asicam&option=4&n='+obj2.lang+'&h='+obj1.lang
			}
		});
	});
	$$('div.hab_to_ready').each(function(element){
		new Draggable(element, {
			revert: true
		})
	})
});
