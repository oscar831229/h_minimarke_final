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

Core::importFromLibrary('Hfos/Tpc','TpcTests.php');
/**
 * TestsController
 *
 * Controlador de tests de TC
 */
class TestsController extends ApplicationController {
	public function indexAction(){
		$this->setResponse('json');
		return TpcTests::main();
	}
	public function limpiarBDAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		TpcTests::limpiarBD($transaction);
		$transaction->commit();
		return true;
	}
}
