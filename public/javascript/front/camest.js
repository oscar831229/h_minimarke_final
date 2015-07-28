
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

function changeAll(){
	new Modal.confirm({
		title: 'Cambiar estado',
		message: '¿Seguro desea cambiar el estado a todas las habitaciones asignadas?',
		onAccept: function(){
			window.location = "?action=camest&option=4&all=1"
		}
	});
}