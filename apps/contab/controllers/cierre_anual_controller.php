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
 * Comprobante_CierreController
 *
 * Realiza el cierre anual
 *
 */
class Cierre_AnualController extends ApplicationController {

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
		$this->setParamToView('message', 'Indique los parámetros del cierre anual y haga click en "Cerrar"');
		$this->setParamToView('nombreComprobante', 'CIERRE ANUAL');

		$cuenta = $this->Cuentas->findFirst(4);
		Tag::displayTo('cuentaInicial', $cuenta->getCuenta());

		$cuenta = $this->Cuentas->findFirst("cuenta LIKE '6%' AND es_auxiliar='S'", 'order: cuenta DESC');
		Tag::displayTo('cuentaFinal', $cuenta->getCuenta());

	}

	public function cerrarAction()
	{

		$this->setResponse('json');

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);

		try
		{

			set_time_limit(0);
			$transaction = TransactionManager::getUserTransaction();

			$empresa = $this->Empresa->findFirst(array('for_update' => true));
			$empresa1 = $this->Empresa1->findFirst(array('for_update' => true));

			$fechaCierre = $empresa->getFCierrec();
			$periodo = $fechaCierre->getPeriod();

			if ($fechaCierre->getMonth()!=12) {
				$transaction->rollback('El cierre contable actual debe estar a Diciembre');
			}

			if ($fechaCierre->getYear()!=($empresa1->getAnoc()+1)) {
				$transaction->rollback('Debe realizar los cierres contables del año actual para cerrar el año');
			}

			$comprobCierre = Settings::get('comprob_cierre');
			if ($comprobCierre=='') {
				$transaction->rollback('No se ha configurado el comprobante de cierre');
			} else {
				$comprob = $this->Comprob->findFirst("codigo='$comprobCierre'");
				if ($comprob==false) {
					$transaction->rollback('El comprobante de cierre configurado no existe');
				}
			}

			if ($this->Movi->count("comprob='$comprobCierre' AND fecha='$fechaCierre'")==0) {
				$transaction->rollback('Debe realizar primero el comprobante de cierre anual para el año '.$fechaCierre->getYear());
			}

			$saldo = 0;
			$debe = 0;
			$haber = 0;
			$base = 0;
			$this->Saldosc->setTransaction($transaction);
			$this->Saldosn->setTransaction($transaction);
			$this->Saldosp->setTransaction($transaction);
			foreach ($this->Comcier->find(array('order' => 'cuentai')) as $comcier)
			{
				$cuenta = BackCacher::getCuenta($comcier->getCuentai());
				if ($cuenta!=false) {

					$codigoCuenta = $comcier->getCuentai();

					$saldosc = $this->Saldosc->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					if ($saldosc!=false) {
						$debe+=$saldosc->getDebe();
						$haber+=$saldosc->getHaber();
						$saldo+=$saldosc->getSaldo();
						if($saldosc->delete()==false){
							foreach ($saldosc->getMessages() as $message)
							{
								$transaction->rollback('Saldosc: '.$message->getMessage());
							}
						}
					}
					unset($saldosc);

					$saldosn = $this->Saldosn->find("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					foreach ($saldosn as $saldon)
					{
						$base+=$saldon->getBaseGrab();
						if ($saldon->delete()==false) {
							foreach ($saldon->getMessages() as $message)
							{
								$transaction->rollback('Saldosn: '.$message->getMessage());
							}
						}
						unset($saldon);
					}
					unset($saldosn);

					$saldosp = $this->Saldosp->find("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					foreach ($saldosp as $saldop)
					{
						if ($saldop->delete()==false) {
							foreach ($saldop->getMessages() as $message)
							{
								$transaction->rollback('Saldosp: '.$message->getMessage());
							}
						}
						unset($saldop);
					}
					unset($saldosp);

					$cuenta = BackCacher::getCuenta($comcier->getCuentaf());
					if ($cuenta==false) {
						$transaction->rollback('No existe la cuenta final de cierre de '.$comcier->getCuentai());
					} else {
						$existeSaldosc = false;
						$codigoCuenta = $comcier->getCuentaf();
						$saldosc = $this->Saldosc->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodo'");
						if ($saldosc==false) {
							$saldosc = new Saldosc();
							$saldosc->setTransaction($transaction);
							$saldosc->setAnoMes($periodo);
							$saldosc->setCuenta($codigoCuenta);
						} else {
							$existeSaldosc = true;
						}
						$saldosc->setDebe($debe+$saldosc->getDebe());
						$saldosc->setHaber($haber+$saldosc->getHaber());
						$saldosc->setSaldo($saldo+$saldosc->getSaldo());
						if ($saldosc->save()==false) {
							foreach ($saldosc->getMessages() as $message)
							{
								$transaction->rollback('Saldosc: '.$message->getMessage());
							}
						}
						if ($cuenta->getPideCentro()=='S') {
							$saldosp = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodo' AND centro_costo='{$empresa->getCentroCosto()}'");
							if ($saldosp==false) {
								$saldosp = new Saldosp();
								$saldosp->setTransaction($transaction);
								$saldosp->setAnoMes($periodo);
								$saldosp->setCuenta($codigoCuenta);
								$saldosp->setCentroCosto($empresa->getCentroCosto());
							}
							$saldosp->setDebe($debe+$saldosp->getDebe());
							$saldosp->setHaber($haber+$saldosp->getHaber());
							$saldosp->setSaldo($saldo+$saldosp->getSaldo());
							if ($saldosp->save()==false) {
								foreach ($saldosp->getMessages() as $message)
								{
									$transaction->rollback('Saldosp: '.$message->getMessage());
								}
							}
							$saldosp = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND ano_mes=0 AND centro_costo='{$empresa->getCentroCosto()}'");
							if ($saldosp==false) {
								$saldosp = new Saldosp();
								$saldosp->setTransaction($transaction);
								$saldosp->setAnoMes(0);
								$saldosp->setCuenta($codigoCuenta);
								$saldosp->setCentroCosto($empresa->getCentroCosto());
								$saldosp->setDebe($debe);
								$saldosp->setHaber($haber);
								$saldosp->setSaldo($saldo);
								if ($saldosp->save()==false) {
									foreach ($saldosp->getMessages() as $message)
									{
										$transaction->rollback('Saldosp: '.$message->getMessage());
									}
								}
							}
							unset($saldosp);
						}
						if ($existeSaldosc==true) {
							$debe-=$saldosc->getDebe();
							$haber-=$saldosc->getHaber();
							$saldo-=$saldosc->getSaldo();
						}

						$saldosc = $this->Saldosc->findFirst("cuenta='$codigoCuenta' AND ano_mes=0");
						if ($saldosc==false) {
							$saldosc = new Saldosc();
							$saldosc->setTransaction($transaction);
							$saldosc->setAnoMes(0);
							$saldosc->setCuenta($codigoCuenta);
							$saldosc->setDebe($debe+$saldosc->getDebe());
							$saldosc->setHaber($haber+$saldosc->getHaber());
							$saldosc->setSaldo($saldo+$saldosc->getSaldo());
							if ($saldosc->save()==false) {
								foreach ($saldosc->getMessages() as $message)
								{
									$transaction->rollback('Saldosc: '.$message->getMessage());
								}
							}
						}
						unset($saldosc);

						if ($cuenta->getPideNit()=='S') {
							$saldosn = $this->Saldosn->findFirst("cuenta='$codigoCuenta' AND ano_mes=0 AND nit='{$comcier->getNit()}'");
							if ($saldosn==false) {
								$saldosn = new Saldosn();
								$saldosn->setTransaction($transaction);
								$saldosn->setCuenta($codigoCuenta);
								$saldosn->setNit($comcier->getNit());
								$saldosn->setAnoMes($periodo);
							}
							$saldosn->setDebe($debe+$saldosn->getDebe());
							$saldosn->setHaber($haber+$saldosn->getHaber());
							$saldosn->setSaldo($saldo+$saldosn->getSaldo());

							if ($saldosn->save()==false) {
								foreach ($saldosn->getMessages() as $message)
								{
									$transaction->rollback('Saldosc: '.$message->getMessage());
								}
							}
							unset($saldosn);
						}
					}

					$saldo = 0;
					$debe = 0;
					$haber = 0;
					$base = 0;

					unset($cuenta);
					unset($codigoCuenta);
				}
			}

			//Eliminar saldosn de cuentas de PyG
			$this->Saldosp->deleteAll("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes=0 AND centro_costo='{$empresa->getCentroCosto()}'");

			//Eliminar saldosn de cuentas de PyG
			foreach ($this->Saldosn->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'") as $saldon)
			{
				if ($saldon->delete()==false) {
					foreach ($saldon->getMessages() as $message)
					{
						$transaction->rollback('Saldosn: '.$message->getMessage());
					}
				}
				unset($saldon);
			}

			//Eliminar saldosc de cuentas de PyG
			foreach ($this->Saldosc->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'") as $saldoc)
			{
				if ($saldoc->delete()==false) {
					foreach ($saldoc->getMessages() as $message)
					{
						$transaction->rollback('Saldosc: '.$message->getMessage());
					}
				}
				unset($saldoc);
			}


			//Eliminar saldosp de cuentas de PyG
			foreach ($this->Saldosp->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'") as $saldop)
			{
				$saldop->setDebe(0);
				$saldop->setHaber(0);
				$saldop->setSaldo(0);
				if ($saldop->save()==false) {
					foreach ($saldop->getMessages() as $message)
					{
						$transaction->rollback('Saldosp: '.$message->getMessage());
					}
				}
				unset($saldop);
			}

			$empresa1->setAnoc($empresa1->getAnoc()+1);
			if ($empresa1->save()==false) {
				foreach ($empresa1->getMessages() as $message)
				{
					$transaction->rollback('Empresa1: '.$message->getMessage());
				}
			}

			$transaction->commit();

			return array(
				'status' => 'OK',
				'message' => 'Se realizó el cierre anual correctamente'
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