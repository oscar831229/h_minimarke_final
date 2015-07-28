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
 * SaldosAlmacenConsolidadoController
 *
 * Saldos Consolidados por Almacén
 *
 */
class Saldos_Almacen_ConsolidadoController extends ApplicationController
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
		$fechaCierre = $empresa->getFCierrei();
		Tag::displayTo('lineaFinal', $this->Lineas->maximum('linea'));
		Tag::displayTo('periodo', $fechaCierre->getPeriod());

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('lineas', $this->Lineas->count('group: linea,nombre'));

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try {
			$lineaInicial = $this->getPostParam('lineaInicial', 'alpha');
			$lineaFinal = $this->getPostParam('lineaFinal', 'alpha');
			if($lineaInicial==''||$lineaFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique los almacenes inicial y final del listado'
				);
			}

			$itemInicial = $this->getPostParam('itemInicial', 'alpha');
			$itemFinal = $this->getPostParam('itemFinal', 'alpha');

			if($itemInicial==''){
				$itemInicial = $this->Inve->minimum('item', 'conditions: estado="A"');
			}
			if($itemFinal==''){
				$itemFinal = $this->Inve->maximum('item', 'conditions: estado="A"');
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('CONSOLIDADO DE SALDOS POR ALMACÉN', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Líneas: '.$lineaInicial.' - '.$lineaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Consolidado de Saldos por Almacén');
			$almacenes = $this->Almacenes->find(array('columns' => 'codigo,nom_almacen'));
			$numAlmacenes = count($almacenes) * 2;
			$columns = array(
				'REF',
				'DESCRIPCIÓN',
				'UNIDAD'
			);
			$report->setColumnHeaders($columns);
			foreach ($almacenes as $almacen) {
				$report->addColumnHeader('SALDO '.strtoupper($almacen->getNomAlmacen()));
				$report->addColumnHeader('COSTO '.strtoupper($almacen->getNomAlmacen()));
			}
			$report->addColumnHeader('SALDO TOTAL');
			$report->addColumnHeader('COSTO TOTAL');

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

			$columnaTotalLinea = new ReportRawColumn(array(
				'value' => 'TOTAL LÍNEA',
				'style' => $rightColumnBold,
				'span' => 3 + $numAlmacenes
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

			$report->setColumnStyle(range(3, $numAlmacenes + 5), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(range(3, $numAlmacenes + 5), $numberFormat);

			$report->start(true);

			$lineas = $this->Lineas->count(array(
				'group' => 'linea,nombre',
				"conditions" => "linea>='$lineaInicial' AND linea<='$lineaFinal' ".
				"AND linea IN (SELECT linea FROM inve WHERE item BETWEEN '$itemInicial' AND '$itemFinal')"));
			foreach($lineas as $linea){
				$columnaAlmacen = new ReportRawColumn(array(
					'value' => 'LÍNEA No. '.$linea->getLinea().' : '.$linea->getNombre(),
					'style' => $leftColumnBold,
					'span' => 5 + $numAlmacenes
				));
				$report->addRawRow(array($columnaAlmacen));
				$totalLineasSaldo = 0;
				$totalLineasValor = 0;
				$referencias = $this->Inve->find('columns: item,descripcion,unidad', "conditions: linea='{$linea->getLinea()}' AND estado='A' ".
					"AND item BETWEEN '$itemInicial' AND '$itemFinal'");
				foreach ($referencias as $inve) {
					$unidad = BackCacher::getUnidad($inve->getUnidad());
					if ($unidad != false) {
						$unidad = $unidad->getNomUnidad();
					} else {
						$unidad = 'NO EXISTE';
					}
					$row = array(
						$inve->getItem(),
						$inve->getDescripcion(),
						$unidad
					);
					$totalAlmacenesSaldo = 0;
					$totalAlmacenesValor = 0;
					$almacenes = $this->Almacenes->find('columns: codigo,nom_almacen');
					foreach($almacenes as $almacen){
						$saldo = $this->Saldos->findFirst("item='{$inve->getItem()}' AND ano_mes=0 AND almacen='{$almacen->getCodigo()}'");
						if($saldo==false){
							$row[] = 0;
							$row[] = 0;
						} else {
							$totalAlmacenesSaldo+= $saldo->getSaldo();
							$totalAlmacenesValor+= $saldo->getCosto();
							$row[] = $saldo->getSaldo();
							$row[] = $saldo->getCosto();
						}
					}
					$row[] = $totalAlmacenesSaldo;
					$row[] = $totalAlmacenesValor;
					$totalLineasSaldo+= $totalAlmacenesSaldo;
					$totalLineasValor+= $totalAlmacenesValor;
					$report->addRow($row);
				}
				$totales = array($columnaTotalLinea);
				$totales[$numAlmacenes+3] = new ReportRawColumn(array(
					'value' => $totalLineasSaldo,
					'style' => $rightColumn,
					'format' => $numberFormat,
					'span' => 1
				));
				$totales[$numAlmacenes+4] = new ReportRawColumn(array(
					'value' => $totalLineasValor,
					'style' => $rightColumn,
					'format' => $numberFormat,
					'span' => 1
				));
				$report->addRawRow($totales);
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/consolidado_almacen');

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
