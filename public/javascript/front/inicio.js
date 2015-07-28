var Tabs = {

	setActiveTab: function(element, number){
		if(element.hasClassName("active_tab")){
			return;
		} else {
			element.removeClassName("inactive_tab");
		};
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
		computeWindowSize();
	}
};

new Event.observe(document, "dom:loaded", function(){
	$('mainTitle').innerHTML = "Inicio de Hotel Front-Office Solution";
	if($Jasmin.high==1){
		$('left_dock').hide();
		$$('.n_link').each(function(element){
			element.observe('mouseover', function(){
				this.addClassName('n_link_hover');
			});
			element.observe('mouseout', function(){
				this.removeClassName('n_link_hover');
			});
		});
	}
});
if(typeof localStorage != "undefined"){
	new Event.observe(window, "load", function(){
		if($Jasmin.high==1){
			if(localStorage.speak==1){
				window.setTimeout(function(){
					new Ajax.Request('dispatch.php?action=welcome', {
						onSuccess: function(transport){
							if(transport.responseText!=""){
								Speak.speak(transport.responseText);
							}
						}
					});
				}, 5000);
			}
		}
	});
};

function loadWheater(){
	if($('tab4').innerHTML.indexOf('Desconocido')!=-1){
		$('load_wheater').show();
		new Ajax.Request('dispatch.php?action=getwheater', {
			onSuccess: function(transport){
				$('tab4').innerHTML = transport.responseText;
			}
		});
 	} else {
		$('load_wheater').hide();
 	}
}