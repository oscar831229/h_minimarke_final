
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

	if(!Prototype.Browser.Gecko){
		var concepto  = ($('codcar').options[$('codcar').selectedIndex].innerText).strip();
	} else {
		var concepto  = ($('codcar').options[$('codcar').selectedIndex].text).strip();
	}

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
		new Modal.confirm({
			title: 'Grabar Movimiento',
			message: '¿Seguro desea grabar un movimiento con concepto "'+concepto+'"?',
			onAccept: function(){
				$('f1').submit();
			}
		});
	} else {
		new Modal.alert({
			title: 'Grabar Movimiento',
			message: 'Por favor ingrese el valor del movimiento'
		});
	}
}

function nuevo(){
	$$('.row-forpag').each(function(element){
		if(element.visible()==false){
			element.show();
			throw $break;
		}
	});
	showDeleteButtons();
}

function showDeleteButtons(){
	var numberVisibles = getNumberVisible();
	if(numberVisibles>1){
		$$('.td-forpag-del').each(function(element){
			element.show();
		});
	} else {
		$$('.td-forpag-del').each(function(element){
			element.hide();
		});
	}
}

function calculaTotal(){
	var total = 0;
	for(var i=0;i<=10;i++){
		if($('valor'+i).value){
			total+= parseFloat($('valor'+i).value);
		}
	};
	$('total').value = formatCurrency(total);
}

function cleanRow(i){
	$('forpag'+i).selectedIndex = 0;
	$('fecven'+i).value = "";
	$('valor'+i).value = "";
	$('numfor'+i).value = "";
	calculaTotal();
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

function nuevoAbono(obj){
	hideLeftPannel();
	$("img_nuevo_abono").hide();
	$("new_abono").show();
	new Element.scrollTo("abajo");
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
	$("valor0").activate();
	$('buttonPanel').hide();
}

function getNumberVisible(){
	var numberVisible = 0;
	$$('.row-forpag').each(function(element){
		if(element.visible()==true){
			numberVisible++;
		}
	});
	return numberVisible;
};

function deleteForpag(i){
	numberVisible = getNumberVisible();
	if(numberVisible>1){
		$("tr"+i).hide();
		cleanRow(i);
		showDeleteButtons();
	}
};

function activateFormas(){
	$("valor0").activate();
}

function showPrint(type, number){
	$('mainTd').style.padding = "0px";
	if(type=="RC"){
		message = 'Imprimir el recibo de caja';
	} else {
		message = 'Imprimir el recibo de egreso';
	};
	var html = "<div style='background: url(img/bg-top.png);padding:10px;padding-right:3px;' align='right'>"+
	"<button class='printControl' id='printButton' style='display:none'><img src='img/print.gif'>&nbsp;&nbsp;"+message+"</button>"+
	"</div>"
	$('print_div').update(html);
	new Effect.Appear('printButton', {duration: 1});
	$('printButton').observe('click', function(type, number){
		if(type=='RC'){
			window.open("dispatch.php?action=genrec&r="+number)
		} else {
			window.open("dispatch.php?action=genegr&r="+number)
		}
		new Effect.Fade(this.parentNode);
	}.bind($('printButton'), type, number));
}

new Event.observe(window, "keydown", function(e){
	if(e.altKey==true&&e.keyCode==32){
		nuevoAbono($("img_nuevo_abono"));
	}
})
