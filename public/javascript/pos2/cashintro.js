
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

function sumCash(){
  	var i;
  	var m = 0;
  	for(i=0;i<=20;i++){
	    if($("m"+i)){
		  	m+=parseFloat($("m"+i).value)
		}
	}
	$("total_cash").innerHTML = "TOTAL $" + m
}

function save(id, m, obj){
	new AJAX.execute(
		{
			action: "cashintro/save/"+id,
			parameters: "cant=" + obj.value + "&valor=" + m
		}
	)
}

function open_cash_tray(id){
	new AJAX.viewRequest(
		{
			action: "cashintro/open/" + id,
			oncomplete: redirect_to_action("appmenu")
		}
	)
}