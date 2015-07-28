
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Back-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var Mail = {

	originalCaption: "",

	initCheckMail: function(){
		new Mail.checkMail();
		window.setInterval(Mail.checkMail, 300000);
	},

	checkMail: function(){
		new Ajax.Request("dispatch.php?action=imap", {
			onSuccess: function(t){
				var response = t.responseText.evalJSON();
				var numberMessages = parseInt(response);
				if(numberMessages>0){
					if(numberMessages==1){
						document.title = '1 Mensaje Nuevo - CORREO ELECTRÓNICO';
					} else {
						document.title = numberMessages+' Mensajes Nuevo - CORREO ELECTRÓNICO';
					};
					new Ajax.Request("dispatch.php?action=refmail", {
						onSuccess: function(t){
							$('mailContent').update(t.responseText);
						}
					});
				} else {
					if(response!="0"){
						alert(t.responseText);
					}
				};
				$$('.menu_option_atd').each(function(element){
					element.innerHTML = Mail.originalCaption;
				});
			}
		});
	}

};

function select_message(obj, message_id, restore_color){
	if(obj.checked==true){
		$('message_'+message_id).style.background = '#ffffcc';
	} else {
		$('message_'+message_id).style.background = restore_color;
	}
	$("select_combo").selectedIndex = 0;
}

function select_all(){
	$$("input[type='checkbox']").each(function(input){
		input.checked = true;
		$('message_'+input.value).style.background = '#ffffcc'
	});
}

function select_none(){
	var i = 0;
	$$("input[type='checkbox']").each(function(input){
		input.checked = false;
		$('message_'+input.value).style.background = '#f1f5fa';
		i++;
	});
}

function select_unread(){
	var i = 0;
	select_none();
	$$(".message_unread").each(function(input){
		input.checked = true;
		$('message_'+input.value).style.background = '#ffffcc';
		i++;
	});
}

function select_read(){
	var i = 0;
	select_none();
	$$(".message_readed").each(function(input){
		input.checked = true;
		$('message_'+input.value).style.background = '#ffffcc';
		i++;
	});
}

function delete_selection(){
	var messages = [];
	$$("input[type='checkbox']").each(function(input){
		if(input.checked==true){
			messages[messages.length] = input.value;
		}
	})
	if(messages.length>0){
		window.location = "?action=mailaction&a=delmes&option=10&mb=inbox&id="+messages.join(",");
	} else {
		alert("No hay mensajes selecionados");
	}
}

function delete_def_selection(){
	var messages = []
	$$("input[type='checkbox']").each(function(input){
		if(input.checked==true){
			messages[messages.length] = input.value;
		}
	});
	if(messages.length>0){
		window.location = "?action=mailaction&a=deletedef&option=10&mb=<?= $mailbox ?>&id="+messages.join(",");
	} else {
		alert("No hay mensajes selecionados");
	}
}

function mark_as_read_selection(){
	var messages = [];
	$$("input[type='checkbox']").each(function(input){
		if(input.checked==true){
			messages[messages.length] = input.value;
		}
	});
	if(messages.length>0){
		window.location = "?action=mailaction&a=markread&option=10&mb=<?= $mailbox ?>&id="+messages.join(",");
	} else {
		alert("No hay mensajes selecionados");
	}
}

function mark_as_unread_selection(){
	var messages = []
	$$("input[type='checkbox']").each(function(input){
		if(input.checked==true){
			messages[messages.length] = input.value;
		}
	})
	if(messages.length>0){
		window.location = "?action=mailaction&a=markunread&option=10&mb=<?= $mailbox ?>&id="+messages.join(",");
	} else {
		alert("No hay mensajes selecionados");
	}
}

function move_to_inbox_selection(){
	var messages = []
	$$("input[type='checkbox']").each(function(input){
		if(input.checked==true){
			messages[messages.length] = input.value
		}
	})
	if(messages.length>0){
		window.location = "?action=mailaction&a=movetoinbox&option=10&mb=<?= $mailbox ?>&id="+messages.join(",");
	} else {
		alert("No hay mensajes selecionados")
	}
}

function do_select(element){
	if($F(element)=='all'){
		select_all();
	}
	if($F(element)=='none'){
		select_none();
	}
	if($F(element)=='unread'){
		select_unread();
	}
	if($F(element)=='read'){
		select_read();
	}
}

function do_action(element){
	if($F(element)=='delete'){
		delete_selection();
	};
	if($F(element)=='read'){
		mark_as_read_selection();
	};
	if($F(element)=='unread'){
		mark_as_unread_selection();
	};
	if($F(element)=='inbox'){
		move_to_inbox_selection();
	};
	if($F(element)=='delete_def'){
		delete_def_selection();
	};
	element.selectedIndex = 0;
}

function showAttachFiles(element){
	element.hide();
	$("tr_file1").show();
	$("tr_file2").show();
	$("tr_file3").show();
}

function send_mail(){
	if($F('to_box').strip()==''){
		$('to_box').activate();
		alert('No ha especificado un destinatario');
		new Effect.Highlight('to_box');
		return;
	}
	if($F('subject').strip()==''){
		if(!confirm('El mensaje no tiene asunto, Desea continuar?')){
			new Effect.Highlight('subject')
			$('subject').activate();
			return;
		}
	}
	$('mail_form').submit();
}

function calculate_size(element){
	var length = element.value.strip().length;
	var height = (parseInt(length/95)+1)*23;
	new Effect.Morph(element, {
		style: {
			height: height+"px"
		}
	});
}

function add_to_box(element, box){
	element = $(element);
	if($F(element)!='@'){
		if($(box).value.strip().length==0){
			$(box).value = $F(element);
		} else {
			$(box).value+=", "+$F(element);
		}
		var length = $(box).value.strip().length
		var height = (parseInt(length/95)+1)*23;
		new Effect.Morph($(box), {
			style: {
				height: height+"px"
			}
		});
	}
	element.selectedIndex = 0
	//alert($F(element))
}

function go_inbox(){
	if(confirm('Desea descartar este mensaje?')){
		window.location = "?action=mail&option=10";
	}
}

new Event.observe(window, "load", function(){
	$('mainTd').style.padding = "0px";
	$('mTr').style.backgroundColor = "#ffffff";
	$('mTr2').style.backgroundColor = "#ffffff";
	if($Jasmin.action=='newmes'){
		new Ajax.Autocompleter("to_box", "to_choices", "webServices/AutocompleteDirectory.php", {
		 	paramName: "id",
		 	minChars: 2,
		 	tokens: [","],
		 	afterUpdateElement: function(){
	 			$("to_choices").removeClassName('autocomplete');
	 			$("to_choices").addClassName('autocomplete');
	 		}
		});
		new Ajax.Autocompleter("cc_box", "cc_choices", "webServices/AutocompleteDirectory.php", {
		 	paramName: "id",
		 	minChars: 2,
		 	tokens: [","],
		 	afterUpdateElement: function(){
	 			$("cc_choices").removeClassName('autocomplete');
	 			$("cc_choices").addClassName('autocomplete');
	 		}
		});
		$('mainTitle').update('Redactar Mensaje');
		$("to_box").activate();
		$('subject').observe('keyup', function(){
			if(this.value==''){
				$('mainTitle').update('Redactar Mensaje');
			} else {
				$('mainTitle').update(this.value+' - Redactar Mensaje');
			}
		});
		if($('subject').value!=""){
			$('mainTitle').update($('subject').value+' - Redactar Mensaje');
		}
	} else {
		if($Jasmin.action=='readmes'){
			$('mainTd').style.padding = "0px"
			$('mTr').style.backgroundColor = "#ffffff"
			$('mTr2').style.backgroundColor = "#ffffff"
			var dim = $('message_content').getDimensions();
			if($('message_content').getHeight()<300){
				$('message_content').setStyle({height:"300px"});
			};
			var messageIframe = $('message_iframe');
			messageIframe.setStyle({
				'height': (messageIframe.contentDocument.body.scrollHeight+100)+'px'
			});
		} else {
			if($Jasmin.action=='mail'){
				window.setTimeout(function(){
					$$('.menu_option_atd').each(function(element){
						Mail.originalCaption = element.innerHTML;
						element.innerHTML+="<div style='float:right;display:table-cell'><img src='img/mail-load.gif'></div>";
					});
					new Mail.initCheckMail();
				}, 15000);
			}
		}
	}
});
