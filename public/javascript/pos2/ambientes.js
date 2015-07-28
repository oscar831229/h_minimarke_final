
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

var AmbientesItems = {

	toogleAmbiente: function(element, number)
	{
		if (element.checked == true) {
			new Effect.BlindDown("div_amb"+number, {duration: 0.2});
		} else {
			new Effect.BlindUp("div_amb"+number, {duration: 0.2});
		}
	},

	saveData: function()
	{
		var error = false;
		$$("input[type=\"checkbox\"]").each(function(element){
			if(element.checked==true){
				var trElement = element.parentNode;
				if ($F("concepto_recepcion" + element.value) == "@") {
					alert("Debe indicar el concepto de recepcion para el ambiente " + element.title);
					error = true;
					return;
				}
				if ($F("printers" + element.value) == "@") {
					alert("Debe indicar la impresora remota número 1 para el ambiente " + element.title);
					error = true;
					return;
				}
				if ($F("printers2" + element.value) == "@") {
					alert("Debe indicar la impresora remota número 2 para el ambiente " + element.title);
					error = true;
					return;
				}
			}
		});
		if (error == false) {
			document.fl.submit();
		}
	},

	cargarAmbientesItems: function(element)
	{
		if ($F(element) != '@') {
			new Ajax.Request(Utils.getKumbiaURL("ambientes_items/getMenusItems/" + $F(element)), {
				onLoading: function()
				{
					$('loader').show();
				},
				onSuccess: function(transport)
				{
					$("lista_items").update(transport.responseText);
					$('loader').hide();
				}
			});
		} else {
			$("lista_items").update("");
			$('loader').hide();
		}
	}

}
