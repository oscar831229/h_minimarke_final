
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

function selectAll(){
	$$("input[type=\"checkbox\"]").each(function(element){
		element.checked = true;
	})
}

function repetirActions(element){
	if($F(element)=='D'||$F(element)=='M'||$F(element)=='A'){
		$("fecha_td").show();
		$("terminar_div").show();
		$("frec_diaria_div").hide();
		$("sem_diaria_div").hide();
		$("mens_diaria_div").hide();
	} else {
		$("terminar_div").hide();
		$("fecha_final_div").hide();
		if($F(element)=='DL'){
			$("frec_diaria_div").show();
			$("sem_diaria_div").hide();
			$("mens_diaria_div").hide();
		}
		if($F(element)=='SL'){
			$("frec_diaria_div").hide();
			$("sem_diaria_div").show();
			$("mens_diaria_div").hide();
		}
		if($F(element)=='ML'){
			$("frec_diaria_div").hide();
			$("sem_diaria_div").hide();
			$("mens_diaria_div").show();
			$("fecha_td").hide();
		}
	}
}
function terminarActions(element){
	if($F(element)=='F'){
		$("fecha_final_div").show();
	} else {
		$("fecha_final_div").hide();
	}
}
function valida(element){
	if($F("titulo").strip()==""){
		new Modal.alert({
			title: 'Recordatorios',
			message: "Debe indicar un titulo para el recordatorio"
		});
		$("titulo").activate();
		return;
	};
	element.disable();
	var url = element.form.serialize()+"&fecha="+$F("id_fecha")+"&fecha_final="+$F("id_fecha_final");
	new Ajax.Request("dispatch.php?action=eventsa&a=save", {
		parameters: url,
		onSuccess: function(element, transport){
			var response = transport.responseText.evalJSON();
			if(response.status=='OK'){
				$("messages_div").update("<div class='message_success'>"+response.message+"</div>");
				window.setTimeout(function(){
					window.location = "?action=events&option=13"
				}, 3000);
			} else {
				$("messages_div").update("<div class='message_error'>"+response.message+"</div>");
			}
			element.enable();
		}.bind(this, element)
	});
}
new Event.observe(window, "load", function(){

	$$('a.drop-event').each(function(element){
		element.observe('click', function(){
			new Modal.confirm({
				title: 'Recordatorios',
				message: '¿Seguro desea eliminar el recordatorio?',
				onAccept: function(eventId){
					window.location = "?action=events&option=13&del="+eventId
				}.bind(this, this.lang)
			});
		});
		element.lang = element.title;
		element.title = '';
	});

	$("titulo").activate();
})
