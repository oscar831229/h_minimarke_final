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
 * Listado_MovimientoController
 *
 * Listado de Movimiento
 *
 */
class Listado_MovimientoController extends ApplicationController {

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

		$this->setParamToView('comprobs', $this->Comprob->find(array('order' => 'nom_comprob')));

	}

	public function generarAction(){

		$this->setResponse('json');

		try
		{
			$comprobInicial = $this->getPostParam('comprobInicial', 'comprob');
			$comprobFinal = $this->getPostParam('comprobFinal', 'comprob');

			$numeroInicial = $this->getPostParam('numeroInicial', 'int');
			$numeroFinal = $this->getPostParam('numeroFinal', 'int');

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');
			if($fechaInicial && $fechaFinal){
				list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);
			}

			$conditions = array();
			if($comprobInicial!=''&&$comprobFinal!=''){
				$conditions[] = "comprob>='$comprobInicial' AND comprob<='$comprobFinal'";
			}
			if($numeroInicial>0 && $numeroFinal> 0){
				$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
			}
			if($fechaInicial!=''&&$fechaFinal!=''){
				$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LISTADO DE MOVIMIENTOS', array(
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

			$report->setDocumentTitle('Listado de Movimiento');
			$report->setColumnHeaders(array(
				'LIN.',
				'CUENTA',
				'DESCRIPCION',
				'NO. DOCUMENTO',
				'CENTRO COSTO',
				'NUMERO DOC.',
				'DEBITOS',
				'CREDITOS',
				'BASE'
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

			$report->setColumnStyle(array(1, 2), $leftColumn);
			$report->setColumnStyle(array(0, 3, 4, 6, 7, 8), $rightColumn);
			$report->setColumnFormat(array(6, 7, 8), $numberFormat);

			$columnaTotalComprob = new ReportRawColumn(array(
				'value' => 'TOTAL COMPROBANTE',
				'style' => $rightColumnBold,
				'span' => 6
			));

			$report->start(true);

			$movis = $this->Movi->find(array(
				join(' AND ', $conditions),
				'columns' => 'comprob,numero,fecha',
				'group' => 'comprob,numero',
				'order' => 'comprob,numero,fecha'
			));
			foreach($movis as $uniqueMovi){
				$comprob = BackCacher::getComprob($uniqueMovi->getComprob());
				$columnaCuenta = new ReportRawColumn(array(
					'value' => 'Comprobante: '.$comprob->getCodigo().' '.$uniqueMovi->getNumero().' '.$comprob->getNomComprob().' Fecha: '.$uniqueMovi->getFecha(),
					'style' => $leftColumnBold,
					'span' => 9
				));
				$report->addRawRow(array($columnaCuenta));

				$linea = 1;
				$totalDebitos = 0;
				$totalCreditos = 0;
				$conditions = "comprob='{$uniqueMovi->getComprob()}' AND numero='{$uniqueMovi->getNumero()}'";
				foreach($this->Movi->find(array($conditions)) as $movi){
					if($movi->getDebCre()=='D'){
						$report->addRow(array(
							$linea,
							$movi->getCuenta(),
							$movi->getDescripcion(),
							$movi->getNit(),
							$movi->getCentroCosto(),
							$movi->getTipoDoc().' '.$movi->getNumeroDoc(),
							$movi->getValor(),
							0,
							$movi->getBaseGrab()
						));
						$totalDebitos+=$movi->getValor();
					} else {
						$report->addRow(array(
							$linea,
							$movi->getCuenta(),
							$movi->getDescripcion(),
							$movi->getNit(),
							$movi->getCentroCosto(),
							$movi->getTipoDoc().' '.$movi->getNumeroDoc(),
							0,
							$movi->getValor(),
							$movi->getBaseGrab()
						));
						$totalCreditos+=$movi->getValor();
					}
					$linea++;
					unset($movi);
				}

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
					$columnaTotalComprob,
					$columnaTotalDebitos,
					$columnaTotalCreditos
				));
				unset($columnaTotalCreditos);
				unset($columnaTotalDebitos);
				unset($conditions);
				unset($uniqueMovi);
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/listado-movimiento');

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
