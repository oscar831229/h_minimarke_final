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

Core::importFromLibrary('Hfos/Socios','SociosMigration.php');
Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
/**
 * TestsController
 *
 * Controlador de tests de TC
 */
class MigrarController extends ApplicationController 
{

	public function indexAction()
	{
		$this->setResponse('json');
		try{
			$sociosMigration = new SociosMigration();
			$sociosMigration->main();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}

	public function saldosAction()
	{
		$this->setResponse('json');
		try
		{
			$sociosMigration = new SociosMigration();
			$sociosMigration->importarSaldos();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}

	public function pagosAction()
	{
		$this->setResponse('json');
		try
		{
			$sociosMigration = new SociosMigration();
			$sociosMigration->importarPagos();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}

	public function prestamosAction()
	{
		$this->setResponse('json');
		try
		{
			$sociosMigration = new SociosMigration();
			$sociosMigration->makePrestamosByExcel();
			return array('status'=>'OK');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}

	
	public function importSociosByFileAction() 
	{
		$this->setResponse('json');
		try
		{
			$sociosMigration = new SociosMigration();
			$sociosMigration->importSociosByFile();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}
	
	public function ajustesAction() 
	{
		$this->setResponse('json');
		try
		{
			$sociosMigration = new SociosMigration();
			$sociosMigration->ajustarSaldos();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}

	public function syncTecerosAction() 
	{
		$this->setResponse('json');
		try
		{
			$transaction = TransactionManager::getUserTransaction();	
			$sociosFactura = new SociosFactura();
			$sociosObj = EntityManager::get('Socios')->find();
			foreach ($sociosObj as $socios)
			{
				$sociosFactura->checkTercero($socios);
			}
			$transaction->commit();
			return array('status'=>'SUCCESS');
		}
		catch(Exception $e){
			return array('status'=>'FAILED', 'message'=>print_r($e,true));	
		}
	}
	
}
