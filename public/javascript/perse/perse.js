
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

var Perse = {

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

new Event.observe(document, 'dom:loaded', function(){
	var currencyId = $('currencyId');
	if(currencyId){
		currencyId.observe('change', function(){
 			window.location = Utils.getKumbiaURL('session/setCurrency/'+$F(this));
 		})
	};
 	$('locale').observe('change', function(){
 		window.location = Utils.getKumbiaURL('session/setLocale/'+$F(this));
 	});
 	if($Kumbia.action=='doPay'){
 		$$('input.abonos').each(function(element){
 			element.observe('keyup', function(){
 				Perse.calculateTotal();
 			});
 		});
 	}
});