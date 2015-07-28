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

Core::importFromLibrary('Hfos/Socios','SociosCore.php');
Core::importFromLibrary('Hfos/Socios','SociosTest.php');
/**
 * TestsController
 *
 * Controlador de tests de Socios
 */
class TestsController extends ApplicationController {
	
	public function indexAction(){
		$this->setResponse('json');
		return SociosTest::main();
	}
	
	public function limpiarBDAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		TpcTests::limpiarBD($transaction);
		$transaction->commit();
		return true;
	}
	
	public function checkTercerosAction(){
		$this->setResponse('json');
		try
		{
			$transaction = TransactionManager::getUserTransaction();
			$sociosFactura = new SociosFactura(); 
			foreach ($this->Socios->find() as $socios)
			{
				print "<br>".$socios->getIdentificacion();
				$sociosFactura->checkTercero($socios);
			}
			$transaction->commit();
			return array(
				'status' => 'OK',
				'message' => 'Se copio los socios a nits'
			);
		}
		catch(SociosException $e)
		{
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(Exception $e)
		{
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
