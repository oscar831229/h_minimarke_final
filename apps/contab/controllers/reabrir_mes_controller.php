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
 * Reabrir_MesController
 *
 * Controlador de presupuestos
 *
 */
class Reabrir_MesController extends ApplicationController
{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$this->setParamToView('message', 'Indique la fecha del nuevo cierre contable');
		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();

		$fechaCierre = $empresa->getFCierrec();
		$anteriorCierre = clone $fechaCierre;
		$anteriorCierre->diffMonths(1);
		$anteriorCierre->toLastDayOfMonth();
		$this->setParamToView('anteriorCierre', $anteriorCierre);

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());
	}

	public function reabrirAction(){

		$this->setResponse('json');

		try {

			set_time_limit(0);
			$transaction = TransactionManager::getUserTransaction();

			$this->Empresa->setTransaction($transaction);
			$empresa = $this->Empresa->findFirst();
			$ultimoCierre = $empresa->getFCierrec();

			$empresa1 = $this->Empresa1->findFirst();

			$fechaCierre = clone $ultimoCierre;
			$fechaCierre->diffMonths(1);
			$fechaCierre->toLastDayOfMonth();

			if(Date::isLater($fechaCierre, $ultimoCierre)){
				$transaction->rollback('El periodo no ha sido cerrado');
			} else {
				if($empresa1->getAnoc()==$ultimoCierre->getYear()){
					$transaction->rollback('El año a reabrir está cerrado, Utilice la opción reabrir año');
				}
			}

			$periodoCierre = $ultimoCierre->getPeriod();
			$this->Saldosn->setTransaction($transaction);
			foreach($this->Saldosn->find("ano_mes='$periodoCierre'") as $saldon){
				if($saldon->delete()==false){
					foreach($saldon->getMessages() as $message){
						$transaction->rollback('Saldon: '.$message->getMessage());
					}
				}
				unset($saldon);
			}

			$this->Saldosc->setTransaction($transaction);
			foreach($this->Saldosc->find("ano_mes='$periodoCierre'") as $saldoc){
				if($saldoc->delete()==false){
					foreach($saldoc->getMessages() as $message){
						$transaction->rollback('Saldoc: '.$message->getMessage());
					}
				}
				unset($saldoc);
			}

			$this->Saldosn->setTransaction($transaction);
			foreach($this->Saldosn->find("ano_mes='$periodoCierre'") as $saldon){
				if($saldon->delete()==false){
					foreach($saldon->getMessages() as $message){
						$transaction->rollback('Saldon: '.$message->getMessage());
					}
				}
				unset($saldon);
			}

			$this->Saldosp->setTransaction($transaction);
			foreach($this->Saldosp->find("ano_mes='$periodoCierre'") as $saldop){
				$saldop->setDebe(0);
				$saldop->setHaber(0);
				$saldop->setSaldo(0);
				if($saldop->save()==false){
					foreach($saldop->getMessages() as $message){
						$transaction->rollback('Saldop: '.$message->getMessage());
					}
				}
				unset($saldop);
			}

			$this->Saldosca->setTransaction($transaction);
			foreach($this->Saldosca->find("ano_mes='$periodoCierre'") as $saldoca){
				if($saldoca->delete()==false){
					foreach($saldoca->getMessages() as $message){
						$transaction->rollback('Saldoca: '.$message->getMessage());
					}
				}
				unset($saldoca);
			}

			$empresa->setFCierrec((string)$fechaCierre);
			if($empresa->save()==false){
				foreach($empresa->getMessages() as $message){
					$transaction->rollback('Empresa: '.$message->getMessage());
				}
			}

			$anteriorCierre = clone $fechaCierre;
			$anteriorCierre->diffMonths(1);
			$anteriorCierre->toLastDayOfMonth();

			//Create movi niif
			$auraNiif = new AuraNiif();
			$auraNiif->setTransaction($transaction);
			$auraNiif->borrarSaldosDelMes($ultimoCierre);

			$transaction->commit();
			return array(
				'status' => 'OK',
				'anteriorCierre' => $anteriorCierre->getLocaleDate('long'),
				'cierreActual' => $fechaCierre->getLocaleDate('short')
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