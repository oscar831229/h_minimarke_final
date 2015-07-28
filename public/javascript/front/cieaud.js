
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

function confirmEndDay(date, url){
	new Modal.confirm({
		title: 'Cierre de Auditoría',
		message: '¿Seguro desea hacer el cierre de auditoría del '+date+'?"',
		onAccept: function(){
			$$('input[type="button"]').each(function(element){
				element.disable();
			});

			var windowScroll = WindowUtilities.getWindowScroll(document.body);
		    var pageSize = WindowUtilities.getPageSize(document.body);
		    var top = (pageSize.windowHeight-170-windowScroll.top)/2;
		    var left = (pageSize.windowWidth-450-windowScroll.left)/2;
			var dm, d = document.createElement("DIV");
			d.id = "protect_shadow";
			d.setStyle({
				top: (windowScroll.top)+"px",
				height: (pageSize.windowHeight+windowScroll.height)+'px',
				background: '#0E040F'
			});
			d.setOpacity(0.5);
			document.body.appendChild(d);

			var d = document.createElement('DIV');
			d.innerHTML = "<table><tr><td><img src='img/close-load.gif' id='close-img'></td>"+
			"<td style='padding-left:15px;font-size:18px;color:#ffffff'>Se está efectuando el cierre, por favor espere...</tr></table>";
			d.setStyle({
				'position': 'absolute',
				'top': top+'px',
				'left': left+'px',
				'zIndex': 1500,
				'width': '450px',
				'background': '#333333',
				'padding': '30px',
				'-mozBorderRadius': '10px'
			});
			document.body.appendChild(d);
			$('close-img').observe('load', function(){
				window.setTimeout(function(){
					window.location = "?action=cieaud&option=6&yes="+url
				}, 2000);
			});
		}
	});
}

function confirmBackDay(url){
	new Modal.confirm({
		title: 'Devolver Auditoría',
		message: '¿Seguro desea regresar el sistema al día anterior?',
		onAccept: function(){
			$$('input[type="button"]').each(function(element){
				element.disable();
			});
			window.location = "?action=cieaud&option=6&return="+url
		}
	});
}