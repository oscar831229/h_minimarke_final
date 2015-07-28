
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

var numfol;
var numcue;
var num_items;
var total_items;
var genfac = 0;

var Notificator = {

	notify: function(text){
		window.setTimeout(function(){
			var windowScroll = WindowUtilities.getWindowScroll(document.body);
    		var pageSize = WindowUtilities.getPageSize(document.body);
			var d = document.createElement("DIV");
			d.className = "error_list";
			d.hide();
			d.innerHTML = "<strong>"+text+"</strong>";
			document.body.appendChild(d);
			new Effect.Appear(d, {duration: 0.4});
			d.style.top = (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px";
			window.setTimeout(function(element){
				new Effect.Fade(element, {duration: 0.4});
			}.bind(this, d), 4000);
			new Event.observe(window, "scroll", function(d){
				if(d.visible()){
					var windowScroll = WindowUtilities.getWindowScroll(document.body);
    				var pageSize = WindowUtilities.getPageSize(document.body);
    				d.setStyle({
    					top: (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px"
    				});
				}
			}.bind(this, d));
		}, 800);
	}

};

function setFactList(){
	var items = response.responseXML.getElementsByTagName("row");
	getObj("ftotal").value = items[0].getAttribute("total");
	getObj("fiva").value = items[0].getAttribute("iva");
	getObj("fvalter").value = items[0].getAttribute("valter");
	getObj("fvalser").value = items[0].getAttribute("valser");
	$("diva").innerHTML = items[0].getAttribute("diva");
};

function getDetails(num, scroll){
	numcue = num;
	for(var i=0;i<=10;i++){
		if($("a"+i)){
			$("a"+i).style.background = "url('img/s_button.png')";
		}
	};
	$("a"+num).style.background = "url('img/sa_button.png')";
	$("filter_list").selectedIndex = 0;
	loadCargos(num, numfol, scroll);
};

function loadCargos(numcue, numfol, scroll){
	new Ajax.Request('webServices/getCargos.php', {
		method: 'GET',
		parameters: {
			'numcue': numcue,
			'numfol': numfol
		},
		onSuccess: function(numcue, numfol, scroll, transport){
			num_items = 0;
			if(typeof JSON == "undefined"){
				var items = transport.responseText.evalJSON();
			} else {
				var items = JSON.parse(transport.responseText);
			};
			var tHtml = "<br><table cellspacing='0' width='100%' id='cargohab'>";
			var txt = "";
			if((items.length>0)){
				tHtml+="<tr>"+
				"<th><img src='img/arrow_down.gif' style='cursor:pointer;border:none' title='Seleccionar Todos' onclick='selectAll()'/></td>"+
				"<th>Fecha</th>"+
				"<th>Concepto</th>"+
				"<th>Documento</th>"+
				"<th>Valor</th>"+
				"<th>Cantidad</th>"+
				"<th>IVA</th>"+
				"<th>Servicio</th>"+
				"<th>Terceros</th>"+
				"<th>Total</th>"+
				"</tr>"
			};
			$("acc_details").innerHTML = "";
			var color = "#CCDEFF";
			var class_name = 'cargo_normal cargo_activo';
			var imtx = "";
			total_items = 0;
			if(items.length==2){
				tHtml+="<tr>"+
				"<td class='nb'></td>"+
				"<td bgcolor='#ccdeff' colspan='9' style='border:1px solid #969696;padding:5px' align='center'>"+
				"NO HAY MOVIMIENTO EN ESTA CUENTA</td>" +
				"</tr>";
				$("filtroTD").hide();
			} else {
				$("filtroTD").show();
			};
			for(var i=0;i<items.length;i++){
				if(typeof items[i].estado != 'undefined'){
					if(items[i].estado=='A'){
						ccolor = '#FFF4F6';
						class_name = 'cargo_auditado cargo_activo';
						if(items[i].hnote=='S'){
							imtx = "<td align='center' style='background-color:"+ccolor+"'><img src='";
							imtx+="img/note_aud.png";
							imtx+="' style='cursor:pointer' " +
							"title='Agregar una nota' onclick=\"Notes.setNote("+
							items[i].item+","+
							items[i].numfol+","+
							items[i].numcue+")\"/></td>";
						} else {
							imtx = "";
						};
						num_items++;
					} else {
						if(items[i].estado=='B'){
							ccolor = "#E4E4E4";
							class_name = 'cargo_borrado';
							if(items[i].restaura=='S'){
								imtx = "<td align='center' style='background-color:"+ccolor+"'><img src='";
								if(items[i].hnote=='S'){
									imtx+="img/note_edit.png";
								} else {
									imtx+="img/note_add.png";
								};
								imtx+="' style='cursor:pointer' " +
								"title='Agregar una nota' onclick=\"Notes.setNote("+
								items[i].item+","+
								items[i].numfol+","+
								items[i].numcue+")\"/></td>"+
								"<td align='center' style='"+
								"background-color:"+ccolor+"'><img src='img/rest.png' style='cursor:pointer' " +
								" title='Restaurar este cargo' onclick=\"restoreCargo("+items[i].numfol+","+
								items[i].numcue+","+items[i].item+")\"/></td>";
							} else {
								imtx = "";
							}
						} else if(items[i].estado=='P'){
							ccolor = "#FFFFD7";
							imtx = "";
							class_name = 'cargo_pendiente cargo_activo';
							if(items[i].hnote=='S'){
								imtx = "<td align='center' style='background-color:"+ccolor+"'><img src='";
								imtx+="img/note_aud.png";
								imtx+="' style='cursor:pointer' " +
								"title='Agregar una nota' onclick=\"Notes.setNote("+
								items[i].item+","+
								items[i].numfol+","+
								items[i].numcue+")\"/></td>";
							} else {
								imtx = "";
							};
						} else {
							class_name = 'cargo_normal cargo_activo';
							if(items[i].tipmov=='D'){
								ccolor = color
								imtx = "<td align='center' style='background-color:"+ccolor+"'><img src='";
								if(items[i].hnote=='S'){
									imtx+="img/note_edit.png";
								} else {
									imtx+="img/note_add.png";
								};
								imtx+="' style='cursor:pointer' " +
								"title='Agregar una nota' onclick=\"Notes.setNote("+
								items[i].item+","+
								items[i].numfol+","+
								items[i].numcue+")\"/></td>"+
								"<td align='center' style='"+
								" background-color:"+ccolor+"'><img src='img/delete.gif' style='cursor:pointer' " +
								" title='Anular este cargo' onclick=\"deleteCargo("+items[i].numfol+","+
								items[i].numcue+","+items[i].item+")\"/></td>";
								num_items++;
							} else {
								ccolor = "#D9FFD9";
								imtx = "<td align='center' style='"+
								"background-color:"+ccolor+"'><img src='img/delete.gif' style='cursor:pointer' " +
								" title='Anular este cargo' onclick=\"deleteCargo("+items[i].numfol+","+
								items[i].numcue+","+items[i].item+")\"/></td>";
							};
							num_items++;
						}
					};
				};
				txt = "<tr>";
				if(items[i].type=='data'){
					txt =  "<tr class='cargo_base "+class_name+"'>";
					if(items[i].estado!='B'){
						txt+="<td align='center'  bgcolor='"+ccolor+"' style='border-bottom:none'><input type='checkbox' style='border:none' id='cb"+total_items+"' "+
						" value='"+items[i].item+"' onclick='checkRow(this)'/>&nbsp;</td>";
					} else {
						txt+="<td align='center' style='border-bottom:none'><input type='checkbox' disabled style='border:none' id='cb"+total_items+"' "+
						" value='"+items[i].item+"'/>&nbsp;</td>";
					};
					txt+="<td align='center' bgcolor='"+ccolor+"'>"+
					items[i].fecha.replace(" ", "&nbsp;")
					if(items[i].hora!="00:00"){
						txt+="<br><span style='font-size:10px'>("+items[i].hora+")</span>";
					} else {
						txt+="<br><span style='font-size:10px'></span>";
					};
					txt+="</td>"+
					"<td align='left' class='de' style='background-color:"+ccolor+"'>"+
					"<b>"+items[i].descripcion+"</b><br><span>"+items[i].nota+"</span></td>"+
					"<td align='center' style='font-size:11px;background-color:"+ccolor+"'>"+items[i].numdoc+"</td>"+
					"<td align='right' style='background-color:"+ccolor+"'>"+items[i].valor+"</td>"+
					"<td align='center' style='background-color:"+ccolor+"'>"+items[i].cantidad+"</td>"+
					"<td align='right' style='background-color:"+ccolor+"'>"+items[i].iva+"</td>"+
					"<td align='right' style='background-color:"+ccolor+"'>"+items[i].valser+"</td>"+
					"<td align='right' style='background-color:"+ccolor+"'>"+items[i].valter+"</td>"+
					"<td align='right' style='background-color:"+ccolor+"'>"+items[i].total+"</td>"+ imtx +
					"</tr>";
					if(color=="#CCDEFF") {
						color = "#FFFFFF";
					} else {
						color = "#CCDEFF";
					};
					total_items++;
				} else {
					if(items[i].type=='totald'){
						txt+="<td class='nb'></td><td align='right' colspan='5' class='tlb'>"+
						"<b>TOTAL CONSUMOS</b></td>"+
						"<td align='right' class='to'>"+items[i].totali+"</td>"+
						"<td align='right' class='to'>"+items[i].totals+"</td>"+
						"<td align='right' class='to'>"+items[i].totale+"</td>"+
						"<td align='right' class='to'>"+items[i].totalt+"</td>"+
						"</tr>";
					} else {
						if(items[i].type=='totala'){
							txt+="<td class='nb'></td><td align='right' colspan='5' class='tlb'>"+
							"<b>TOTAL ABONOS/PAGOS </b></td>"+
							"<td align='right' class='to'>0</td>"+
							"<td align='right' class='to'>0</td>"+
							"<td align='right' class='to'>0</td>"+
							"<td align='right' class='to'>"+items[i].totala+"</td>"+
							"</tr>"
						} else {
							if(items[i].type=='genfac'){
								genfac = items[i].value;
							}
						}
					}
				};
				tHtml+=txt;
			};
			tHtml+="<tr>"+
			"<td class='nb'></td>"+
			"<th colspan='9' class='lb'>Grabar Nuevo Cargo/Descuento/Ajuste</th>"+
			"</tr>"
			tHtml+="<tr>"+
			"<td class='nb'></td>"+
			"<th class='lb'>Fecha</th>"+
			"<th>Concepto</th>"+
			"<th>Documento</th>"+
			"<th>Valor</th>"+
			"<th>Cantidad</th>"+
			"<th>IVA</th>"+
			"<th>Servicio</th>"+
			"<th>Terceros</th>"+
			"<th>Total</th>"+
			"</tr>"
			txt =  "<tr>"+
			"<td class='nb'></td>" +
			"<td align='center' bgcolor='"+color+"' class='lb'>"+document.getElementById("date_today").value+"</td>"+
			"<td align='center' bgcolor='"+color+"'>"+comboCargos+"</td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength='16' style='text-align:right' id='fnumdoc'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength='16' style='text-align:right' id='fvalor' "+
			"onkeyup='if(this.value) ajaxXMLRequest(\"getFactValues\", \"codcar=\"+$F(\"cargos\")+\"&valor=\"+parseInt(getObj(\"fvalor\").value)+\"&cant=\"+getObj(\"fcantidad\").value, \"setFactList()\")'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=4 maxlength='10' value=1 style='text-align:right' id='fcantidad'"+
			"onblur='if(this.value) ajaxXMLRequest(\"getFactValues\", \"codcar=\"+$F(\"cargos\")+\"&valor=\"+parseInt(getObj(\"fvalor\").value)+\"&cant=\"+getObj(\"fcantidad\").value, \"setFactList()\")'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength='16' readonly value=0 style='text-align:right' id='fiva'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength=16 readonly value=0 style='text-align:right' id='fvalser'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength=16 readonly value=0 style='text-align:right' id='fvalter'></td>"+
			"<td align='center' bgcolor='"+color+"'>"+
			"<input type=text size=8 maxlength=16 readonly value=0 style='font-size:18px;text-align:right' id='ftotal'></td>"+
			"</tR><tr><td class='nb'></td><td colspan='9' style='background: url(img/localnavg.gif); border:1px solid #969696;font-size:10px' align='right' id='diva'>&nbsp;</td></tr>"+
			"<tr><td class='nb'><td class='nb'><td style='padding:5px' class='nb' align='center' valign='top'><input type='button' class='controlButtonBack' value='Adicionar' type='button' onclick='saveCargo()' /><bR><td colspan='7' style='padding:5px' class='nb'>"+
			"<table align='right' style='border:1px solid #969696; background: url(img/bg2.jpg)' cellspacing='0'>"+
			"<tr><td colspan='2' class='nb' style='padding:3px'><b>Más opciones de esta cuenta</b></td></tr>"+
			"<tr>"
			if(genfac==1){
				txt+="<td class='nb'><input type='button' class='controlButtonAccept' value='Generar Factura' type='button' "+
				"onclick='window.location=\"?action=optfac&fl_numcue="+encodeURIComponent(Base64.encode(numcue))+"&fl_numfol="+encodeURIComponent(Base64.encode(numfol))+"&option=5\"'/></td>"
			};
			txt+="<td class='nb'><input type='button' class='controlButton' value='Abonos/Devoluciones' type='button' "+
			"onclick='window.location=\"?action=abonos&fl_numcue="+encodeURIComponent(numcue)+"&fl_numfol="+encodeURIComponent(numfol)+"&option=5\"'/></td>"+
			"<td class='nb'><input type='button' class='controlButton' value='Pre-Facturas' type='button' onclick='window.location=\"?action=prefac2&option=5&numfol="+numfol+"&numcue="+numcue+"\"'/></td></tr></table></td>"+
			"</tr><tr><td class='nb'><br></td></tr>";
			tHtml+=txt;
			tHtml+="</table>";
			$("acc_details").innerHTML = tHtml;
			if(numcue>1){
				$("deleteExtra").show()
			} else {
				$("deleteExtra").hide()
			};
			if(num_items>0){
				$("transExtra").show();
			} else {
				$("transExtra").hide();
			};
			cancelaTrans();
			if($('leftPannel').visible()){
				new Effect.SlideUp('leftPannel', {duration: 0.5});
				$("mainTd").setStyle({width : "100%", "border":"none"});
				$("tleftPannel").hide();
			};
			if(scroll){
				if($("add_button")){
					new Effect.ScrollTo('add_button');
				}
			};
			if($("cargos")){
				$("cargos").activate();
				new Event.observe("fvalor", "keydown", function(e){
					if(e.keyCode==Event.KEY_RETURN){
						$("fcantidad").focus();
						saveCargo();
					}
				});
			};
			computeWindowSize(true);
		}.bind(this, numcue, numfol, scroll)
	})
}

function createExtra(){
	new Ajax.Request("webServices/newExtra.php?numfol="+numfol, {
		onSuccess: function(){
			new Notificator.notify('Se cre&oacute; una nueva cuenta extra correctamente');
			loadAccounts($F('numfol'));
		}
	});
}

function loadAccounts(numfol){
	new Ajax.Request('webServices/getAccounts.php', {
		method: 'GET',
		parameters: {
			'numfol': numfol
		},
		onSuccess: function(transport){
			var load_first_account = false;
			if(typeof JSON == "undefined"){
				var items = transport.responseText.evalJSON();
			} else {
				var items = JSON.parse(transport.responseText);
			};
			var num_cuentas = 0;
			var facturas = "";
			$("accounts").innerHTML = "";
			$("acc_details").innerHTML = "";
			$("customer_info").innerHTML = "<div style='padding:5px'><b>Documento </b>"+
			items[0].cedula+"&nbsp;&nbsp;<b>Nombre </b>"+items[0].nombre+"<br>";
			if(items[0].nota.strip()!=""){
				$("customer_info").innerHTML+="&nbsp;&nbsp;&nbsp;<b>NOTA </b>"+items[0].nota.toUpperCase()+"<br>"
			};
			num_cuentas = items[0].num_cuentas;
			facturas = items[0].facturas;
			$("customer_info").innerHTML+="</div>";
			$("customer_info").show();
			for(var i=1;i<items.length;i++){
				var id = "a"+items[i].numcue;
				numfol = items[i].numfol;
				var sp = "<input type='button' id='"+id+"' class='ac_button'"+
				"onclick='getDetails(\""+items[i].numcue+"\", false)' "+
				"value='"+items[i].nombre+"'/>";
				$("accounts").innerHTML+=(sp+"&nbsp;");
				if(!(i%6)){
					$("accounts").innerHTML+="<br/><br/>";
				};
				if(!load_first_account){
					getDetails(items[i].numcue, scroll);
					load_first_account = true;
				}
			};
			if(num_cuentas==0){
				var html = "<table background='img/bg2.jpg' width='450' align='center'>"+
				"<tr><td><img src='img/adverp.png' alt=''></td>"+
				"<td width='350'>Ya se han facturado todas las cuentas de este folio. "
				if(facturas!=""){
					html+="Facturas: "+facturas;
				};
				html+="</td></tr></table><br>";
				$("acc_details").innerHTML = html;
				$('numfol').activate();
			};
			$("newExtra").show();
		}
	});
};

function loadTransferAccounts(){
	this.blur();
	new Ajax.Request('webServices/getAccounts.php', {
		method: 'GET',
		parameters: {
			'numfol': $F(this)
		},
		onSuccess: function(transport){
			if(typeof JSON == "undefined"){
				var items = transport.responseText.evalJSON();
			} else {
				var items = JSON.parse(transport.responseText);
			};
			var cNumcue = $('c_numcue');
			while(cNumcue.lastChild){
				cNumcue.removeChild(cNumcue.lastChild);
			};
			for(var i=1;i<items.length;i++){
				if((numcue==parseInt(items[i].numcue))&&(numfol==$F('c_numhab'))){
					//Nada
				} else {
					var option = document.createElement('OPTION');
					option.value = items[i].numcue;
					if(Prototype.Browser.IE){
						option.innerText = items[i].nombre;
					} else {
						option.text = items[i].nombre;
					};
					cNumcue.appendChild(option);
				}
			};
		}
	});
};

function saveCargo(){
	if($F("cargos")=='@'){
		alert("Debe Seleccionar un concepto");
		new Effect.Highlight('cargos');
		return;
	};
	if($F("fvalor")<=0){
		alert("Debe digitar el valor del movimiento");
		new Effect.Highlight('fvalor', {
			afterFinish: function(){
				$("fvalor").activate();
			}
		});
		return;
	};
	if($F("fcantidad")<=0){
		alert("Debe digitar la cantidad");
		$("fcantidad").activate();
		return;
	};
	var url = "numfol="+numfol+"&codcar="+$F("cargos")+
	"&numcue="+numcue+"&valor="+$F("fvalor")+"&cantidad="+getObj("fcantidad").value+
	"&iva="+$F("fiva")+"&valser="+getObj("fvalser").value+"&total="+getObj("ftotal").value+
	"&numdoc="+$F("fnumdoc");
	new Ajax.Request("webServices/saveCargo.php?"+url, {
		onSuccess: function(transport){
			new Notificator.notify(transport.responseText);
			loadCargos(numcue, numfol)
		}
	});
};

function deleteCargo(numfol, numcue, item){
	new Modal.confirm({
		'title': 'Carga/Consulta de Consumos',
		'message': '¿Está seguro de anular el movimiento?',
		'onAccept': function(){
			new Ajax.Request('webServices/deleteCargo.php', {
				method: 'GET',
				parameters: {
					'numfol': numfol,
					'numcue': numcue,
					'item': item
				},
				onSuccess: function(t){
					new Notificator.notify(t.responseText);
					$("filter_list").selectedIndex = 0;
					loadCargos(numcue, numfol);
				}
			});
		}
	});
};

function restoreCargo(numfol, numcue, item){
	new Modal.confirm({
		'title': 'Carga/Consulta de Consumos',
		'message': '¿Está seguro de restaurar el movimiento?',
		'onAccept': function(){
			new Ajax.Request('webServices/restoreCargo.php', {
				method: 'GET',
				parameters: {
					'numfol': numfol,
					'numcue': numcue,
					'item': item
				},
				onSuccess: function(t){
					new Notificator.notify('Se restauró el movimiento correctamente');
					$("filter_list").selectedIndex = 0;
					loadCargos(numcue, numfol, false);
				}
			});
		}
	});
};

function selectAll(){
	$$('input[type="checkbox"]').each(function(element){
		if(element.disabled==false){
			if(element.parentNode.parentNode.visible()==false){
				element.checked = false;
			}
		}
	});
	var numberChecked = 0;
	$$('input[type="checkbox"]').each(function(element){
		if(element.disabled==false){
			if(element.parentNode.parentNode.visible()){
				if(element.checked==true){
					element.checked = false;
				} else {
					element.checked = true;
					var es_auditado = element.parentNode.parentNode.hasClassName('cargo_auditado');
					if(es_auditado==false){
						numberChecked++;
					}
				}
			}
		};
		checkRow(element);
	});
	if(numberChecked>0){
		$("eliminarMov").show();
	} else {
		$("eliminarMov").hide();
	}
	if(numberChecked==1){
		$("splitMov").show();
	} else {
		$("splitMov").hide();
	}
};

function splitCargos(){
	var parameters = "numfol="+numfol+"&numcue="+numcue+"&item="
	var selected = false;
	var items = [];
	var checkBoxes = $$('input[type="checkbox"]');
	for(var i=0;i<checkBoxes.length;i++){
		if(checkBoxes[i].disabled==false){
			if(checkBoxes[i].parentNode.parentNode.visible()){
				if(checkBoxes[i].checked==true){
					var es_auditado = checkBoxes[i].parentNode.parentNode.hasClassName('cargo_auditado');
					if(es_auditado==false){
						items.push(checkBoxes[i].value);
					} else {
						checkBoxes[i].checked = false;
						checkRow(checkBoxes[i])
					}
				}
			}
		}
	}
	if(items.length!=1){
		new Modal.alert({
			'title': 'Dividir Cargos',
			'message': 'Selecione un solo movimiento no-auditado para ser dividido'
		});
	} else {
		parameters+=items.join('-');
		window.location = "?option=5&action=divcar&"+parameters;
	}
};

function deleteCargos(){
	var parameters = "numfol="+numfol+"&numcue="+numcue+"&items="
	var selected = false;
	var items = [];
	var checkBoxes = $$('input[type="checkbox"]');
	for(var i=0;i<checkBoxes.length;i++){
		if(checkBoxes[i].disabled==false){
			if(checkBoxes[i].parentNode.parentNode.visible()){
				if(checkBoxes[i].checked==true){
					var es_auditado = checkBoxes[i].parentNode.parentNode.hasClassName('cargo_auditado');
					if(es_auditado==false){
						items.push(checkBoxes[i].value);
					} else {
						checkBoxes[i].checked = false;
						checkRow(checkBoxes[i])
					}
				}
			}
		}
	}
	if(items.length==0){
		new Modal.alert({
			'title': 'Anular Movimiento',
			'message': 'Selecione al menos un movimiento no-auditado a anular'
		});
	} else {
		parameters+=items.join('-');
		new Modal.confirm({
			'title': 'Anular Movimientos',
			'message': '¿Seguro desea anular los movimientos no-auditados seleccionados?',
			'onAccept': function(parameters){
				new Ajax.Request('webServices/deleteCargo.php', {
					method: 'GET',
					parameters: parameters,
					onSuccess: function(t){
						new Notificator.notify(t.responseText);
						$("filter_list").selectedIndex = 0;
						loadCargos(numcue, numfol);
					}
				});
			}.bind(this, parameters)
		});
	}
};

function borrarExtra(){
	if(num_items==0){
		new Modal.confirm({
			'title': 'Borrar cuenta extra',
			'message': '¿Seguro desea borrar la cuenta extra?',
			'onAccept': function(){
				ajaxXMLRequest("deleteExtra", "numfol="+numfol+"&numcue="+numcue, "successBorraExtra()")
			}
		});
	} else {
		new Modal.confirm({
			'title': 'Transferir Cargos',
			'message': 'Antes de eliminar la cuenta debe transferir el movimiento hecho en esta cuenta a otra. ¿Desea hacerlo ahora?',
			'onAccept': function(){
				transCargos();
				for(var i=0;i<total_items;i++){
					getObj("cb"+i).checked = true
				}
			}
		});
	}
}

function loadAccountsFromFolio(element){
	numfol = $F(element);
	loadAccounts(numfol);
	cancelaTrans();
	element.blur();
}

function successBorraExtra(){
	new Notificator.notify('Se eliminó correctamente la cuenta extra');
	loadAccounts(numfol);
}

function transCargos(){
	$("trans_to").show();
	$("transExtra").hide();
	$("deleteExtra").hide();
	$("eliminarMov").hide();
	$("splitMov").hide();
	$("newExtra").hide();
	$("filtroTD").hide();
	$('c_numhab').selectedIndex = $('numfol').selectedIndex-1;
	loadTransferAccounts.bind($('c_numhab'))();
}

function cancelaTrans(){
	$("c_numcue").selectedIndex = 0;
	$("trans_to").hide();
	$("transExtra").show();
	$("deleteExtra").show();
	$("eliminarMov").hide();
	$("splitMov").hide();
	$("newExtra").show();
	$("filtroTD").show();
}

function cargaAccounts(){
	var items = response.responseXML.getElementsByTagName("row");
	var cNumcue = $('c_numcue');
	var vNumhab = $F('c_numhab');
	while(cNumcue.lastChild){
		cNumcue.removeChild(cNumcue.lastChild)
	};
	for(var i=1;i<items.length;i++){
		if((numcue==parseInt(items[i].getAttribute("numcue")))&&(numfol==vNumhab)){
			//Nada
		} else {
			var option = document.createElement('OPTION');
			option.value = items[i].getAttribute("numcue")
			if(document.all){
				option.innerText = items[i].getAttribute("nombre")
			} else {
				option.text = items[i].getAttribute("nombre")
			};
			cNumcue.appendChild(x)
		}
	}
}

function doTransfer(){
	var x;
	var toCuenta = $F('c_numcue');
	if(toCuenta<1){
		new Modal.alert({
			'title': 'Transferir Cargos',
			'message': 'La cuenta destino no es válida'
		});
		return;
	}
	var parameters = "numfol="+numfol+"&to_fol="+$F('c_numhab')+"&from_cue="+numcue+"&to_cue="+toCuenta+"&items="
	var x = 0;
	for(var i=0;i<total_items;i++){
		if(getObj("cb"+i).checked){
			parameters += getObj("cb"+i).value+"-"
			x = 1
		}
	}
	if(x==0){
		new Modal.alert({
			'title': 'Transferir Cargos',
			'message': 'Debe seleccionar al menos un movimiento a transferir'
		});
	} else {
		parameters +="0";
		new Ajax.Request('webServices/moveCargo.php', {
			'method': 'GET',
			'parameters': parameters,
			'onSuccess': function(t){
				new Notificator.notify(t.responseText)
				getDetails(numcue, false);
			}
		});
	}
};

var Notes = {

	item: 0,
	numfol: 0,
	numcue: 0,

	setNote: function(item, numfol, numcue){

		Notes.numfol = numfol;
		Notes.numcue = numcue;
		Notes.item = item;
		new Ajax.Request('webServices/getMoviNote.php', {
			parameters: {
				'numfol': Notes.numfol,
				'numcue': Notes.numcue,
				'item': Notes.item
			},
			onSuccess: function(t){
				var windowScroll = WindowUtilities.getWindowScroll(document.body);
			    var pageSize = WindowUtilities.getPageSize(document.body);
				var dm, d = document.createElement("DIV");
				d.id = "note_shadow";
				d.setStyle({
					position: "absolute",
					top: windowScroll.top+"px",
					left: "0px",
					background: "#000",
					width: "100%",
					height: "100%",
					zIndex: 700
				});
				d.setOpacity(0.4);
				document.body.appendChild(d);

				var left = (pageSize.windowWidth-650-windowScroll.left)/2;

				dm = document.createElement("DIV");
				dm.id = "note_dialog";
				dm.setStyle({
					top: (windowScroll.top)+"px",
					left: left+"px"
				});
				dm.hide();
				document.body.appendChild(dm);
				dm.innerHTML = "<table align='center' width='100%'><tr><td><img src='img/edit.png' width='96'></td>"+
				"<td width='15px'></td>"+
				"<td>Agregue una nota/observación para soportar para el cargo"+
				"<textarea style='width:100%' id='note'></textarea><br><br>"+
				"<div align='right'>"+
				"<input type='button' value='Grabar' class='controlButton' onclick='Notes.confirmDialog()'>&nbsp;"+
				"<input type='button' value='Cancelar' class='controlButtonCancel' onclick='Notes.cancelDialog()'></div>"+
				"</td></tr></table>";

				$('note').setValue(t.responseText);

				$('note').observe('keyup', function(event){
					if(event.keyCode==Event.KEY_RETURN){
						$('note').disable();
						Notes.confirmDialog();
					}
					if(event.keyCode==Event.KEY_ESC){
						Notes.cancelDialog();
					}
				});
				new Effect.BlindDown(dm, {
					duration: 0.3,
					afterFinish: function(){
						$("note").activate();
					}
				});
			}
		});
	},

	cancelDialog: function(){
		new Effect.Fade('note_dialog', {
			duration: 0.5,
			afterFinish: function(){
				document.body.removeChild($("note_shadow"));
				document.body.removeChild($("note_dialog"));
			}
		});
	},

	confirmDialog: function(){
		new Ajax.Request('webServices/saveMoviNote.php', {
			parameters: {
				'numfol': Notes.numfol,
				'numcue': Notes.numcue,
				'item': Notes.item,
				'note': $F('note')
			}
		});
		Notes.cancelDialog();
	}

}

function parseColor(x){
	var color='#';
	if(x.slice(0,4)=='rgb('){
		var cols=x.slice(4,x.length-1).split(',');
		var i=0;
		do {
			color+=parseInt(cols[i]).toColorPart()
		} while(++i<3);
	} else {
		if(x.slice(0,1) == '#'){
			if(x.length==4){
				for(var i=1;i<4;i++){
					color+=(x.charAt(i)+x.charAt(i)).toLowerCase();
				}
			}
			if(x.length==7){
				color=x.toLowerCase();
			}
		}
	}
	return(color.length==7 ? color:(arguments[0]||x));
}


function checkRow(element){
	var tr = $(element.parentNode.parentNode);
	var td = tr.childNodes[1];
	if(element.checked){
		var color = parseColor(td.getStyle('backgroundColor'));
		for(var i=1;i<tr.childNodes.length;i++){
			tr.childNodes[i].setStyle({backgroundColor: "#ffffcc"});
			tr.childNodes[i].restoreColor = color;
		}
	} else {
		for(var i=1;i<tr.childNodes.length;i++){
			tr.childNodes[i].setStyle({backgroundColor: tr.childNodes[i].restoreColor});
		}
	};
	var checkBoxes = $$('input[type="checkbox"]');
	for(var i=0;i<checkBoxes.length;i++){
		if(checkBoxes[i].disabled==false){
			if(checkBoxes[i].parentNode.parentNode.visible()){
				if(checkBoxes[i].checked==true){
					var es_auditado = checkBoxes[i].parentNode.parentNode.hasClassName('cargo_auditado');
					if(es_auditado==false){
						$('eliminarMov').show();
						$('splitMov').show();
						return;
					}
				}
			}
		}
	};
	$('eliminarMov').hide();
	$("splitMov").hide();
}

new Event.observe(document, 'dom:loaded', function(){
	$('c_numhab').observe('change', loadTransferAccounts);
	$('filter_list').observe('change', function(){
		if($F(this)=="all"){
			$$(".cargo_base").each(function(element){
				element.show();
			});
			return;
		};
		$$(".cargo_base").each(function(element){
			element.hide();
		});
		$$(".cargo_"+$F(this)).each(function(element){
			element.show();
		});
	});
	$('mainDiv').observe('mouseenter', linkDock.hideTip);
});