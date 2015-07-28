
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

function dropPassword(){
	new Effect.Shake("view_pass", {duration: 0.4});
	new Effect.Morph("view_pass", {
		duration: 0.4,
		style: {
			color: "#FFFFFF"
		},
		afterFinish: function(){
			$('view_pass').value = "";
			$('view_pass').style.color = "#000000";
		}
	})
	$('clave').value = "";
	$('okButton').disabled = true;
	if($("password_incorrecto")){
		document.body.removeChild($("password_incorrecto"));
	}
}

function cancelPassword(){
	$("myWindow").close();
}

function acceptPassword(action){
	new Ajax.Request(Utils.getKumbiaURL("clave/autenticar/"+hex_sha1($('clave').value)), {
		method: 'GET',
		onSuccess: function(transport){
			var response = transport.responseText.evalJSON();
			if(response==1){
				new Utils.redirectToAction(action);
			} else {
				var windowScroll = WindowUtilities.getWindowScroll(document.body);
		    	var pageSize = WindowUtilities.getPageSize(document.body);
		    	var d = $('growler');
		    	if(!d){
					var d = document.createElement("DIV");
					d.id = "growler";
					d.setStyle({
						top: (pageSize.windowHeight-50+windowScroll.top)+"px"
					});
					d.innerHTML = "<b>Contraseña Incorrecta</b>";
					document.body.appendChild(d);
					new Effect.Move("view_pass", {
						duration: 0.1,
						y: -10,
						afterFinish: function(){
							new Effect.Move("view_pass", {
								duration: 0.1,
								y: 10,
								afterFinish: function(){
									$('view_pass').value = "";
									$('clave').value = '';
									new Effect.Shake(d, {duration: 0.4});
								}
							});
						}
					});
		    	} else {
		    		$('view_pass').value = "";
					$('clave').value = '';
					new Effect.Shake(d, {duration: 0.4});
		    	};
			};
		}
	});
}

var Clave = {

	addToPassword: function(pressed, event){
		var value = "";
		var oClave = $('clave');
		var viewPass = $('view_pass');
		this.blur();
		if(pressed){
			if(typeof event.keyCode != "undefined"){
				if(event.keyCode==Event.KEY_RETURN||event.keyCode==0){
					return;
				}
			} else {
				return;
			};
		};
		oClave.value+=this.value;
		if(oClave.value!=""){
			$('okButton').disabled = false;
		};
		viewPass.value = "";
		for(var i=0;i<oClave.value.length;i++) {
			viewPass.value+="*";
		}
	},

	big: function(event){
		this.className = "bigButton2";
		this.style.fontSize = "44px";
		new Effect.Morph(this, {
			duration: 0.3,
			style: {
				fontSize: "30px"
			},
			afterFinish: function(){
				this.className = "bigButton";
				this.style.width = "70px";
				this.style.height = "70px";
				this.style.fontSize = "30px";
			}.bind(this)
		});
		this.blur();
	},

	authForModule: function(action){
		new WINDOW.open({
			url: "clave/index/"+action,
			title: "Digite su Clave",
			width: "230px",
			height: "380px",
			afterRender: function(){
				$$('.bigButton').each(function(element){
					element.observe('click', Clave.addToPassword.bind(element, false));
					element.observe('mousedown', Clave.big);
				});
				window.setTimeout(function(){
					$('view_pass').activate();
				}, 100);
			}
		});
	}
};

new Event.observe(window, "keyup", function(event){
	if($("myWindow")){
		var code = 0;
		var keyCode = parseInt(event.keyCode);
		if(keyCode==Event.KEY_BACKSPACE||keyCode==Event.KEY_ESC){
			dropPassword();
			new Event.stop(event);
			return;
		};
		if(keyCode==Event.KEY_RETURN){
			$("okButton").click();
			new Event.stop(event);
			return;
		};
		if(keyCode>=48&&keyCode<=57){
			code = keyCode - 48;
			var bCode = $("b"+code);
			if(bCode){
				Clave.big.bind(bCode)(event);
				Clave.addToPassword.bind(bCode)(true, event);
				new Event.stop(event);
				return;
			}
		};
		if(keyCode>=96 && keyCode<=105){
			code = keyCode - 96;
			var bCode = $("b"+code);
			if(bCode){
				Clave.big.bind(bCode)(event);
				Clave.addToPassword.bind(bCode)(true, event);
				new Event.stop(event);
				return;
			}
		}
	}
});

function changePassword(){
	acceptPassword = function(){
		new Ajax.Request(Utils.getKumbiaURL("clave/autenticar/"+hex_sha1($('clave').value)), {
			onSuccess: function(transport){
				var response = transport.responseText.evalJSON();
				if(response==1){
					$("myWindow").onclose = function(){
						acceptPassword = function(){
							new Ajax.Request(Utils.getKumbiaURL('clave/change'), {
								parameters: {
									"nuevo": hex_sha1($('clave').value)
								},
								onSuccess: function(t){
									$("myWindow").onclose = function(){

									};
									$("myWindow").close();
									alert('Se cambió el password');
									window.location = Utils.getKumbiaURL();
								}
							});
						};
						new WINDOW.open({
							url: "clave/input/",
							title: "Nueva Clave",
							width: "220px",
							height: "380px",
							afterRender: function(){
								$('clave').value="";
								$$('.bigButton').each(function(element){
									element.observe('click', Clave.addToPassword.bind(element, false));
									element.observe('mousedown', Clave.big);
								});
							}
						});
					};
					$("myWindow").close();
				} else {
					alert("Contraseña incorrecta")
					window.location = Utils.getKumbiaURL();
				}
			}
		});
	};
	cancelPassword = function(){
		window.location = Utils.getKumbiaURL();
	};
	new WINDOW.open({
		url: "clave/input/",
		title: "Digite su Clave",
		width: "220px",
		height: "380px",
		afterRender: function(){
			$$('.bigButton').each(function(element){
				element.observe('click', Clave.addToPassword.bind(element, false));
				element.observe('mousedown', Clave.big);
			});
		}
	});
}
