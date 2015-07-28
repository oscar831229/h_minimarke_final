
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

var Utility = {

	getFromAjax: function(fields, action ,callback, controller){
		if(Object.isUndefined(controller)){
			controller = 'ajax';
		}
		var parameters = Object.extend({});
		if(Object.isArray(fields)){
			for(var i = 0;i<fields.length;i++){
				parameters[fields[i]] = $F(fields[i]);
			}
		} else {
			parameters = Object.extend(fields);
		}
		new Ajax.Request(Utils.getKumbiaURL(controller+"/"+action), {
			parameters: parameters,
			onSuccess: function(transport){
				try {
					var fields = transport.responseText.evalJSON();
					eval(callback + '(fields)');
				}
				catch(e){
					respuesta['type'] = 'exception';
					respuesta['msg'] = e;
					eval(callback + '(respuesta)');
				}
			},
			onFailure: function(error){
				var respuesta = new Object();
				respuesta['type'] = 'error';
				respuesta['msg'] = error.responseText;
				eval(callback + '(respuesta)');
			}
		});
	}
}
