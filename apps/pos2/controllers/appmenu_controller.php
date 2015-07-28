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

class AppmenuController extends ApplicationController
{

	public function beforeFilter()
	{
		if (POSGardien::hasPanic() == true) {
			Router::routeTo(array('controller' => 'panic'));
			return false;
		}
		parent::beforeFilter();
	}

	public function indexAction()
	{
		Session::unsetData('auth');
		Session::unsetData('role');
		$this->loadModel('Datos', 'DatosHotel');
	}

}
