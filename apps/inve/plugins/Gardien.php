<?php

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

class GardienPlugin extends ControllerPlugin {

	private function _noAccessBecauseError($errorMessage){
		$controllerName = Router::getController();
		//if($controllerName!='gardien'){
			Router::routeTo(array(
				'controller' => 'gardien',
				'action' => 'noAccess',
				'id' => $errorMessage
			));
		//}
	}

	public function beforeExexcuteRoute($controller){
		$controllerName = Router::getController();
		//if($controllerName!='gardien'){
			if(!Gardien::isAllowed($controllerName, $actionName)){
				Router::routeTo(array(
					'controller' => 'gardien',
					'action' => 'noAccess',
					'0' => $controllerName,
					'1' => $actionName
				));
			}
		//}
	}

}