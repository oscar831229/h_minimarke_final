<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Accounts extends UserComponent
{

	private $_total;
	private $_cuentas;
	private $_abonos;
	private $_consumos;

	public function getResume($numeroCuenta=0){
		$guestInfo = SessionNamespace::get('guestInfo');
		$cuentas = array();
		$total = 0;
		$cargos = array();
		if($numeroCuenta>0){
			$carghabs = $this->Carghab->find("numfol='{$guestInfo->getFolio()}' AND numcue<>'$numeroCuenta' AND estado IN ('N', 'A')");
		} else {
			$carghabs = $this->Carghab->find("numfol='{$guestInfo->getFolio()}' AND estado IN ('N', 'A')");
		}
		foreach($carghabs as $carghab){
			$cuentas[$carghab->getNumcue()] = array(
				'abonos' => 0,
				'servicio' => 0,
				'iva' => 0,
				'valor' => 0,
				'total' => 0,
			);
			foreach($this->Valcar->find("numfol='{$guestInfo->getFolio()}' AND numcue = '{$carghab->getNumcue()}' AND estado<>'B'") as $valcar){
				if(!isset($cargos[$valcar->getCodcar()])){
					$cargos[$valcar->getCodcar()] = $this->Cargos->findFirst("codcar='{$valcar->getCodcar()}'");
				}
				$cargo = $cargos[$valcar->getCodcar()];
				if($cargo->getTipmov()=='D'){
					if($cargo->getDescaj()=='S'){
						$cuentas[$carghab->getNumcue()]['servicio']-=$valcar->getValser();
						$cuentas[$carghab->getNumcue()]['iva'] -= ($valcar->getIva() + $valcar->getImpo());
						$cuentas[$carghab->getNumcue()]['valor']-=$valcar->getValor();
						$cuentas[$carghab->getNumcue()]['total']-=$valcar->getTotal();
						$total-=$valcar->getTotal();
					} else {
						$cuentas[$carghab->getNumcue()]['servicio']+=$valcar->getValser();
						$cuentas[$carghab->getNumcue()]['iva'] += ($valcar->getIva() + $valcar->getImpo());
						$cuentas[$carghab->getNumcue()]['valor']+=$valcar->getValor();
						$cuentas[$carghab->getNumcue()]['total']+=$valcar->getTotal();
						$total+=$valcar->getTotal();
					}
				} else {
					if($cargo->getDescaj()=='S'){
						$cuentas[$carghab->getNumcue()]['abonos']-=$valcar->getTotal();
						$total+=$valcar->getTotal();
					} else {
						$cuentas[$carghab->getNumcue()]['abonos']+=$valcar->getTotal();
						$total-=$valcar->getTotal();
					}
				}
			}
		}
		$this->_total = $total;
		return $cuentas;
	}


	public function getResumeFrom($numeroCuenta){
		$guestInfo = SessionNamespace::get('guestInfo');
		$cuentas = array();
		$total = 0;
		$cargos = array();
		$carghabs = $this->Carghab->find("numfol='{$guestInfo->getFolio()}' AND numcue='$numeroCuenta' AND estado IN ('N', 'A')");
		foreach($carghabs as $carghab){
			$cuenta = array(
				'abonos' => 0,
				'servicio' => 0,
				'iva' => 0,
				'valor' => 0,
				'total' => 0,
			);
			foreach($this->Valcar->find("numfol='{$guestInfo->getFolio()}' AND numcue = '{$carghab->getNumcue()}' AND estado<>'B'") as $valcar){
				if(!isset($cargos[$valcar->getCodcar()])){
					$cargos[$valcar->getCodcar()] = $this->Cargos->findFirst("codcar='{$valcar->getCodcar()}'");
				}
				$cargo = $cargos[$valcar->getCodcar()];
				if($cargo->getTipmov()=='D'){
					if($cargo->getDescaj()=='S'){
						$cuenta['servicio']-=$valcar->getValser();
						$cuenta['iva']     -=($valcar->getIva() + $valcar->getImpo());
						$cuenta['valor']-=$valcar->getValor();
						$cuenta['total']-=$valcar->getTotal();
						$total-=$valcar->getTotal();
					} else {
						$cuenta['servicio']+=$valcar->getValser();
						$cuenta['iva'] += ($valcar->getIva() + $valcar->getImpo());
						$cuenta['valor']+=$valcar->getValor();
						$cuenta['total']+=$valcar->getTotal();
						$total+=$valcar->getTotal();
					}
				} else {
					if($cargo->getDescaj()=='S'){
						$cuenta['abonos']-=$valcar->getTotal();
						$total+=$valcar->getTotal();
					} else {
						$cuenta['abonos']+=$valcar->getTotal();
						$total-=$valcar->getTotal();
					}
				}
			}
		}
		$this->_total = $total;
		return $cuenta;
	}

	public function getMovement($carghab){

		$guestInfo = SessionNamespace::get('guestInfo');

		$locale = new Locale(Session::get('locale'));
		$language = $locale->getLanguage();
		$lcPath = 'apps/'.Router::getApplication().'/languages/'.$language.'/LC_MESSAGES/dict-'.$language.'.php';
		if(file_exists($lcPath)){
			require $lcPath;
		} else {
			require 'apps/'.Router::getApplication().'/languages/es/LC_MESSAGES/dict-es.php';
		}
		$dict = new Traslate('Array', $messages);

		$total = 0;
		$abonos = 0;
		$consumos = 0;
		$movimientos = array();
		$cargos = array();
		foreach($this->Valcar->find("numfol='{$guestInfo->getFolio()}' AND numcue = '{$carghab->getNumcue()}' AND estado<>'B'") as $valcar){
			$fecha = substr($valcar->getFecha(), 0, 10);
			if(!isset($cargos[$valcar->getCodcar()])){
				$cargos[$valcar->getCodcar()] = $this->Cargos->findFirst("codcar='{$valcar->getCodcar()}'");
			}
			$cargo = $cargos[$valcar->getCodcar()];
			if ($cargo->getTipmov() == 'D') {
				if ($cargo->getDescaj() == 'S') {
					$movimientos[$fecha][] = array(
						'concepto' => $dict[trim($cargo->getDescripcion())],
						'servicio' => -$valcar->getValser(),
						'iva' => -($valcar->getIva() + $valcar->getImpo()),
						'valor' => -$valcar->getValor(),
						'total' => -$valcar->getTotal(),
					);
					$total+=$valcar->getTotal();
					$consumos-=$valcar->getTotal();
				} else {
					$movimientos[$fecha][] = array(
						'concepto' => $dict[trim($cargo->getDescripcion())],
						'servicio' => $valcar->getValser(),
						'iva' => ($valcar->getIva() + $valcar->getImpo()),
						'valor' => $valcar->getValor(),
						'total' => $valcar->getTotal(),
					);
					$total-=$valcar->getTotal();
					$consumos+=$valcar->getTotal();
				}
			} else {
				if($cargo->getDescaj()=='S'){
					$movimientos[$fecha][] = array(
						'concepto' => $dict[$cargo->getDescripcion()],
						'valor' => -$valcar->getValor(),
						'total' => -$valcar->getTotal(),
					);
					$total-=$valcar->getTotal();
					$abonos-=$valcar->getTotal();
				} else {
					$movimientos[$fecha][] = array(
						'concepto' => $dict[trim($cargo->getDescripcion())],
						'valor' => $valcar->getValor(),
						'total' => $valcar->getTotal(),
					);
					$total  += $valcar->getTotal();
					$abonos += $valcar->getTotal();
				}
			}
		}
		$this->_consumos = $consumos;
		$this->_abonos = $abonos;
		$this->_total = $total;
		return $movimientos;
	}

	public function getTotal(){
		return $this->_total;
	}

	public function getAbonos(){
		return $this->_abonos;
	}

	public function getConsumos(){
		return $this->_consumos;
	}

}