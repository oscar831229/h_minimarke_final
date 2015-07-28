
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var VirtualKeyBoard = Class.create({

	initialize: function(){
		new Ajax.Request(Utils.getKumbiaURL('keyboard'), {
			method: 'GET',
			onSuccess: function(t){
				if($('virtual-keyboard')){
					return;
				};
				var keyboard = document.createElement('DIV');
				keyboard.id = 'virtual-keyboard';
				keyboard.update(t.responseText);
				document.body.appendChild(keyboard);
				keyboard.select('div.key').each(function(element){
					element.observe('mousedown', function(event){
						var activeElement = document.activeElement;
						if(activeElement.tagName=='INPUT'){
							var content = this.innerHTML;
							if(content!='&nbsp;'&&content.substr(0, 4)!='<img'){
								activeElement.value+=this.innerHTML;
							} else {
								if(content=='&nbsp;'){
									activeElement.value+=' ';
								} else {
									activeElement.value = activeElement.value.substr(0, activeElement.value.length-1);
								}
							}
							activeElement.fire('keyup');
							activeElement.fire('keydown');
						};
						Event.stop(event);
					})
				});
				$('exit').observe('click', function(){
					$('virtual-keyboard').remove();
				})
			}
		});
	}

});
