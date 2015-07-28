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
 * Balance_GeneralController
 *
 * Balance General Comparativo
 *
 */
class Balance_GeneralController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		//$fechaCierre->addDays(1);

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		Tag::displayTo('ano', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');

	}

	public function generarAction(){

		$this->setResponse('json');

		try
		{

			//$_POST['ano'] = '2010';
			//$_POST['mes'] = '8';

			$ano = $this->getPostParam('ano', 'int');
			if($ano==0){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique el año del reporte'
				);
			}

			$mes = $this->getPostParam('mes', 'int');
			if($mes==0){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique el mes del reporte'
				);
			}

			$fechaInicial = Date::fromParts($ano, $mes, 1);
			$fechaFinal = Date::fromParts($ano, $mes, 1);
			$fechaFinal->toLastDayOfMonth();

			$periodo = $fechaInicial->getPeriod();

			$fechaAnterior = clone $fechaInicial;
			$fechaAnterior->diffMonths(1);
			$periodoAnterior = $fechaAnterior->getPeriod();

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('BALANCE GENERAL COMPARATIVO', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('A '.$fechaFinal->getLocaleDate('long'), array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$centerColumn = new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			));

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

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setHeader(array($titulo, $titulo2),false);//false no muestra fecha de impresion
			$report->setDocumentTitle('Balance General Comparativo');

			$report->start(true);

			$empresa = $this->Empresa->findFirst();
			$fechaCierre = $empresa->getFCierrec();

			$periodoAnterior = $fechaCierre->getPeriod();
			$fechaCierre = (string) $fechaCierre;

			$empresa = $this->Empresa->findFirst();
			$periodoPasado = $empresa->getFCierrec()->getPeriod();

			$informe = array();
			foreach($this->Cuentas->find("es_auxiliar='S'") as $cuenta){
				$codigoCuenta = $cuenta->getCuenta();
				$mayor = substr($codigoCuenta, 0, 2);
				if(!isset($informe[$mayor])){
					$informe[$mayor] = array(
						'saldoAnterior' => 0,
						'saldo' => 0
					);
				}

				$saldoc = $this->Saldosc->findFirst("ano_mes='$periodoPasado' AND cuenta='$codigoCuenta'");
				if($saldoc!=false){
					$informe[$mayor]['saldoAnterior']+=$saldoc->getSaldo();
				}

				$conditions = "cuenta='$codigoCuenta' AND fecha>'$fechaCierre' AND fecha<'$fechaInicial'";
				foreach($this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor')) as $movi){
					if($movi->getDebCre()=='D'){
						$informe[$mayor]['saldoAnterior']+=$movi->getValor();
					} else {
						$informe[$mayor]['saldoAnterior']-=$movi->getValor();
					}
				}
				unset($conditions);

				$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
				foreach($this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor')) as $movi){
					if($movi->getDebCre()=='D'){
						$informe[$mayor]['saldo']+=$movi->getValor();
					} else {
						$informe[$mayor]['saldo']-=$movi->getValor();
					}
				}
				unset($conditions);

				unset($codigoCuenta);
				unset($mayor);

			}

			$columnaActivo = new ReportRawColumn(array(
				'value' => 'ACTIVO',
				'style' => $centerColumn
			));

			$columnaPasivo = new ReportRawColumn(array(
				'value' => 'PASIVO',
				'style' => $centerColumn
			));

			$columnaNota = new ReportRawColumn(array(
				'value' => 'Nota No.',
				'style' => $centerColumn
			));

			$columnaBlanco = new ReportRawColumn(array(
				'value' => '',
				'span' => 4,
			));

			$mesActual = new ReportRawColumn(array(
				'value' => i18n::strtoupper($fechaInicial->getMonthName()).' '.$fechaInicial->getYear(),
				'style' => $centerColumn
			));

			$mesPasado = new ReportRawColumn(array(
				'value' => i18n::strtoupper($fechaAnterior->getMonthName()).' '.$fechaAnterior->getYear(),
				'style' => $centerColumn
			));

			$report->addRawRow(array(
				$columnaActivo,
				$columnaNota,
				$mesPasado,
				$mesActual,
				$columnaPasivo,
				$columnaNota,
				$mesPasado,
				$mesActual
			));

			$columnaCorriente = new ReportRawColumn(array(
				'value' => 'Corriente',
				'style' => $leftColumnBold,
				'span' => 4
			));

			$report->addRawRow(array(
				$columnaCorriente,
				$columnaCorriente
			));

			$totales = array();
			$mayores = array(
				array('',   '21'),
				array('11', '22'),
				array('13', '23'),
				array('14', '24'),
				array('17', '25')
			);
			foreach($mayores as $linea){
				$row = array();
				foreach($linea as $mayor){
					if($mayor==''){
						$row[] = $columnaBlanco;
					} else {
						$cuenta = BackCacher::getCuenta($mayor);
						$row[] = new ReportRawColumn(array(
							'value' => $cuenta->getNombre(),
							'style' => $leftColumn
						));
						$row[] = new ReportRawColumn(array(
							'value' => '',
							'style' => $leftColumn
						));
						if(isset($informe[$mayor])){
							$tipo = substr($mayor, 0, 1);
							$row[] = new ReportRawColumn(array(
								'value' => abs($informe[$mayor]['saldoAnterior']),
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							$row[] = new ReportRawColumn(array(
								'value' => abs($informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']),
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							if(!isset($totales[$tipo])){
								$totales[$tipo] = array(
									'saldoAnterior' => $informe[$mayor]['saldoAnterior'],
									'saldo' => $informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']
								);
							} else {
								$totales[$tipo]['saldoAnterior']+=$informe[$mayor]['saldoAnterior'];
								$totales[$tipo]['saldo']+=($informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']);
							}
						} else {
							$row[] = new ReportRawColumn(array(
								'value' => 0,
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							$row[] = new ReportRawColumn(array(
								'value' => 0,
								'style' => $rightColumn,
								'format' => $numberFormat
							));
						}
					}
				}
				$report->addRawRow($row);
			}

			$totalActivoAnterior = 0;
			$totalActivoPresente = 0;
			$totalPasivoAnterior = 0;
			$totalPasivoPresente = 0;

			$totalActivoPasado = new ReportRawColumn(array(
				'value' => abs($totales['1']['saldoAnterior']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalActivoAnterior = abs($totales['1']['saldoAnterior']);

			$totalActivoActual = new ReportRawColumn(array(
				'value' => abs($totales['1']['saldo']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalActivoPresente = abs($totales['1']['saldo']);

			$totalPasivoPasado = new ReportRawColumn(array(
				'value' => abs($totales['2']['saldoAnterior']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalPasivoAnterior = abs($totales['2']['saldoAnterior']);

			$totalPasivoActual = new ReportRawColumn(array(
				'value' => abs($totales['2']['saldo']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalPasivoPresente = abs($totales['2']['saldo']);

			$columnaTotalCorriente = new ReportRawColumn(array(
				'value' => 'Total Corriente',
				'style' => $leftColumnBold,
				'span' => 2
			));

			$report->addRawRow(array(
				$columnaTotalCorriente,
				$totalActivoPasado,
				$totalActivoActual,
				$columnaTotalCorriente,
				$totalPasivoPasado,
				$totalPasivoActual
			));

			$columnaNoCorriente = new ReportRawColumn(array(
				'value' => 'No Corriente',
				'style' => $leftColumnBold,
				'span' => 4
			));

			$report->addRawRow(array(
				$columnaNoCorriente,
				$columnaNoCorriente
			));

			$totales = array();
			$mayores = array(
				array('',   '26'),
				array('12', '28')
			);
			foreach($mayores as $linea){
				$row = array();
				foreach($linea as $mayor){
					if($mayor==''){
						$row[] = $columnaBlanco;
					} else {
						$cuenta = BackCacher::getCuenta($mayor);
						$row[] = new ReportRawColumn(array(
							'value' => $cuenta->getNombre(),
							'style' => $leftColumn
						));
						$row[] = new ReportRawColumn(array(
							'value' => '',
							'style' => $leftColumn
						));
						if(isset($informe[$mayor])){
							$tipo = substr($mayor, 0, 1);
							$row[] = new ReportRawColumn(array(
								'value' => abs($informe[$mayor]['saldoAnterior']),
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							$row[] = new ReportRawColumn(array(
								'value' => abs($informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']),
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							if(!isset($totales[$tipo])){
								$totales[$tipo] = array(
									'saldoAnterior' => $informe[$mayor]['saldoAnterior'],
									'saldo' => $informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']
								);
							} else {
								$totales[$tipo]['saldoAnterior']+=$informe[$mayor]['saldoAnterior'];
								$totales[$tipo]['saldo']+=($informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']);
							}
						} else {
							$row[] = new ReportRawColumn(array(
								'value' => 0,
								'style' => $rightColumn,
								'format' => $numberFormat
							));
							$row[] = new ReportRawColumn(array(
								'value' => 0,
								'style' => $rightColumn,
								'format' => $numberFormat
							));
						}
					}
				}
				$report->addRawRow($row);
			}

			$totalActivoPasado = new ReportRawColumn(array(
				'value' => abs($totales['1']['saldoAnterior']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalActivoAnterior+=abs($totales['1']['saldoAnterior']);

			$totalActivoActual = new ReportRawColumn(array(
				'value' => abs($totales['1']['saldo']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalActivoPresente+=abs($totales['1']['saldo']);

			$totalPasivoPasado = new ReportRawColumn(array(
				'value' => abs($totales['2']['saldoAnterior']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalPasivoAnterior+=abs($totales['2']['saldoAnterior']);

			$totalPasivoActual = new ReportRawColumn(array(
				'value' => abs($totales['2']['saldo']),
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$totalPasivoPresente+=abs($totales['2']['saldo']);

			$columnaTotalNoCorriente = new ReportRawColumn(array(
				'value' => 'Total No Corriente',
				'style' => $leftColumnBold,
				'span' => 2
			));

			$report->addRawRow(array(
				$columnaTotalNoCorriente,
				$totalActivoPasado,
				$totalActivoActual,
				$columnaTotalNoCorriente,
				$totalPasivoPasado,
				$totalPasivoActual
			));

			$columnaTotalPasivo = new ReportRawColumn(array(
				'value' => 'TOTAL PASIVO',
				'style' => $leftColumnBold,
				'span' => 2
			));

			$totalPasivoGeneralPasado = new ReportRawColumn(array(
				'value' => $totalPasivoAnterior,
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$totalPasivoGeneralPresente = new ReportRawColumn(array(
				'value' => $totalPasivoPresente,
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$report->addRawRow(array(
				$columnaBlanco,
				$columnaTotalPasivo,
				$totalPasivoGeneralPasado,
				$totalPasivoGeneralPresente
			));

			foreach($informe as $mayor => $valores){
				$tipo = substr($mayor, 0, 1);
				if($tipo>='3'){
					if(!isset($totales[$tipo])){
						$totales[$tipo] = array(
							'saldoAnterior' => $informe[$mayor]['saldoAnterior'],
							'saldo' => $informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']
						);
					} else {
						$totales[$tipo]['saldoAnterior']+=$informe[$mayor]['saldoAnterior'];
						$totales[$tipo]['saldo']+=($informe[$mayor]['saldoAnterior']+$informe[$mayor]['saldo']);
					}
				}
			}

			for($i=3;$i<7;$i++){

				$cuenta = BackCacher::getCuenta($i);

				$columnaTotalPatrimonio = new ReportRawColumn(array(
					'value' => 'TOTAL '.$cuenta->getNombre(),
					'style' => $leftColumnBold,
					'span' => 2
				));

				$totalPatrimonioGeneralPasado = new ReportRawColumn(array(
					'value' => abs($totales[$i]['saldoAnterior']),
					'style' => $rightColumn,
					'format' => $numberFormat
				));

				$totalPatrimonioGeneralPresente = new ReportRawColumn(array(
					'value' => abs($totales[$i]['saldoAnterior']-$totales[$i]['saldo']),
					'style' => $rightColumn,
					'format' => $numberFormat
				));

				$report->addRawRow(array(
					$columnaBlanco,
					$columnaTotalPatrimonio,
					$totalPatrimonioGeneralPasado,
					$totalPatrimonioGeneralPresente
				));

			}

			$columnaTotalActivo = new ReportRawColumn(array(
				'value' => 'TOTAL ACTIVO',
				'style' => $leftColumnBold,
				'span' => 2
			));

			$totalActivoGeneralPasado = new ReportRawColumn(array(
				'value' => $totalActivoAnterior,
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$totalActivoGeneralPresente = new ReportRawColumn(array(
				'value' => $totalActivoPresente,
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$columnaTotalPasivoPatri = new ReportRawColumn(array(
				'value' => 'TOTAL PASIVO MÁS PATRIMONIO',
				'style' => $leftColumnBold,
				'span' => 2
			));

			$totalPasivoPatriGeneralPasado = new ReportRawColumn(array(
				'value' => ($totalPasivoAnterior+abs($totales['3']['saldoAnterior'])),
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$totalPasivoPatriGeneralPresente = new ReportRawColumn(array(
				'value' => ($totalPasivoPresente+abs($totales['3']['saldoAnterior']+$totales['3']['saldo'])),
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$report->addRawRow(array(
				$columnaTotalActivo,
				$totalActivoGeneralPasado,
				$totalActivoGeneralPresente,
				$columnaTotalPasivoPatri,
				$totalPasivoPatriGeneralPasado,
				$totalPasivoPatriGeneralPresente
			));

			$report->addRawRow(array(
				$columnaBlanco,
				$columnaBlanco
			));

			$columnaCuentasOrden = new ReportRawColumn(array(
				'value' => 'CUENTAS DE ORDEN',
				'style' => $leftColumnBold,
				'span' => 4
			));

			$report->addRawRow(array(
				$columnaCuentasOrden,
				$columnaCuentasOrden
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/balance-general');

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
