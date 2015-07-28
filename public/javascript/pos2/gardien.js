
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

var Gardien = {

	initialize: function(){
		var first = true;
		$$('div#acl input[type="checkbox"]').each(function(element){
			if(first==true){
				first = false;
			} else {
				if(element.checked==false){
					element.disable();
				}
			}
			element.observe('click', function(){
				Gardien.rollTree(this);
			})
		});
		$$('div#acl input[type="checkbox"]').each(function(element){
			if(element.checked==true){
				Gardien.rollTree(element);
			}
		});
		$('role').observe('change', function(){
			if($('acl').innerHTML!=''){
				$('acl').update('');
				$('submitButton').setValue('Ver');
			}
		})
	},

	rollTree: function(element){
		var enable = element.checked;
		var ulElement = element.adjacent('UL')[0];
		element.parentNode.select('input[type="checkbox"]').each(function(inputElement){
			if(inputElement.up(1)==ulElement){
				if(enable==true){
					inputElement.enable();
				} else {
					inputElement.disable();
				}
			}
		});
	}

}

new Event.observe(document, 'dom:loaded', Gardien.initialize);