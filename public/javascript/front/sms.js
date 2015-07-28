
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

var Sms = {

	numfol: 0,

	initialize: function(){
		$('numfol').observe('change', function(){
			if(this.selectedIndex>0){
				new Ajax.Request('webServices/getPhones.php?numfol='+$F('numfol'), {
					method: 'GET',
					onSuccess: function(t){
						var phones = t.responseText.evalJSON();
						$('tr_phones').show();
						if(phones.length>0){
							var html = '<table cellspacing="0" width="100%" class="lista_p"><tr><th></th><th>Teléfono</th><th>Operador</th></tr>';
							phones.each(function(phone){
								html+="<tr><td width='7%'><input name='phones[]' type='checkbox' checked='checked' value='"+phone.number+"'/></td>"+
								"<td>"+phone.number+'</td><td>'+phone.type+'</td></tr>';
							});
							html+="</table>";
							$('phones').update(html);
							$('sms_text').focus();
						} else {
							$('phones').update('El cliente no tiene celulares registrados')
						}
					}
				});
				Sms.numfol = this.numfol;
			} else {
				$('tr_phones').hide();
			}
		});
		$('sms_text').observe('keyup', function(){
			var text = this.value;
			var rest = 140-text.length;
			if(rest<100){
				$('char_count').setStyle('color:#990000;font-weight:bold');
			} else {
				$('char_count').setStyle('color:#000000;font-weight:normal');
			};
			if(rest>=0){
				$('char_count').innerHTML = 'Quedan '+rest+' carácteres';
			} else {
				$('char_count').innerHTML = 'El mensaje excede 140 carácteres';
			};
			if(rest!=140&&Sms.numfol!=0){
				$('sendButton').enable();
			} else {
				$('sendButton').disable();
			}
		});
	}

};

new Event.observe(window, "load", function(){
	Sms.initialize();
})
