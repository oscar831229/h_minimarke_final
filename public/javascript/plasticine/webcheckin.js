
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Back-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var Plasticine = {

	calculateTotal: function(){
		var total = 0;
		var abonos = $$('input.abonos');
		for(var i=0;i<abonos.length;i++){
			if(abonos[i].getValue()!=''){
				total+=parseFloat(abonos[i].getValue(), 10);
			}
		};
		$('total-general').update(Utils.numberFormat(total));
	}

};

new Event.observe(window, 'load', function(){
	if($Kumbia.action=="index"){
		if(Prototype.Browser.IE){
			var windowHeight = document.body.offsetHeight;
		} else {
			if(window.innerHeight){
				var windowHeight = window.innerHeight;
			} else {
				var windowHeight = document.documentElement.clientHeight;
			}
		};
		if(windowHeight<400){
			windowHeight = 750;
		};
		var headerHeight = $('header').getHeight();
		var footerHeight = $('footer').getHeight();
		var formCheckHeight = $('formCheckin').getHeight();
		var noticeEl = $('notice');
		if(noticeEl){
			noticeHeight = noticeEl.getHeight()+10;
		} else {
			noticeHeight = 0;
		};
		var padding = (windowHeight-headerHeight-footerHeight-formCheckHeight-noticeHeight-8)/2;
		$('formCheckin').setStyle({
			'marginTop': (padding-20)+'px',
			'marginBottom': (padding+20)+'px'
		});
		$('numeroReserva').activate();
	};
	if($Kumbia.action=="enterInformation"){
		['locDireccion', 'locDireccionEmpresa', 'locProcedencia'].each(function(element){
			$(element).observe('focus', function(field){
				$(field+'Tip').show();
			}.bind($(element), element));
			$(element).observe('blur', function(field){
				$(field+'Tip').hide();
			}.bind($(element), element));
			new Ajax.Autocompleter(element, element+"Choices", $Kumbia.path+'common/getLocations', {
				paramName: "id",
				afterUpdateElement: function(element, obj, li){
					$(element+'Codigo').value = li.id;
					$(element).focus();
				}.bind($(element), element)
			});
		});
		$('conEmpresa').observe('click', function(){
			if(this.checked){
				$('companyData').show();
			} else {
				$('companyData').hide();
			}
		});
		if($('conEmpresa').checked){
			$('companyData').show();
		} else {
			$('companyData').hide();
		};
		var errorField = $F('errorField');
		if(errorField!=''){
			var errorFieldTr = $(errorField+'Tr');
			if(errorFieldTr){
				errorFieldTr.addClassName('error-field');
			};
			var errorFieldElement = $(errorField);
			if(errorFieldElement){
				errorFieldElement.activate();
			}
		}
	};
	if($Kumbia.action=="doPay"){
		$$('input.abonos').each(function(element){
 			element.observe('keyup', function(){
				Plasticine.calculateTotal();
 			})
		});
	}
})
