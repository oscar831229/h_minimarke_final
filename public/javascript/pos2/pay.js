
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

/* �Swing! */

function toggleNode(obj, id, n)
{
	if (obj.src.indexOf("minus") != -1) {
		obj.src = "img/tplus.gif";
		for (var i = 0;i < parseInt(n); i++) {
			$("node" + id + "_" + i).hide()
		}
	} else {
		obj.src = "img/tminus.gif";
		for (var i = 0; i < parseInt(n); i++) {
			$("node" + id + "_" + i).show()
		}
	}
}

function collapseTree(obj, id, n){
	if(obj.checked){
		for(var i=0;i<parseInt(n);i++){
			if($("check"+id+"_"+i)){
				$("check"+id+"_"+i).checked = true
				$("check"+id+"_"+i).disabled = false
				$("td"+id+"_"+i).className = "tsmall"
			}
		}
	} else {
		for(var i=0;i<parseInt(n);i++){
			$("check"+id+"_"+i).checked = false
			$("check"+id+"_"+i).disabled = true
			$("td"+id+"_"+i).className = "dtsmall"
		}
	};
	getAccountData()
}

function getAccountData()
{
	var list = [];
	var n = 0;
	for (var i = 1; i <= numNodes; i++) {
		if ($("check"+(i-1)).checked) {
			for (var j=0;j<Nodes[i];j++) {
				if ($("check"+NodesIds[i]+"_"+j)) {
					if ($("check"+NodesIds[i]+"_"+j).checked) {
						list[n++] = $("check"+NodesIds[i]+"_"+j).lang;
					}
				}
			}
		}
	};
	if (list.length > 0) {
		//alert("pay/loadAccount/"+list.join("-"))
		new Ajax.Request(Utils.getKumbiaURL("pay/loadAccount/"+list.join("-")), {
			method: 'GET',
			onSuccess: function(t){
				$('account').update(t.responseText);
				var i = 0;
				$$('.forma_p').each(function(element){
					element.observe('change', showOptions.bind(element, i));
					i++;
				});
				if ($('add_forma_div')) {
					$('add_forma_div').observe('click', addFormaPago);
				};
				$('total_propina').activate();
			},
			onFailure: function(t){
				$('account').update(t.responseText);
			}
		});
	} else {
		alert('Seleccione una cuenta')
	}
}

function totalPagos(obj)
{
	if (obj) {
		if (obj.value == "") {
			obj.value = 0;
		}
	};
	var total = 0;
	for (var i = 0;i <= 8; i++){
		if ($("pago"+i)) {
			if ($("pago"+i).value == "") {
				$("pago"+i).value = 0;
			}
			total += parseFloat($("pago"+i).getValue());
		}
	};
	total = parseFloat(total).toFixed(2);
	$("total_pagos").value = total;
	$("total_saldo").value = parseFloat($('total_cuenta').getValue())+parseFloat($('total_propina').getValue())-total;
}

function showOptions(n){
	var value = $F(this);
	if(value==0){
		if(!$('trnum'+n).visible()){
			$('trnum'+n).show();
		}
	} else {
		if(value>0){
			if(!$('trnum'+n).visible()){
				$('trnum'+n).show();
			}
		} else {
			$('habsel'+n).hide();
			$('numsel'+n).hide();
		}
	}
}

function showVueltas(inputPago){
	var total_pagos = parseInt(inputPago.value);
	
	var dinero = prompt("Ingrese dinero recibido:", total_pagos);

	dinero = parseInt(dinero);
	var diff = dinero - total_pagos;
	
	if (diff < 0) {
		showVueltas(inputPago);
		return;
	}

	diff = Math.abs(diff);

	alert("VALOR A PAGAR:  " + format2(total_pagos, '') + 
		"\nVALOR RECIBIDO: " + format2(dinero, '') + 
		"\n" +
		"\n" +
		"\nVALOR CAMBIO:   " + format2(diff, '')
	);
}

function format2(n, currency) {
  return currency + n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
}

function pay(){
	var cargo_habitacion = false;
	var facturar = false;
	for (var i = 0; i <= 8; i++) {
		if ($("tr"+i)){
			if ($("tr"+i).style.display!="none") {
				if (parseFloat($("pago"+i).value)) {
					if ($("forma"+i).tagName == "SELECT") {
						//if($("forma"+i).selectedIndex==0){
						//	new Effect.Highlight("forma"+i, {startcolor: "#800000"})
						//	return
						//}
					}
				}
			}
		}
	}
	if (parseFloat($("total_saldo").value).toFixed(2) > 0) {
		alert("El pago no corresponde al total de la cuenta más la propina");
		new Effect.Highlight("pago0", {startcolor: "#800000"});
		$('pago0').focus();
		return;
	}

	if (parseFloat($("total_saldo").getValue()).toFixed(2) < 0) {
		Modal.confirm('¿El valor a pagar supera el pendiente. ¿Desea continuar?', function(){
			document.forms[0].submit()
		})
	} else {
		document.forms[0].submit()
	}
}

function getAccounts(obj, n)
{
	var id = obj.options[obj.selectedIndex].value
	new AJAX.viewRequest({
		action: "pay/getAccounts/"+id+"/&n="+n,
		container:"cuentas"+n
	})
}

function getCustomerId(iid, xid)
{
	if (!$('nombre_cliente').readOnly) {
		$('documento').value = xid.id
	}
}

function saveClient(){
	new AJAX.viewRequest({
		action: "pay/saveClient/"+$("documento").value,
		parameters: "nombre="+$("nombre_cliente").value,
		container: "messages"
	})
}

function addFormaPago(){
	for(var i=0;i<=8;i++){
		if($("tr"+i).style.display=="none"){
			new Effect.BlindDown("tr"+i)
			return
		}
	}
}

function showPrefactura(id){
	if($('documento').value){
		if($('nombre_cliente').value.indexOf("NO EXISTE")==-1){
			window.open(
				'prefactura/index/'+id+"/&documento="+$('documento').value,
				null,
				'width=700, height=700, toolbar=no, statusbar=no'
			)
		} else {
			alert('Debe especificar el cliente antes de la Prefactura');
			new Effect.Highlight('documento')
			return
		}
	} else {
		alert('Debe especificar el cliente antes de la Prefactura');
		new Effect.Highlight('documento')
		return;
	}
};

new Event.observe(window, 'load', function(){
	window.setTimeout(function(){
		if(!$('total_saldo')){
			$("bcuentas").toggle();
		}
	}, 300)
});
