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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * SaldosAlmacenController
 *
 * Saldos por Almacén
 *
 */
class Saldos_AlmacenController extends ApplicationController {

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

		$meses = array(
			'01' => 'ENERO',
			'02' => 'FEBRERO',
			'03' => 'MARZO',
			'04' => 'ABRIL',
			'05' => 'MAYO',
			'06' => 'JUNIO',
			'07' => 'JULIO',
			'08' => 'AGOSTO',
			'09' => 'SEPTIEMBRE',
			'10' => 'OCTUBRE',
			'11' => 'NOVIEMBRE',
			'12' => 'DICIEMBRE'
		);

		$periodoActual = $fechaCierre->getPeriod();
		$periodos = array(0 => 'ACTUAL');
		$saldos = $this->Saldos->distinct(array("ano_mes", "conditions" => "ano_mes>0 and ano_mes<=$periodoActual", "order" => "1 DESC"));
		foreach($saldos as $anoMes){
			$mes = substr($anoMes, 4, 2);
			$periodos[$anoMes] = $meses[$mes].' '.substr($anoMes, 0, 4);
		}
		$this->setParamToView('periodos', $periodos);

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
			$codigoAlmacenInicial = $this->getPostParam('almacenInicial', 'alpha');
			$codigoAlmacenFinal = $this->getPostParam('almacenFinal', 'alpha');

			if($codigoAlmacenInicial==''||$codigoAlmacenFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique los almacenes inicial y final del listado'
				);
			}

			list($codigoAlmacenInicial, $codigoAlmacenFinal) = Utils::sortRange($codigoAlmacenInicial, $codigoAlmacenFinal);

			$periodo = $this->getPostParam('periodo', 'int');
			$itemInicial = $this->getPostParam('itemInicial', 'alpha');
			$itemFinal = $this->getPostParam('itemFinal', 'alpha');

			if($itemInicial==''){
				$itemInicial = $this->Inve->minimum('item', 'conditions: estado="A"');
			}
			if($itemFinal==''){
				$itemFinal = $this->Inve->maximum('item', 'conditions: estado="A"');
			}

			list($itemInicial, $itemFinal) = Utils::sortStringRange($itemInicial, $itemFinal);

			$detallado = $this->getPostParam('detallado', 'onechar');
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LISTADO DE SALDOS POR ALMACÉN', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$almacenInicial = BackCacher::getAlmacen($codigoAlmacenInicial);
			if($almacenInicial==false){
				return array(
					'status' => 'FAILED',
					'message' => 'El almacén inicial no existe'
				);
			}

			$almacenFinal = BackCacher::getAlmacen($codigoAlmacenFinal);
			if($almacenFinal==false){
				return array(
					'status' => 'FAILED',
					'message' => 'El almacén final no existe'
				);
			}

			$inactivos = $this->getPostParam('inactivos', 'onechar');
			$sinExistencia = $this->getPostParam('sinExistencia', 'onechar');

			if($codigoAlmacenInicial!=$codigoAlmacenFinal){
				$caption = 'Almacenes: '.$codigoAlmacenInicial.'/'.$almacenInicial->getNomAlmacen().' - '.$codigoAlmacenFinal.'/'.$almacenFinal->getNomAlmacen();
			} else {
				$caption = 'Almacén: '.$codigoAlmacenInicial.'/'.$almacenInicial->getNomAlmacen();
			}

			$titulo2 = new ReportText($caption, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			if($periodo==0){
				$titulo3 = new ReportText('PERIODO: ACTUAL', array(
					'fontSize' => 11,
					'fontWeight' => 'bold',
					'textAlign' => 'center'
				));
			} else {
				$titulo3 = new ReportText('PERIODO: '.$periodo, array(
					'fontSize' => 11,
					'fontWeight' => 'bold',
					'textAlign' => 'center'
				));
			}

			$report->setHeader(array($titulo, $titulo2, $titulo3));
			$report->setDocumentTitle('Saldos por Almacén');

			$headers = array(
				'CÓD.',
				'DESCRIPCIÓN',
				'UNIDAD',
				'SALDO',
				'COSTO UNI.',
				'COSTO TOTAL',
			);
			$condiciones = Settings::get('condiciones');
			if($condiciones=='S'){
				$headers[] = 'COND. ORG.';
				$span = 10;
			} else {
				$span = 9;
			}
			$report->setColumnHeaders($headers);

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

			$report->setColumnStyle(array(3, 4, 5), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(3, 4, 5), $numberFormat);

			$report->start(true);

			$saldosAlmacen = array();
			$almacenes = $this->Almacenes->find(array("codigo>='$codigoAlmacenInicial' AND codigo<='$codigoAlmacenFinal'", 'order' => 'codigo'));
			foreach($almacenes as $almacen){
				$conditions = "ano_mes='$periodo' AND almacen='{$almacen->getCodigo()}' AND item>='$itemInicial' AND item<='$itemFinal'";
				foreach($this->Saldos->find($conditions) as $saldos){
					$inve = BackCacher::getInve($saldos->getItem());
					if($inve==false){
						continue;
					} else {
						if($inactivos!='S'){
							if($inve->getEstado()!='A'){
								continue;
							}
						}
					}
					if($sinExistencia=='S'){
						$saldosAlmacen[$almacen->getCodigo()][$inve->getLinea()][$inve->getItem()] = array(
							'saldo' => $saldos->getSaldo(),
							'costo' => $saldos->getCosto()
						);
					} else {
						if($saldos->getSaldo()!=0||$saldos->getCosto()!=0){
							$saldosAlmacen[$almacen->getCodigo()][$inve->getLinea()][$inve->getItem()] = array(
								'saldo' => $saldos->getSaldo(),
								'costo' => $saldos->getCosto()
							);
						}
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
					'span' => $span
				));
				$report->addRawRow(array($columnaAlmacen));

				$totalAlmacen = 0;
				$lineas = array();
				$totalLineas = array();
				$otrasLineas = array();
				$encabezadoLineas = array();
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
								'span' => $span
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
						'span' => $span
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
						if($saldos['saldo']>0){
							$costoUnitario = $saldos['costo']/$saldos['saldo'];
						} else {
							$costoUnitario = 0;
						}
						$row = array(
							$inve->getItem(),
							$inve->getDescripcion(),
							$nombreUnidad,
							$saldos['saldo'],
							$costoUnitario,
							$saldos['costo']
						);
						if($condiciones=='S'){
							$row[] = '';
						}
						$report->addRow($row);
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
						'span' => 5
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
									'span' => 5
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
					'span' => 5
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
			
			$columnaTotal = new ReportRawColumn(array(
				'value' => 'O=olor   C=color  T=textura  A=apariencia',
				'span' => $span
			));
			$report->addRawRow(array(
				$columnaTotal,
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/saldos-almacen-'.$periodo);

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
