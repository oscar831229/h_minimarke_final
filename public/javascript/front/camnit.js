
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

function actionEliminar(){
	if($("rut_viejo").value){
		new Modal.confirm({
			title: "Eliminar Cliente",
			message: "¿Seguro desea eliminar el número de documento "+$("rut_viejo").value+"?",
			onAccept: function(){
				document.fl.eliminar.value = 1;
				document.fl.submit();
			}
		});
	} else {
		new Modal.alert({
			title: "Eliminar Cliente",
			message: "Debe digitar el número de documento a eliminar",
			onAccept: function(){
				$('rut_viejo').activate();
				new Effect.Highlight("rut_viejo")
			}
		});
	}
}

function actionInactivate(){
	if($("rut_viejo").value){
		new Modal.confirm({
			title: "Eliminar Cliente",
			message: "¿Seguro desea inactivar el número de documento "+$("rut_viejo").value+"?",
			onAccept: function(){
				document.fl.inactivar.value = 1;
				document.fl.submit()
			}
		});
	} else {
		new Modal.alert({
			title: "Eliminar Cliente",
			message: "Debe digitar el número de documento a inactivar",
			onAccept: function(){
				$('rut_viejo').activate();
				new Effect.Highlight("rut_viejo")
			}
		});
	}
}