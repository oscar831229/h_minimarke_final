
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

function select_all(){
   	$$('input[type="checkbox"]').each(function(element){
   		if(element.checked==true){
   			element.checked = false;
   		} else {
   			element.checked = true;
   		}
   	})
}

function backToCargar(){
	$("previ").hide();
	$("apli").show();
	$("tab_ver").hide();
}

new Event.observe(document, "dom:loaded", function(){
	$$('.arrow_down').each(function(element){
		element.lang = element.title;
		element.title = 'Ver Detalles';
		element.observe('click', function(){
			if($('tr'+this.lang).visible()==false){
				this.title = 'Ocultar Detalles';
				new Effect.BlindDown('tr'+this.lang, {duration: 0.5});
			} else {
				this.title = 'Ver Detalles';
				new Effect.BlindUp('tr'+this.lang, {duration: 0.5});
			}
		});
	});
})
