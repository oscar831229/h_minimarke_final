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
 * PyGController
 *
 * Estado de Perdidas y Ganancias
 *
 */
class PyGController extends ApplicationController
{

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
		$fechaCierre->addDays(1);
		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$fecha = new Date();
		Tag::displayTo('ano', $fecha->getYear());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		try
		{
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

			$incluirCodigos = $this->getPostParam('codigos', 'onechar');

			$fechaInicial = Date::fromParts($ano, $mes, 1);
			$fechaFinal = Date::fromParts($ano, $mes, 1);
			$fechaFinal->toLastDayOfMonth();

			$fechaAnterior = clone $fechaInicial;
			$fechaAnterior->diffMonths(1);
			$periodo = $fechaAnterior->getPeriod();

			$informe = array();
			$kontcs = $this->Kontc->find(array("informe='PYG' AND linea>0 AND tipo='N'", "order" => "linea"));
			foreach($kontcs as $kontc){
				$linea = $kontc->getLinea();
				$conditions = "cuenta>='{$kontc->getCuentaI()}' AND cuenta<='{$kontc->getCuentaF()}' AND es_auxiliar='S'";
				foreach($this->Cuentas->find(array($conditions, 'columns' => 'cuenta')) as $cuenta){
					$codigoCuenta = $cuenta->getCuenta();
					if(!isset($informe[$kontc->getLinea()])){
						$informe[$linea] = array(
							'saldoAnterior' => 0,
							'debitos' => 0,
							'creditos' => 0
						);
					}
					$saldoc = $this->Saldosc->findFirst("ano_mes='$periodo' AND cuenta='$codigoCuenta'");
					if($saldoc!=false){
						$informe[$linea]['saldoAnterior']+=$saldoc->getSaldo();
					}
					$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
					foreach($this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor')) as $movi){
						if($movi->getDebCre()=='D'){
							$informe[$linea]['debitos']+=$movi->getValor();
						} else {
							$informe[$linea]['creditos']+=$movi->getValor();
						}
					}
				}
				unset($kontc);
			}

			$kontcs = $this->Kontc->find(array("informe='PYG' AND linea>0 AND tipo='T'", "order" => "linea"));
			foreach($kontcs as $kontc){
				foreach($informe as $linea => $valores){
					if($linea>=$kontc->getCuentaI() && $linea<=$kontc->getCuentaF()){
						if(!isset($informe[$kontc->getLinea()])){
							$informe[$kontc->getLinea()] = $valores;
						} else {
							$informe[$kontc->getLinea()]['saldoAnterior'] += $valores['saldoAnterior'];
							$informe[$kontc->getLinea()]['debitos'] += $valores['debitos'];
							$informe[$kontc->getLinea()]['creditos'] += $valores['creditos'];
						}
					}
				}
				unset($kontc);
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('ESTADO DE PERDIDAS Y GANANCIAS', array(
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
			$report->setDocumentTitle('Estado de Perdidas y Ganancias');

			$headers = array(
				'DESCRIPCIÓN',
				'S. ANTERIOR',
				'DEBITOS',
				'CRÉDITOS',
				'N. SALDO'
			);
			if($incluirCodigos=='S'){
				array_unshift($headers, 'CÓDIGO');
			}
			$report->setColumnHeaders($headers);

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			));

			$rightColumn = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			if($incluirCodigos=='S'){
				$report->setColumnStyle(array(0, 1), $leftColumn);
				$report->setColumnStyle(array(2, 3, 4, 5), $rightColumn);
				$report->setColumnFormat(array(2, 3, 4, 5), $numberFormat);
			} else {
				$report->setColumnStyle(array(0), $leftColumn);
				$report->setColumnStyle(array(1, 2, 3, 4), $rightColumn);
				$report->setColumnFormat(array(1, 2, 3, 4), $numberFormat);
			}

			$report->start(true);

			$kontcs = $this->Kontc->find(array("informe='PYG' AND linea>0", "order" => "linea"));
			foreach($kontcs as $kontc){
				if($kontc->getTipo()=='L'){
					if(trim($kontc->getDescripcion())!=''){
						if($incluirCodigos=='S'){
							$report->addRow(array(
								'',
								$kontc->getDescripcion(),
								'',
								'',
								'',
								''
							));
						} else {
							$report->addRow(array(
								$kontc->getDescripcion(),
								'',
								'',
								'',
								''
							));
						}
					} else {
						if($incluirCodigos=='S'){
							$columnaLinea = new ReportRawColumn(array(
								'value' => '&nbsp;',
								'style' => $leftColumn,
								'span' => 6
							));
						} else {
							$columnaLinea = new ReportRawColumn(array(
								'value' => '&nbsp;',
								'style' => $leftColumn,
								'span' => 5
							));
						}
						$report->addRawRow(array($columnaLinea));
					}
				} else {
					if($kontc->getTipo()=='N'){
						$linea = $kontc->getLinea();
						if(isset($informe[$linea])){
							$lineaReporte = array(
								$kontc->getDescripcion(),
								$informe[$linea]['saldoAnterior'],
								$informe[$linea]['debitos'],
								$informe[$linea]['creditos'],
								$informe[$linea]['saldoAnterior']+$informe[$linea]['debitos']-$informe[$linea]['creditos'],
							);
							if($incluirCodigos=='S'){
								array_unshift($lineaReporte, $kontc->getCuentai());
							}
							$report->addRow($lineaReporte);
							unset($informe[$linea]);
							unset($lineaReporte);
						}
					} else {
						if($kontc->getTipo()=='T'){
							$linea = $kontc->getLinea();
							if(isset($informe[$linea])){
								$lineaReporte = array(
									$kontc->getDescripcion(),
									$informe[$linea]['saldoAnterior'],
									$informe[$linea]['debitos'],
									$informe[$linea]['creditos'],
									$informe[$linea]['saldoAnterior']+$informe[$linea]['debitos']-$informe[$linea]['creditos'],
								);
								if($incluirCodigos=='S'){
									array_unshift($lineaReporte, '');
								}
								$report->addRow($lineaReporte);
								unset($lineaReporte);
								unset($informe[$linea]);
							}
						}
					}
				}
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/pyg');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
