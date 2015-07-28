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
 * ConsumosController
 *
 * Consumos / Pedidos por Centro de Costo
 *
 */
class ConsumosController extends ApplicationController {

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
		$this->setParamToView('centros', $this->Centros->find(array("estado='A'", 'order' => 'codigo')));
		$this->setParamToView('tipoMovimiento', array('C' => 'CONSUMOS', 'P' => 'PEDIDOS'));

		$centroFinal = $this->Centros->maximum('codigo');
		Tag::displayTo('centroFinal', $centroFinal);

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

		

			$nitTercero = $this->getPostParam('nitTercero', 'terceros');
			if($nitTercero!=''){
				$tercero = BackCacher::getTercero($nitTercero);
				if($tercero==false){
					return array(
						'status' => 'FAILED',
						'message' => 'El tercero con número de documento "'.$nitTercero.'" no existe'
					);
				}
			}

			$tipoMovimiento = $this->getPostParam('tipoMovimiento', 'onechar');

			$almacenInicial = $this->getPostParam('almacenInicial', 'int');
			$almacenFinal = $this->getPostParam('almacenFinal', 'int');

			list($almacenInicial, $almacenFinal) = Utils::sortRange($almacenInicial, $almacenFinal);

			$centroInicial = $this->getPostParam('centroInicial', 'int');
			$centroFinal = $this->getPostParam('centroFinal', 'int');

			list($centroInicial, $centroFinal) = Utils::sortRange($centroInicial, $centroFinal);

			$numeroInicial = $this->getPostParam('numeroInicial', 'int');
			$numeroFinal = $this->getPostParam('numeroFinal', 'int');

			list($numeroInicial, $numeroFinal) = Utils::sortRange($numeroInicial, $numeroFinal);

			$consolidado = $this->getPostParam('consolidado', 'onechar');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			if($tipoMovimiento=='C'){
				$titulo = new ReportText('CONSUMOS POR CENTRO DE COSTO', array(
					'fontSize' => 16,
					'fontWeight' => 'bold',
					'textAlign' => 'center'
				));
			} else {
				$titulo = new ReportText('PEDIDOS POR CENTRO DE COSTO', array(
					'fontSize' => 16,
					'fontWeight' => 'bold',
					'textAlign' => 'center'
				));
			}

			$titulo1 = new ReportText('Desde '.$fechaInicial.' hasta '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$tercero = BackCacher::getTercero($nitTercero);
			if($tercero==false){
				$report->setHeader(array($titulo, $titulo1));
			} else {
				$titulo3 = new ReportText('Tercero Convención: '.$tercero->getNit().'/'.$tercero->getNombre(), array(
					'fontSize' => 11,
					'fontWeight' => 'bold',
					'textAlign' => 'center'
				));
				$report->setHeader(array($titulo, $titulo1, $titulo2, $titulo3));
			}

			$report->setDocumentTitle('Consumos/Pedidos por Centro de Costo');
			$report->setColumnHeaders(array(
				'REFERENCIA',
				'DESCRIPCIÓN',
				'CANTIDAD',
				'VALOR TOTAL'
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

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0, 1), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(2, 3), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(2, 3), $numberFormat);

			$report->start(true);

			$conditions = array("comprob LIKE '$tipoMovimiento%'");

			if($almacenInicial==$almacenFinal){
				$conditions[] = "almacen='$almacenInicial'";
			} else {
				$conditions[] = "almacen>='$almacenInicial' AND almacen<='$almacenFinal'";
			}

			if($nitTercero!=''){
				$conditions[] = "nit='$nitTercero'";
			}

			if($fechaInicial!=$fechaFinal){
				$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
			} else {
				$conditions[] = "fecha='$fechaInicial'";
			}

			if($centroInicial>0&&$centroFinal>0){
				$conditions[] = "centro_costo>='$centroInicial' AND centro_costo<='$centroFinal'";
			}

			if($numeroInicial>0&&$numeroFinal>0){
				$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
			}

			$consumos = array();
			$conditions = join(' AND ', $conditions);
			$moviheads = $this->Movihead->find(array($conditions, 'columns' => 'almacen,comprob,numero,centro_costo'));
			foreach($moviheads as $movihead){
				$comprob = $movihead->getComprob();
				$codigoAlmacen = $movihead->getAlmacen();
				$centroCosto = $movihead->getCentroCosto();
				$movilins = $this->Movilin->find(array("comprob='$comprob' AND numero='{$movihead->getNumero()}'", 'columns' => 'comprob,numero,item,cantidad,valor'));
				foreach($movilins as $movilin){
					$inve = BackCacher::getInve($movilin->getItem());
					$codigoItem = $inve->getItem();
					$codigoLinea = $inve->getLinea();
					if(!isset($consumos[$codigoAlmacen][$centroCosto][$codigoLinea][$codigoItem])){
						$consumos[$codigoAlmacen][$centroCosto][$codigoLinea][$codigoItem] = array(
							'cantidad' => 0,
							'valor' => 0
						);
					}
					$consumos[$codigoAlmacen][$centroCosto][$codigoLinea][$codigoItem]['cantidad']+=$movilin->getCantidad();
					$consumos[$codigoAlmacen][$centroCosto][$codigoLinea][$codigoItem]['valor']+=$movilin->getValor();
				}
			}

			$totalGeneral = 0;
			foreach($consumos as $codigoAlmacen => $consumosAlmacen){
				$totalAlmacen = 0;
				$almacen = BackCacher::getAlmacen($codigoAlmacen);
				if($almacen==false){
					$nombreAlmacen = 'NO EXISTE ALMACÉN '.$codigoAlmacen;
				} else {
					$nombreAlmacen = $almacen->getNomAlmacen();
				}
				$columnaAlmacen = new ReportRawColumn(array(
					'value' => $codigoAlmacen.' : '.$nombreAlmacen,
					'style' => $leftColumnBold,
					'span' => 9
				));
				$report->addRawRow(array($columnaAlmacen));
				foreach($consumosAlmacen as $codigoCentro => $consumosCentros){
					$centroCosto = BackCacher::getCentro($codigoCentro);
					if($centroCosto==false){
						$nombreCentro = 'NO EXISTE CENTRO DE COSTO '.$codigoCentro;
					} else {
						$nombreCentro = $centroCosto->getNomCentro();
					}
					$columnaCentro = new ReportRawColumn(array(
						'value' => $codigoCentro.' : '.$nombreCentro,
						'style' => $leftColumnBold,
						'span' => 9
					));
					$report->addRawRow(array($columnaCentro));
					$totalCentro = 0;
					foreach($consumosCentros as $codigoLinea => $consumosLinea){
						$linea = BackCacher::getLinea($codigoAlmacen, $codigoLinea);
						if($linea==false){
							$nombreLinea = 'NO EXISTE LINEA '.$codigoLinea;
						} else {
							$nombreLinea = $linea->getNombre();
						}
						$columnaLinea = new ReportRawColumn(array(
							'value' => $codigoLinea.' : '.$nombreLinea,
							'style' => $leftColumnBold,
							'span' => 9
						));
						$totalLinea = 0;
						$report->addRawRow(array($columnaLinea));
						foreach($consumosLinea as $codigoItem => $consumo){
							if($consolidado!='S'){
								$inve = BackCacher::getInve($codigoItem);
								$report->addRow(array(
									$codigoItem,
									$inve->getDescripcion(),
									$consumo['cantidad'],
									$consumo['valor']
								));
							}
							$totalLinea+=$consumo['valor'];
							$totalCentro+=$consumo['valor'];
							$totalAlmacen+=$consumo['valor'];
							$totalGeneral+=$consumo['valor'];
						}
						$columnaTotalLinea = new ReportRawColumn(array(
							'value' => 'TOTAL LINEA '.$codigoLinea.' : '.$nombreLinea,
							'style' => $rightColumnBold,
							'span' => 3
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
					}
					$columnaTotalCentro = new ReportRawColumn(array(
						'value' => 'TOTAL CENTRO '.$nombreCentro,
						'style' => $rightColumnBold,
						'span' => 3
					));
					$columnaTotalCentroValor = new ReportRawColumn(array(
						'value' => $totalCentro,
						'style' => $rightColumn,
						'format' => $numberFormat,
						'span' => 1
					));
					$report->addRawRow(array(
						$columnaTotalCentro,
						$columnaTotalCentroValor
					));
				}
				$columnaTotalAlmacen = new ReportRawColumn(array(
					'value' => 'TOTAL ALMACÉN '.$nombreAlmacen,
					'style' => $rightColumnBold,
					'span' => 3
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
			$columnaTotal = new ReportRawColumn(array(
				'value' => 'TOTAL GENERAL',
				'style' => $rightColumnBold,
				'span' => 3
			));
			$columnaTotalValor = new ReportRawColumn(array(
				'value' => $totalGeneral,
				'style' => $rightColumn,
				'format' => $numberFormat,
				'span' => 1
			));
			$report->addRawRow(array(
				$columnaTotal,
				$columnaTotalValor
			));


			$report->finish();
			$fileName = $report->outputToFile('public/temp/consumos');

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
