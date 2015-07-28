
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

var editRoom = false;
var drags = [];
var Mesas = {};
Mesas.venta_a = 'C';

var Growler = {

	timeout: null,

	addTimeout: function(d){
		if(Growler.timeout!=null){
			window.clearTimeout(Growler.timeout);
			Growler.timeout = null;
		};
		Growler.timeout = window.setTimeout(function(d){
			document.body.removeChild(d);
			Growler.timeout = null;
		}.bind(this, d), 3500)
	},

	show: function(msg){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
	    var pageSize = WindowUtilities.getPageSize(document.body);
	    var d = $('growler');
	    if(!d){
			var d = document.createElement("DIV");
			d.id = "growler";
			d.setStyle({
				top: (pageSize.windowHeight-100+windowScroll.top)+"px",
				left: (pageSize.windowWidth-250+windowScroll.left)+"px"
			});
			d.innerHTML = msg;
			document.body.appendChild(d);
			Growler.addTimeout(d);
	    } else {
	    	d.innerHTML = msg;
	    	Growler.addTimeout(d);
	    }
	}
};

function maximoMesa(){
	var maximo = 0;
	$$('.numeroMesaInp').each(function(element){
		var value = parseInt(element.value);
		if(value>maximo){
			maximo = value;
		}
	});
	$$('span.numeroOcupado').each(function(element){
		var value = parseInt(element.innerHTML);
		if(value>maximo){
			maximo = value;
		}
	});
	return maximo+1;
};

function newMesa(){
	var maximo = maximoMesa();
	var d = $(document.createElement('DIV'));
	d.addClassName("newMesa");
	d.addClassName("mesaButton");
	d.innerHTML = "<br><img src='"+$Kumbia.path+"img/pos2/mesa.png' width='30'/><span class='numeroMesa'><br/><input type='text' size='4' class='numeroMesaInp' value='"+maximo+"'/></span>";
	d.observe('click', clickTable);
	return d;
};

function showSalon(id){
  	new Utils.redirectToAction('tables/index/'+id);
};

function deleteTable(obj, id){
  new AJAX.viewRequest({action:"tables/deleteTable/"+id, container:"messages"});
};

function addTableHere(){
	if(editRoom==true){
		if(Mesas.venta_a=='C'){
			var newMesaDiv;
			var dim = this.id.replace("tx", "").split("_");
			var x = dim[0];
			var y = dim[1];
			if(this.innerHTML=="&nbsp;"){
				this.innerHTML = "";
				Droppables.remove(this);
				newMesaDiv = newMesa();
				this.appendChild(newMesaDiv);
				clickTable.bind(newMesaDiv)();
				new Ajax.Request(Utils.getKumbiaURL('tables/addTable'), {
					parameters: {
						"x": x,
						"y": y
					},
					onSuccess: function(newMesaDiv, transport){
						var id = transport.responseText.evalJSON();
						newMesaDiv.id = id;
						drags[id] = new Draggable(newMesaDiv, {revert: true});
						$$('#'+id+' .numeroMesaInp').each(function(element){
							element.lang = element.value;
							element.observe('blur', changeNumberMesa);
							element.observe('focus', focusNumberMesa);
							element.observe('keyup', keyUpNumberMesa);
						});
					}.bind(this, newMesaDiv)
				});
				if(activeSet==true){
					var parent = $(activeSet).parentNode;
					parent.innerHTML = "&nbsp;";
					new Effect.Fade($(activeSet), {duration: 0.5});
					$("new_mesa").id = $(activeSet).id;
				};
			}
		}
	}
}

function borrarMesa(mesa, me, event){
	var parent = $(mesa).parentNode;
	if(parent.tagName!='TR'){
		new Ajax.Request(Utils.getKumbiaURL("tables/deleteTable/"+mesa.id), {
			onSuccess: function(mesa, event, transport){
				var d = document.createElement('DIV');
				var parent = $(mesa).parentNode;
				var mouseX = Event.pointerX(event)-80;
				var mouseY = Event.pointerY(event)-80;
				d.setStyle({
					position: "absolute",
					top: mouseY+"px",
					left: mouseX+"px"
				});
				d.innerHTML = "<img src='"+$Kumbia.path+"img/pos2/poof.png'/>"
				document.body.appendChild(d);
				new Effect.Fade(d);
				parent.removeChild(mesa);
				parent.innerHTML = "&nbsp;";
			}.bind(this, mesa, event)
		});
	}
}

function seleccionarBoton(button){
	$$(".salonOption").each(function(element){
		element.addClassName("salonOptionUnselected");
	});
	button.removeClassName("salonOptionUnselected")
}

function showClientes(button){
	$$('.mesaButton').each(function(element){
		element.show();
	});
	$$('.mesaButton2').each(function(element){
		element.show();
	});
	$$('.mesero_name').each(function(element){
		element.hide();
	});
	$$('.cliente_name').each(function(element){
		element.show();
	});
	seleccionarBoton(button);
}

function showLibres(button){
	$$('.mesaButton2').each(function(element){
		element.hide();
	});
	$$('.mesaLibre').each(function(element){
		element.show();
	});
	seleccionarBoton(button);
}

function showAbiertas(button){
	$$('.mesaButton').each(function(element){
		element.hide();
	});
	$$('.mesaOcupada').each(function(element){
		element.show();
	});
	seleccionarBoton(button);
}

function showTodas(button){
	$$('.mesaButton').each(function(element){
		element.show();
	});
	$$('.mesaButton2').each(function(element){
		element.show();
	});
	seleccionarBoton(button);
}

function showMisMesas(id, button){
	$$('.mesaButton').each(function(element){
		element.hide();
	});
	$$('.mesaButton2').each(function(element){
		element.hide();
	});
	$$('.mesero'+id).each(function(element){
		element.show();
	});
	seleccionarBoton(button);
}

function mesasShortCut(event){
	if(event.keyCode==68){
		showTodas($('showTodas'));
		return;
	};
	if(event.keyCode==65){
		showAbiertas($('showAbiertas'));
		return;
	};
	if(event.keyCode==76){
		showLibres($('showLibres'));
		return;
	};
	if(event.keyCode==77){
		showMisMesas($('showMisMesas'));
		return;
	};
	if(event.keyCode==67){
		showClientes($('showClientes'));
		return;
	}
};

function clickTable(){
	if(editRoom){
		$$('div.selectedTable').each(function(element){
			element.removeClassName('selectedTable');
		});
		this.addClassName('selectedTable');
	} else {
		new Utils.redirectToAction("order/add/"+this.id);
	}
};

function changeCoors(mesa1, mesa2){
	var dim = mesa1.parentNode.id.replace("tx", "").split("_");
	var x = dim[0];
	var y = dim[1];
	new Ajax.Request(Utils.getKumbiaURL("tables/moveTable/"+mesa2.id), {
		parameters: {
			"x": x,
			"y": y
		},
		onSuccess: function(transport){
			$('messages').update(transport.responseText);
		}
	});
};

function moveTableToHere(mesa, space){
	var dim, newMesa, x, y;
	var tableCell = mesa.parentNode;
	Droppables.remove(space);
	space.innerHTML = tableCell.innerHTML;
	tableCell.innerHTML = "&nbsp;";
	Droppables.add(tableCell, {
		hoverclass: "overTable",
		onDrop: moveTableToHere
	});
	$$('#'+space.id+' .mesaB').each(function(element){
		element.observe('click', clickTable);
	});
	$$('#'+space.id+' .numeroMesaInp').each(function(element){
		element.observe('blur', changeNumberMesa);
		element.observe('focus', focusNumberMesa);
		element.observe('keyup', keyUpNumberMesa);
	});
	dim = space.id.replace('tx', '').split('_');
	x = dim[0];
	y = dim[1];
	newMesa = space.firstDescendant();
	newMesa.setStyle({
		'position': 'relative',
		'top': '0px',
		'left': '0px'
	});
	newMesa.setOpacity(1.0);
	drags[mesa.id] = new Draggable(newMesa, {revert: true});
	new Ajax.Request(Utils.getKumbiaURL('tables/moveTable/'+mesa.id), {
		parameters: {
			'x': x,
			'y': y
		},
		onSuccess: function(transport){
			$('messages').update(transport.responseText);
		}
	});
};

function changeNumberMesa(){
	if(this.lang==this.value){
		return;
	};
	var existeMesa = false;
	var numeroMesa = this;
	var mesaRepetida;
	var numeroOcupado = false;
	$$('span.numeroOcupado').each(function(element){
		if(element.innerHTML==numeroMesa.value){
			numeroOcupado = true;
			return;
		}
	});
	if(numeroOcupado==true){
		Growler.show('No es posible usar el número de mesa "'+numeroMesa.value+'" porque se encuentra en uso');
		numeroMesa.value = numeroMesa.lang;
		numeroMesa.activate();
		return;
	};
	$$('input.numeroMesaInp').each(function(element){
		if(element!=numeroMesa){
			if(element.value==numeroMesa.value){
				existeMesa = true;
				mesaRepetida = element;
			}
		}
	});
	if(existeMesa){
		numeroMesa.value = numeroMesa.lang;
		numeroMesa.blur();
		clickTable.bind(mesaRepetida.parentNode.parentNode)();
		interchangePosition(numeroMesa, mesaRepetida);
	} else {
		numeroMesa.lang = numeroMesa.value;
		new Ajax.Request(Utils.getKumbiaURL('tables/changeNumber/'+numeroMesa.parentNode.parentNode.id), {
			parameters: {
				"numero": numeroMesa.value
			},
			onSuccess: function(){
				numeroMesa.blur();
				numeroMesa.value = numeroMesa.lang;
			}.bind(this, numeroMesa)
		});
	}

};

function interchangePosition(div1, div2){
	var x1, y1, x2, y2;
	var parentDiv1 = div1.parentNode.parentNode;
	var parentDiv2 = div2.parentNode.parentNode;
	var position1 = parentDiv1.positionedOffset();
	var position2 = parentDiv2.positionedOffset();
	if(position1[1]>position2[1]){
		y1 = -(Math.abs(position1[1]-position2[1]));
	} else {
		y1 = Math.abs(position1[1]-position2[1]);
	};
	if(position1[0]>position2[0]){
		x1 = -(Math.abs(position1[0]-position2[0]));
	} else {
		x1 = Math.abs(position1[0]-position2[0]);
	};
	new Effect.Move(parentDiv1, {
		"y": y1,
		"x": x1,
		afterFinish: function(parentDiv1, parentDiv2){
			drags[parentDiv1.id].destroy();
			parentDiv1.style.position = "relative";
			drags[parentDiv1.id] = new Draggable(parentDiv1, {revert:true});
			changeCoors(parentDiv1, parentDiv2);
		}.bind(this, parentDiv1, parentDiv2)
	});
	if(position1[1]>position2[1]){
		y2 = Math.abs(position1[1]-position2[1]);
	} else {
		y2 = -(Math.abs(position1[1]-position2[1]));
	};
	if(position1[0]>position2[0]){
		x2 = Math.abs(position1[0]-position2[0]);
	} else {
		x2 = -(Math.abs(position1[0]-position2[0]));
	};
	new Effect.Move(parentDiv2, {
		"y": y2,
		"x": x2,
		afterFinish: function(parentDiv2){
			drags[parentDiv2.id].destroy();
			parentDiv2.style.position = "relative";
			drags[parentDiv2.id] = new Draggable(parentDiv2, {revert:true});
			changeCoors(parentDiv2, parentDiv1);
		}.bind(this, parentDiv2, parentDiv1)
	});
};

function focusNumberMesa(){
	window.setTimeout(function(){
		this.activate();
	}.bind(this), 100);
};

function keyUpNumberMesa(event){
	if(event.keyCode==Event.KEY_RETURN){
		changeNumberMesa.bind(this)();
	}
};

new Event.observe(document, 'dom:loaded', function(){
	Droppables.add('basura', { onDrop: borrarMesa });
	var editarButton = $('editarButton');
	if(editarButton){
		editarButton.lang = editarButton.title;
		editarButton.title = "";
		editarButton.observe('click', function(){
			if(editRoom==false){
				editRoom = true;
				window.clearTimeout(mesasTimeout);
				$('salonOptions').hide();
				$('viewOptions').hide();
				$('selectMesa').hide();
				this.value = "Aplicar";
				new Effect.Appear("basura", {duration:0.5});
				$$('.mesaB').each(function(element){
					if(element.hasClassName('mesaSelected')){
						element.removeClassName('mesaSelected');
					}
					element.setStyle('cursor:move');
				});
				$$('.mesaButton').each(function(element){
					drags[element.id] = new Draggable(element, {revert:true});
				});
				$$('.tableCell').each(function(element){
					if(element.innerHTML=="&nbsp;"){
						Droppables.add(element, {
							hoverclass: "overTable",
							onDrop: moveTableToHere
						});
					}
				});
				$$('.numeroMesa').each(function(element){
					var numeroMesa = element.innerHTML;
					var tableCell = element.parentNode;
					var tableImg = tableCell.childNodes[1];
					tableImg.style.width = "30px";
					element.update('<INPUT TYPE="text" value="'+numeroMesa+'" size="4" title="'+numeroMesa+'" class="numeroMesaInp"/>');
				});
				$$('.numeroMesaInp').each(function(element){
					element.lang = element.title;
					element.title = "";
					element.observe('blur', changeNumberMesa);
					element.observe('focus', focusNumberMesa);
					element.observe('keyup', keyUpNumberMesa);
				});
			} else {
				window.location = Utils.getKumbiaURL("tables/index/"+this.lang);
			}
		});
	};
	$('cancelButton').observe('click', function(){
		window.location = Utils.getKumbiaURL("appmenu/");
	});
	$$('.mesaB').each(function(element){
		element.observe('click', clickTable);
	});
	$$('.tableCell').each(function(element){
		element.observe('dblclick', addTableHere);
	});
});
new Event.observe(window, "keyup", mesasShortCut);

var mesasTimeout = window.setTimeout(function(){
	window.location = Utils.getKumbiaURL();
}, 300000)
