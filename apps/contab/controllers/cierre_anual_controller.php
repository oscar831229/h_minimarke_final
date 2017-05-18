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

			if ($fechaCierre->getMonth() != 12) {
				$transaction->rollback('El cierre contable actual debe estar a Diciembre');
			}

			if ($fechaCierre->getYear() != ($empresa1->getAnoc() + 1)) {
				$transaction->rollback('Debe realizar los cierres contables del año actual para cerrar el año');
			}

			$comprobCierre = Settings::get('comprob_cierre');
			if ($comprobCierre == '') {
				$transaction->rollback('No se ha configurado el comprobante de cierre');
			} else {
				$comprob = $this->Comprob->findFirst("codigo='$comprobCierre'");
				if ($comprob == false) {
					$transaction->rollback('El comprobante de cierre "' + $comprobCierre + '" configurado no existe');
				}
			}

			$periodoIniNiif = Settings::get('period_saldos_ini_niif');
			if ($periodoIniNiif < 199001) {
				$transaction->rollback('No se ha configurado el periodo de los saldos iniciales NIIF');
			}

			$comprobCierreNiif = Settings::get('comprob_cierre_niif');
			if ($comprobCierreNiif == '') {
				$transaction->rollback('No se ha configurado el comprobante de cierre NIIF');
			} else {
				$comprobNiif = $this->Comprob->findFirst("codigo='$comprobCierreNiif'");
				if ($comprobNiif == false) {
					$transaction->rollback('El comprobante de cierre NIIF "' + $comprobCierreNiif + '" configurado no existe');
				}
			}

			if ($this->Movi->count("comprob='$comprobCierre' AND fecha='$fechaCierre'") == 0) {
				$transaction->rollback('Debe realizar primero el comprobante de cierre anual para el año ' . $fechaCierre->getYear());
			}

			$periodCierreDate = new Date($fechaCierre);
			$usarCierreNiif = ($periodoIniNiif < $periodCierreDate->getPeriod());

			if ($usarCierreNiif) {
				if ($this->MoviNiif->count("comprob='$comprobCierreNiif' AND fecha='$fechaCierre'") == 0) {
					$transaction->rollback('Debe realizar primero el comprobante de cierre anual niif para el año ' . $fechaCierre->getYear());
				}
			}

			$saldo = 0;
			$debe  = 0;
			$haber = 0;
			$base  = 0;

			$saldoNiif = 0;
			$debeNiif  = 0;
			$haberNiif = 0;
			$baseNiif  = 0;

			$this->Saldosc->setTransaction($transaction);
			$this->Saldosn->setTransaction($transaction);
			$this->Saldosp->setTransaction($transaction);

			if ($usarCierreNiif) {
				$this->SaldoscNiif->setTransaction($transaction);
				$this->SaldosnNiif->setTransaction($transaction);
				//$this->SaldospNiif->setTransaction($transaction);
			}

			foreach ($this->Comcier->find(array('order' => 'cuentai')) as $comcier) {
				$cuenta = BackCacher::getCuenta($comcier->getCuentai());
				if ($cuenta != false) {

					$codigoCuenta = $comcier->getCuentai();
					$codigoCuentaNiif = $cuenta->getCuentaNiif();

					$saldosc = $this->Saldosc->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					if ($saldosc != false) {
						$debe  += $saldosc->getDebe();
						$haber += $saldosc->getHaber();
						$saldo += $saldosc->getSaldo();
						if($saldosc->delete() == false){
							foreach ($saldosc->getMessages() as $message) {
								$transaction->rollback('Saldosc: ' . $message->getMessage());
							}
						}
					}
					unset($saldosc);

					if ($usarCierreNiif) {
						$saldoscNiif = $this->SaldoscNiif->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuentaNiif'");
						if ($saldoscNiif != false) {
							$debeNiif  += $saldoscNiif->getDebe();
							$haberNiif += $saldoscNiif->getHaber();
							$saldoNiif += $saldoscNiif->getSaldo();
							if($saldoscNiif->delete() == false){
								foreach ($saldoscNiif->getMessages() as $message) {
									$transaction->rollback('SaldoscNiif: ' . $message->getMessage());
								}
							}
						}
						unset($saldoscNiif);
					}

					$saldosn = $this->Saldosn->find("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					foreach ($saldosn as $saldon) {
						$base += $saldon->getBaseGrab();
						if ($saldon->delete() == false) {
							foreach ($saldon->getMessages() as $message) {
								$transaction->rollback('Saldosn: ' . $message->getMessage());
							}
						}
						unset($saldon);
					}
					unset($saldosn);

					if ($usarCierreNiif) {
						$saldosnNiifs = $this->SaldosnNiif->find("ano_mes='$periodo' AND cuenta='$codigoCuentaNiif'");
						foreach ($saldosnNiifs as $saldon) {
							$baseNiif += $saldon->getBaseGrab();
							if ($saldon->delete() == false) {
								foreach ($saldon->getMessages() as $message) {
									$transaction->rollback('SaldosnNiif: ' . $message->getMessage());
								}
							}
							unset($saldon);
						}
						unset($saldosnNiifs);
					}

					$saldosp = $this->Saldosp->find("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					foreach ($saldosp as $saldop) {
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
					if ($cuenta == false) {
						$transaction->rollback('No existe la cuenta final de cierre de '.$comcier->getCuentai());
					} else {
						$existeSaldosc = false;
						$existeSaldoscNiif = false;

						$codigoCuenta  = $comcier->getCuentaf();
						$codigoCuentaNiif  = $cuenta->getCuentaNiif();

						$saldosc = $this->Saldosc->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodo'");
						if ($saldosc == false) {
							$saldosc = new Saldosc();
							$saldosc->setTransaction($transaction);
							$saldosc->setAnoMes($periodo);
							$saldosc->setCuenta($codigoCuenta);
						} else {
							$existeSaldosc = true;
						}

						$saldosc->setDebe($debe + $saldosc->getDebe());
						$saldosc->setHaber($haber + $saldosc->getHaber());
						$saldosc->setSaldo($saldo + $saldosc->getSaldo());

						if ($saldosc->save() == false) {
							foreach ($saldosc->getMessages() as $message) {
								$transaction->rollback('Saldosc: ' . $message->getMessage());
							}
						}

						if ($usarCierreNiif) {
							$saldoscNiif = $this->SaldoscNiif->findFirst("cuenta='$codigoCuentaNiif' AND ano_mes='$periodo'");
							if ($saldoscNiif == false) {
								$saldoscNiif = new SaldoscNiif();
								$saldoscNiif->setTransaction($transaction);
								$saldoscNiif->setAnoMes($periodo);
								$saldoscNiif->setCuenta($codigoCuentaNiif);
								$saldoscNiif->setDepre(0);
							} else {
								$existeSaldoscNiif = true;
							}

							$saldoscNiif->setDebe($debeNiif + $saldoscNiif->getDebe());
							$saldoscNiif->setHaber($haberNiif + $saldoscNiif->getHaber());
							$saldoscNiif->setSaldo($saldoNiif + $saldoscNiif->getSaldo());

							if ($saldoscNiif->save() == false) {
								foreach ($saldoscNiif->getMessages() as $message) {
									$transaction->rollback('SaldoscNiif: ' . $message->getMessage());
								}
							}
						}


						if ($cuenta->getPideCentro() == 'S') {
							$saldosp = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodo' AND centro_costo='{$empresa->getCentroCosto()}'");

							if ($saldosp == false) {
								$saldosp = new Saldosp();
								$saldosp->setTransaction($transaction);
								$saldosp->setAnoMes($periodo);
								$saldosp->setCuenta($codigoCuenta);
								$saldosp->setCentroCosto($empresa->getCentroCosto());
							}

							$saldosp->setDebe($debe + $saldosp->getDebe());
							$saldosp->setHaber($haber + $saldosp->getHaber());
							$saldosp->setSaldo($saldo + $saldosp->getSaldo());

							if ($saldosp->save() == false) {
								foreach ($saldosp->getMessages() as $message) {
									$transaction->rollback('Saldosp: ' . $message->getMessage());
								}
							}

							$saldosp = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND ano_mes=0 AND centro_costo='{$empresa->getCentroCosto()}'");
							if ($saldosp == false) {
								$saldosp = new Saldosp();
								$saldosp->setTransaction($transaction);
								$saldosp->setAnoMes(0);
								$saldosp->setCuenta($codigoCuenta);
								$saldosp->setCentroCosto($empresa->getCentroCosto());
								$saldosp->setDebe($debe);
								$saldosp->setHaber($haber);
								$saldosp->setSaldo($saldo);

								if ($saldosp->save() == false) {
									foreach ($saldosp->getMessages() as $message) {
										$transaction->rollback('Saldosp: '.$message->getMessage());
									}
								}
							}
							unset($saldosp);
						}

						if ($existeSaldosc == true) {
							$debe  -= $saldosc->getDebe();
							$haber -= $saldosc->getHaber();
							$saldo -= $saldosc->getSaldo();
						}

						if ($existeSaldoscNiif == true) {
							$debeNiif  -= $saldoscNiif->getDebe();
							$haberNiif -= $saldoscNiif->getHaber();
							$saldoNiif -= $saldoscNiif->getSaldo();
						}

						$saldosc = $this->Saldosc->findFirst("cuenta='$codigoCuenta' AND ano_mes=0");
						if ($saldosc == false) {
							$saldosc = new Saldosc();
							$saldosc->setTransaction($transaction);
							$saldosc->setAnoMes(0);
							$saldosc->setCuenta($codigoCuenta);
							$saldosc->setDebe($debe+$saldosc->getDebe());
							$saldosc->setHaber($haber+$saldosc->getHaber());
							$saldosc->setSaldo($saldo+$saldosc->getSaldo());

							if ($saldosc->save() == false) {
								foreach ($saldosc->getMessages() as $message) {
									$transaction->rollback('Saldosc: ' . $message->getMessage());
								}
							}
						}
						unset($saldosc);

						if ($usarCierreNiif) {
							$saldoscNiif = $this->SaldoscNiif->findFirst("cuenta='$codigoCuentaNiif' AND ano_mes=0");
							if ($saldoscNiif == false) {
								$saldoscNiif = new SaldoscNiif();
								$saldoscNiif->setTransaction($transaction);
								$saldoscNiif->setDepre(0);
								$saldoscNiif->setAnoMes(0);
								$saldoscNiif->setCuenta($codigoCuentaNiif);
								$saldoscNiif->setDebe($debeNiif + $saldoscNiif->getDebe());
								$saldoscNiif->setHaber($haberNiif + $saldoscNiif->getHaber());
								$saldoscNiif->setSaldo($saldoNiif + $saldoscNiif->getSaldo());

								if ($saldoscNiif->save() == false) {
									foreach ($saldoscNiif->getMessages() as $message) {
										$transaction->rollback('saldoscNiif: ' . $message->getMessage());
									}
								}
							}
							unset($saldoscNiif);
						}

						if ($cuenta->getPideNit() == 'S') {
							$saldosn = $this->Saldosn->findFirst("cuenta='$codigoCuenta' AND ano_mes=0 AND nit='{$comcier->getNit()}'");
							if ($saldosn == false) {
								$saldosn = new Saldosn();
								$saldosn->setTransaction($transaction);
								$saldosn->setCuenta($codigoCuenta);
								$saldosn->setNit($comcier->getNit());
								$saldosn->setAnoMes($periodo);
							}

							$saldosn->setDebe($debe + $saldosn->getDebe());
							$saldosn->setHaber($haber + $saldosn->getHaber());
							$saldosn->setSaldo($saldo + $saldosn->getSaldo());

							if ($saldosn->save() == false) {
								foreach ($saldosn->getMessages() as $message) {
									$transaction->rollback('Saldosc: ' . $message->getMessage());
								}
							}
							unset($saldosn);
						}
					}

					$saldo = 0;
					$debe  = 0;
					$haber = 0;
					$base  = 0;

					$saldoNiif = 0;
					$debeNiif  = 0;
					$haberNiif = 0;
					$baseNiif  = 0;

					unset($cuenta);
					unset($codigoCuenta);
					unset($codigoCuentaNiif);
				}
			}

			//Eliminar saldosn de cuentas de PyG
			$this->Saldosp->deleteAll("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes=0 AND centro_costo='{$empresa->getCentroCosto()}'");

			//Eliminar saldosn de cuentas de PyG
			$records = $this->Saldosn->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'");
			foreach ($records as $saldon) {
				if ($saldon->delete() == false) {
					foreach ($saldon->getMessages() as $message) {
						$transaction->rollback('Saldosn: ' . $message->getMessage());
					}
				}
				unset($saldon);
			}

			//Eliminar saldosc de cuentas de PyG
			$records = $this->Saldosc->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'");
			foreach ($records as $saldoc) {
				if ($saldoc->delete() == false) {
					foreach ($saldoc->getMessages() as $message)
					{
						$transaction->rollback('Saldosc: '.$message->getMessage());
					}
				}
				unset($saldoc);
			}

			if ($usarCierreNiif) {
				//Eliminar saldosnNiif de cuentas de PyG
				$records = $this->SaldosnNiif->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'");
				foreach ($records as $saldon) {
					if ($saldon->delete() == false) {
						foreach ($saldon->getMessages() as $message) {
							$transaction->rollback('SaldosnNiif: ' . $message->getMessage());
						}
					}
					unset($saldon);
				}

				//Eliminar SaldoscNiif de cuentas de PyG
				$records = $this->SaldoscNiif->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'");
				foreach ($records as $saldoc) {
					if ($saldoc->delete() == false) {
						foreach ($saldoc->getMessages() as $message)
						{
							$transaction->rollback('SaldoscNiif: '.$message->getMessage());
						}
					}
					unset($saldoc);
				}
			}


			//Eliminar saldosp de cuentas de PyG
			$records = $this->Saldosp->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND ano_mes='$periodo'");
			foreach ($records as $saldop) {
				$saldop->setDebe(0);
				$saldop->setHaber(0);
				$saldop->setSaldo(0);
				if ($saldop->save() == false) {
					foreach ($saldop->getMessages() as $message) {
						$transaction->rollback('Saldosp: ' . $message->getMessage());
					}
				}
				unset($saldop);
			}

			$empresa1->setAnoc($empresa1->getAnoc()+1);
			if ($empresa1->save() == false) {
				foreach ($empresa1->getMessages() as $message) {
					$transaction->rollback('Empresa1: ' . $message->getMessage());
				}
			}

			$transaction->commit();

			return array(
				'status'  => 'OK',
				'message' => 'Se realizó el cierre anual correctamente'
			);

		}
		catch(TransactionFailed $e){
			return array(
				'status'  => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}