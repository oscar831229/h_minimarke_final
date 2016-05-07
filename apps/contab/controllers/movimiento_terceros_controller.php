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
 * Movimiento_TercerosController
 *
 * Listado de Movimiento de Terceros
 *
 */
class Movimiento_TercerosController extends ApplicationController
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

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try {
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if ($fechaInicial == '' || $fechaFinal == '') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del movimiento de terceros'
				);
			}

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nitInicial = $this->getPostParam('nitInicial', 'terceros');
			$nitFinal = $this->getPostParam('nitFinal', 'terceros');

			$orden = $this->getPostParam('orden', 'onechar');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('MOVIMIENTO DE CUENTAS POR TERCEROS', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2), false, true);
			$report->setDocumentTitle('Movimiento de Cuentas Por Terceros');
			$report->setColumnHeaders(array(
				'NO. DOCUMENTO',
				'NOMBRE',
				'FECHA',
				'COMPROBANTE',
				'DESCRIPCIÓN',
				//'SALDO ANTERIOR',
				'DEBITOS',
				'CRÉDITOS',
				'SALDO'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			));

			$leftColumnBold = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$rightColumn = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			));

			$rightColumnBold = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setColumnStyle(array(0, 1, 2, 3, 4), $leftColumn);

			$report->setColumnStyle(array(5, 6, 7, 8), $rightColumn);
			$report->setColumnFormat(array(5, 6, 7, 8), $numberFormat);

			$columnaSaldo = new ReportRawColumn(array(
				'value' => 'SALDO ANTERIOR',
				'style' => $rightColumn
			));

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CUENTA',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaTotalNit = new ReportRawColumn(array(
				'value' => 'TOTAL NIT',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$report->start(true);

			$empresa = $this->Empresa->findFirst();
			$fecInicial = new Date($fechaInicial);
			$fechaIn = Date::getFirstDayOfMonth($fecInicial->getMonth(), $fecInicial->getYear());
			if (Date::isLater($fechaIn, $empresa->getFCierrec())) {
				$fechaIn = $empresa->getFCierrec();
				$periodoAnterior = $fechaIn->getPeriod();
			} else {
				$fechaPasada = new Date($fechaIn);
				$fechaPasada->diffMonths(1);
				$periodoAnterior = $fechaPasada->getPeriod();
			}

			$fechaIn = (string) $fechaIn;

			$nitAnterior = '';
			$cuentaAnterior = '';

			$orden = 'C';

			$conditions = array();
			if ($cuentaInicial && $cuentaFinal) {
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}

			$cuentasMovimiento = array();
			$conditionsStr = '';
			if (count($conditions)) {
				$conditionsStr = join(' AND ', $conditions);
				$cuentas = $this->Cuentas->find($conditionsStr);
			} else {
				$cuentas = $this->Cuentas->find();
			}

			foreach ($cuentas as $cuenta) {
				$codigoCuenta = trim($cuenta->getCuenta());
				$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
				if ($nitInicial != '' && $nitFinal != '') {
					if ($nitInicial == $nitFinal) {
						$conditions.= " AND nit='$nitInicial'";
					} else {
						$conditions.= " AND nit>='$nitInicial' AND nit<='$nitFinal'";
					}
				}
				$movis = $this->Movi->find(array(
					$conditions,
					'columns' => 'cuenta,nit',
					'group' => 'cuenta,nit',
					'order' => 'cuenta,nit'
				));
				foreach ($movis as $movi) {
					$codigoCuentaMovi = trim($movi->getCuenta());
					$codigoNitMovi = trim($movi->getNit());
					if ($orden=='C') {
						if (!isset($cuentasMovimiento[$codigoCuentaMovi][$codigoNitMovi])) {
							$cuentasMovimiento[$codigoCuentaMovi][$codigoNitMovi] = true;
						}
					} else {
						if (!isset($cuentasMovimiento[$codigoNitMovi][$codigoCuentaMovi])) {
							$cuentasMovimiento[$codigoNitMovi][$codigoCuentaMovi] = true;
						}
					}
					unset($codigoCuentaMovi, $codigoNitMovi);
				}
				unset($movis, $codigoCuenta);
			}

			$conditions = array();
			$conditions[] = "(ano_mes=0 OR ano_mes='$periodoAnterior') AND (debe<>0 OR haber<>0 OR saldo<>0)";
			if ($cuentaInicial != '' && $cuentaFinal != '') {
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if ($nitInicial!=''&&$nitFinal!='') {
				if ($nitInicial==$nitFinal) {
					$conditions[] = "nit='$nitInicial'";
				} else {
					$conditions[] = "nit>='$nitInicial' AND nit<='$nitFinal'";
				}
			}
			$conditions = join(' AND ', $conditions);

			if ($orden == 'C') {
				//CUENTAS
				$saldosns = $this->Saldosn->find(array($conditions, 'column' => 'cuenta,nit', 'group' => 'cuenta,nit', 'order' => 'cuenta,nit'));
			} else {
				//DOCUMENTOS
				$saldosns = $this->Saldosn->find(array($conditions, 'column' => 'cuenta,nit', 'group' => 'nit,cuenta', 'order' => 'nit,cuenta'));
			}

			foreach ($saldosns as $saldosn) {
				$codigoCuentaSaldosn = trim($saldosn->getCuenta());
				$codigoNitSaldosn = trim($saldosn->getNit());

				if ($orden=='C') {
					if (!isset($cuentasMovimiento[$codigoCuentaSaldosn])) {
						$cuentasMovimiento[$codigoCuentaSaldosn] = array();
					}
					if (!isset($cuentasMovimiento[$codigoCuentaSaldosn][$codigoNitSaldosn])) {
						$cuentasMovimiento[$codigoCuentaSaldosn][$codigoNitSaldosn] = true;
					}

				} else {
					if (!isset($cuentasMovimiento[$codigoNitSaldosn])) {
						$cuentasMovimiento[$codigoNitSaldosn] = array();
					}
					if (!isset($cuentasMovimiento[$codigoNitSaldosn][$codigoCuentaSaldosn])) {
						$cuentasMovimiento[$codigoNitSaldosn][$codigoCuentaSaldosn] = true;
					}
				}
				unset($codigoCuentaSaldosn, $codigoNitSaldosn);
			}

			ksort($cuentasMovimiento, SORT_STRING);

			#print_r($cuentasMovimiento);
			foreach ($cuentasMovimiento as $codigoCuenta => $movimientoCuentas) {

				$totalDebitosCuenta = 0;
				$totalCreditosCuenta = 0;
				$totalSaldoCuenta = 0;

				$cuenta = BackCacher::getCuenta($codigoCuenta);
				if ($cuenta==false) {
					$cuenta = new Cuentas();
					$cuenta->setCuenta($codigoCuenta);
					$cuenta->setNombre('NO EXISTE CUENTA');
				}

				$columnaCuenta = new ReportRawColumn(array(
					'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
					'style' => $leftColumnBold,
					'span' => 9
				));
				$report->addRawRow(array($columnaCuenta));

				foreach ($movimientoCuentas as $nitTercero => $movimientoNit) {

					$nitTercero = trim($nitTercero);
					$totalDebitosNit = 0;
					$totalCreditosNit = 0;
					$totalSaldoNit = 0;

					$saldoCuenta = 0;
					$conditionsSadosn = "cuenta='$codigoCuenta' AND nit='$nitTercero' AND ano_mes='$periodoAnterior'";

					$saldosnAnterior = $this->Saldosn->findFirst($conditionsSadosn);
					if ($saldosnAnterior!=false) {
						$saldoCuenta = $saldosnAnterior->getSaldo();
					}
					unset($saldosnAnterior);

					$conditions = "cuenta='$codigoCuenta' AND nit='$nitTercero' AND fecha>'$fechaIn' AND fecha<'$fechaInicial'";
					$movis = $this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor'));
					foreach ($movis as $movi) {
						if ($movi->getDebCre() == 'D' || $movi->getDebCre() == '0') {
							$saldoCuenta+=$movi->getValor();
						} else {
							$saldoCuenta-=$movi->getValor();
						}
						unset($movi);
					}
					unset($conditions);
					unset($movis);

					$nit = BackCacher::getTercero($nitTercero);
					if ($nit==false) {
						$nitNombre = 'NO EXISTE TERCERO';
					} else {
						$nitNombre = $nit->getNombre();
					}

					$conditions = "cuenta='$codigoCuenta' AND nit='$nitTercero' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
					$movis = $this->Movi->find(array($conditions, 'order' => 'fecha,comprob,numero', 'columns' => 'fecha,comprob,numero,descripcion,valor,deb_cre'));
					if (count($movis) > 0 || $saldoCuenta != 0) {

						$report->addRow(array(
							$nitTercero,
							$nitNombre,
							'',
							'',
							'SALDO ANTERIOR',
							'',
							'',
							$saldoCuenta
						));

						$totalSaldoCuenta+=$saldoCuenta;
						$totalSaldoNit+=$saldoCuenta;

						foreach ($movis as $movi) {
							$row = array(
								'',
								'',
								$movi->getFecha(),
								$movi->getComprob().'-'.$movi->getNumero(),
								$movi->getDescripcion()
							);
							if ($movi->getDebCre()=='D'||$movi->getDebCre()=='0') {
								$saldoCuenta+=$movi->getValor();
								$totalDebitosCuenta+=$movi->getValor();
								$totalDebitosNit+=$movi->getValor();
								$totalSaldoCuenta+=$movi->getValor();
								$totalSaldoNit+=$movi->getValor();
								$row[] = $movi->getValor();
								$row[] = 0;
							} else {
								$saldoCuenta-=$movi->getValor();
								$totalCreditosCuenta+=$movi->getValor();
								$totalCreditosNit+=$movi->getValor();
								$totalSaldoCuenta-=$movi->getValor();
								$totalSaldoNit-=$movi->getValor();
								$row[] = 0;
								$row[] = $movi->getValor();
							}
							$row[] = $saldoCuenta;
							$report->addRow($row);
							unset($movi);
						}

						$columnaTotalDebitos = new ReportRawColumn(array(
							'value' => $totalDebitosNit,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalCreditos = new ReportRawColumn(array(
							'value' => $totalCreditosNit,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalSaldo = new ReportRawColumn(array(
							'value' => $totalSaldoNit,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$report->addRawRow(array(
							$columnaTotalNit,
							$columnaTotalDebitos,
							$columnaTotalCreditos,
							$columnaTotalSaldo
						));

						$columnaEspacio = new ReportRawColumn(array(
							'value' => '',
							'style' => $leftColumnBold,
							'span' => 9
						));
						$report->addRawRow(array($columnaEspacio));

						unset($nitNombre);
					}
					unset($saldoCuenta);
				}

				$columnaTotalDebitos = new ReportRawColumn(array(
					'value' => $totalDebitosCuenta,
					'style' => $rightColumn,
					'format' => $numberFormat
				));

				$columnaTotalCreditos = new ReportRawColumn(array(
					'value' => $totalCreditosCuenta,
					'style' => $rightColumn,
					'format' => $numberFormat
				));

				$columnaTotalSaldo = new ReportRawColumn(array(
					'value' => $totalSaldoCuenta,
					'style' => $rightColumn,
					'format' => $numberFormat
				));

				$report->addRawRow(array(
					$columnaTotalCuenta,
					$columnaTotalDebitos,
					$columnaTotalCreditos,
					$columnaTotalSaldo
				));
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/movimiento-terceros');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}

