function addCargoNote(numrec){
	var windowScroll = WindowUtilities.getWindowScroll(document.body);
    var pageSize = WindowUtilities.getPageSize(document.body);
    var left = (pageSize.windowWidth-650-windowScroll.left)/2;

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

	dm = document.createElement("DIV");
	dm.id = "note_dialog";
	dm.setStyle({
		position: "absolute",
		top: (windowScroll.top)+"px",
		background: "#fff",
		left: left+"px",
		width: "650px",
		zIndex: 710,
		padding: "10px",
		border: "1px solid #969696"
	});
	dm.hide();

	document.body.appendChild(dm);
	dm.innerHTML = "<table align='center' width='100%'><tr><td><img src='img/edit.png' width='96'></td>"+
	"<td width='15px'></td>"+
	"<td>Agregue/Modifique la nota/observaci&oacute;n del recibo de caja"+
	"<textarea style='width:99%' id='note'></textarea><br><br>"+
	"<div align='right'>"+
	"<input type='button' value='Confirmar' class='controlButton' onclick='confirmNote("+numrec+")'>&nbsp;"+
	"<input type='button' value='Cancelar' class='controlButtonCancel' onclick='cancelNote()'></div>"+
	"</td></tr></table>";
	new Ajax.Request("webServices/getReciboNote.php?numrec="+numrec, {
		onSuccess: function(transport){
			new Effect.BlindDown(dm, {
				duration: 0.3,
				afterFinish: function(){
					$("note").value = transport.responseText;
					$("note").activate();
				}
			});
		}
	});
}

function cancelNote(){
	document.body.removeChild($("note_shadow"));
	document.body.removeChild($("note_dialog"));
}

function confirmNote(numrec){
	new Ajax.Request("webServices/saveReciboNote.php", {
		parameters: {
			numrec: numrec,
			note: $F("note")
		},
		onSuccess: function(){
			document.body.removeChild($("note_shadow"));
			document.body.removeChild($("note_dialog"));
		}
	});
}

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
				if(isNaN(eValue)){
					message = 'Por favor indique el IVA reportado para la tarjeta de crédito';
				} else {
					message = 'Por favor indique el IVA reportado para la tarjeta de crédito por valor '+eValue;
				};
				if(eIvaValue<=0){
					new Modal.alert({
						title: 'IVA Reportado',
						message: message
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
		$('total').form.submit();
	} else {
		new Modal.alert({
			title: 'Garantizar Reserva',
			message: 'Por favor ingrese el valor del depósito'
		});
	}
}

function nuevaGarantia(){
	shLeftPannel();
	$("img_new_abono").hide();
	new Effect.Appear("new_abono", {
		duration: 0.5,
		afterFinish: goAbajo
	});
	window.setTimeout(function(){
		$("valor0").activate();
	}, 800);
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

function nuevo(){
	for(var i=10;i>=0;i--){
		if(!$('tr'+i).visible()){
			$('tr'+i).show();
			return
		}
	}
}

function calculaTotal(){
	var total = 0;
	for(var i=0;i<=10;i++){
		if($('valor'+i).value){
			total+=parseFloat($('valor'+i).value);
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

function anulaCarta(numres){
	new Modal.confirm({
		title: "Anular Garantia",
		message: "¿Seguro desea anular la garantía con carta?",
		onAccept: function(){
			window.location = "?action="+$Jasmin.action+"&option="+$Jasmin.option+"&g=2&fl_numres="+numres;
		}
	});
};

new Event.observe(window, "load", function(){
	$$('.valorForma').each(function(element){
		element.observe('focus', function(){
			this.activate();
		});
	});
});
