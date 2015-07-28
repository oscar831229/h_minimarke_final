
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

var Menu = {

	timeOut: null,

	initMenu: function(){
		$$('.menu_item').each(function(element){
			element.observe('mouseover', function(){
				$$('.menu_list').each(function(menuElement){
					if(this.id+'_list'!=menuElement.id){
						document.body.removeChild(menuElement);
					}
				});
				if($(this.id+'_list')){
					return;
				}
				if($(this.id+'_content')){
					var position = element.cumulativeOffset();
					var d = document.createElement('DIV');
					d.id = this.id+'_list';
					d.addClassName('menu_list');
					d.setStyle({
						'position': 'absolute',
						'top': (position[1]+43)+'px',
						'left': position[0]+'px'
					});
					d.innerHTML = $(this.id+'_content').innerHTML;
					window.setTimeout(function(d){
						d.observe('mouseout', function(event){
							document.body.removeChild(this);
							new Event.stop(event);
						});
					}.bind(this, d), 5000);
					document.body.appendChild(d);
					if(Menu.timeOut==null){
						Menu.timeOut = window.setTimeout(function(){
							$$('.menu_list').each(function(menuElement){
								document.body.removeChild(menuElement);
							});
							Menu.timeOut = null;
						}, 20000);
					}
				}
			});
		});
		new Event.observe(document.body, "click", function(){
			$$('.menu_list').each(function(menuElement){
				document.body.removeChild(menuElement);
			});
		})
	}
}
