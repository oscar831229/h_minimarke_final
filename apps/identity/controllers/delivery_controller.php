<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	IdentiyManager
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * DeliveryController
 *
 * Controlador de envío de correos
 *
 */
class DeliveryController extends WebServiceController {

	/**
	 * Realiza el envío de correo electrónico interno
	 *
	 * @param	array $params
	 * @return	boolean
	 */
	public function sendAction($params){
		try {
			$message = new HfosMessage();
			$message->setFrom($params['from']);
			$message->setToList($params['to']);
			$message->setSubject($params['subject']);
			$message->setBody($params['body']);
			return $message->send();
		}
		catch(HfosMailException $e){
			return false;
		}
	}

}