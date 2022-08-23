
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

				var i = 0;
				$$('.btnredeban').each(function(element){
					element.observe('click', redeban.interfazRedeban.bind(element, i));
					i++;
				});

				var i = 0;
				$$('.btndelete').each(function(element){
					element.observe('click', redeban.deleteInterfazRedeban.bind(element, i));
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

	var pago = this;
	var index = pago.dataset.index;
	var opcionpago = pago.options[pago.options.selectedIndex];
	var operacion = opcionpago.dataset.operacion;

	document.querySelector('.redeban[data-index="'+index+'"] > div').hide()
	if(operacion != '@'){
		document.querySelector('.redeban[data-index="'+index+'"] > div').show()
	}	

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
// url_api : 'http://redeban.test:81/api/v1',
redeban = {

	data : [],

	url_api : 'http://localhost:8080/api/v1',

	account_cuenta_id : null,

	usuario_id : null,

	setUsuario : function(usuario_id){
		redeban.usuario_id = usuario_id;
	},

	getDataStorange : function(){
		return JSON.parse(sessionStorage.getItem(redeban.account_cuenta_id));
	},

	updataTransactionStorange : function(numero){
		sessionStorage.setItem(redeban.account_cuenta_id, JSON.stringify(redeban.data));
	},

	cargarCuenta : function(account_cuenta_id){
		redeban.account_cuenta_id = account_cuenta_id;
		redeban.cargarPagos();
	},

	cargarPagos : function(){

		if(redeban.getDataStorange())
			redeban.data =redeban.getDataStorange();
		else{
			redeban.data = [];
		}

		for(var i = 0; i < redeban.data.length; i++){
			let pago = redeban.data[i];
			let selectpago = document.querySelector('.forma_p[data-index="'+pago.index+'"]');
			let inputpago = document.querySelector('.pago[data-index="'+pago.index+'"]');
			document.querySelector('input[name="redeban'+pago.index+'"]').setValue(pago.transaccionjson)
			selectpago.value = pago.forpag;
			inputpago.value = pago.monto;
			selectpago.dispatchEvent(new Event('change'));
			inputpago.disabled = true;
			selectpago.disabled = true;

		}
		redeban.setcssbtnaction();
	},

	setcssbtnaction : function(){

		$$('.btnredeban').each(function(element){
			element.classList.add("btn-primary");
			element.querySelector('span').innerHTML = 'Enviar';
			element.classList.remove("btn-success");
			element.classList.remove("btn-warning");
		});

		$$('.btndelete').each(function(element){
			element.classList.add("btn-danger");
			element.querySelector('span').innerHTML = 'Anular';
			element.classList.remove("btn-warning");
			element.hide();
		});


		for(var i = 0; i < redeban.data.length; i++){
			var pago = redeban.data[i];
			document.querySelector('.btnredeban[data-index="'+pago.index+'"]').classList.remove("btn-primary");
			if(pago.transaccion){
				document.querySelector('.btndelete[data-index="'+pago.index+'"]').show();
				document.querySelector('.btnredeban[data-index="'+pago.index+'"]').classList.add("btn-success");
				document.querySelector('.btnredeban[data-index="'+pago.index+'"]').querySelector('span').innerHTML = 'Exitoso';
			}else{
				document.querySelector('.btnredeban[data-index="'+pago.index+'"]').classList.add("btn-warning");
				document.querySelector('.btnredeban[data-index="'+pago.index+'"]').querySelector('span').innerHTML = 'Consultar';
			}

			if(pago.transacciondelete != undefined && pago.transacciondelete){
				document.querySelector('.btndelete[data-index="'+pago.index+'"]').classList.remove("btn-danger");
				document.querySelector('.btndelete[data-index="'+pago.index+'"]').classList.add("btn-warning");
				document.querySelector('.btndelete[data-index="'+pago.index+'"]').querySelector('span').innerHTML = 'Consultar';
			}
		}
	},

	interfazRedeban : function(numero){

		var pago = document.querySelector('.pago[data-index="'+numero+'"]');
		if(pago.value == 0){
			alert('No se puede realiizar pagos en cero.');
			return false;
		}

		var select = document.querySelector('.forma_p[data-index="'+numero+'"]');

		// Validar si es para consultar el estado de un pago con redeban
		var pagoaux = null;
		var indice_pago = null;
		for(var i = 0; i < redeban.data.length; i++){
			var pagoaux = redeban.data[i];
			if(pagoaux.index == numero){
				indice_pago = i;
				break;
			}
		}

		if(pagoaux!= null && pagoaux.transaccion == true){
			alert('Transacción generada de forma exitosa. ' + redeban.data[indice_pago].transaccionjson);
			return false;
		}

		if(pagoaux!= null && pagoaux.transaccion == false){

			if(confirm('¿Desea consultar el estado del pago con redeban por valor de '+pago.value+'?')){

				let transaccion = {
					index : numero,
					operacion : pagoaux.operacion
				};
	
				// datos mandados con la solicutud POST
				fetch(redeban.url_api + '/response-redeban', {
					method: "POST",
					body: JSON.stringify(transaccion),
					headers: {"Content-type": "application/json; charset=UTF-8"}
				})
				.then(response => response.json()) 
				.then(json => {
					if(json.data.success){
						if(json.data.redeban.respuesta == '00'){
							redeban.data[indice_pago].transaccion = true;
							redeban.data[indice_pago].transaccionjson = JSON.stringify(json.data.redeban);
							alert('Transacción exitosa redeban. autorizacion: ' + json.data.redeban.autorizacion);	
							document.querySelector('input[name="redeban'+numero+'"]').setValue(JSON.stringify(json.data.redeban))
						}else{
							redeban.showerror(json.data.redeban);
							redeban.data.splice(indice_pago, 1);
							redeban.data.sort();
							pago.disabled = false;
							select.disabled = false;
						}
						redeban.updataTransactionStorange();
						redeban.setcssbtnaction();
					}
				})
				.catch(err => {
					alert('La api redeban amadeus no responde.');
				});
	
			}

			return false;

		}

		if(confirm('¿Desea realizar interfaz con redeban valor '+pago.value+'?')){

			var select = document.querySelector('.forma_p[data-index="'+numero+'"]');
			var opcionpago = select.options[select.options.selectedIndex];
			var operacion = opcionpago.dataset.operacion;

			pago.disabled = true;
			select.disabled = true;

			let transaccion = {
				index : numero,
				forpag : opcionpago.value,
				operacion : operacion,
				monto : pago.value,
				iva : '0',
				factura : redeban.account_cuenta_id,
				base_dev : '0',
				imp_consu : '0', 
				cod_cajero : redeban.usuario_id,
				readresponse : false,
				transaccion : false,
			};

			// datos mandados con la solicutud POST
			fetch(redeban.url_api + '/request-redeban', {
				method: "POST",
				body: JSON.stringify(transaccion),
				headers: {"Content-type": "application/json; charset=UTF-8"}
			})
			.then(response => response.json()) 
			.then(json => {
				if(json.data.success){
					redeban.data.push(transaccion);
					redeban.updataTransactionStorange();
					redeban.setcssbtnaction();
				}else{
					alert(json.data.message);
					pago.disabled = false;
					select.disabled = false;
				}
			})
			.catch(err => {
				alert('La api redeban amadeus no responde.');
			});

		}
	},

	deleteInterfazRedeban : function(numero){

		// Validar si es para consultar el estado de un pago con redeban
		var pagoaux = null;
		var indice_pago = null;
		for(var i = 0; i < redeban.data.length; i++){
			var pagoaux = redeban.data[i];
			if(pagoaux.index == numero){
				indice_pago = i;
				break;
			}
		}

		if(indice_pago != null && pagoaux.transaccion == true){

			if(pagoaux.transacciondelete != undefined && pagoaux.transacciondelete){

				if(confirm('¿Desea consultar el estado de la anulación del pago?')){

					var pago = document.querySelector('.pago[data-index="'+numero+'"]');
					var select = document.querySelector('.forma_p[data-index="'+numero+'"]');

					let transaccion = {
						index : numero,
						operacion : '1'
					};
		
					// datos mandados con la solicutud POST
					fetch(redeban.url_api + '/response-redeban', {
						method: "POST",
						body: JSON.stringify(transaccion),
						headers: {"Content-type": "application/json; charset=UTF-8"}
					})
					.then(response => response.json()) 
					.then(json => {
						if(json.data.success){
							
							if(json.data.redeban.respuesta == '00'){
								alert('La transacción de redeban se anulo de forma exitosa.');
								redeban.data.splice(indice_pago, 1);
								redeban.data.sort();
								document.querySelector('input[name="redeban'+numero+'"]').setValue('')
								pago.disabled = false;
								select.disabled = false;
							}else{
								redeban.showerror(json.data.redeban);
								redeban.data[indice_pago].transacciondelete = false;
							}
							redeban.setcssbtnaction();
							redeban.updataTransactionStorange();
						}
					})
					.catch(err => {
						alert('La api redeban amadeus no responde.');
					});
		
				}
	
				return false;

			}

			pw_prompt({
				lm:"Por favor ingrese la contraseña:", 
				bm:"Continuar",
				transaction : pagoaux,
				index_transaccion: indice_pago,
				callback: function(password, transaction, index_transaccion) {

					let transactionjson = JSON.parse(transaction.transaccionjson);

					let transaccion = {
						index : numero,
						operacion : '1',
						recibo : transactionjson.recibo,
						factura : redeban.account_cuenta_id,
						cod_cajero : redeban.usuario_id,
						clave : password
					};
	
					fetch(redeban.url_api + '/request-redeban', {
						method: "POST",
						body: JSON.stringify(transaccion),
						headers: {"Content-type": "application/json; charset=UTF-8"}
					})
					.then(response => response.json()) 
					.then(json => {
						if(json.data.success){
							redeban.data[index_transaccion].transacciondelete = true;
							redeban.updataTransactionStorange();
							redeban.setcssbtnaction();
						}else{
							alert(json.data.message);
						}
					})
					.catch(err => {
						alert('La api redeban amadeus no responde.');
					});

				}
			});

			return false;
			
		}
	},

	showerror : function(response){
		proceso = false;
		switch (response.respuesta) {
			case '00':
				proceso = true;
				alert('Transacción aprobada')
				break;
			case '01':
				proceso = false;
				alert('Transacción declinada')
				break;
			case '02':
				proceso = false;
				alert('Pin incorrecto')
				break;
			case '03':
				proceso = false;
				alert('Clave del supervisor errada')
				break;
			case '04':
				proceso = false;
				alert('Entidad no responde')
				break;
			case '99':
				proceso = false;
				alert(response.autorizacion)
				break;
			default:
				break;
		}

		return proceso;

	}

}

var promptCount = 0;
window.pw_prompt = function(options) {
    var lm = options.lm || "Password:",
        bm = options.bm || "Submit";
    if(!options.callback) { 
        alert("No callback function provided! Please provide one.") 
    };
                   
    var prompt = document.createElement("div");
    prompt.className = "pw_prompt";
    
    var submit = function() {
        options.callback(input.value, options.transaction, options.index_transaccion);
        document.body.removeChild(prompt);
    };

	var cancel = function() {
        document.body.removeChild(prompt);
    };

    var label = document.createElement("label");
    label.textContent = lm;
    label.for = "pw_prompt_input" + (++promptCount);
    prompt.appendChild(label);

    var input = document.createElement("input");
    input.id = "pw_prompt_input" + (promptCount);
    input.type = "password";
	input.setAttribute("class", "azul text-left");
    input.addEventListener("keyup", function(e) {
        if (e.keyCode == 13) submit();
    }, false);
    prompt.appendChild(input);
	

    var button = document.createElement("button");
    button.textContent = bm;
	button.setAttribute("class", "btn btn-sm btn-primary");
    button.addEventListener("click", submit, false);
    prompt.appendChild(button);

	var button = document.createElement("button");
    button.textContent = 'cancelar';
	button.setAttribute("class", "btn btn-sm btn-secondary ml-4");
    button.addEventListener("click", cancel, false);
    prompt.appendChild(button);

    document.body.appendChild(prompt);
	input.focus();
};


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
			
			document.querySelectorAll('input:disabled, .forpag-select').forEach(element => {
				element.disabled = false;
			});			

			document.forms[0].submit()
		})
	} else {
		
		document.querySelectorAll('input.pago:disabled, .forma_p').forEach(element => {
			element.disabled = false;
		});	

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
