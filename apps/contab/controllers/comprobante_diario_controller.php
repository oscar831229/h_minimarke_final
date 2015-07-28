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
 * Comprobante_DiarioController
 *
 * Comprobante de Diario
 *
 */
class Comprobante_DiarioController extends ApplicationController {

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

		$this->setParamToView('diarios', $this->Diarios->find());
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		$codigoDiario = $this->getPostParam('codigoDiario', 'int');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$numeroInicial = $this->getPostParam('numeroInicial', 'int');
		$numeroFinal = $this->getPostParam('numeroFinal', 'int');

		try {

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if($fechaInicial==''||$fechaFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del comprobante de diario'
				);
			}

			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);

			if($numeroInicial==''||$numeroFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique el rango de consecutivos'
				);
			}

			$diario = $this->Diarios->findFirst("codigo='$codigoDiario'");
			if($diario==false){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique el diario para generar el reporte'
				);
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('COMPROBANTE DE DIARIO: '.i18n::strtoupper($diario->getNombre()), array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo3 = new ReportText('Fecha Impresión: '.date('Y-m-d H:i a'), array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Comprobante de Diario');
			$report->setColumnHeaders(array(
				'CUENTA',
				'NOMBRE',
				'DEBITOS MOV.',
				'CRÉDITOS MOV.',
				'DEBITOS SAL.',
				'CRÉDITOS SAL.'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
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

			$report->setColumnStyle(array(0, 1), $leftColumn);
			$report->setColumnStyle(array(2, 3, 4, 5), $rightColumn);
			$report->setColumnFormat(array(2, 3, 4, 5), $numberFormat);

			$report->start(true);

			$totalDebitos = 0;
			$totalCreditos = 0;

			$comprobante = array();
			$comprobDiarios = $this->Comprob->find("diario='$codigoDiario'");
			foreach(Date::getRange($fechaInicial, $fechaFinal) as $fecha){
				foreach($comprobDiarios as $comprob){
					$codigoComprob = $comprob->getCodigo();
					$conditions = "comprob='$codigoComprob' AND
					numero>='$numeroInicial' AND numero<='$numeroFinal' AND fecha='$fecha'";
					$movis = $this->Movi->find($conditions, 'columns: cuenta,deb_cre,valor');
					foreach($movis as $movi){
						$codigoCuenta = $movi->getCuenta();
						if(!isset($comprobante[$fecha][$codigoCuenta])){
							$comprobante[$fecha][$codigoCuenta] = array(
								'debitos' => 0,
								'creditos' => 0
							);
						}
						if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
							$comprobante[$fecha][$codigoCuenta]['debitos']+=$movi->getValor();
						} else {
							$comprobante[$fecha][$codigoCuenta]['creditos']+=$movi->getValor();
						}
						unset($movi);
					}
					unset($codigoCuenta);
					unset($cuenta);
				}
			}

			if(count($comprobante)){

				$partes = array(
					'tipo' => 1,
					'mayor' => 2,
					'clase' => 4,
					'subclase' => 6,
					'auxiliar' => 9
				);
				foreach($comprobante as $fecha => $balanceFecha){
					foreach($balanceFecha as $codigoCuenta => $balanceCuenta){
						foreach($partes as $tipoParte => $valorNivel){
							$length = strlen($codigoCuenta);
							if($length>$valorNivel){
								$parte = substr($codigoCuenta, 0, $valorNivel);
								if($parte!=''){
									if(!isset($comprobante[$fecha][$parte])){
										$comprobante[$fecha][$parte] = array(
											'debitos' => $balanceCuenta['debitos'],
											'creditos' => $balanceCuenta['creditos']
										);
									} else {
										$comprobante[$fecha][$parte]['debitos']+=$balanceCuenta['debitos'];
										$comprobante[$fecha][$parte]['creditos']+=$balanceCuenta['creditos'];
									}
								}
								unset($parte);
							}
							unset($valorNivel);
							unset($tipoParte);
						}
						unset($codigoCuenta);
						unset($balanceCuenta);
					}
					unset($codigoCuenta);
					unset($balanceFecha);
				}

				foreach($comprobante as $fecha => $balanceFecha){
					ksort($balanceFecha, SORT_STRING);

					$columnaFecha = new ReportRawColumn(array(
						'value' => $fecha,
						'style' => $leftColumnBold,
						'span' => 6
					));
					$report->addRawRow(array($columnaFecha));
					$totalDebitos = 0;
					$totalCreditos = 0;
					foreach($balanceFecha as $codigoCuenta => $balanceCuenta){
						$cuenta = BackCacher::getCuenta($codigoCuenta);
						if($cuenta==false){
							$nombreCuenta = 'NO EXISTE CUENTA';
						} else {
							$nombreCuenta = $cuenta->getNombre();
						}
						$report->addRow(array(
							$codigoCuenta,
							$nombreCuenta,
							$balanceCuenta['debitos'],
							$balanceCuenta['creditos'],
							$balanceCuenta['debitos'],
							$balanceCuenta['creditos']
						));
						if($cuenta->getEsAuxiliar()=='S'){
							$totalDebitos+=$balanceCuenta['debitos'];
							$totalCreditos+=$balanceCuenta['creditos'];
						}
						unset($cuenta);
						unset($balanceCuenta);
					}

					$columnaSumas = new ReportRawColumn(array(
						'value' => 'SUMAS IGUALES '.$fecha,
						'style' => $rightColumnBold,
						'span' => 2
					));

					$columnaTotalDebitos = new ReportRawColumn(array(
						'value' => $totalDebitos,
						'style' => $rightColumn,
						'format' => $numberFormat
					));

					$columnaTotalCreditos = new ReportRawColumn(array(
						'value' => $totalCreditos,
						'style' => $rightColumn,
						'format' => $numberFormat
					));

					$report->addRawRow(array(
						$columnaSumas,
						$columnaTotalDebitos,
						$columnaTotalCreditos,
						$columnaTotalDebitos,
						$columnaTotalCreditos
					));

				}

				/*$report->setTotalizeValues(array(
					3 => $totalDebitos,
					4 => $totalCreditos
				));*/

			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/comprobante-diario');

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
