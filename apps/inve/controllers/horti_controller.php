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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * HortiController
 *
 * Retención de Hortifrutícula
 *
 */
class HortiController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrei();

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('almacenes', $this->Almacenes->find("estado='A'"));
		$this->setParamToView('lineas', $this->Lineas->find("almacen=1 AND porc_hortic>0"));

		Tag::displayTo('fechaInicial', Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', $fechaCierre->getDate());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		try {

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);

			$lineaInicial = $this->getPostParam('lineaInicial', 'alpha');
			$lineaFinal = $this->getPostParam('lineaFinal', 'alpha');

			list($lineaInicial, $lineaFinal) = Utils::sortRange($lineaInicial, $lineaFinal);

			$codigoAlmacen = $this->getPostParam('almacen', 'int');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('RETEFUENTE PRODUCTOS HORTIFRUTÍCULAS', array(
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
			$report->setDocumentTitle('Retefuentes Productos Hortifrutículas');
			$report->setColumnHeaders(array(
				'PRODUCTO',
				'CIUDAD',
				'ORIGEN',
				'DEPARTAMENTO',
				'KILOS',
				'VALOR RETENIDO',
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

			$rightColumnBold = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$columnaTotal = new ReportRawColumn(array(
				'value' => 'TOTAL',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0, 1, 2), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(3, 4, 5, 6, 7, 8), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(4, 5), $numberFormat);

			$report->start(true);

			$productos = array();
			$codigoComprob = sprintf('E%02s', $codigoAlmacen);
			foreach($this->Inve->find("linea>='$lineaInicial' AND linea<='$lineaFinal'") as $inve){
				$conditions = "comprob LIKE '$codigoComprob' AND fecha >='$fechaInicial' AND fecha<='$fechaFinal' AND item='{$inve->getItem()}'";
				$movilins = $this->Movilin->find(array($conditions, 'columns' => 'comprob,numero,item,valor,cantidad'));
				foreach($movilins as $movilin){
					$movih1 = $this->Movih1->findFirst("comprob='$codigoComprob' AND numero='{$movilin->getNumero()}'");
					if($movih1!=false){
						if(!isset($productos[$inve->getDescripcion()])){
							$productos[$inve->getDescripcion()] = array(
								'cantidad' => 0,
								'valor' => 0
							);
						}
						$productos[$inve->getDescripcion()]['cantidad'] += $movilin->getCantidad();
						$productos[$inve->getDescripcion()]['valor'] += $movilin->getValor()*0.01;
					}
				}
				BackCacher::setInve($inve);
			}

			ksort($productos);
			$totalRetencion = 0;
			foreach($productos as $descripcion => $producto){
				$report->addRow(array(
					$descripcion,
					'',
					'',
					'',
					$producto['cantidad'],
					$producto['valor']
				));
				$totalRetencion+=$producto['valor'];
			}

			$columnaTotalValor = new ReportRawColumn(array(
				'value' => $totalRetencion,
				'style' => $rightColumn,
				'format' => $numberFormat,
				'span' => 1
			));

			$report->addRawRow(array(
				$columnaTotal,
				$columnaTotalValor
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/horti-'.$codigoAlmacen);

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
