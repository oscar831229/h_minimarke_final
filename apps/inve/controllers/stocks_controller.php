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
 * StocksController
 *
 * Muestra los saldos que tienen stocks bajos y altos
 *
 */
class StocksController extends ApplicationController {

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

		Tag::displayTo('almacenInicial', '1');
		Tag::displayTo('almacenFinal', $this->Almacenes->maximum('codigo','conditions: estado="A"'));
		Tag::displayTo('detallado', 'S');

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('almacenes', $this->Almacenes->find('estado="A"'));

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');
		try 
		{
			$almacenInicial = $this->getPostParam('almacenInicial', 'alpha');
			$almacenFinal = $this->getPostParam('almacenFinal', 'alpha');

			if($almacenInicial==''||$almacenFinal==''){
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

			list($itemInicial, $itemFinal) = Utils::sortRange($itemInicial, $itemFinal);

			$detallado = $this->getPostParam('detallado', 'onechar');
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('STOCKS ALTOS Y BAJOS DE INVENTARIO', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Almacenes: '.$almacenInicial.' - '.$almacenFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Stocks Altos y Bajos');
			$report->setColumnHeaders(array(
				'CÓD.',
				'DESCRIPCIÓN',
				'UNIDAD',
				'SALDO',
				'STOCK MÍNIMO',
				'STOCK MÁXIMO',
				'ESTADO',
				'FALTA/SOBRA',
				'COSTO TOTAL',
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

			$columnaTotalInventario = new ReportRawColumn(array(
				'value' => 'TOTAL INVENTARIO',
				'style' => $rightColumnBold,
				'span' => 8
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

			$report->setColumnStyle(array(3, 4, 5, 7, 8), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(3, 4, 5, 7, 8), $numberFormat);

			$report->start(true);

			$saldosAlmacen = array();
			foreach($this->Almacenes->find(array("codigo>='$almacenInicial' AND codigo<='$almacenFinal'", 'order' => 'codigo')) as $almacen){
				$conditions = "ano_mes='0' AND almacen='{$almacen->getCodigo()}' AND item>='$itemInicial' AND item<='$itemFinal'";
				foreach($this->Saldos->find($conditions) as $saldos){
					$inveStock = $this->InveStocks->findFirst("almacen='{$almacen->getCodigo()}' AND item='{$saldos->getItem()}'");
					if($inveStock==false){
						continue;
					}
					$inve = BackCacher::getInve($saldos->getItem());
					if($inve==false){
						continue;
					}
					if($saldos->getSaldo()<$inveStock->getMinimo()||$saldos->getSaldo()>$inveStock->getMaximo()){
						$saldosAlmacen[$almacen->getCodigo()][$inve->getLinea()][$inve->getItem()] = array(
							'saldo' => $saldos->getSaldo(),
							'costo' => $saldos->getCosto(),
							'minimo' => $inveStock->getMinimo(),
							'maximo' => $inveStock->getMaximo()
						);
					}
				}
				unset($conditions);
			}

			$totalInventario = 0;
			ksort($saldosAlmacen);
			foreach($saldosAlmacen as $codigoAlmacen => $saldoAlmacen){
				$almacen = BackCacher::getAlmacen($codigoAlmacen);
				if($almacen==false){
					$nombreAlmacen = 'NO EXISTE ALMACÉN';
				} else {
					$nombreAlmacen = $almacen->getNomAlmacen();
				}
				$columnaAlmacen = new ReportRawColumn(array(
					'value' => 'ALMACÉN No. '.$codigoAlmacen.' : '.$nombreAlmacen,
					'style' => $leftColumnBold,
					'span' => 9
				));

				$totalAlmacen = 0;
				$lineas = array();
				$totalLineas = array();
				$otrasLineas = array();
				$encabezadoLineas = array();
				$report->addRawRow(array($columnaAlmacen));
				ksort($saldoAlmacen, SORT_STRING);
				foreach($saldoAlmacen as $codigoLinea => $saldoLinea){
					$length = strlen($codigoLinea)-1;
					for($i=0;$i<$length;$i++){
						$codigoSubLinea = substr($codigoLinea, 0, $i);
						$linea = BackCacher::getLinea($codigoAlmacen, $codigoSubLinea);
						if($linea!=false){
							if(!isset($otrasLineas[$codigoSubLinea])){
								if(!isset($encabezadoLineas[$codigoLinea])){
									$encabezadoLineas[$codigoLinea] = array();
								}
								$encabezadoLineas[$codigoLinea][] = $linea;
							}
							if(!isset($otrasLineas[$codigoSubLinea])){
								$otrasLineas[$codigoSubLinea] = 1;
							} else {
								$otrasLineas[$codigoSubLinea]++;
							}
							$lineas[$codigoLinea][$codigoSubLinea] = 0;
							$totalLineas[$codigoSubLinea] = 0;
						}
					}
				}

				foreach($saldoAlmacen as $codigoLinea => $saldoLinea){

					if(isset($encabezadoLineas[$codigoLinea])){
						foreach($encabezadoLineas[$codigoLinea] as $linea){
							$columnaLinea = new ReportRawColumn(array(
								'value' => 'LÍNEA '.$linea->getLinea().' : '.$linea->getNombre(),
								'style' => $leftColumnBold,
								'span' => 9
							));
							$report->addRawRow(array($columnaLinea));
						}
					}

					$linea = BackCacher::getLinea($codigoAlmacen, $codigoLinea);
					if($linea==false){
						$nombreLinea = 'NO EXISTE LA LÍNEA';
					} else {
						$nombreLinea = $linea->getNombre();
					}
					$columnaLinea = new ReportRawColumn(array(
						'value' => 'LÍNEA '.$codigoLinea.' : '.$nombreLinea,
						'style' => $leftColumnBold,
						'span' => 9
					));
					$report->addRawRow(array($columnaLinea));

					$totalLinea = 0;
					ksort($saldoLinea, SORT_STRING);
					foreach($saldoLinea as $codigoItem => $saldos){
						$inve = BackCacher::getInve($codigoItem);
						if($inve==false){
							continue;
						}
						$unidad = BackCacher::getUnidad($inve->getUnidad());
						if($unidad==false){
							$nombreUnidad = 'NO EXISTE';
						} else {
							$nombreUnidad = $unidad->getNomUnidad();
						}
						if($saldos['saldo']<$saldos['minimo']){
							$estadoStock = 'BAJO';
							$diferencia = $saldos['minimo']-$saldos['maximo'];
						} else {
							$estadoStock = 'ALTO';
							$diferencia = $saldos['saldo']-$saldos['maximo'];
						}
						$report->addRow(array(
							$inve->getItem(),
							$inve->getDescripcion(),
							$nombreUnidad,
							$saldos['saldo'],
							$saldos['minimo'],
							$saldos['maximo'],
							$estadoStock,
							$diferencia,
							$saldos['costo']
						));
						$totalLinea+=$saldos['costo'];
						$totalAlmacen+=$saldos['costo'];
						$totalInventario+=$saldos['costo'];
						if(isset($lineas[$inve->getLinea()])){
							foreach($lineas[$inve->getLinea()] as $codigoSubLinea => $valor){
								$totalLineas[$codigoSubLinea]+=$saldos['costo'];
							}
						}
					}
					$columnaTotalLinea = new ReportRawColumn(array(
						'value' => 'TOTAL LÍNEA '.$codigoLinea.' : '.$nombreLinea,
						'style' => $rightColumnBold,
						'span' => 8
					));
					$columnaTotalLineaValor = new ReportRawColumn(array(
						'value' => $totalLinea,
						'style' => $rightColumn,
						'format' => $numberFormat,
						'span' => 1
					));
					$report->addRawRow(array(
						$columnaTotalLinea,
						$columnaTotalLineaValor
					));
					if(isset($lineas[$codigoLinea])){
						foreach($lineas[$inve->getLinea()] as $codigoSubLinea => $valor){
							if($otrasLineas[$codigoSubLinea]==1){
								$linea = BackCacher::getLinea($codigoAlmacen, $codigoSubLinea);
								if($linea==false){
									$nombreLinea = 'NO EXISTE LA LÍNEA';
								} else {
									$nombreLinea = $linea->getNombre();
								}
								$columnaTotalLinea = new ReportRawColumn(array(
									'value' => 'TOTAL LÍNEA '.$codigoSubLinea.' : '.$nombreLinea,
									'style' => $rightColumnBold,
									'span' => 8
								));
								$columnaTotalLineaValor = new ReportRawColumn(array(
									'value' => $totalLineas[$codigoSubLinea],
									'style' => $rightColumn,
									'format' => $numberFormat,
									'span' => 1
								));
								$report->addRawRow(array(
									$columnaTotalLinea,
									$columnaTotalLineaValor
								));
							}
							$otrasLineas[$codigoSubLinea]--;
						}
					}
				}
				$columnaTotalAlmacen = new ReportRawColumn(array(
					'value' => 'TOTAL ALMACÉN '.$almacen->getCodigo().' : '.$almacen->getNomAlmacen(),
					'style' => $rightColumnBold,
					'span' => 8
				));
				$columnaTotalAlmacenValor = new ReportRawColumn(array(
					'value' => $totalAlmacen,
					'style' => $rightColumn,
					'format' => $numberFormat,
					'span' => 1
				));
				$report->addRawRow(array(
					$columnaTotalAlmacen,
					$columnaTotalAlmacenValor
				));
			}
			$columnaTotalInventarioValor = new ReportRawColumn(array(
				'value' => $totalInventario,
				'style' => $rightColumn,
				'format' => $numberFormat,
				'span' => 1
			));
			$report->addRawRow(array(
				$columnaTotalInventario,
				$columnaTotalInventarioValor
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/stocks');

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
