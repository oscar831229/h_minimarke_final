
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
	$$('select.tipfac').each(function(element){
		element.lang = element.title;
		element.title = '';
		element.observe('change', function(){
			if($F(this)=='A'){
				$('apofol'+this.lang).show();
			} else {
				$('apofol'+this.lang).hide();
			}
		})
	})
})