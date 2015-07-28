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
 * Ejecucion_PresController
 *
 * Ejecución del Presupuesto
 *
 */
class Ejecucion_PresController extends ApplicationController {

	public function initialize() {
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction() {
		Tag::displayTo('fechaInicial', Date::getCurrentDate());
		Tag::displayTo('fechaFinal', Date::getCurrentDate());
		$this->setParamToView('centros', $this->Centros->find());
		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction() {

		$this->setResponse('json');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		if ($fechaInicial==''||$fechaFinal=='') {
			return array(
				'status' => 'FAILED',
				'message' => 'Indique las fechas inicial y final del reporte'
			);
		}

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		$centroInicial = $this->getPostParam('centroInicial', 'int');
		$centroFinal = $this->getPostParam('centroFinal', 'int');

		$fechaInicial = new Date($fechaInicial);
		$periodoInicial = $fechaInicial->getPeriod();

		$fechaFinal = new Date($fechaFinal);
		$periodoFinal = $fechaFinal->getPeriod();

		$conditions = array("ano_mes>='$periodoInicial' AND ano_mes<='$periodoFinal' AND (pres!=0 OR saldo!=0) AND ano_mes>0");
		if ($cuentaInicial!=''&&$cuentaFinal!='') {
			$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
		}
		if ($centroInicial!=0&&$centroFinal!=0) {
			$conditions[] = "centro_costo>='$centroInicial' AND centro_costo<='$centroFinal'";
		}

		$periodos = array();
		$presupuesto = array();
		$conditionsStr = join(' AND ', $conditions);

		foreach($this->Saldosp->find($conditionsStr) as $saldop) {
			$anoMes = $saldop->getAnoMes();
			$codigoCuenta = $saldop->getCuenta();
			$centroCosto = $saldop->getCentroCosto();
			if (!isset($presupuesto[$codigoCuenta][$centroCosto][$anoMes])) {
				$presupuesto[$codigoCuenta][$centroCosto][$anoMes] = array(
					'pres' => $saldop->getPres(),
					'ejec' => $saldop->getSaldo(),
				);
			}
			$periodos[$anoMes] = true;
		}

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

  		$titulo = new ReportText('EJECUCIÓN DEL PRESUPUESTO', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

 		$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
			'fontSize' => 11,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
 		));

  		$report->setHeader(array($titulo, $titulo2));
  		$report->setDocumentTitle('Ejecución del Presupuesto');

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));

		$report->setColumnStyle(0, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));
		$report->setColumnStyle(1, $leftColumn);

		$headerStyle = new ReportStyle(array(
			'textAlign' => 'center',
  			'fontSize' => 11,
  			'fontWeight' => 'bold'
		));

		$rightColumn = new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		));

  		$headers = array();
		$headers[] = new ReportRawColumn(array(
			'value' => '',
			'style' => $headerStyle
		));
		$periodos = array_keys($periodos);
  		foreach ($periodos as $periodo) {
  			$ano = substr($periodo, 0, 4);
  			$mes = substr($periodo, 4, 2);
  			try {
  				$fecha = Date::fromParts($ano, $mes, 1);
  			} catch (Exception $e) {
  				throw new Exception("Error al procesar ano_mes='$ano, $mes'", 1);
  			}
  			$headers[] = new ReportRawColumn(array(
				'value' => i18n::strtoupper($fecha->getMonthName()).' '.$ano,
				'style' => $headerStyle,
				'span' => 4
			));
		}

		$numberColumns = 1+count($periodos)*4;
  		$report->setColumnStyle(range(1, $numberColumns-1), $rightColumn);
  		$report->setColumnFormat(range(1, $numberColumns-1), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		)));

		$report->start(true);

		$headers2 = array();
		$headers2[] = new ReportRawColumn(array(
			'value' => 'CENTRO DE COSTO',
			'style' => $headerStyle
		));
		foreach ($periodos as $periodo) {
			$headers2[] = new ReportRawColumn(array(
				'value' => 'PRESUPUESTADO',
				'style' => $headerStyle,
			));
			$headers2[] = new ReportRawColumn(array(
				'value' => 'EJECUTADO',
				'style' => $headerStyle,
			));
			$headers2[] = new ReportRawColumn(array(
				'value' => 'DIFERENCIA',
				'style' => $headerStyle,
			));
			$headers2[] = new ReportRawColumn(array(
				'value' => 'PORCENTAJE',
				'style' => $headerStyle,
			));
		}

		$totalCentro = new ReportRawColumn(array(
			'value' => 'TOTAL CENTRO DE COSTO',
			'style' => $rightColumn
		));

		foreach ($presupuesto as $codigoCuenta => $presCentroCosto) {
			$totales = array();
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			$columnaCuenta = new ReportRawColumn(array(
				'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
				'style' => $leftColumn,
				'span' => $numberColumns
			));
			$report->addRawRow(array($columnaCuenta));
			$report->addRawRow($headers);
			$report->addRawRow($headers2);
			foreach ($presCentroCosto as $centroCosto => $presAnoMes) {
				$centro = BackCacher::getCentro($centroCosto);
				$row = array($centro->getNomCentro());
				foreach ($periodos as $periodo) {
					//throw new Exception(print_r($periodo,true));

					if (!isset($presAnoMes[$periodo])) {
						$row[] = 0;
						$row[] = 0;
						$row[] = 0;
					} else {
						$row[] = $presAnoMes[$periodo]['pres'];
						$row[] = $presAnoMes[$periodo]['ejec'];
						$row[] = ($presAnoMes[$periodo]['pres']-$presAnoMes[$periodo]['ejec']);
						if (!isset($totales[$periodo])) {
							$totales[$periodo] = array(
								'pres' => $presAnoMes[$periodo]['pres'],
								'ejec' => $presAnoMes[$periodo]['ejec']
							);
						} else {
							$totales[$periodo]['pres']+=$presAnoMes[$periodo]['pres'];
							$totales[$periodo]['ejec']+=$presAnoMes[$periodo]['ejec'];
						}
					}
				}

				$report->addRow($row);
			}
			$filaTotal = array();
			$filaTotal[] = $totalCentro;
			foreach ($periodos as $periodo) {
				if (isset($totales[$periodo])) {
					$valorPres = $totales[$periodo]['pres'];
					$valorEjec = $totales[$periodo]['ejec'];
				} else {
					$valorPres = 0;
					$valorEjec = 0;
				}
				$filaTotal[] = new ReportRawColumn(array(
					'value' => $valorPres,
					'style' => $rightColumn,
				));
				$filaTotal[] = new ReportRawColumn(array(
					'value' => $valorEjec,
					'style' => $rightColumn,
				));
				$filaTotal[] = new ReportRawColumn(array(
					'value' => $valorPres-$valorEjec,
					'style' => $rightColumn,
				));
				if ($valorPres>0) {
					$filaTotal[] = new ReportRawColumn(array(
						'value' => Currency::number($valorEjec/$valorPres*100, 2),
						'style' => $rightColumn,
					));
				} else {
					$filaTotal[] = new ReportRawColumn(array(
						'value' => 0,
						'style' => $rightColumn,
					));
				}
			}
			$report->addRawRow($filaTotal);
			unset($totales);
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/pres');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

}