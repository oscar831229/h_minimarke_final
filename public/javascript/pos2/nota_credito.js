
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


window.cancelNumber = function(){
	new Utils.redirectToAction('appmenu')
}

try {
	window.acceptNumber = function(){
		if($F("number").strip()!=''){
			new Ajax.Request("anula_factura/exists/"+$F("number")+"/"+$F("salon_id"), {
				onSuccess: function(transport){
					var response = transport.responseText.evalJSON();
					if(response=='yes'){
						if(confirm("¿Seguro desea anular la orden/factura No. "+$F("number")+"?")){
							new Utils.redirectToAction('anula_factura/anula/'+hex_sha1($("number").value)+"/"+$F("salon_id")+"/"+$F("tipo_venta"))
						}
					} else {
						alert("No existe la factura/orden "+$("number").value+" en el ambiente indicado");
					}
				}
			});
		}
	}
}
catch(e){
	alert(e.stack)
}


new Event.observe(window, "load", function(){

});

var nota = {

	data : [],

	formas_pago : [],

	total_pago : 0,

	total_nota : 0,

	total_propina : 0,

	consultarFactura:function(element){

		if($('consecutivo_facturacion').getValue().trim() == ''){
			nota.alert("Debe digitar la factura de referencia.", 'warning');
			return false;
		}

		document.querySelector('.loading').style.display = '';
		$s('#consultar').style.display = "none";

		setTimeout(function(){

			new Ajax.Request("nota_credito/findFactura/"+$('prefijo_facturacion').getValue().trim()+"/"+$('consecutivo_facturacion').getValue().trim(), {
				onSuccess: function(transport){

					var response = transport.responseText.evalJSON();
					document.querySelector('.loading').style.display = 'none';

					if(response.success){
	
						// No se encontro la factura
						if(response.data.factura == false){
							nota.alert('No existe la factura', 'danger');
							return false;
						}
	
						// La busqueda corresponde a orden de servicio
						if(response.data.factura.tipo == 'O'){
							nota.alert('Los datos corresponde a una orden de servicio', 'danger');
							return false;
						}
	
						nota.data = response.data;
						nota.loadDetalles();
						nota.loadFormasPago();
						document.querySelector('#total_propina').value = parseFloat(nota.data.factura.propina);
						document.querySelector('#total_pagos').value = parseFloat(0);
						document.querySelector('#total_propina_nota').value = parseFloat(nota.total_propina);
						document.querySelector('#total_propina_nota').readOnly = false;
						if(parseFloat(nota.data.factura.propina) <= 0){
							document.querySelector('#total_propina_nota').readOnly = true;
						}
						document.querySelector('#cliente').innerHTML = nota.data.factura.nombre + ' - ' + nota.data.factura.cedula;
						$s('#consultar').style.display = "none";
						$s('#nueva').style.display = "";
	
						document.querySelector('#prefijo_facturacion').readOnly = true;
						document.querySelector('#consecutivo_facturacion').readOnly = true;
	
					} else {
						$s('#consultar').style.display = "";
						nota.alert(response.message, 'danger');
					}
				}
			});

		}, 3000);

	},

	alert : function(message, $type = 'success'){

		var notificator = new Notification(document.querySelector('.notification'));

		switch ($type) {
			case 'success':
				notificator.success(message);
				break;

			case 'danger':
				notificator.error(message);				
				break;

			case 'warning':
				notificator.warning(message);
				break;				
		
			default:
				notificator.info(message);
				break;
		}

	},

	loadDetalles : function(){
		
		var tr = '';
		var totalfac = 0;
		var totalnoc = 0;

		$each(nota.data.detalle, function(index, detalle){

			if(detalle.cannot == undefined){
				detalle.cannot = '';
			}

			var value_nota = 0;
			if(detalle.cannot != '')
				value_nota = detalle.total / detalle.cantidad * detalle.cannot;


			var valtaxe = detalle.iva > 0 ? detalle.iva : detalle.impo;
			totalfac = totalfac + parseFloat(detalle.total);
			detalle.total = parseFloat(detalle.total);
			totalnoc = totalnoc + parseFloat(value_nota);

			tr  +=  '<tr>'
				+	'	<td class="al-l">'+  detalle.nombre +'</td>'
				+	'	<td class="al-c">'+ detalle.cantidad +'</td>'
				+	'	<td class="al-c"><input type="number" data-index="'+index+'" maxlength="'+detalle.cantidad.length+'"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); if(this.dataset.maxcan<this.value) this.value = this.dataset.maxcan" style="width: 40px;" min="1" max="'+detalle.cantidad+'" data-maxcan="'+detalle.cantidad+'" value="'+detalle.cannot+'" onblur="nota.add(this); nota.totalPago()"></td>'	
				+	'	<td class="al-r">'+ formatNumber.new(detalle.total) +'</td>'
				+	'	<td class="al-r valnot">'+ formatNumber.new(value_nota) +'</td>'
				+	'</tr>'
		});

		document.querySelector('#tbldetalle tbody').innerHTML=tr;
		document.querySelector('.totalfac').innerHTML=formatNumber.new(totalfac);
		document.querySelector('.totalnoc').innerHTML=formatNumber.new(totalnoc);

		// Cargar notas credito creadas
		var tr = '';
		$each(nota.data.notas, function(index, componente){

			var auxnota = componente.data;
			var _self = componente._self
			tr  +=  '<tr>'
				+	'	<td class="al-c">'+ auxnota.prefijo_documento + '-'+ auxnota.consecutivo_documento +'</td>'
				+	'	<td class="al-c">'+ auxnota.fecha +'</td>'
				+	'	<td class="al-r">'+ formatNumber.new(auxnota.total) +'</td>'
				+	'	<td class="al-c"><button class="btnprint btnimg" onclick="nota.printGenerate(\''+_self+'\')">'+ nota.data.iconprint +'</button></td>'
				+	'</tr>'
		});
		

		document.querySelector('#tblnotas tbody').innerHTML=tr;
		

	},

	loadFormasPago: function(){

		var select = '<select name="forpag" style="width: 200px;" onchange="nota.addForma(this)">'
				   + '<option value="@">Seleccione...</option>';

		$each(nota.data.formas_pago, function(index, forma){
			select += '<option value="'+forma.id+'">'+forma.detalle+'</option>';
		})

		select += '</select>';


		var tr = '';
		for (i=0; i < nota.data.pago_factura.length; i++) {
			tr  += '<tr id="tr0" bgcolor="#FFFFFF">'
				+'	<td align="center">'
				+   select 
				+'	</td>'
				+'	<td align="center">'
				+'		<input type="text" onkeydown="valNumeric(event);" name="valor_factura" style="width: 100px;" onblur="nota.addForma(this); nota.totalPago()">'
				+'	</td>'	
				+'	<td align="center">'
				+'		<input type="text" onkeydown="valNumeric(event);" name="valor" style="width: 100px;" onblur="nota.addForma(this); nota.totalPago()">'
				+'	</td>'	
				+'	<td align="center">'
				+'		<input type="text" onkeydown="valNumeric(event);" name="numero" style="width: 100px;" onblur="nota.addForma(this)">'
				+'	</td>'
				+'	<td align="center">'
				+'		<input type="date" onkeydown="valNumeric(event);" name="fecha" style="width: 150x;" onblur="nota.addForma(this)">'
				+'	</td>'
				+'</tr>'
		}

		document.querySelector('#tblformaspago tbody').innerHTML=tr;

		$each(nota.data.pago_factura, function(index, forma){
			var indextr = index + 1;

			var tr = document.querySelector("#tblformaspago tbody tr:nth-child("+indextr+")");
			tr.querySelector('select[name=forpag]').value = forma.formas_pago_id;
			tr.querySelector('input[name=numero]').value = forma.numero;
			tr.querySelector('input[name=fecha]').value = forma.fecha;
			tr.querySelector('input[name=valor]').value = forma.valor;
			tr.querySelector('input[name=valor_factura]').value = parseFloat(forma.pago);

			tr.querySelector('select[name=forpag]').disabled = true;
			tr.querySelector('input[name=valor_factura]').disabled = true;

			nota.formas_pago[nota.formas_pago.length] = {
				forpag : forma.formas_pago_id,
				numero : forma.valor,
				fecha  : forma.fecha,
				valor  : forma.valor,
				valor_factura : parseFloat(forma.pago),
				pagos_factura_id : forma.pagos_factura_id,
				factura_id : forma.factura_id
			}

		});

	},

	addForma : function(element){

		var index = element.closest('tr').rowIndex;

		if(nota.formas_pago[index - 1] == undefined){
			nota.formas_pago[index - 1] = {
				forpag : '@',
				numero : '',
				fecha  : '',
				valor  : ''
			}
		}

		var value = element.value;
		var name = element.name;

		var valor_factura = nota.formas_pago[index - 1]['valor_factura']

		if(valor_factura < value){
			value = valor_factura
			element.value = value
			nota.alert('El valor digitado es mayor a la forma de pago.', 'warning');
		}
		nota.formas_pago[index - 1][name] = value;
	},

	newForma: function(){

		nota.formas_pago[nota.formas_pago.length] = {
			forpag : '@',
			numero : '',
			fecha  : '',
			valor  : ''
		}

		nota.loadFormasPago();
	},

	deleteForma : function(element){
		var index = element.closest('tr').rowIndex;
		nota.formas_pago.splice( index - 1, 1 );
		nota.loadFormasPago();
	},

	add : function(element){
		var value = element.value;
		var index = element.dataset.index;
		nota.data.detalle[index].cannot = value;
		nota.loadValueNota();
	},

	loadValueNota : function(){

		var totalnoc = 0;

		
		$each(nota.data.detalle, function(index, detalle){

			var value_nota = 0;
			if(detalle.cannot != '' && detalle.cannot != 0)
				value_nota = detalle.total / detalle.cantidad * detalle.cannot;

			totalnoc = totalnoc + parseFloat(value_nota);
			var indextr = index +1;
			var tr = document.querySelector("#tbldetalle tbody tr:nth-child("+indextr+")");
			tr.querySelector('.valnot').innerHTML= value_nota;

		});

		document.querySelector('.totalnoc').innerHTML=formatNumber.new(totalnoc);
		nota.total_nota = totalnoc;

	},

	totalPago : function(){

		nota.total_pago = 0;

		$each(document.querySelectorAll("#tblformaspago tbody tr"),function(index, tr){
			value = tr.querySelector('input[name=valor]').value;
			if(value != ''){
				nota.total_pago += parseFloat(value);
			}
		})

		document.querySelector('#total_pagos').value = formatNumber.new(nota.total_pago);
		document.querySelector('#total_saldo').value = formatNumber.new(parseFloat(nota.total_nota) + parseFloat(nota.total_propina) - parseFloat(nota.total_pago));
		
	},

	setPropina : function(element){

		var value = element.value;
		if(value > parseFloat(nota.data.factura.propina)){
			element.value = parseFload(nota.data.factura.propina)
		}

		nota.total_propina = element.value;
		nota.totalPago();

	},

	nuevaConsulta : function(){

		$s('#consultar').style.display = "";
		$s('#nueva').style.display = "none";

		document.querySelector('#prefijo_facturacion').readOnly = false;
		document.querySelector('#consecutivo_facturacion').readOnly = false;
		document.querySelector('#tbldetalle tbody').innerHTML = '';
		document.querySelector('#tblformaspago tbody').innerHTML = '';
		document.querySelector('#tblnotas tbody').innerHTML = '';
		document.querySelector('#total_propina').value = 0;
		document.querySelector('#total_propina_nota').value = 0;
		document.querySelector('#total_pagos').value = 0;
		document.querySelector('#total_saldo').value = 0;
		document.querySelector('#prefijo_facturacion').value = '';
		document.querySelector('#consecutivo_facturacion').value = '';
		document.querySelector('#prefijo_facturacion').focus();
		document.querySelector('.totalfac').innerHTML = '';
		document.querySelector('.totalnoc').innerHTML = '';
		document.querySelector('#cliente').innerHTML = '';
		document.querySelector('.okButton').style.display = '';

		
		nota.formas_pago = [];
		nota.data = [];

	},

	pay : function(){

		if((nota.total_pago - nota.total_nota - nota.total_propina) != 0){
			nota.alert('No puede existir saldo', 'warning');
			return false;
		}

		if((nota.total_nota + nota.total_propina) == 0){
			nota.alert('No existen valores de nota a registrar', 'warning');
			return false;
		}

		if(!confirm("¿Seguro desea crear la nota credito.?")){
			return false;
		}

		nota.data.factura.total_nota_credi = nota.total_nota;
		nota.data.factura.propina_nota_credi = nota.total_propina;

		document.querySelector('.saveloading').style.display = '';
		document.querySelector('.okButton').style.display = 'none';
		

		setTimeout(function(){ 

			new Ajax.Request('nota_credito/save', {
				method: 'POST',
				parameters: {
					'formas_pago': JSON.stringify(nota.formas_pago),
					'factura': JSON.stringify(nota.data.factura),
					'detalle': JSON.stringify(nota.data.detalle),
				},
				onSuccess: function(transport){

					var response = transport.responseText.evalJSON();
					document.querySelector('.saveloading').style.display = 'none';

					if(response.success){
						nota.alert('Nota credito realizada con exito', 'success');
						nota.printGenerate(response.data.urlprint);
						var consultar = document.querySelector('#consultar');
						consultar.dispatchEvent(new Event('click'));
					} else {
						document.querySelector('.okButton').style.display = '';
						nota.alert(response.message, 'danger');
					}
										
				}
			})

		}, 3000);

	},

	printGenerate: function(_self){
		window.open(_self, null, "width=300, height=700, toolbar=no, statusbar=no")
	},

	consultarFacturaAsociada : function(prefijo_facturacion, consecutivo_facturacion){
		nota.nuevaConsulta();
		document.querySelector('#prefijo_facturacion').value = prefijo_facturacion; 
		document.querySelector('#consecutivo_facturacion').value = consecutivo_facturacion; 

		var tab = document.querySelectorAll('.tab_basic')[0];
		tab.dispatchEvent(new Event('click'));

		var consultar = document.querySelector('#consultar');
		consultar.dispatchEvent(new Event('click'));

	}



}