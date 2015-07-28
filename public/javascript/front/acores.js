
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

new Event.observe(document, 'dom:loaded', function(){
	$$('.documento').each(function(element){
		element.observe('blur', getAcoresCustomer);
	});
});

function getAcoresCustomer(event){
	var number = this.id.replace('cedula', '');
	if(this.value!=""){
	    new Ajax.Request('webServices/infoCustomer.php?value='+this.value, {
			onSuccess: function(number, transport){
				alert(transport.responseText);
				var response = transport.responseText.evalJSON();
				if(response.status=='OK'){
					var items = response.data;
					for(var i=0;i<items.length;i++){
					    var name = items[i].f;
					    if(name=='cedula_det'){
					    	name = 'nombre';
					    }
						if($(name+number)){
							$(name+number).setValue(items[i].v);
							if(name=='fecnac'){
								var fecha = items[i].v;
								var year = fecha.substring(0, 4);
								var month = fecha.substring(5, 7);
								var day = fecha.substring(8, 10);
								$(name+number+'_year').value = year;
								$(name+number+'_month').value = month;
								$(name+number+'_day').value = day;
							}
						}
					}
				}
			}.bind(this, number)
		});
	}
}
