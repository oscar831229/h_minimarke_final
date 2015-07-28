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

/**
 * TestUnitController
 *
 * Controlador auxiliar de las pruebas unitarias del sistema
 *
 */
class TestUnitController extends ApplicationController {

	public function beforeFilter(){
		$this->setResponse('json');
		$mode = CoreConfig::getAppSetting('mode');
		if($mode=='test'){
			return true;
		}
		return false;
	}

	/**
	 * Borra la BD para hacer las pruebas de unidad
	 *
	 */
	public function prepareUnitTestingAction(){

		$this->Centros->deleteAll();
		$this->Almacenes->deleteAll();
		$this->Lineas->deleteAll();
		$this->Unidad->deleteAll();
		$this->Inve->deleteAll();
		$this->FormaPago->deleteAll();

		$this->Nits->deleteAll();
		$this->Movihead->deleteAll();
		$this->Movilin->deleteAll();

		/*$this->Movih1->deleteAll();
		$this->Criterio->deleteAll();*/

		return array('status' => 'OK');
	}

}