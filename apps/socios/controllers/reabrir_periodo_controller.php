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
 * Reabrir_PeriodoController
 *
 * Controlador de reabrir cierres de periodo
 *
 */
class Reabrir_PeriodoController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		try 
		{
			$this->setParamToView('message', 'Reabre periodos cerrados');

			$datosClub = $this->DatosClub->findFirst();
			$fechaCierre = $datosClub->getFCierre();

			if ($fechaCierre == '0000-00-00') {
				$fechaCierre = SociosCore::getCurrentPeriodo();
			}

			$anteriorCierre = clone $fechaCierre;
			$anteriorCierre->diffMonths(1);
			$anteriorCierre->toLastDayOfMonth();
			$this->setParamToView('anteriorCierre', $anteriorCierre);

			$this->setParamToView('fechaCierre', $fechaCierre);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}	
		
	}

	public function reabrirAction(){

		$this->setResponse('json');

		try {

			set_time_limit(0);
			Core::importFromLibrary('Hfos/Socios','SociosCore.php');

			$allMessages = array();
			$transaction = TransactionManager::getUserTransaction();

			$datosClub = $this->DatosClub->findFirst();
			$ultimoCierre = $datosClub->getFCierre();

			$fechaCierre = clone $ultimoCierre;
			$fechaCierre->diffMonths(1);
			$fechaCierre->toLastDayOfMonth();

			$periodoCierre = $ultimoCierre->getPeriod();

			$currentPeriod = SociosCore::getCurrentPeriodo();

			//Cierra los periodos mayor a el abierto
			$periodoObj = EntityManager::get('Periodo')->setTransaction($transaction)->find(array('conditions'=>"periodo >= '$currentPeriod'"));
			foreach ($periodoObj as $periodo) 
			{
				$periodo->setTransaction($transaction);
				$periodo->setCierre('S');
				$periodo->setFacturado('N');
				if ($periodo->save()==false) {
					foreach ($periodo->getMessages() as $message)
					{
						$transaction->rollback('Periodo Cierrar nuevos periodos: '.$message->getMessage());
					}
				}
			}
			
			$nuevoPeriodo = SociosCore::subPeriodo($currentPeriod,1);
			//throw new Exception($nuevoPeriodo);

			//Abre periodo actual
			$periodoNew = EntityManager::get('Periodo')->setTransaction($transaction)->findFirst(array('conditions'=>"periodo = '$nuevoPeriodo'"));
			if ($periodoNew!=false) {
				$periodoNew->setTransaction($transaction);
				$periodoNew->setCierre('N');
				$periodoNew->setFacturado('N');
				if ($periodoNew->save()==false) {
					foreach ($periodoNew->getMessages() as $message)
					{
						$transaction->rollback('periodoNew: '.$message->getMessage());
					}
				}
			}

			$anteriorCierre = $ultimoCierre;
			$anteriorCierre->diffMonths(1);
			$anteriorCierre->toLastDayOfMonth();

			//throw new Exception($anteriorCierre->getDate());
			
			$datosClub->setFCierre($anteriorCierre);
			if ($datosClub->save()==false) {
				foreach ($datosClub->getMessages() as $message)
				{
					$transaction->rollback('datosClub: '.$message->getMessage());
				}
			}

			#Suspendemos socios
			$autosupender = Settings::get("autosuspender_usar", 'SO');
			if ($autosupender=='S') {
				$sociosFactura = new SociosFactura(); 
				$sociosFactura->checkAutoActivar();
			}

			$transaction->commit();
			return array(
				'status' => 'OK',
				'anteriorCierre' => $anteriorCierre->getLocaleDate('long'),
				'cierreActual' => $fechaCierre->getLocaleDate('short')
			);

		}
		catch(SociosException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

}