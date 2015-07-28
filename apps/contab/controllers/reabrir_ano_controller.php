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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Reabrir_AnoController
 *
 * Reabrir año
 *
 */
class Reabrir_AnoController extends ApplicationController
{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('message', 'Haga click en "Reabrir Año" para volver al año anterior');
		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		if($fechaCierre->getMonth()==12){
			if($empresa1->getAnoc()==$fechaCierre->getYear()){
				$nuevoCierre = clone $fechaCierre;
				$nuevoCierre->addDays(1);
				Tag::displayTo('fechaCierre', (string) Date::getLastDayOfMonth($nuevoCierre->getMonth(), $nuevoCierre->getYear()));
				$this->setParamToView('fechaCierre', $fechaCierre);
				$this->setParamToView('anoCierre', $empresa1->getAnoc());
			} else {
				$this->setParamToView('fechaCierre', $fechaCierre);
				return $this->routeToAction('noDiciembre');
			}
		} else {
			$this->setParamToView('fechaCierre', $fechaCierre);
			return $this->routeToAction('noDiciembre');
		}
	}

	public function reabrirAction(){

		$this->setResponse('json');

		try {

			set_time_limit(0);
			$transaction = TransactionManager::getUserTransaction();

			$empresa = $this->Empresa->findFirst(array('for_update' => true));
			$empresa1 = $this->Empresa1->findFirst(array('for_update' => true));

			$fechaCierre = $empresa->getFCierrec();

			if($fechaCierre->getMonth()!=12){
				$transaction->rollback('El cierre contable actual debe estar a Diciembre');
			}

			if($fechaCierre->getYear()!=$empresa1->getAnoc()){
				$transaction->rollback('Debe reabrir los cierres contables del año actual hasta Diciembre para reabrir el año');
			}

			/*if($this->Movi->count("fecha>'$fechaCierre'")>0){
				$transaction->rollback('No se puede abrir el año porque ya hay movimiento en el año '.($fechaCierre->getYear()+1));
			}*/

			$empresa1->setAnoc($empresa1->getAnoc()-1);
			if($empresa1->save()==false){
				foreach($empresa1->getMessages() as $message){
					$transaction->rollback('Empresa1: '.$message->getMessage());
				}
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK',
			'message' => 'Se reabrió el cierre anual correctamente'
		);

	}

	public function noDiciembreAction(){

	}

}