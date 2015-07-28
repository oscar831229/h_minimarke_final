
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

function valida(){

	var total = 0;
	for(var i=0;i<11;i++){

		var eValor = $('valor'+i);
		var eValue = parseFloat(eValor.value);

		var eForma = $('forpag'+i);
		var eOption = eForma.options[eForma.selectedIndex];
		if(eOption.hasClassName('tipforT')){
			var eIvaRep = $('ivarep'+i);
			if(!eIvaRep.hasClassName('no-ivarep')){
				var eIvaValue = parseFloat(eIvaRep.value);
				if(eIvaValue<=0){
					new Modal.alert({
						title: 'IVA Reportado',
						message: 'Por favor indique el IVA reportado para la tarjeta de crédito por valor '+eValue
					});
					return;
				}
			}
		}

		if(eValue>0){
			total+=eValue;
		}
	};

	if(total>0){
		$('f1').submit();
	} else {
		new Modal.alert({
			title: 'Abonos a Cartera',
			message: 'Por favor ingrese el valor del abono'
		});
	}
}

function nuevo(){
	for(var i=10;i>=0;i--){
		if(!$('tr'+i).visible()){
			$('tr'+i).show();
			return;
		}
	}
}

function showForpagColumns(){
	var i = 0;
	var numberTarjetasT = 0, numberConsignaciones = 0, numberCheques = 0;
	$$('.forpag-select').each(function(element){
		var optionElement  = element.options[element.selectedIndex];
		if(optionElement.hasClassName('tipforT')){
			numberTarjetasT++;
		} else {
			if(optionElement.hasClassName('tipforN')){
				numberConsignaciones++;
			} else {
				if(optionElement.hasClassName('tipforC')){
					numberCheques++;
				}
			}
		}
	});
	showFecven = false;
	showNumfor = false;
	showIvarep = false;
	var cFecven = $$('.c-fecven');
	var cNumfor = $$('.c-numfor');
	var cIvarep = $$('.c-ivarep');
	if(numberTarjetasT>0){
		showFecven = true;
		showNumfor = true;
		showIvaRep = true;
	}
	if(numberCheques>0||numberConsignaciones>0){
		showNumfor = true;
	}
	if(showFecven){
		cFecven.each(function(element){
			element.show();
		});
	} else {
		cFecven.each(function(element){
			element.hide();
		});
	};
	if(showNumfor){
		cNumfor.each(function(element){
			element.show();
		});
	} else {
		cNumfor.each(function(element){
			element.hide();
		});
	};
	if(showIvaRep){
		cIvarep.each(function(element){
			element.show();
		});
	} else {
		cIvarep.each(function(element){
			element.hide();
		});
	}
};

function calculaTotal(){
	var total = 0;
	for(var i=0;i<=10;i++){
		if($('valor'+i).value){
			total+=parseFloat($('valor'+i).value);
		}
	}
	$('total').value = formatCurrency(total);
}

function cleanRow(i){
	$('forpag'+i).selectedIndex = 0;
	$('fecven'+i).value = "";
	$('valor'+i).value = "";
	$('numfor'+i).value = "";
	calculaTotal();
}

function showGrabar(element){
	element.hide();
	new Effect.Appear("new_abono", {
		duration: 0.5,
		afterUpdate: function(){
			new Element.scrollTo("abajo")
		}
	});
	$("valor0").observe("keydown", function(e){
		if(e.keyCode==Event.KEY_RETURN){
			valida();
		}
	});
	['.c-fecven', '.c-numfor', '.c-ivarep'].each(function(className){
		$$(className).each(function(element){
			element.hide();
		});
	});
	$$('.forpag-select').each(function(element){
		element.observe('change', function(){
			var optionElement = this.options[this.selectedIndex];
			showForpagColumns();
			numberRow = this.name.replace('forpag', '');
			this.blur();
			$('valor'+numberRow).activate();
		});
	});
};

function confirmAnular(url){
	new Modal.confirm({
		title: "Anular Abono a Cartera",
		message: "¿Seguro desea anular el Abono a Cartera?",
		onAccept: function(url){
			window.location = url;
		}.bind(this, url)
	});
}

new Event.observe(window, "load", function(){
	$$('.valorForma').each(function(element){
		element.observe('focus', function(){
			this.activate();
		});
	});
	window.setTimeout(function(){
		$("numfac_box").activate()
	}, 300);
});
