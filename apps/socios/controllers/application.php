<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

Core::importFromLibrary('Hfos', 'Loader/Loader.php');

class ControllerBase {

	public function init()
	{
		Router::routeTo(array('controller' => 'index'));
	}

	public function initialize()
	{
		i18n::isUnicodeEnabled();
		LocaleMath::enableBcMath();
	}

	public static function getAppModel()
	{
		$datosclub = EntityManager::get('DatosClub')->findFirst();
		return $datosclub;
	}

	public function beforeFilter()
	{
		if (!Gardien::isAllowed(Router::getController(), Router::getAction(), null)) {
			Router::routeTo(array(
				'controller' => 'gardien',
				'action' => 'noAccess',
				0 => Router::getController(),
				1 => Router::getAction()
			));
			return false;
		}
		return true;
	}

	public function notFoundAction()
	{
		Router::routeTo(array(
			'controller' => 'index',
			'action' => 'index'
		));
	}
}
