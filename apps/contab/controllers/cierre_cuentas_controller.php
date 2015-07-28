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
 * Cierre_CuentasController
 *
 * Realiza el cierre de cuentas de iva y retención
 *
 */
class Cierre_CuentasController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$this->setParamToView('message', 'Indique el tipo de cuenta y la fecha de cierre');
		$empresa1 = $this->Empresa1->findFirst();

		$otros = $empresa1->getOtros();
		try {
			$fechaRetencion = Date::fromFormat(substr($otros, 4, 10), 'mm/dd/yyyy');
			$fechaIVA = Date::fromFormat(substr($otros, 14, 10), 'mm/dd/yyyy');
		}
		catch(DateException $e){
			$fechaRetencion = new Date();
			$fechaIVA = new Date();
		}

		$empresa = $this->Empresa->findFirst();
		$cierreContable = $empresa->getFCierrec();

		$fechaCierre = clone $fechaRetencion;
		$fechaCierre->addDays(1);
		Tag::displayTo('fechaCierre', (string)$fechaCierre);

		$this->setParamToView('cierreContable', $cierreContable);
		$this->setParamToView('cierreRetencion', $fechaRetencion);
		$this->setParamToView('cierreIVA', $fechaIVA);
	}

	public function cierreAction(){
		$this->setResponse('json');

		try {

			set_time_limit(0);

			$transaction = TransactionManager::getUserTransaction();
			$transaction->setRollbackOnAbort(true);

			$empresa = $this->Empresa->findFirst();
			$empresa1 = $this->Empresa1->findFirst();

			$fechaCierre = $this->getPostParam('fechaCierre', 'date');
			$fechaCierre = new Date($fechaCierre);
			$fechaCierre->toLastDayOfMonth();

			$tipo = $this->getPostParam('tipo', 'onechar');

			if(Date::isEarlier($fechaCierre, $empresa->getFCierrec())){
				$transaction->rollback('La fecha no puede ser menor a la fecha del cierre contable');
			} else {
				if(Date::isLater($fechaCierre, Date::getCurrentDate())){
					$transaction->rollback('La fecha no puede ser mayor a hoy');
				} else {
					if(Date::isLater($fechaCierre, $empresa->getFCierrei())&&$empresa->getContabiliza()=='S'){
						$transaction->rollback('La fecha no puede ser mayor al cierre de inventarios');
					}
				}
			}

			$otros = '';
			if($tipo=='R'){
				$otros = Utils::replaceText($empresa1->getOtros(), 4, 10, $fechaCierre->getUsingFormat('MM/DD/YYYY'));
			} else {
				if($tipo=='I'){
					$otros = Utils::replaceText($empresa1->getOtros(), 14, 10, $fechaCierre->getUsingFormat('MM/DD/YYYY'));
				}
			}

			$empresa1 = $this->Empresa1->findFirst();
			$empresa1->setOtros($otros);
			if($empresa1->save()==false){
				foreach($empresa1->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}

			return array(
				'status' => 'OK'
			);

		}
		catch(DbLockAdquisitionException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}