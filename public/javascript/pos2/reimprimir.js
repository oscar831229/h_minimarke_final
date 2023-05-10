
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
	}

};

window.cancelNumber = function(){
	new Utils.redirectToAction('appmenu')
};

window.acceptNumber = function(){
	var type = $F('tipo_venta');
	var message;
	if($F("number").strip()!=''){
		if(AJAX.query("reimprimir/exists/"+$F("number")+"/"+$F("salon_id")+"/"+$F("tipo_venta")+"/"+$F("prefijo"))=='yes'){
			var url = Utils.getKumbiaURL('reimprimir/reprint/'+$F("number")+"/"+$F("salon_id")+"/"+$F("tipo_venta")+"/"+$F("prefijo"));
			window.open(url, null, 'width=300, height=700, toolbar=no, statusbar=no');
		} else {
			if(type=='F'){
				message = "No existe la factura "+$("number").value+" en el ambiente indicado";
			} else {
				message = "No existe la orden de servicio "+$("number").value+" en el ambiente indicado"
			};
			alert(message);
		}
	}
};


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

function addToNumber(obj, event){
	if(event.keyCode!=Event.KEY_RETURN){
	  	$('number').value+=obj.value;
	  	if($('number').value!=""){
		    $('okButton').disabled = false;
		};
		$('number').select();
	}
}

function dropNumber(){
	new Effect.Shake("number", {
		duration: 0.5
	});
	new Effect.Morph("number", {
		duration: 0.5,
		style: {
			color: "#FFFFFF"
		},
		afterFinish: function(){
			$('number').value = "";
			$('number').style.color = "#000000";
		}
	})
	$('okButton').disabled = true;
}

function cancelNumber(){
	$('myWindow').close('cancel');
}

function acceptNumber(){
  	$('myWindow').close('ok');
}

function big(obj){
	$(obj).className = "bigButton2";
	$(obj).style.fontSize = "44px";
	new Effect.Morph(obj, {
		duration: 0.3,
		style: {
			fontSize: "30px"
		},
		afterFinish: function(){
			$(obj).className = "bigButton";
			$(obj).style.width = "70px";
			$(obj).style.height = "70px";
			$(obj).style.fontSize = "30px";
		}
	})
}


window.addEventListener('load', function() {
    new Event.observe(window.document.querySelector('#number'), "keyup", function(event){
		try 
		{
			if($("myWindow")){
				$('number').select();
				var code = 0;
				var ev = parseInt(event.keyCode);
				if(ev==Event.KEY_BACKSPACE||ev==Event.KEY_ESC){
					dropNumber();
					new Event.stop(event);
					return;
				};
				if(ev==Event.KEY_RETURN){
					$("okButton").click();
					new Event.stop(event);
					return;
				};
				if(ev==190||ev==110){
					  if($('number').value.indexOf('.')==-1){
						var punto = new Object();
						if($('number').value.length==0){
							punto.value = "0.";
						} else {
							punto.value = ".";
						};
						addToNumber(punto, event);
						new Event.stop(event);
					}
				}
				if(ev>=48&&ev<=57){
					code = event.keyCode - 48;
					if($("b"+code)){
						big($("b"+code));
						addToNumber($("b"+code), event);
						new Event.stop(event);
						return;
					}
				};
				if(ev>=96&&ev<=105){
					code = event.keyCode - 96;
					if($("b"+code)){
						big($("b"+code));
						addToNumber($("b"+code), event);
						new Event.stop(event);
						return;
					}
				};
			}
		}
		catch(e){
			
		}
	})
});



/*new Event.observe(window, "keyup", function(event){
	var code = 0;
	if(parseInt(event.keyCode)>=48&&parseInt(event.keyCode)<=57){
		code = event.keyCode - 48;
		if($("b"+code)){
			big($("b"+code));
			addToNumber($("b"+code), event);
			new Event.stop(event);
			return;
		}
	}
	if(parseInt(event.keyCode)>=96&&parseInt(event.keyCode)<=105){
		code = event.keyCode - 96;
		if($("b"+code)){
			big($("b"+code));
			addToNumber($("b"+code), event);
			new Event.stop(event);
			return;
		}
	}
	if(event.keyCode==Event.KEY_RETURN){
		$("okButton").click();
		new Event.stop(event);
	}
});
*/