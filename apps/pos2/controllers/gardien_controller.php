<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class GardienController extends ApplicationController
{

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isSetPostParam('role')) {
			$roleName = $controllerRequest->getParamPost('role', 'alpha');
			if ($controllerRequest->isSetPostParam('access')) {
				$allowedAccess = $controllerRequest->getParamPost('access');
				$roleAcl = array();
				foreach($allowedAccess as $access){
					$accessPart = explode('/', $access);
					if(!isset($roleAcl[$accessPart[0]])){
						$roleAcl[$accessPart[0]] = array();
					}
					$roleAcl[$accessPart[0]][$accessPart[1]] = true;
				}
				POSGardien::saveRoleAcl($roleName, $roleAcl, 'Public');
				Flash::success('Se actualizaron los permisos correctamente');
			}
			$this->setParamToView('acl', POSGardien::getRoleAcl($roleName));
		}
	}

}
