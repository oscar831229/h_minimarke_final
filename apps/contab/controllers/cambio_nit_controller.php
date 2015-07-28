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
 * Cambio_NitController
 *
 * Controlador de cambio de un tercero por otro
 *
 */
class Cambio_NitController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		Tag::displayTo('fechaInicial', Date::getFirstDayOfYear());
		Tag::displayTo('fechaFinal', Date::getLastDayOfYear());

		$this->setParamToView('message', 'Unifique terceros ingresando el errado y el correcto');
	}

	public function cambiarAction(){
		$this->setResponse('json');

		$nitCorrecto = $this->getPostParam('nitCorrecto', 'terceros');
		$nitErrado = $this->getPostParam('nitErrado', 'terceros');

		try {

			$transaction = TransactionManager::getUserTransaction();

			$moviConditions = array();
			$saldosConditions = "";

			$moviConditions[] = "nit='$nitErrado'";

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');
			if($fechaInicial!=''&&$fechaFinal!=''){

				try {

					$fechaInicial = new Date($fechaInicial);
					$fechaFinal = new Date($fechaFinal);

					list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);

					$empresa = $this->Empresa->findFirst();
					if(Date::isEarlier($fechaInicial, $empresa->getFCierrec())){
						$transaction->rollback('El rango de fechas debe ser posterior al último cierre contable');
					}

					$fechaInicial->toFirstDayOfMonth();
					$fechaFinal->toLastDayOfMonth();

					$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
					$saldosConditions = "AND ano_mes>='{$fechaInicial->getPeriod()}' AND ano_mes<='{$fechaFinal->getPeriod()}'";

				}
				catch(DateException $e){
					$transaction->rollback($e->getMessage());
				}

			}

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');
			if($cuentaInicial!=''&&$cuentaFinal!=''){
				$moviConditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}

			$moviConditions = join(' AND ', $moviConditions);

			$this->Nits->setTransaction($transaction);
			$this->Movi->setTransaction($transaction);
			$this->Movihead->setTransaction($transaction);
			$this->Movi00->setTransaction($transaction);
			$this->Movi99->setTransaction($transaction);
			$this->Activos->setTransaction($transaction);
			$this->Cartera->setTransaction($transaction);
			$this->Saldosca->setTransaction($transaction);
			$this->Saldosn->setTransaction($transaction);

			$terceroCorrecto = $this->Nits->findFirst("nit='{$nitCorrecto}'");
			if($terceroCorrecto==false){
				$transaction->rollback('El tercero con documento "'.$nitCorrecto.'" no ha sido creado');
			}

			$terceroErrado = $this->Nits->findFirst("nit='{$nitErrado}'");
			if($terceroErrado==false){
				$transaction->rollback('El tercero con documento "'.$nitCorrecto.'" no ha sido creado');
			}


			$this->Movi->updateAll("nit='$nitCorrecto'", $moviConditions);
			$this->Movi00->updateAll("nit='$nitCorrecto'", $moviConditions);
			$this->Movi99->updateAll("nit='$nitCorrecto'", $moviConditions);

			$this->Movihead->updateAll("nit='$nitCorrecto'", "nit='$nitErrado'");

			foreach($this->Activos->find("responsable='$nitErrado'") as $activo){
				$activo->setResponsable($nitCorrecto);
				if($activo->save()==false){
					foreach($activo->getMessages() as $message){
						$transaction->rollback('Activos: '.$message->getMessage());
					}
				}
			}

			foreach($this->Cartera->find("nit='$nitErrado'") as $cartera){
				$conditions = "cuenta='{$cartera->getCuenta()}' AND nit='$nitCorrecto' AND tipo_doc='{$cartera->getTipoDoc()}' AND numero_doc='{$cartera->getNumeroDoc()}'";
				$carteraCorrecto = $this->Cartera->findFirst($conditions);
				if($carteraCorrecto==false){
					$carteraCorrecto = clone $cartera;
					$carteraCorrecto->setNit($nitCorrecto);
				} else {
					$carteraCorrecto->setValor($carteraCorrecto->getValor()+$cartera->getValor());
					$carteraCorrecto->setSaldo($carteraCorrecto->getSaldo()+$cartera->getSaldo());
				}
				if($carteraCorrecto->save()==false){
					foreach($carteraCorrecto->getMessages() as $message){
						$transaction->rollback('Cartera: '.$message->getMessage());
					}
				}
				if($cartera->delete()==false){
					foreach($cartera->getMessages() as $message){
						$transaction->rollback('Cartera: '.$message->getMessage());
					}
				}
				unset($conditions);
				unset($carteraCorrecto);
				unset($cartera);
			}

			foreach($this->Saldosca->find("nit='$nitErrado'".$saldosConditions) as $saldosca){
				$conditions = "cuenta='{$saldosca->getCuenta()}' AND nit='$nitCorrecto' AND tipo_doc='{$saldosca->getTipoDoc()}' AND numero_doc='{$saldosca->getNumeroDoc()}'";
				$saldoscaCorrecto = $this->Saldosca->findFirst($conditions);
				if($saldoscaCorrecto==false){
					$saldoscaCorrecto = clone $saldosca;
					$saldoscaCorrecto->setNit($nitCorrecto);
				} else {
					$saldoscaCorrecto->setDebe($saldoscaCorrecto->getDebe()+$saldosca->getDebe());
					$saldoscaCorrecto->setHaber($saldoscaCorrecto->getHaber()+$saldosca->getHaber());
					$saldoscaCorrecto->setSaldo($saldoscaCorrecto->getSaldo()+$saldosca->getSaldo());
				}
				if($saldoscaCorrecto->save()==false){
					foreach($saldoscaCorrecto->getMessages() as $message){
						$transaction->rollback('Saldosca: '.$message->getMessage());
					}
				}
				if($saldosca->delete()==false){
					foreach($saldosca->getMessages() as $message){
						$transaction->rollback('Saldosca: '.$message->getMessage());
					}
				}
				unset($conditions);
				unset($saldoscaCorrecto);
				unset($saldosca);
			}

			foreach($this->Saldosn->find("nit='$nitErrado'".$saldosConditions) as $saldosn){
				$conditions = "cuenta='{$saldosn->getCuenta()}' AND nit='$nitCorrecto' AND ano_mes='{$saldosn->getAnoMes()}'";
				$saldosnCorrecto = $this->Saldosn->findFirst($conditions);
				if($saldosnCorrecto==false){
					$saldosnCorrecto = clone $saldosn;
					$saldosnCorrecto->setNit($nitCorrecto);
				} else {
					$saldosnCorrecto->setDebe($saldosnCorrecto->getDebe()+$saldosn->getDebe());
					$saldosnCorrecto->setHaber($saldosnCorrecto->getHaber()+$saldosn->getHaber());
					$saldosnCorrecto->setSaldo($saldosnCorrecto->getSaldo()+$saldosn->getSaldo());
					$saldosnCorrecto->setBaseGrab($saldosnCorrecto->getBaseGrab()+$saldosn->getBaseGrab());
				}
				if($saldosnCorrecto->save()==false){
					foreach($saldosnCorrecto->getMessages() as $message){
						$transaction->rollback('Saldosn: '.$message->getMessage());
					}
				}
				if($saldosn->delete()==false){
					foreach($saldosn->getMessages() as $message){
						$transaction->rollback('Saldosn: '.$message->getMessage());
					}
				}
				unset($conditions);
				unset($saldosnCorrecto);
				unset($saldosn);
			}

			new EventLogger('SE CAMBIÓ EL TERCERO '.$nitErrado.'/'.$terceroErrado->getNombre().' POR '.$nitCorrecto.'/'.$terceroCorrecto->getNombre(), 'A');

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
			'message' => 'Se hizo el cambio de terceros correctamente'
		);
	}

}