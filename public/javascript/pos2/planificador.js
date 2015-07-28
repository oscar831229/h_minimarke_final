
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

var Planificador = {

	costos: [],

	totalize: function(){

		var totalCosto = 0;
		$$('input.costo').each(function(element){
			if(element.getValue()!=''){
				totalCosto+=parseFloat(element.getValue(), 10);
			};
		});
		$('costo').update(Utils.numberFormat(totalCosto.toFixed(2)));

		var totalValor = 0;
		$$('input.valor').each(function(element){
			if(element.getValue()!=''){
				totalValor+=parseFloat(element.getValue(), 10);
			};
		});
		$('valor').update(Utils.numberFormat(totalValor.toFixed(2)));

		var totalUtilidad = 0;
		$$('input.utilidad').each(function(element){
			if(element.getValue()!=''){
				totalUtilidad+=parseFloat(element.getValue(), 10);
			};
		});
		$('utilidad').update(Utils.numberFormat(totalUtilidad.toFixed(2)));

		if(totalValor>0){
			$('pcosto').update(Utils.numberFormat((totalCosto/totalValor).toFixed(2)));
		} else {
			$('pcosto').update('0.00');
		}
	},

	addCallbacks: function(){
		var i = 0;
		$$('.menuItem').each(function(element){
			element.lang = i;
			element.observe('change', function(){
				if(this.getValue()!='@'){
					var next = parseInt(this.lang, 10)+2;
					var rowId = $('row-'+next);
					if(rowId){
						rowId.show();
					};
					new Ajax.Request(Utils.getKumbiaURL('planificador/getCosto'), {
						parameters: {
							'menuItemId': this.getValue()
						},
						onSuccess: function(index, transport){
							var response = JSON.parse(transport.responseText);
							if(response.status=='OK'){
								$('cantidad'+index).setValue(1);
								$('costo'+index).setValue(response.costo);
								$('valor'+index).setValue(response.venta);
								$('pcosto'+index).setValue(response.pcosto);
								$('utilidad'+index).setValue(response.utilidad);
								Planificador.totalize();
								Planificador.costos[this.lang] = response;
							}
						}.bind(this, this.lang)
					});
				}
			});
			$('costo'+i).setAttribute('readOnly', true);
			$('valor'+i).setAttribute('readOnly', true);
			$('pcosto'+i).setAttribute('readOnly', true);
			$('utilidad'+i).setAttribute('readOnly', true);
			i++;
		});
		var i = 0;
		$$('input.cantidad').each(function(element){
			element.lang = i;
			element.observe('keyup', function(){
				if(this.getValue()!=''){
					var index = this.lang;
					if(typeof Planificador.costos[index] != "undefined"){
						var cantidad = parseFloat(this.getValue(), 10);
						$('costo'+index).setValue((Planificador.costos[index].costo*cantidad).toFixed(2));
						$('valor'+index).setValue((Planificador.costos[index].venta*cantidad).toFixed(2));
						$('utilidad'+index).setValue((Planificador.costos[index].utilidad*cantidad).toFixed(2));
						Planificador.totalize();
					};
				}
			});
			i++;
		});

		var i = 0;
		$$('img.delete').each(function(element){
			element.lang = i;
			element.title = 'Quitar Item de Menú';
			element.observe('click', function(index){
				$('menuItemId'+index).selectedIndex = 0;
				$('cantidad'+index).setValue('');
				$('costo'+index).setValue('');
				$('valor'+index).setValue('');
				$('pcosto'+index).setValue('');
				$('utilidad'+index).setValue('');
			}.bind(element, i));
		});

		Planificador.totalize();
	}

}

new Event.observe(document, 'dom:loaded', Planificador.addCallbacks);