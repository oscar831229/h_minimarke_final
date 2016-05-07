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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version	$Id$
 */

class TaticoException extends Exception
{

}

/**
 * TaticoKardx
 *
 * Clase para generar kardex y recalculo de existencias
 *
 */
class TaticoKardex extends UserComponent
{

	/**
	 * Indica si el kardex se recalcula en modo rápido
	 *
	 * @var boolean
	 */
	private static $_fastRecalculate = false;

	/**
	 * Indica si el kardex se debe recalcular en modo rápido
	 *
	 * @param boolean $fastRecalculate
	 */
	public static function setFastRecalculate($fastRecalculate) {
		self::$_fastRecalculate = $fastRecalculate;
	}

	/**
	 * Imprime una línea del Kardex
	 *
	 */
	private static function _showKardexRow($tipo, &$contents, $fecha, $movilin, &$mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior=false, $fechaProceso=false) {
		$nuevoCostoTotal = 0;
		if (self::$_fastRecalculate == false) {

			if ($mostrarSaldoAnterior == false) {

				$costoTotalAnterior = 0;
				$costoPromedioAnterior = 0;
				if ($fechaProceso && $nuevoSaldo) {
					$date = new Date($fechaProceso);
					$periodo = Date::subPeriodo($date->getPeriod(), 1);
					$saldos = EntityManager::get("Saldos")->findFirst("ano_mes='$periodo' AND item='{$movilin->getItem()}' AND almacen='{$movilin->getAlmacen()}'");
					if ($saldos) {
						$costoTotalAnterior = $saldos->getCosto();
						if (!$nuevoSaldo) {
							$nuevoSaldo = 1;
						}
						$costoPromedioAnterior = $costoTotalAnterior / $nuevoSaldo;
					}
				}

				$contents.= '<tr>
					<td align="right" colspan="9"><b>Saldo Anterior</b></td>
					<td align="right">'.Currency::number($saldoAnterior, 2).'</td>
					<td align="right">'.Currency::number($costoPromedioAnterior, 2).'</td>
					<td align="right">'.Currency::number($costoTotalAnterior, 2).'</td>
					<td>&nbsp;</td>
				</tr>';
				$mostrarSaldoAnterior = true;
			}

			$nuevoCostoTotal = $movilin->getValor();
			if ($nuevoSaldo < $saldoAnterior) {
				$symbol = '↓';
				if ($lastSaldoAnterior) {
					$nuevoCostoTotal = $lastSaldoAnterior - $movilin->getValor();
				}
			} else {
				$symbol = '↑';
				if ($lastSaldoAnterior) {
					$nuevoCostoTotal = $lastSaldoAnterior + $movilin->getValor();
				}
			}

			$almacen = BackCacher::getAlmacen($movilin->getAlmacen());
			if ($almacen==false) {
				$nombreAlmacen = 'NO EXISTE';
			} else {
				$nombreAlmacen = str_replace('ALMACEN ', 'A. ', $almacen->getNomAlmacen());
			}

			$almacen = BackCacher::getAlmacen($movilin->getAlmacenDestino());
			if ($almacen==false) {
				$nombreDestino = 'NO EXISTE';
			} else {
				$nombreDestino = str_replace('ALMACEN ', 'A. ', $almacen->getNomAlmacen());
			}

			$cantidad = (double) $movilin->getCantidad();
			if ($cantidad) {
			 	$costo = $movilin->getValor() / $cantidad;
			} else {
				$costo = 0;
			}

			$nuevoCostoPromedio = $movilin->getValor();
			//Nuevos campos
			if ($lastSaldoAnterior > 0) {
				if (!$nuevoSaldo) {
					$nuevoSaldo = 1;
				}
				$nuevoCostoPromedio = $nuevoCostoTotal / $nuevoSaldo;
				//$contents .=  "<br>".$movilin->getComprob()."-".$movilin->getNumero().">$nuevoCostoPromedio = $nuevoCostoTotal / $nuevoSaldo<br>";
			}

			if (LocaleMath::round($nuevoSaldo, 2) <= 0) {
				$nuevoCostoPromedio = $costo;
			}

			$contents.= '<tr>
				<td align="right">'.$movilin->getComprob().'</td>
				<td align="right">'.$movilin->getNumero().'</td>
				<td>'.$tipo.'</td>
				<td align="right">'.$fecha.'</td>
				<td align="right">'.$movilin->getAlmacen().'/'.$nombreAlmacen.'</td>
				<td align="right">'.$movilin->getAlmacenDestino().'/'.$nombreDestino.'</td>
				<td align="right">'.$cantidad.'</td>
				<td align="right">'.Currency::number($costo, 2).'</td>
				<td align="right">'.Currency::number($movilin->getValor(), 2).'</td>
				<td align="right">'.Currency::number($nuevoSaldo, 2).'</td>
				<td align="right">'.Currency::number($nuevoCostoPromedio, 2).'</td>
				<td align="right">'.Currency::number($nuevoCostoTotal, 2).'</td>
				<td align="center">'.$symbol.'</td>
			</tr>';
		}

		return $nuevoCostoTotal;
	}

	/**
	 * Imprime el Kardex de Inventarios
	 *
	 * @param string $codigoItem
	 * @param string $codigoAlmacen
	 * @param string $fechaProceso
	 */
	public static function show($codigoItem, $codigoAlmacen, $fechaProceso) {

		$contents = '';
		try {

			$inve = BackCacher::getInve($codigoItem);
			if ($inve == false) {
				Flash::error("No existe la referencia '$codigoItem'");
				return false;
			}

			$almacen = BackCacher::getAlmacen($codigoAlmacen);
			if ($almacen == false) {
				Flash::error("No existe el almacen '$codigoAlmacen'");
				return false;
			}

			set_time_limit(0);

			$fechaProceso = new Date($fechaProceso);

			ActiveRecord::disableEvents(true);
			ActiveRecord::refreshPersistance(false);
			$externalTransaction = TransactionManager::hasUserTransaction();
			$transaction = TransactionManager::getUserTransaction();

			$empresa = BackCacher::getEmpresa();

			$contents = '';
			if (self::$_fastRecalculate==false) {
				$contents.= '<h1 class="kardex">Kardex Inventario</h1>';
				$contents.= "<h2 class='kardex'>". $empresa->getNombre(). "</h2>";
				$contents.= '<h2 class="kardex">Almacén: '.$almacen->getCodigo().'-'.$almacen->getNomAlmacen().'</h2>';
				$contents.= '<h2 class="kardex">Referencia: '.$inve->getItem().'-'. utf8_encode($inve->getDescripcion()). '</h2>';
				$contents.= '<h2 class="kardex">Fecha: '.$fechaProceso.'</h2>';
				$contents.= '<h2 class="kardex">Fecha Reporte: '.Date::now().'</h2>';
			}

			$fechaCierre = $empresa->getFCierrei();
			$fechaCierre->toLastDayOfMonth();
			$proximoCierre = clone $fechaCierre;
			$proximoCierre->addDays(1);
			$proximoCierre->toLastDayOfMonth();

			$saldos = array();
			$saldosc = array();
			$periodoAnterior = 0;
			$nuevoSaldo = 0;
			$saldoAnterior = 0;
			$mostrarSaldoAnterior = false;

			$Saldos = self::getModel('Saldos');
			$Saldos->setTransaction($transaction);

			$Inve = self::getModel('Inve');
			$Inve->setTransaction($transaction);

			$Movilin = self::getModel('Movilin');
			$Movilin->setTransaction($transaction);

			$Saldos->deleteAll("item='{$inve->getItem()}' AND almacen='$codigoAlmacen'");

			if (self::$_fastRecalculate==false) {
				$contents.= '<br/>
				<table class="kardex" cellspacing="0">
					<tr>
						<th>Comprobante</th>
						<th>Número</th>
						<th>Movimiento</th>
						<th>Fecha</th>
						<th>Almacén Origen</th>
						<th>Almacén Destino</th>
						<th>Cantidad</th>
						<th>Costo Unit.</th>
						<th>Costo Total</th>
						<th>Nueva Cantidad</th>
						<th>Nuevo Costo Promedio</th>
						<th>Nuevo Costo Total</th>
						<th>&nbsp;</th>
					</tr>';
			}

			$lastSaldoAnterior = 0;
			$movilins = $Movilin->find(array("item='{$inve->getItem()}' AND fecha<='$proximoCierre'", "order" => "fecha,almacen,prioridad"));
			foreach ($movilins as $movilin) {

				$tipoComprob = substr($movilin->getComprob(), 0, 1);
				$fecha = $movilin->getFecha();
				$item = strtoupper($movilin->getItem());
				$almacenMov = $movilin->getAlmacen();

				if ($tipoComprob=="E") {
					if (!isset($saldos[$almacenMov][$item])) {
						$saldos[$almacenMov][$item]['saldo'] = 0;
						$saldos[$almacenMov][$item]['costo'] = 0;
					}
					$saldos[$almacenMov][$item]['saldo']+=$movilin->getCantidad();
					$saldos[$almacenMov][$item]['costo']+=($movilin->getValor());
					if ($almacenMov==$codigoAlmacen) {
						if ($periodoAnterior=='') {
							if (!isset($saldosc[$fecha->getPeriod()])) {
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if ($periodoAnterior!=$fecha->getPeriod()) {
								if (isset($saldosc[$periodoAnterior])) {
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$saldoAnterior = $nuevoSaldo;
						$nuevoSaldo+=$movilin->getCantidad();
						if (Date::compareDates($fechaProceso, $fecha)<1) {
							$lastSaldoAnterior = self::_showKardexRow('ENTRAN', $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
						}
					}
					continue;
				}

				//Ajuste
				if ($tipoComprob=='A') {
					if (!isset($saldos[$almacenMov][$item])) {
						$saldos[$almacenMov][$item]['saldo'] = 0;
						$saldos[$almacenMov][$item]['costo'] = 0;
					}
					$saldos[$almacenMov][$item]['saldo']+=$movilin->getCantidad();
					$saldos[$almacenMov][$item]['costo']+=($movilin->getValor());
					if ($almacenMov==$codigoAlmacen) {

						if ($periodoAnterior=='') {
							if (!isset($saldosc[$fecha->getPeriod()])) {
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if ($periodoAnterior!=$fecha->getPeriod()) {
								if (isset($saldosc[$periodoAnterior])) {
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$saldoAnterior = $nuevoSaldo;
						$nuevoSaldo+=$movilin->getCantidad();
						if (Date::compareDates($fechaProceso, $fecha)<1) {
							if ($movilin->getCantidad()==0) {
								$tipoAjuste = 'A. COSTO';
							} else {
								if ($movilin->getCantidad()>0) {
									$tipoAjuste = 'A. SOBRANTE';
								} else {
									$tipoAjuste = 'A. FALTANTE';
								}
							}
							$lastSaldoAnterior = self::_showKardexRow($tipoAjuste, $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
						}

					}
					continue;
				}

				//Traslados
				if ($tipoComprob=='T') {
					if ($almacenMov!=$movilin->getAlmacenDestino()) {
						if (!isset($saldos[$almacenMov][$item])) {
							$saldos[$almacenMov][$item]['saldo'] = 0;
							$saldos[$almacenMov][$item]['costo'] = 0;
						}
						if (!isset($saldos[$movilin->getAlmacenDestino()][$item])) {
							$saldos[$movilin->getAlmacenDestino()][$item]['saldo'] = 0;
							$saldos[$movilin->getAlmacenDestino()][$item]['costo'] = 0;
						}
						$saldos[$almacenMov][$item]['saldo']-=$movilin->getCantidad();
						$saldos[$almacenMov][$item]['costo']-=($movilin->getValor());
						$saldos[$movilin->getAlmacenDestino()][$item]['saldo']+=$movilin->getCantidad();
						$saldos[$movilin->getAlmacenDestino()][$item]['costo']+=($movilin->getValor());
						if ($movilin->getAlmacenDestino()==$codigoAlmacen) {

							if ($periodoAnterior=='') {
								if (!isset($saldosc[$fecha->getPeriod()])) {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							} else {
								if ($periodoAnterior!=$fecha->getPeriod()) {
									if (isset($saldosc[$periodoAnterior])) {
										$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
										$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
									} else {
										$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
									}
								}
							}
							$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
							$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
							$periodoAnterior = $fecha->getPeriod();

							$saldoAnterior = $nuevoSaldo;
							$nuevoSaldo+=$movilin->getCantidad();
							if (Date::compareDates($fechaProceso, $fecha)<1) {
								$lastSaldoAnterior = self::_showKardexRow('T. ENTRAN', $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
							}

						}

						if ($almacenMov==$codigoAlmacen) {

							if ($periodoAnterior=='') {
								if (!isset($saldosc[$fecha->getPeriod()])) {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							} else {
								if ($periodoAnterior!=$fecha->getPeriod()) {
									if (isset($saldosc[$periodoAnterior])) {
										$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
										$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
									} else {
										$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
									}
								}
							}
							$saldosc[$fecha->getPeriod()]['costo']-=$movilin->getValor();
							$saldosc[$fecha->getPeriod()]['saldo']-=$movilin->getCantidad();
							$periodoAnterior = $fecha->getPeriod();

							$saldoAnterior = $nuevoSaldo;
							$nuevoSaldo-=$movilin->getCantidad();
							if (Date::compareDates($fechaProceso, $fecha)<1) {
								$lastSaldoAnterior = self::_showKardexRow('T. SALEN', $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
							}

						}
					}
					continue;
				}

				//Consumos
				if ($tipoComprob=="C") {
					if (!isset($saldos[$almacenMov][$item])) {
						$saldos[$almacenMov][$item]['saldo'] = 0;
						$saldos[$almacenMov][$item]['costo'] = 0;
					}
					$saldos[$almacenMov][$item]['saldo']-=$movilin->getCantidad();
					$saldos[$almacenMov][$item]['costo']-=$movilin->getValor();
					if ($almacenMov==$codigoAlmacen) {
						if ($periodoAnterior=='') {
							if (!isset($saldosc[$fecha->getPeriod()])) {
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if ($periodoAnterior!=$fecha->getPeriod()) {
								if (isset($saldosc[$periodoAnterior])) {
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']-=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']-=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$saldoAnterior = $nuevoSaldo;
						$nuevoSaldo-=$movilin->getCantidad();
						if (Date::compareDates($fechaProceso, $fecha)<1) {
							$lastSaldoAnterior = self::_showKardexRow('SALEN', $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
						}

					}
					continue;
				}

				//Transformaciones
				if ($tipoComprob=="R") {
					if (!isset($saldos[$almacenMov][$item])) {
						$saldos[$almacenMov][$item]['saldo'] = 0;
						$saldos[$almacenMov][$item]['costo'] = 0;
					}
					$saldos[$almacenMov][$item]['saldo']+=$movilin->getCantidad();
					$saldos[$almacenMov][$item]['costo']+=($movilin->getValor());
					if ($almacenMov==$codigoAlmacen) {

						if ($periodoAnterior=='') {
							if (!isset($saldosc[$fecha->getPeriod()])) {
								$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
							}
						} else {
							if ($periodoAnterior!=$fecha->getPeriod()) {
								if (isset($saldosc[$periodoAnterior])) {
									$saldosc[$fecha->getPeriod()]['costo'] = $saldosc[$periodoAnterior]['costo'];
									$saldosc[$fecha->getPeriod()]['saldo'] = $saldosc[$periodoAnterior]['saldo'];
								} else {
									$saldosc[$fecha->getPeriod()] = array('costo' => 0, 'saldo' => 0);
								}
							}
						}
						$saldosc[$fecha->getPeriod()]['costo']+=$movilin->getValor();
						$saldosc[$fecha->getPeriod()]['saldo']+=$movilin->getCantidad();
						$periodoAnterior = $fecha->getPeriod();

						$saldoAnterior = $nuevoSaldo;
						$nuevoSaldo+=$movilin->getCantidad();
						if (Date::compareDates($fechaProceso, $fecha)<1) {
							$lastSaldoAnterior = self::_showKardexRow('TRANSFORMACIÓN', $contents, $fecha, $movilin, $mostrarSaldoAnterior, $saldoAnterior, $nuevoSaldo, $lastSaldoAnterior, $fechaProceso);
						}

					}
					continue;
				}

			}
			$contents.= '</table>';

			$almacenes = array();
			$saldoTotal = 0;
			$costoTotal = 0;
			$stockMinimoTotal = 0;
			$stockMaximoTotal = 0;
			if (self::$_fastRecalculate==false) {
				$contents.= '<br/>
				<b class="kardex">Saldos/Costos Actuales</b>
				<table class="kardex" cellspacing="0">
				<thead>
					<tr>
						<th>Almacén</th>
						<th>Saldo</th>
						<th>Costo</th>
						<th>Stock Mínimo</th>
						<th>Stock Máximo</th>
					</tr>';
			}
			foreach ($saldos as $numeroAlmacen => $saldo) {
				$almacenes[$numeroAlmacen] = true;
				foreach ($saldo as $item => $s) {
					$almacen = BackCacher::getAlmacen($numeroAlmacen);
					if ($s['saldo']==0) {
						$s['costo'] = 0;
					}

					$inveStock = self::getModel('InveStocks')->findFirst("almacen='{$numeroAlmacen}' AND item='{$inve->getItem()}'");
					if ($inveStock==false) {
						$stockMinimo = 0;
						$stockMaximo = 0;
					} else {
						$stockMinimo = $inveStock->getMinimo();
						$stockMaximo = $inveStock->getMaximo();
						$stockMinimoTotal+=$stockMinimo;
						$stockMaximoTotal+=$stockMaximo;
					}

					if (self::$_fastRecalculate==false) {

						//Almacen?
						$almacenName = 'NO EXISTE ALMACEN';
						if ($almacen) {
							$almacenName = $almacen->getNomAlmacen();
						}

						$contents.= '<tr>
							<td align="left">'.$numeroAlmacen.' / '.$almacenName.'</td>
							<td align="right">'.Currency::number($s['saldo'], 2).'</td>
							<td align="right">'.Currency::number($s['costo'], 2).'</td>
							<td align="right">'.Currency::number($stockMinimo, 2).'</td>
							<td align="right">'.Currency::number($stockMaximo, 2).'</td>
						</tr>';
					}
					$saldoTotal+=$s['saldo'];
					$costoTotal+=$s['costo'];
					$saldo = $Saldos->findFirst("almacen='$numeroAlmacen' AND item='{$inve->getItem()}' AND ano_mes=0");
					if ($saldo==false) {
						$saldo = new Saldos();
						$saldo->setTransaction($transaction);
						$saldo->setAlmacen($numeroAlmacen);
						$saldo->setItem($inve->getItem());
						$saldo->setAnoMes(0);
					}
					$saldo->setSaldo($s['saldo']);
					$saldo->setCosto($s['costo']);
					if ($saldo->save()==false) {
						foreach ($saldo->getMessages() as $message) {
							$contents.='z.'.$message->getMessage();
						}
					}
				}
			}
			if (self::$_fastRecalculate==false) {
				$contents.= '<tr>
					<td align="right"><b class="kardex">Consolidado</b></td>
					<td align="right"><b class="kardex">'. Currency::number($saldoTotal, 2). '</b></td>
					<td align="right"><b class="kardex">'. Currency::number($costoTotal, 2). '</b></td>
					<td align="right">'. Currency::number($stockMinimoTotal, 2). '</td>
					<td align="right">'. Currency::number($stockMaximoTotal, 2). '</td>
				</tr></table>';
			}

			if (count($saldosc)) {

				if (self::$_fastRecalculate==false) {
					$contents.= '<br/><b class="kardex">Histórico Referencia</b>';
					$contents.= '<table class="kardex" cellspacing="0">
						<thead>
							<tr>
								<th>Periodo</th>
								<th>Saldo</th>
								<th>Costo</th>
								<th>Saldo Almacenes</th>
								<th>Costo Almacenes</th>
							</tr>';
				}
				$periodoAnterior = '0';

				foreach ($saldosc as $periodo => $saldoc) {

					$saldoPeriodo = $Saldos->findFirst("almacen='$codigoAlmacen' AND item='{$inve->getItem()}' AND ano_mes='$periodo'");
					if ($saldoPeriodo==false) {
						$saldo = new Saldos();
						$saldo->setTransaction($transaction);
						$saldo->setItem($inve->getItem());
						$saldo->setAnoMes($periodo);
						$saldo->setAlmacen($codigoAlmacen);
					}

					if ($saldoc['saldo'] == 0) {
						$saldoc['costo'] = 0;
					}

					$saldo->setSaldo($saldoc['saldo']);
					$saldo->setCosto($saldoc['costo']);
					if ($saldo->save()==false) {
						foreach ($saldo->getMessages() as $message) {
							//$contents.='1.'.$message->getMessage();
						}
					}

					//Recalcular el almacen=0
					$saldoPeriodo = 0;
					$costoPeriodo = 0;
					$saldosPeriodos = $Saldos->find("item='{$inve->getItem()}' AND ano_mes='$periodo' AND almacen<>0");
					foreach ($saldosPeriodos as $saldo) {
						$saldoPeriodo+=$saldo->getSaldo();
						$costoPeriodo+=$saldo->getCosto();
					}

					$saldoAlmacenCero = $Saldos->findFirst("almacen='0' AND item='{$inve->getItem()}' AND ano_mes='$periodo'");
					if ($saldoAlmacenCero==false) {
						$saldoAlmacenCero = new Saldos();
						$saldoAlmacenCero->setTransaction($transaction);
						$saldoAlmacenCero->setItem($inve->getItem());
						$saldoAlmacenCero->setAnoMes($periodo);
						$saldoAlmacenCero->setAlmacen(0);
					}
					$saldoAlmacenCero->setSaldo($saldoPeriodo);
					$saldoAlmacenCero->setCosto($costoPeriodo);
					if ($saldoAlmacenCero->save()==false) {
						foreach ($saldoAlmacenCero->getMessages() as $message) {
							//$contents.='2.'.$message->getMessage();
						}
					}

					if (self::$_fastRecalculate==false) {
						$contents.= '<tr>
							<td>'. $periodo. '</td>
							<td align="right">'. Currency::number($saldoc['saldo'], 2). '</td>
							<td align="right">'. Currency::number($saldoc['costo'], 2). '</td>
							<td align="right">'. Currency::number($saldoPeriodo, 2). '</td>
							<td align="right">'. Currency::number($costoPeriodo, 2). '</td>
						</tr>';
					}
				}
				$contents.= '</table>';
			} else {
				$contents.= '<br><span class="kardex">La referencia no tiene histórico</span>';
			}

			$almacenes = array_values($almacenes);
			$almacenes[] = '0';

			$periodoMayor = $fechaCierre->getPeriod();
			$periodoMenor = $Saldos->minimum(array("ano_mes", "conditions" => "item='{$inve->getItem()}' AND ano_mes<>0"));
			if ($periodoMenor != '') {

				if ($periodoMenor <= $periodoMayor) {

					$anoMenor = substr($periodoMenor, 0, 4);
					$mesMenor = substr($periodoMenor, 4, 2);

					while ($periodoMenor != $periodoMayor) {

						$mesMenor++;
						if ($mesMenor > 12) {
							$anoMenor++;
							$mesMenor = 1;
						}

						$periodoMenor = $anoMenor.sprintf('%02s', $mesMenor);
						foreach ($almacenes as $numeroAlmacen) {
							$saldoPeriodo = $Saldos->findFirst("item='{$inve->getItem()}' AND almacen='$numeroAlmacen' AND ano_mes='$periodoMenor'");
							if ($saldoPeriodo == false) {
								$saldoPeriodoAnterior = $Saldos->findFirst("item='{$inve->getItem()}' AND almacen='$numeroAlmacen' AND ano_mes<'$periodoMenor' AND ano_mes>0", "order: ano_mes DESC");
								if ($saldoPeriodoAnterior != false) {
									$saldoPeriodo = new Saldos();
									$saldoPeriodo->setTransaction($transaction);
									$saldoPeriodo->setItem($inve->getItem());
									$saldoPeriodo->setAlmacen($numeroAlmacen);
									$saldoPeriodo->setAnoMes($periodoMenor);
									$saldoPeriodo->setSaldo($saldoPeriodoAnterior->getSaldo());
									$saldoPeriodo->setCosto($saldoPeriodoAnterior->getCosto());
									if ($saldoPeriodo->save() == false) {
										foreach ($saldoPeriodo->getMessages() as $message) {
											$contents.='5.'.$message->getMessage();
										}
									}
								}
							}
						}
					}
				}
			}

			$inve->setTransaction($transaction);
			$inve->setSaldoActual($saldoTotal);
			$inve->setCostoActual($costoTotal);
			if ($inve->save()==false) {
				foreach ($inve->getMessages() as $message) {
					$contents.='6.'.$message->getMessage();
				}
			}

			if ($externalTransaction==false) {
				$transaction->commit();
			}
		}
		catch(DbLockAdquisitionException $e) {
			Flash::error('La base de datos está bloqueda por otro usuario, espere un momento y vuelva a intentar el proceso');
		}
		catch(DateException $e) {
			throw new TaticoException($e->getMessage());
		}
		catch(TransactionFailed $e) {

		}

		ActiveRecord::disableEvents(false);
		ActiveRecord::refreshPersistance(true);

		if (isset($contents)) {
			return $contents;
		}
	}

}
