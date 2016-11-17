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

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
        
/**
 * Reabrir_PeriodoController
 *
 * Controlador de reabrir cierres de periodo
 *
 */
class Reabrir_PeriodoController extends ApplicationController {

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		try {

			$this->setParamToView('message', 'Reabre periodos cerrados');

			$periodoActual = SociosCore::getCurrentPeriodo();
			$periodoAbrir = SociosCore::subPeriodo($periodoActual, 1);
			
			$this->setParamToView('periodoActual', $periodoActual);
			$this->setParamToView('periodoAbrir', $periodoAbrir);
		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}	
		
	}

	public function reabrirAction()
	{

		$this->setResponse('json');

		try {

			set_time_limit(0);
			Core::importFromLibrary('Hfos/Socios','SociosCore.php');

			$allMessages = array();
			$transaction = TransactionManager::getUserTransaction();

			$periodoActual = SociosCore::getCurrentPeriodo();
			$periodoAbrir = SociosCore::subPeriodo($periodoActual, 1);
			$yearAbrir = substr($periodoAbrir, 0, 4);
			$monthAbrir = substr($periodoAbrir, 4, 2);

			//throw new SociosException("periodoActual: $periodoActual, periodoAbrir: $periodoAbrir", 1);

			//Cierra los periodos mayor a el abierto
			$periodoObj = EntityManager::get('Periodo')->setTransaction($transaction)->find(array('conditions'=>"periodo >= '$periodoActual'"));
			foreach ($periodoObj as $periodo) {
				$periodo->setTransaction($transaction);
				$periodo->setCierre('S');
				$periodo->setFacturado('N');
				if ($periodo->save() == false) {
					foreach ($periodo->getMessages() as $message) {
						$transaction->rollback('Periodo Cierrar nuevos periodos: ' . $message->getMessage());
					}
				}
			}
			
			//Abre periodo actual
			$periodoNew = EntityManager::get('Periodo')->setTransaction($transaction)->findFirst(array('conditions'=>"periodo = '$periodoAbrir'"));
			if ($periodoNew != false) {
				$periodoNew->setTransaction($transaction);
				$periodoNew->setCierre('N');
				$periodoNew->setFacturado('N');
				if ($periodoNew->save() == false) {
					foreach ($periodoNew->getMessages() as $message) {
						$transaction->rollback('periodoNew: ' . $message->getMessage());
					}
				}
			}

			$ultimoCierre = new Date("$yearAbrir-$monthAbrir-01");
			$fechaCierre = $ultimoCierre;
			$fechaCierre->toLastDayOfMonth();

			//throw new Exception($fechaCierre->getDate());
			
			$datosClub = EntityManager::get("DatosClub")->findFirst();
			$datosClub->setFCierre($fechaCierre);
			if ($datosClub->save() == false) {
				foreach ($datosClub->getMessages() as $message) {
					$transaction->rollback('datosClub: ' . $message->getMessage());
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
				'periodoActual' => $periodoActual,
				'periodoAbrir' => $periodoAbrir
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