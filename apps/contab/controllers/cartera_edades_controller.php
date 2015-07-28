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
 * Cartera_EdadesController
 *
 * Listado de Cartera por Edades
 *
 */
class Cartera_EdadesController extends ApplicationController
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

		Tag::displayTo('fechaLimite', Date::getCurrentDate());
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try {

			$fechaLimite = $this->getPostParam('fechaLimite', 'date');

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nitInicial = $this->getPostParam('nitInicial', 'terceros');
			$nitFinal = $this->getPostParam('nitFinal', 'terceros');

			$orden = $this->getPostParam('orden', 'onechar');

			$conditions = array();
			$conditions[] = "f_emision<='$fechaLimite' AND saldo != 0";
			if ($cuentaInicial!='' && $cuentaFinal != '') {
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if ($nitInicial != '' && $nitFinal != '') {
				$conditions[] = "nit>='$nitInicial' AND nit<='$nitFinal'";
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('CARTERA POR EDADES', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Documentos por Vencimiento Hasta: ' . $fechaLimite, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Cartera Por Edades');
			$report->setColumnHeaders(array(
				'COMPROB.',
				'NÚMERO',
				'TIPO DOC.',
				'NÚMERO DOC.',
				'F. EMISIÓN',
				'VALOR DOC.',
				'SALDO',
				'0 A 30 DÍAS',
				'31 A 60 DÍAS',
				'61 A 90 DÍAS',
				'91 A 120 DÍAS',
				'> 4 MESES',
				'DESDE',
				'DÍAS',
				//'INTERESES'
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

			$report->setColumnStyle(array(0, 2, 4), $leftColumn);
			$report->setColumnStyle(array(1, 3, 5, 6, 7, 8, 9, 10, 11, 13), $rightColumn);
			$report->setColumnFormat(array(5, 6, 7, 8, 9, 10, 11), $numberFormat);

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CUENTA',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaTotalTercero = new ReportRawColumn(array(
				'value' => 'TOTAL TERCERO',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaTotalGeneral = new ReportRawColumn(array(
				'value' => 'TOTAL GENERAL',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaSpacer = new ReportRawColumn(array(
				'value' => '',
				'span' => 3
			));

			$columnaSpacerAll = new ReportRawColumn(array(
				'value' => '',
				'span' => 14
			));

			$report->start(true);

			$cuentaAnterior = '';
			$terceroAnterior = '';

			if ($orden == 'N') {
				$carteras = $this->Cartera->find(array(join(' AND ', $conditions), 'order' => 'nit,cuenta,f_emision'));
			} else {
				$carteras = $this->Cartera->find(array(join(' AND ', $conditions), 'order' => 'cuenta,nit,f_emision'));
			}

			$rows = array();
			foreach ($carteras as $cartera) {

				if (!$cartera->getFVence()) {
					$cartera->setFVence($cartera->setFEmision());
				}
				$codigoCuenta = $cartera->getCuenta();

				$codigoComprob = '';
				$numeroComprob = 0;
				$saldoCartera = 0;
				$conditions = "cuenta='$codigoCuenta' AND nit='{$cartera->getNit()}' AND
				tipo_doc='{$cartera->getTipoDoc()}' AND numero_doc='{$cartera->getNumeroDoc()}' AND
				fecha<='$fechaLimite'";
				$movis = $this->Movi->find($conditions);
				foreach ($movis as $movi) {
					if (substr($codigoCuenta, 0, 1) == '1'){
						if ($movi->getDebCre() == 'D') {
							$codigoComprob = $movi->getComprob();
							$numeroComprob = $movi->getNumero();
							$saldoCartera += $movi->getValor();
						} else {
							$saldoCartera -= abs($movi->getValor());
						}
					} else {
						if ($movi->getDebCre() == 'D') {
							$codigoComprob = $movi->getComprob();
							$numeroComprob = $movi->getNumero();
							$saldoCartera += $movi->getValor();
						} else {
							$saldoCartera -= abs($movi->getValor());
						}
					}
				}

				$saldoCartera = (double) $saldoCartera;
				if ($saldoCartera != 0) {

					$row = array(
						$codigoComprob, //0
						$numeroComprob, //1
						$cartera->getTipoDoc(), //2
						$cartera->getNumeroDoc(), //3
						(string) $cartera->getFEmision(), //4
						(double) $cartera->getValor(), //5
						$saldoCartera, //6
						0, //7
						0, //8
						0, //9
						0, //10
						0, //11
						(string) $cartera->getFEmision(), //12
					);

					if (Date::isLater($cartera->getFEmision(), $fechaLimite)) {
						$jt = (Date::difference($cartera->getFEmision(), $fechaLimite)/30) + 1;
						$row[13] = 0;
					} else {
						$jt = (Date::difference($fechaLimite, $cartera->getFEmision())/30) + 1;
						$row[13] = Date::difference($fechaLimite, $cartera->getFEmision());
					}

					if ($jt < 6) {
						$row[$jt + 6] = $saldoCartera;
					} else {
						$row[11] = $saldoCartera;
					}

					$totalIntereses = 0; //$saldoCartera*$porcen;
					$row[6] += $totalIntereses;
					//$row[14] = $totalIntereses;

					if ($orden == 'N') {
						if (!isset($rows[$cartera->getNit()][$codigoCuenta])) {
							$rows[$cartera->getNit()][$codigoCuenta] = array();
						}
						$rows[$cartera->getNit()][$codigoCuenta][] = $row;
					} else {
						if (!isset($rows[$codigoCuenta][$cartera->getNit()])) {
							$rows[$codigoCuenta][$cartera->getNit()] = array();
						}
						$rows[$codigoCuenta][$cartera->getNit()][] = $row;
					}
				}
			}

			$totalGeneral = array();
			for ($i = 5; $i < 12; $i++) {
				$totalGeneral[$i] = 0;
			}

			if ($orden == 'N') {

				foreach ($rows as $nit => $rowsNit) {

					$tercero = BackCacher::getTercero($nit);
					if ($tercero) {
						$columnaTercero = new ReportRawColumn(array(
							'value' => $tercero->getNit() . ' : ' . $tercero->getNombre(),
							'style' => $leftColumnBold,
							'span'  => 14
						));
					} else {
						$columnaTercero = new ReportRawColumn(array(
							'value' => $cartera->getNit() . ' : NO EXISTE EL TERCERO',
							'style' => $leftColumnBold,
							'span' => 14
						));
					}
					$report->addRawRow(array($columnaTercero));

					$totalTercero = array();
					for ($i = 5; $i < 12; $i++) {
						$totalTercero[$i] = 0;
					}

					foreach ($rowsNit as $cuenta => $rowsCuenta) {

						$cuenta = BackCacher::getCuenta($cuenta);
						if ($cuenta == false) {
							return array(
								'status' => 'FAILED',
								'message' => "No existe la cuenta '$cuenta' en el plan contable"
							);
						}

						$columnaCuenta = new ReportRawColumn(array(
							'value' => 'CUENTA No. ' . $cuenta->getCuenta() . ' : ' . $cuenta->getNombre(),
							'style' => $leftColumnBold,
							'span' => 14
						));
						$report->addRawRow(array($columnaCuenta));

						$totalCuenta = array();
						for ($i = 5; $i < 12; $i++) {
							$totalCuenta[$i] = 0;
						}

						foreach ($rowsCuenta as $row) {

							//Acumular totales por cuenta y tercero
							for ($i = 5; $i < 12; $i++) {
								$totalCuenta[$i] += $row[$i];
								$totalTercero[$i] += $row[$i];
							}

							$report->addRow($row);
						}

						$columnaTotalTercero = new ReportRawColumn(array(
							'value' => $tercero->getNombre() . ' - TOTAL TERCERO',
							'style' => $rightColumnBold,
							'span' => 5
						));

						$totales = array($columnaTotalCuenta);
						for ($i = 5; $i < 12; $i++) {
							$totales[$i - 4] = new ReportRawColumn(array(
								'value' => $totalCuenta[$i],
								'style' => $rightColumn,
								'format' => $numberFormat
							));
						}
						$totales[13] = $columnaSpacer;
						$report->addRawRow($totales);

					}

					$totales = array($columnaTotalTercero);
					for ($i = 5; $i < 12; $i++) {
						$totales[$i - 4] = new ReportRawColumn(array(
							'value' => $totalTercero[$i],
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$totalGeneral[$i] += $totalTercero[$i];
					}
					$totales[13] = $columnaSpacer;
					$report->addRawRow($totales);
				}
			} else {

				foreach ($rows as $cuenta => $rowsCuenta) {

					$cuenta = BackCacher::getCuenta($cuenta);
					if ($cuenta == false) {
						return array(
							'status' => 'FAILED',
							'message' => "No existe la cuenta '$cuenta' en el plan contable"
						);
					}

					$columnaCuenta = new ReportRawColumn(array(
						'value' => 'CUENTA No. ' . $cuenta->getCuenta() . ' : ' . $cuenta->getNombre(),
						'style' => $leftColumnBold,
						'span' => 14
					));
					$report->addRawRow(array($columnaCuenta));

					$totalCuenta = array();
					for ($i = 5; $i < 12; $i++) {
						$totalCuenta[$i] = 0;
					}

					foreach ($rowsCuenta as $nit => $rowsNits) {

						$tercero = BackCacher::getTercero($nit);
						if ($tercero) {
							$columnaTercero = new ReportRawColumn(array(
								'value' => $tercero->getNit() . ' : ' . $tercero->getNombre(),
								'style' => $leftColumnBold,
								'span'  => 14
							));
						} else {
							$columnaTercero = new ReportRawColumn(array(
								'value' => $cartera->getNit() . ' : NO EXISTE EL TERCERO',
								'style' => $leftColumnBold,
								'span' => 14
							));
						}
						$report->addRawRow(array($columnaTercero));

						$totalTercero = array();
						for ($i = 5; $i < 12; $i++) {
							$totalTercero[$i] = 0;
						}

						foreach ($rowsNits as $row) {

							//Acumular totales por cuenta y tercero
							for ($i = 5; $i < 12; $i++) {
								$totalCuenta[$i] += $row[$i];
								$totalTercero[$i] += $row[$i];
							}

							$report->addRow($row);
						}

						$totales = array($columnaTotalTercero);
						for ($i = 5; $i < 12; $i++) {
							$totales[$i - 4] = new ReportRawColumn(array(
								'value' => $totalTercero[$i],
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							$totalGeneral[$i] += $totalTercero[$i];
						}
						$totales[13] = $columnaSpacer;
						$report->addRawRow($totales);

					}

					$totales = array($columnaTotalCuenta);
					for ($i = 5; $i < 12; $i++) {
						$totales[$i - 4] = new ReportRawColumn(array(
							'value' => $totalCuenta[$i],
							'style' => $rightColumn,
							'format' => $numberFormat
						));
					}
					$totales[13] = $columnaSpacer;
					$report->addRawRow($totales);
				}

			}

			$totales = array($columnaSpacerAll);
			$report->addRawRow($totales);

			$totales = array($columnaTotalGeneral);
			for ($i = 5; $i < 12; $i++) {
				$totales[$i - 4] = new ReportRawColumn(array(
					'value' => $totalGeneral[$i],
					'style' => $rightColumn,
					'format' => $numberFormat
				));
			}
			$totales[13] = $columnaSpacer;
			$report->addRawRow($totales);

			/*$report->setTotalizeValues(array(
				2 => $totalSaldoAnterior,
				3 => $totalDebitos,
				4 => $totalCreditos,
				5 => $totalDiferencia,
				6 => $totalSaldoAnterior + $totalDebitos - $totalCreditos
			));*/

			$report->finish();
			$fileName = $report->outputToFile('public/temp/cartera-edades');

			return array(
				'status' => 'OK',
				'file'   => 'temp/' . $fileName
			);

		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'backtrace' => $e->getTrace()
			);
		}
	}

}
