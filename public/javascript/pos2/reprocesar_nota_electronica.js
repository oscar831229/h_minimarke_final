
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

	consultarNota:function(element){

		if($('consecutivo_documento').getValue().trim() == ''){
			nota.alert("Debe digitar el número de la factura.", 'warning');
			return false;
		}

		document.querySelector('.loading').style.display = '';
		$s('#consultar').style.display = "none";

		setTimeout(function(){

			new Ajax.Request("reprocesar_nota_electronica/findNota/"+$('prefijo_documento').getValue().trim()+"/"+$('consecutivo_documento').getValue().trim(), {
				onSuccess: function(transport){

					var response = transport.responseText.evalJSON();
					document.querySelector('.loading').style.display = 'none';

					if(response.success){
	
						// No se encontro la factura
						if(response.data.length == false){
							nota.alert('No exite la nota credito', 'danger');
							$s('#consultar').style.display = "";
							return false;
						}
	
						// La busqueda corresponde a orden de servicio
						if(response.data.factura.tipo == 'O'){
							nota.alert('Los datos corresponde a una orden de servicio', 'danger');
							$s('#consultar').style.display = "";
							return false;
						}
	
						nota.data = response.data;
						nota.loadDatos();

						$s('#consultar').style.display = "none";
						$s('#nueva').style.display = "";
	
						document.querySelector('#prefijo_documento').readOnly = true;
						document.querySelector('#consecutivo_documento').readOnly = true;
	
					} else {
						$s('#consultar').style.display = "";
						nota.alert(response.message, 'danger');
					}
				}
			});

		}, 1000);

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

	loadDatos : function(){
		
		var tr = '';
		var factura = this.data.factura;
		var notacredito = this.data.notacredito;
		var _self = this.data._self;
		tr  +=  '<tr>'
				+	'	<td class="al-l">'+ factura.nombre +'</td>'
				+	'	<td class="al-c">'+ factura.cedula +'</td>'
				+	'	<td class="al-c">'+ notacredito.prefijo_documento +'</td>'	
				+	'	<td class="al-c">'+ notacredito.consecutivo_documento +'</td>'	
				+	'	<td class="al-c">'+ notacredito.fecha +'</td>'	
				+	'	<td class="al-r">'+ formatNumber.new(notacredito.total) +'</td>'
				+	'	<td class="al-r">'+ factura.prefijo_facturacion + '-' + factura.consecutivo_facturacion +'</td>'
				+	'	<td class="al-c"><button class="btnxml btnimg" onclick="nota.xmlGenerate(this, \''+notacredito.id+'\')">'+ this.data.iconxml +'</button>'+nota.data.xmlloading+'<button class="btnprint btnimg" onclick="nota.printGenerate(\''+_self+'\')">'+ this.data.iconprint +'</button></td>'
				+	'</tr>'

		document.querySelector('#tbldetalle tbody').innerHTML=tr;
		

	},

	printGenerate : function(url){
		window.open(url, null, "width=300, height=700, toolbar=no, statusbar=no")
	},

	xmlGenerate : function(element, factura_id){

		if(confirm("Desea generar el XML Dian ?")){

			var td = element.closest('td');

			td.querySelector('.xmlloading').style.display = '';
			td.querySelector('.btnxml').style.display = 'none';
			
			setTimeout(function(){ 

				new Ajax.Request('reprocesar_nota_electronica/save', {
					method: 'POST',
					parameters: {
						'nota_id': factura_id
					},
					onSuccess: function(transport){
	
						var response = transport.responseText.evalJSON();
						td.querySelector('.xmlloading').style.display = 'none';
	
						if(response.success){
							nota.alert('El xml de la factura se genero exitosamente', 'success');
							window.open(response.data.path, null, "width=300, height=700, toolbar=no, statusbar=no")
						} else {
							nota.alert(response.message, 'danger');
						}

						td.querySelector('.btnxml').style.display = '';
											
					}
				})

				
	
			}, 1000);

		}

	},

	nuevaConsulta : function(){

		$s('#consultar').style.display = "";
		$s('#nueva').style.display = "none";

		document.querySelector('#prefijo_documento').readOnly = false;
		document.querySelector('#consecutivo_documento').readOnly = false;
		document.querySelector('#tbldetalle tbody').innerHTML = '';
		document.querySelector('#prefijo_documento').value = '';
		document.querySelector('#consecutivo_documento').value = '';
		document.querySelector('#prefijo_documento').focus();
		document.querySelector('.okButton').style.display = '';
		
		nota.formas_pago = [];
		nota.data = [];

	}


}