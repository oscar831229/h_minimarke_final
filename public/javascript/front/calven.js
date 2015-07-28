
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

var monthOffsets = [];
new Event.observe(window, "load", function(){
	var viewPort;
	for(var i=1;i<13;i++){
		viewPort = $('mon'+i).positionedOffset();
		monthOffsets[i] = viewPort[1];
	};
	var d = document.createElement('DIV');
	var windowScroll = WindowUtilities.getWindowScroll(document.body);
    var pageSize = WindowUtilities.getPageSize(document.body);
	d.id = "optionBox";
	d.addClassName('option_box');
	d.innerHTML = $("option_div").innerHTML;
	$('option_div').innerHTML = "";
    d.style.top = (pageSize.windowHeight+windowScroll.top-170)+'px';
    d.style.left = (pageSize.windowWidth+windowScroll.left-230)+'px';
    document.body.appendChild(d);
	new Event.observe(window, 'scroll', function(){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
    	var pageSize = WindowUtilities.getPageSize(document.body);
    	for(var i=1;i<13;i++){
    		if((windowScroll.top-50)<monthOffsets[i]){
    			$('monthSelector').setValue(i);
    			break;
    		}
    	};
    	$("optionBox").style.top = (pageSize.windowHeight+windowScroll.top-170)+'px';
	});
	$('chkResAll').observe('click', function(){
		$$('.cDa').each(function(element){
			element.hide();
		});
		$$('.cAll').each(function(element){
			element.show();
		});
	})
	$('chkResPen').observe('click', function(){
		$$('.cDa').each(function(element){
			element.hide();
		});
		$$('.cRp').each(function(element){
			element.show();
		});
	});
	$('chkResGar').observe('click', function(){
		$$('.cDa').each(function(element){
			element.hide();
		});
		$$('.cRg').each(function(element){
			element.show();
		});
	});
	$('monthSelector').observe('change', function(){
		new Effect.ScrollTo($('mon'+$F(this)))
		this.blur();
	});
	new Draggable(d);
});
