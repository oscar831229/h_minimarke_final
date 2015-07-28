
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
 * Bindings a los tipos de mensajes
 */
var HyperMessagesTypes = {

	M_NOTICE: HfosMessages.M_NOTICE,
	M_ERROR: HfosMessages.M_ERROR,
	M_SUCCESS: HfosMessages.M_SUCCESS

};

/**
 * Permite mostrar mensajes en un contenedor HyperForm
 */
var HyperMessages = Class.create(HfosMessages, {

});
