
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

function selectFact(element){
	switch($F(element)){
		case 'all':
			$$(".folio").each(function(element){
				element.show();
			});
			break;
		default:
			$$(".folio").each(function(element){
				element.hide();
			});
			$$("."+$F(element)).each(function(element){
				element.show();
			})
	}
	element.blur();
}

new Event.observe(document, 'dom:loaded', function(){
	FilterTable.initialize("q", "tr_row", "lista_fi");
	FilterTable.afterSearch = function(rows){
		$$('.tr_det').each(function(element){
			element.hide();
		});
		if(rows.length==1){
			var trId = rows[0].replace("trp", "tr");
			var trP = $(trId);
			var trElement = $(trP.parentNode.parentNode);
			trElement.show();
			trP.show();
		}
	};
	$$('.arrow').each(function(element){
		element.lang = element.title;
		element.title = "Ver Detalles";
		element.observe('click', function(){
			if(this.src.indexOf('down')!= -1){
				this.src = 'img/arrow_up.gif';
			} else {
				this.src = 'img/arrow_down.gif';
			};
			var trP = $('tr'+this.lang);
			var trElement = $(trP.parentNode.parentNode);
			if(trP.visible()){
				trElement.hide();
				trP.hide();
			} else {
				trElement.show();
				trP.show();
			};
			if(this.src.indexOf('up')!=-1){
				var pos = trElement.firstDescendant().positionedOffset();
				//alert(pos);
				new Effect.ScrollTo(trElement.firstDescendant(), { duration: 0.5 });
			};
		});
	});
	$('q').activate();
});
