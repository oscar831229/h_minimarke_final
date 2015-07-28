
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

new Event.observe(window, "load", function(){
    var pageSize = WindowUtilities.getPageSize(document.body);
    document.body.scrollTop = 0;
    $('IFrame1').style.height = (pageSize.windowHeight-58)+"px";
	$('mainTitle').innerHTML = "Forecast - Reservas";
});

new Event.observe(window, "resize", function(){
	var pageSize = WindowUtilities.getPageSize(document.body);
    $('IFrame1').style.height = (pageSize.windowHeight-58)+"px";
})