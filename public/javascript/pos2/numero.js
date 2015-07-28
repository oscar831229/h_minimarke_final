
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

new Event.observe(window, "keyup", function(event){
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


