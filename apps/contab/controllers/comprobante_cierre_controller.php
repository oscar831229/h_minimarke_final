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
 * Realiza el comprobante de cierre anual
 *
 */
class Comprobante_CierreController extends ApplicationController
{

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

		$this->setParamToView('message', 'Indique los parámetros del comprobante y haga click en "Generar"');
		$this->setParamToView('nombreComprobante', 'CIERRE ANUAL');

		$cuenta = $this->Cuentas->findFirst(4);
		Tag::displayTo('cuentaInicial', $cuenta->getCuenta());

		$cuenta = $this->Cuentas->findFirst("cuenta LIKE '6%' AND es_auxiliar='S'", 'order: cuenta DESC');
		Tag::displayTo('cuentaFinal', $cuenta->getCuenta());
	}

	public function generarAction()
	{

		$this->setResponse('json');

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);

		try {

			set_time_limit(0);
			$transaction = TransactionManager::getUserTransaction();

			$empresa = $this->Empresa->findFirst();
			$fechaCierre = $empresa->getFCierrec();

			if ($fechaCierre->getMonth() != 11) {
				$transaction->rollback('El cierre contable actual debe estar a Noviembre');
			}

			$codigoEjercicio = $this->getPostParam('codigoEjercicio', 'cuentas');
			$cuenta = $this->Cuentas->findFirst($codigoEjercicio);
			if  ($cuenta == false) {
				$transaction->rollback('La cuenta de resultado de ejercicio no existe');
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$transaction->rollback('La cuenta de resultado de ejercicio no es auxiliar');
				}
			}

			$comprobCierre = Settings::get('comprob_cierre');
			if($comprobCierre==''){
				$transaction->rollback('No se ha configurado el comprobante de cierre');
			} else {
				$comprob = $this->Comprob->findFirst("codigo='$comprobCierre'");
				if($comprob==false){
					$transaction->rollback('El comprobante de cierre configurado no existe');
				}
			}

			$ultimoCierre = clone $fechaCierre;
			$ultimoCierre->addMonths(1);
			$ultimoCierre->toLastDayOfMonth();

			$fechaAnterior = clone $fechaCierre;
			$fechaAnterior->diffMonths(1);
			$fechaAnterior->toLastDayOfMonth();

			$periodo = $fechaCierre->getPeriod();
			$periodoAnterior = $fechaAnterior->getPeriod();

			//$transaction->rollback($periodo);

			$centroDefecto = $this->Centros->findFirst("codigo=({$empresa->getCentroCosto()}+50) AND estado='A'");
			if($centroDefecto==false){
				$centroDefecto = $this->Centros->findFirst("codigo<>'{$empresa->getCentroCosto()}' AND estado='A'");
				if($centroDefecto==false){
					$transaction->rollback('No se encontró el centro de costo por defecto');
				}
			}

			$codigoCentroDefecto = $centroDefecto->getCodigo();
			$fechaMovi = (string)$fechaCierre;

			$primerDia = Date::getFirstDayOfMonth(12, $fechaCierre->getYear());
			$ultimoDia = Date::getLastDayOfMonth(12, $fechaCierre->getYear());

			try {

				if($this->Movi->count("comprob='$comprobCierre' AND fecha='$fechaMovi'")>0){
					$transaction->rollback('Ya existe un comprobante de cierre anual para el año '.$fechaCierre->getYear());
				}

				$balance = 0;
				$cierreAnual = array();
				$numeroMovimientos = 0;
				$aura = new Aura($comprobCierre, 0, (string)$ultimoDia, Aura::OP_CREATE);
				$conditions = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'";
				foreach($this->Cuentas->find(array($conditions, 'columns' => 'cuenta,nombre,pide_centro')) as $cuenta){

					$codigoCuenta = $cuenta->getCuenta();
					$saldoc = $this->Saldosc->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					if($saldoc!=false){
						if($saldoc->getSaldo()!=0){
							$saldosp = $this->Saldosp->find("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
							if(count($saldosp)){
								foreach($saldosp as $saldop){
									if($saldop->getSaldo()!=0){
										$balance+=$saldop->getSaldo();
										if($saldop->getSaldo()>0){
											if(!isset($cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['C'])){
												$cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['C'] = 0;
											}
											$cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['C'] += $saldop->getSaldo();
										} else {
											if(!isset($cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['D'])){
												$cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['D'] = 0;
											}
											$cierreAnual[$codigoCuenta][$saldop->getCentroCosto()]['D'] += -$saldop->getSaldo();
										}
									}
									$numeroMovimientos++;
									unset($saldop);
								}
							} else {
								if($saldoc->getSaldo()!=0){
									$balance+=$saldoc->getSaldo();
									if($saldoc->getSaldo()>0){
										if(!isset($cierreAnual[$codigoCuenta][$codigoCentroDefecto]['C'])){
											$cierreAnual[$codigoCuenta][$codigoCentroDefecto]['C'] = 0;
										}
										$cierreAnual[$codigoCuenta][$codigoCentroDefecto]['C'] += $saldoc->getSaldo();
									} else {
										if(!isset($cierreAnual[$codigoCuenta][$codigoCentroDefecto]['D'])){
											$cierreAnual[$codigoCuenta][$codigoCentroDefecto]['D'] = 0;
										}
										$cierreAnual[$codigoCuenta][$codigoCentroDefecto]['D'] += -$saldoc->getSaldo();
									}
									$numeroMovimientos++;
								}
							}
							unset($saldosp);
						}
					}
					unset($saldoc);

					foreach($this->Movi->find(array("cuenta='$codigoCuenta' AND fecha>='$primerDia' AND fecha<='$ultimoDia'", "columns" => "centro_costo,deb_cre,valor")) as $movi){

						if($movi->getDebCre()=='D'){
							$naturaleza = 'C';
						} else {
							$naturaleza = 'D';
						}

						if($naturaleza=='C'){
							$balance+=$movi->getValor();
						} else {
							$balance-=$movi->getValor();
						}

						if($cuenta->getPideCentro()=='S'){
							$centroCosto = $movi->getCentroCosto();
						} else {
							$centroCosto = $codigoCentroDefecto;
						}

						if(!isset($cierreAnual[$codigoCuenta][$centroCosto][$naturaleza])){
							$cierreAnual[$codigoCuenta][$centroCosto][$naturaleza] = 0;
						}
						$cierreAnual[$codigoCuenta][$centroCosto][$naturaleza] += $movi->getValor();

						$numeroMovimientos++;
						unset($movi);
					}

					unset($cuenta);
				}

				$nitEmpresa = $empresa->getNit();
				ksort($cierreAnual, SORT_STRING);
				foreach($cierreAnual as $codigoCuenta => $cierreCuentas){
					foreach($cierreCuentas as $centroCosto => $cierreCentros){
						foreach($cierreCentros as $naturaleza => $valor){
							if($valor>0){
								$aura->addMovement(array(
									'Cuenta' => $codigoCuenta,
									'Nit' => $nitEmpresa,
									'Valor' => $valor,
									'Descripcion' => 'CIERRE DEL EJERCICIO',
									'CentroCosto' => $centroCosto,
									'DebCre' => $naturaleza
								));
							}
						}
					}
				}

				if($balance!=0){
					if($balance>0){
						$aura->addMovement(array(
							'Cuenta' => $codigoEjercicio,
							'Nit' => $empresa->getNit(),
							'Valor' => $balance,
							'Descripcion' => 'CIERRE DEL EJERCICIO',
							'CentroCosto' => $centroDefecto->getCodigo(),
							'DebCre' => 'D'
						));
					} else {
						$aura->addMovement(array(
							'Cuenta' => $codigoEjercicio,
							'Nit' => $empresa->getNit(),
							'Valor' => abs($balance),
							'Descripcion' => 'CIERRE DEL EJERCICIO',
							'CentroCosto' => $centroDefecto->getCodigo(),
							'DebCre' => 'C'
						));
					}
					$numeroMovimientos++;
				}

				if($numeroMovimientos>0){
					$aura->save();
				}
			}
			catch(AuraException $e){
				$transaction->rollback($e->getMessage());
			}

			$transaction->commit();
			return array(
				'status' => 'OK'
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
