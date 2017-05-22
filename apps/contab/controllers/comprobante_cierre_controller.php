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

			$empresa = $this->Empresa->findFirst();
			$fechaCierre = $empresa->getFCierrec();

			if ($fechaCierre->getMonth() != 11) {
				throw new Exception('El cierre contable actual debe estar a Noviembre');
			}

			$codigoEjercicio = $this->getPostParam('codigoEjercicio', 'cuentas');
			$cuenta = $this->Cuentas->findFirst($codigoEjercicio);
			if  ($cuenta == false) {
				throw new Exception('La cuenta de resultado de ejercicio no existe');
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					throw new Exception('La cuenta de resultado de ejercicio no es auxiliar');
				}
			}

			$comprobCierre = Settings::get('comprob_cierre');
			if($comprobCierre==''){
				throw new Exception('No se ha configurado el comprobante de cierre');
			} else {
				$comprob = $this->Comprob->findFirst("codigo='$comprobCierre'");
				if($comprob==false){
					throw new Exception('El comprobante de cierre configurado no existe');
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
					throw new Exception('No se encontró el centro de costo por defecto');
				}
			}

			$codigoCentroDefecto = $centroDefecto->getCodigo();
			$fechaMovi = (string)$fechaCierre;

			$primerDia = Date::getFirstDayOfMonth(12, $fechaCierre->getYear());
			$ultimoDia = Date::getLastDayOfMonth(12, $fechaCierre->getYear());

			$periodoIniNiif = (int) Settings::get('period_saldos_ini_niif');
			if ($periodoIniNiif < 199001) {
				throw new Exception('No se ha configurado el periodo de los saldos iniciales NIIF');
			}

			if($this->Movi->count("comprob='$comprobCierre' AND fecha='$fechaMovi'")>0){
				throw new Exception('Ya existe un comprobante de cierre anual para el año ' . $fechaCierre->getYear());
			}

			$balance = 0;
			$cierreAnual = array();
			$numeroMovimientos = 0;
			
			$aura = new Aura($comprobCierre, 0, (string)$ultimoDia, Aura::OP_CREATE);

			$conditions = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'";
			foreach ($this->Cuentas->find(array($conditions, 'columns' => 'cuenta,nombre,pide_centro')) as $cuenta) {

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

				$status = $this->cierreEjercicioAnualNiif($cuentaInicial, $cuentaFinal, $fechaCierre, $codigoEjercicio, $codigoCentroDefecto, $centroDefecto);
				
				if ($status == true) {
					$aura->save();

					return array(
						'status' => 'OK'
					);
				}
			}

			throw new Exception("No se encontro movimiento a cerrar", 1);

		}
		catch(TransactionFailed $e){
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

	public function cierreEjercicioAnualNiif($cuentaInicial, $cuentaFinal, $fechaCierre, $codigoEjercicio, $codigoCentroDefecto, $centroDefecto)
	{
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();

			
		$fechaMovi = (string) $fechaCierre;

		$periodCierreDate = new Date($fechaCierre);
		$periodo = $periodCierreDate->getPeriod();
		$periodoAnterior = Date::subPeriodo($periodo, 1);

		$periodoIniNiif = Settings::get('period_saldos_ini_niif');
		if ($periodoIniNiif < 199001) {
			throw new Exception('No se ha configurado el periodo de los saldos iniciales NIIF');
		}

		$usarCierreNiif = ($periodoIniNiif < $periodo);

		if (!$usarCierreNiif) {
			return;
		}

		$comprobCierreNiif = Settings::get('comprob_cierre_niif');
		if ($comprobCierreNiif == '') {
			throw new Exception('No se ha configurado el comprobante de cierre NIIF');
		} else {
			$comprobNiif = $this->Comprob->findFirst("codigo='$comprobCierreNiif'");
			if ($comprobNiif == false) {
				throw new Exception('El comprobante de cierre NIIF "' + $comprobCierreNiif + '" configurado no existe');
			}
		}

		if ($this->MoviNiif->count("comprob='$comprobCierreNiif' AND fecha='$fechaMovi'") > 0) {
			throw new Exception(
				'Ya existe un comprobante de cierre anual niif para el año ' . $fechaCierre->getYear()
			);
		}

		$balance = 0;
		$cierreAnual = [];
		$numeroMovimientos = 0;
		
		$primerDia = Date::getFirstDayOfMonth(12, $fechaCierre->getYear());
		$ultimoDia = Date::getLastDayOfMonth(12, $fechaCierre->getYear());

		$aura = new AuraNiif($comprobCierreNiif, 0, (string)$ultimoDia, Aura::OP_CREATE);

		$conditions = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'";
		
		foreach ($this->Cuentas->find(array($conditions, 'columns' => 'cuenta,nombre,pide_centro,cuenta_niif')) as $cuenta) {
			$codigoCuenta = $cuenta->getCuentaNiif();
			if (!$codigoCuenta) {
				continue;
			}

			$saldoc = $this->SaldoscNiif->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
			
			if($saldoc!=false){
				if($saldoc->getSaldo()!=0){
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
			}
			unset($saldoc);

			$movis = $this->MoviNiif->find(array(
				"cuenta='$codigoCuenta' AND fecha>='$primerDia' AND fecha<='$ultimoDia'", 
				"columns" => "centro_costo,deb_cre,valor")
			);
			foreach($movis as $movi){

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
			return true;
		}

		return false;
	}

}
