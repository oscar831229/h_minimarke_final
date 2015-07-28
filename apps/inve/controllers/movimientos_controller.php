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
 * MovimientosController
 *
 * Movimientos de los Movimientos
 *
 */
class MovimientosController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrei();
		$fecha = new Date();
		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fecha->getMonth(), $fecha->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fecha->getMonth(), $fecha->getYear()));

		Tag::displayTo('centroFinal', $this->Centros->maximum('codigo','conditions: estado="A"'));
		Tag::displayTo('almacenFinal', $this->Almacenes->maximum('codigo'));

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('tipoMovimiento', array(
			'@' => 'TODOS',
			'E' => 'ENTRADAS',
			'C' => 'SALIDAS',
			'A' => 'AJUSTES',
			'T' => 'TRASLADOS',
			'R' => 'TRANSFORMACIONES'
		));
		$this->setParamToView('centros', $this->Centros->find(array('estado="A"', 'order' => 'codigo')));
		$this->setParamToView('almacenes', $this->Almacenes->find(array('order' => 'codigo')));

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try {
			$conditions = array();

			$almacenInicial = $this->getPostParam('almacenInicial', 'int');
			$almacenFinal = $this->getPostParam('almacenFinal', 'int');
			if ($almacenInicial != 0 && $almacenFinal != 0) {
				list($almacenInicial, $almacenFinal) = Utils::sortRange($almacenInicial, $almacenFinal);
				$conditions[] = "almacen>='$almacenInicial' AND almacen<='$almacenFinal'";
			}

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');
			if ($fechaInicial==''||$fechaFinal=='') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del listado'
				);
			}

			try {

				list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);
				$conditions[] = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";

				$numeroInicial = $this->getPostParam('numeroInicial', 'int');
				$numeroFinal = $this->getPostParam('numeroFinal', 'int');
				if ($numeroInicial!=0&&$numeroFinal!=0) {
					list($numeroInicial, $numeroFinal) = Utils::sortRange($numeroInicial, $numeroFinal);
					$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
				}
			}
			catch(DateException $e) {
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}

			$tipoMovimiento = $this->getPostParam('tipoMovimiento', 'onechar');
			if ($tipoMovimiento!='@') {
				if (!in_array($tipoMovimiento, array('A', 'E', 'C', 'T', 'R'))) {
					return array(
						'status' => 'FAILED',
						'message' => 'El tipo de movimiento no existe'
					);
				} else {
					$conditions[] = "comprob LIKE '$tipoMovimiento%'";
				}
			}

			$centroInicial = $this->getPostParam('centroInicial', 'int');
			$centroFinal = $this->getPostParam('centroFinal', 'int');
			if ($centroInicial!=0&&$centroFinal!=0) {
				list($centroInicial, $centroFinal) = Utils::sortRange($centroInicial, $centroFinal);
				$conditions[] = "centro_costo>='$centroInicial' AND centro_costo<='$centroFinal'";
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LISTADO DE MOVIMIENTOS', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Fechas: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
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

			$leftColumnBold = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Listado de Movimientos');
			$report->setColumnHeaders(array(
				'REFERENCIA',
				'UNDIDAD',
				'DESCRIPCIÓN',
				'CANTIDAD',
				'VALOR UNITARIO',
				'VALOR TOTAL',
				'VALOR IVA'				
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$columnaTotalDocumento = new ReportRawColumn(array(
				'value' => 'TOTAL MOVIMIENTO',
				'style' => $rightColumnBold,
				'span' => 4
			));

			$columnaTotalDocumentoTodos = new ReportRawColumn(array(
				'value' => 'TOTAL',
				'style' => $rightColumnBold,
				'span' => 4
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0, 1, 2), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(range(3, 6), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(range(3, 6), $numberFormat);

			$report->start(true);

			$totalDocumentoTodos = 0;
			$movimientos = $this->Movihead->find(join(' AND ', $conditions), 'order: almacen');
			foreach ($movimientos as $movihead) {

				$totalDocumento = 0;
				$tipoComprob = substr($movihead->getComprob(), 0, 1);
				if ($tipoComprob == 'E' || $tipoComprob == 'O') {
					$nit = BackCacher::getTercero($movihead->getNit());
					if ($nit == false) {
						$tercero = 'NO EXISTE EL TERCERO';
					} else {
						$tercero = $nit->getNombre();
					}
					if ($tipoComprob == 'E') {
						$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' Factura: '.$movihead->getFacturaC().
							' - ' . $tercero . ' | Orden Compra: '.$movihead->getNPedido();
					} else {
						$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' - '.$tercero;
					}
				} else {
					if ($tipoComprob=='C' || $tipoComprob=='T') {
						if ($movihead->getNPedido() > 0) {
							$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' | Pedido: '.$movihead->getNPedido();
						} else {
							$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' | Sin Pedido';
						}
					} else {
						$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero();
					}
				}
				$cabecera .= ' | Fecha: ' . $movihead->getFecha();

				if ($movihead->getNumeroComprobContab()) {
					$cabecera .= ' | C. Contable: ' . $movihead->getNumeroComprobContab();
				}

				if ($movihead->getObservaciones()) {
					$cabecera .= ' | Observaciones: ' . $movihead->getObservaciones();
				}

				$cabeceraDocumento = new ReportRawColumn(array(
					'value' => $cabecera,
					'style' => $leftColumnBold,
					'span' => 7
				));

				$report->addRawRow(array($cabeceraDocumento));
				$conditions = "comprob='{$movihead->getComprob()}' AND numero='{$movihead->getNumero()}' AND almacen='{$movihead->getAlmacen()}'";
				$detalle = $this->Movilin->find($conditions);
				foreach ($detalle as $movilin) {

					$item = BackCacher::getInve($movilin->getItem());
					if ($item == false) {
						$descripcion = 'NO EXISTE EL ITEM';
						$unidad = 'NO EXISTE LA UNIDAD';
					} else {
						$descripcion = $item->getDescripcion();
						$unidad = BackCacher::getUnidad($item->getUnidad());
						if ($unidad == false) {
							$unidad = 'NO EXISTE LA UNIDAD';
						} else {
							$unidad = $unidad->getNomUnidad();
						}
					}

					if ($movilin->getCantidad() == 0) {
						$valorUnitario = 0;
					} else {
						$valorUnitario = $movilin->getValor() / $movilin->getCantidad();
					}

					$row = array(
						$movilin->getItem(),
						$unidad,
						$descripcion,
						$movilin->getCantidad(),
						$valorUnitario,
						$movilin->getValor(),
						$movilin->getIva()
					);
					$report->addRow($row);
					$totalDocumento += $movilin->getValor();

					if ($tipoComprob == 'E') {
						$totalDocumentoTodos += $movilin->getValor();
					} else {
						if ($tipoComprob == 'C') {
							$totalDocumentoTodos -= $movilin->getValor();
						}
					}
				}

				$totales = array($columnaTotalDocumento);

				$totales[5] = new ReportRawColumn(array(
					'value' => $totalDocumento,
					'style' => $rightColumn,
					'format' => $numberFormat,
					'span' => 2
				));

				$totales[7] = new ReportRawColumn(array(
					'value' => '',
					'style' => $rightColumn,
					'span' => 1
				));
				$report->addRawRow($totales);
			}

			$totales = array($columnaTotalDocumentoTodos);

			$totales[5] = new ReportRawColumn(array(
				'value' => $totalDocumentoTodos,
				'style' => $rightColumn,
				'format' => $numberFormat,
				'span' => 2
			));

			$totales[7] = new ReportRawColumn(array(
				'value' => '',
				'style' => $rightColumn,
				'span' => 1
			));
			$report->addRawRow($totales);

			$report->finish();
			$fileName = $report->outputToFile('public/temp/movimientos');

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
