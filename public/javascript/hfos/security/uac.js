
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

/**
 * HfosUac
 *
 * User Account Control (UAC), Permite elevar un privilegio a otro usuario de manera temporal
 *
 */
var HfosUac = {

	_element: null,

	/**
	 * Genera el cuadro de dialogo de elevación de privilegios
	 *
	 * @this {HfosUac}
	 */
	requestElevation: function(resource, onSuccess){

		var windowScroll = WindowUtilities.getWindowScroll(document.body);
    	var pageSize = WindowUtilities.getPageSize(document.body);
    	var left = (pageSize.windowWidth-500-windowScroll.left)/2;
    	var d = document.createElement('DIV');
    	d.addClassName('modalLayout');
    	d.id = 'modalLayout';
    	var html = "<table width='100%'>"+
    	"<tr><td id='imgCon' valign='top'><img src='"+$Kumbia.path+"img/backoffice/blank.gif' class='lock32'/></td><td>"+
    	"<h1>Cambio de Usuario</h1><h2>Puede cambiar temporalmente de usuario para ejecutar la acción que estaba realizando. "+
    	"Indique un usuario que tenga privilegios suficientes para realizar esta acción.</h2></td></tr>"+
    	"<tr><td align='right' colspan='2'><table class='loginUAC'><tr><td align='right'><b>Usuario</b></td><td><input type='text' id='login' autocomplete='off'></tr><tr>"+
    	"<td align='right'><b>Password</b></td><td><input type='password' id='password'/></tr></td></table></td>"+
    	"<tr><td align='right' colspan='2'>"+
    	"<div class='buttonContainer'><table><tr>"+
    	"<td><input type='button' value='Cerrar' class='controlButton controlButtonShadow' id='acceptButton'></td>"+
    	"<td><input type='button' value='Cambiar de Usuario' class='controlButton controlButtonShadow' id='authButton'></td>"+
    	"<td><div class='loadUAC' style='display:none'></div></td>"+
    	"</tr></table></div>"+
    	"</td></tr></table>";
    	d.innerHTML = html;
    	document.body.appendChild(d);
    	Hfos.getUI().centerAtScreen(d);

    	Hfos.getUI().showScreenShadow();

    	var acceptButton = d.selectOne('input#acceptButton');
	    acceptButton.observe('click', HfosUac.removeDialog);

	    var authButton = d.selectOne('input#authButton');
	    authButton.observe('click', HfosUac.approveElevation.bind(this, resource, onSuccess));

	    var inputBox = d.selectOne('input#login');
	    inputBox.activate();

    	new Draggable(d);
    	HfosUac._element = d;
	},

	/**
	 *
	 * @this {HfosUac}
	 */
	approveElevation: function(resource, onSuccess){
		HfosUac._element.getElement('loadUAC').show();
		HfosUac._element.selectOne('input#acceptButton').disable();
		HfosUac._element.selectOne('input#authButton').disable();
		var loginBox = HfosUac._element.selectOne('input#login');
		var passwordBox = HfosUac._element.selectOne('input#password');
		new HfosAjax.JsonRequest('gardien/elevate', {
			'parameters': 'resource='+resource+'&login='+loginBox.getValue()+'&password='+passwordBox.getValue(),
			'onSuccess': function(resource, onSuccess, response){
				if(response.status=="OK"){
					HfosUac.removeDialog();
					onSuccess();
				} else {
					if(response.status=='FAILED'){
						new Effect.Shake(HfosUac._element, {
							duration: 0.5
						});
						HfosUac._element.getElement('loadUAC').hide();
						HfosUac._element.selectOne('input#acceptButton').enable();
						HfosUac._element.selectOne('input#authButton').enable();
						HfosUac._element.selectOne('input#login').activate();
					}
				}
			}.bind(this, resource, onSuccess)
		});
	},

	/**
	 *
	 * @this {HfosUac}
	 */
	removeDialog: function(){
		HfosUac._element.erase();
		HfosUac._element = null;
		Hfos.getUI().hideScreenShadow();
	}

}