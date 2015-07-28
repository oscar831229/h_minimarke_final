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
 * Listado_ComprobController
 *
 * Listado de Comprobantes
 *
 */
class Listado_ComprobController extends ApplicationController {

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

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');

	}

	public function generarAction(){

		$this->setResponse('json');
		try
		{
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if($fechaInicial==''||$fechaFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del listado de comprobantes'
				);
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LISTADO DE CONSECUTIVOS', array(
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
			$report->setDocumentTitle('Listado de Consecutivos');
			$report->setColumnHeaders(array(
				'COMPROBANTE',
				'DEBITOS',
				'CRÉDITOS',
				''
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

			$report->setColumnStyle(array(0), $leftColumn);

			$report->setColumnStyle(array(1, 2), $rightColumn);
			$report->setColumnFormat(array(1, 2), $numberFormat);

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'SUB TOTAL',
				'style' => $rightColumnBold
			));

			$report->start(true);

			$totalGeneralD = 0;
			$totalGeneralC = 0;

			$comprobObj = EntityManager::get('Comprob')->find();
			foreach($comprobObj as $comprob){
				$totalDebitos = 0;
				$totalCreditos = 0;

				//movimientos de comprobante
				$moviObj = EntityManager::get('Movi')->find(array('conditions'=>"comprob='{$comprob->getCodigo()}' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'"));

				foreach($moviObj as $movi){
					if($movi->getDebCre()=='D'){
						$totalDebitos+=$movi->getValor();
						$totalGeneralD+=$movi->getValor();
					} else {
						$totalCreditos+=$movi->getValor();
						$totalGeneralC+=$movi->getValor();
					}
				}
				$diff = '';
				if($totalDebitos!=$totalCreditos){
					$diff = '*';
				}

				$report->addRow(array(
					$comprob->getCodigo().' ('.$comprob->getNomComprob().')',
					$totalDebitos,
					$totalCreditos,
					$diff
				));
			}

			$columnaTotalDebitos = new ReportRawColumn(array(
				'value' => $totalGeneralD,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalCreditos = new ReportRawColumn(array(
				'value' => $totalGeneralC,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$report->addRawRow(array(
				$columnaTotalCuenta,
				$columnaTotalDebitos,
				$columnaTotalCreditos
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/listado-comprob');

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
