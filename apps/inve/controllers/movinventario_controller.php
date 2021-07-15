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
class MovinventarioController extends ApplicationController
{

	private $movimientos = array(
		'E' => 'ENTRADAS',
		'C' => 'SALIDAS',
		// 'A' => 'AJUSTES',
		'T' => 'TRASLADOS',
		//'R' => 'TRANSFORMACIONES'
	);

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
		Tag::displayTo('almacenFinal', $this->Almacenes->maximum('codigo'));

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('tipoMovimiento', $this->movimientos);
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
				$conditions[] = "ml.almacen>='$almacenInicial' AND ml.almacen<='$almacenFinal'";
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
				$conditions[] = "ml.fecha>='$fechaInicial' AND ml.fecha<='$fechaFinal'";
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
					$conditions[] = "ml.comprob LIKE '$tipoMovimiento%'";
				}

				$detalle_movimiento = $this->movimientos[$tipoMovimiento];

			}else{
				return array(
					'status' => 'FAILED',
					'message' => 'Tipo de movimento sin definir.'
				);
			}

			$sql = "
				SELECT 
					res.*,
					(
						SELECT valor / cantidad
						FROM movilin 
						WHERE 	item = res.item 
							AND almacen = res.almacen
						ORDER BY fecha DESC LIMIT 1
					) AS precio_ult_compra,
					(
						SELECT MAX(fecha)
						FROM movilin 
						WHERE 	item = res.item 
							AND almacen = res.almacen
					) AS fecha_ult_compra
				FROM (
					SELECT 
						ml.almacen, 
						a.nom_almacen, 
						ml.item, 
						i.descripcion, 
						i.linea,
						i.unidad,
						u.nom_unidad,
						l.nombre AS nombre_linea, 
						SUM(ml.cantidad) AS cantidad, 
						SUM(ml.valor) AS valor
					FROM movilin ml
						LEFT JOIN inve i ON i.item = ml.item
						LEFT JOIN lineas l ON l.linea = i.linea AND ml.almacen = l.almacen
						LEFT JOIN almacenes a ON a.codigo = ml.almacen
						LEFT JOIN unidad u ON u.codigo = i.unidad
					WHERE ".join(' AND ', $conditions)."
					GROUP BY ml.almacen, a.nom_almacen, ml.item, i.descripcion, i.linea, l.nombre
					ORDER BY ml.almacen, i.linea, ml.item
				)AS res
			";

			$db = DbBase::rawConnect();

			$registros = $db->inQueryAssoc($sql);

			# PARAMETRIZAR ESTILOS REPORTE
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('REPORTE DE MOVIMIENTOS POR REFERENCIA - '.strtoupper($detalle_movimiento), array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Rango de fechas: '.$fechaInicial.' - '.$fechaFinal, array(
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


			$StyleAlmacen = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'backgroundColor' => '#D6DBDF'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Movimientos por referencia');
			$report->setColumnHeaders(array(
				'REFERENCIA',
				'DESCRIPCIÓN',
				'UNDIDAD',
				'CANTIDAD',
				'VALOR TOTAL',
				'COSTO PROM.',
				'PRECIO ULT. COMPRA',
				'FECHA ULT. COMPRA',
				'DIF. VS ULT. COMPRA'			
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

			$almacen = [];

			foreach ($registros as $key => $value) {

				$value = (object) $value;

				#CREAR CABECERA ALMACEN
				if(!isset($almacen[$value->almacen])){

					$cab_almacen = new ReportRawColumn(array(
						'value' => $value->almacen.' - '.$value->nom_almacen,
						'style' => $StyleAlmacen,
						'span' => 9
					));

					$report->addRawRow(array($cab_almacen));

				}

				#CREAR CABECERA LINEA
				if(!isset($almacen[$value->almacen][$value->linea])){

					$cab_linea = new ReportRawColumn(array(
						'value' => $value->linea.' - '.$value->nombre_linea,
						'style' => $leftColumnBold,
						'span' => 9
					));

					$almacen[$value->almacen][$value->linea] = true;

					$report->addRawRow(array($cab_linea));

				}

				$valor_promedio = round(($value->valor / $value->cantidad),2);
				$diferencia_costo = $value->precio_ult_compra - $valor_promedio;

				$datos = [
					$value->item,
					$value->descripcion,
					$value->nom_unidad,
					$value->cantidad,
					$value->valor,
					$valor_promedio,
					$value->precio_ult_compra,
					$value->fecha_ult_compra,
					round($diferencia_costo,2)
				];

				$report->addRow($datos);


			}

			# Generar el reporte
			$report->finish();
			$fileName = $report->outputToFile('public/temp/movimientos');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);

			// $totalDocumentoTodos = 0;
			// $movimientos = $this->Movihead->find(join(' AND ', $conditions), 'order: almacen');
			// foreach ($movimientos as $movihead) {

			// 	$totalDocumento = 0;
			// 	$tipoComprob = substr($movihead->getComprob(), 0, 1);
			// 	if ($tipoComprob == 'E' || $tipoComprob == 'O') {
			// 		$nit = BackCacher::getTercero($movihead->getNit());
			// 		if ($nit == false) {
			// 			$tercero = 'NO EXISTE EL TERCERO';
			// 		} else {
			// 			$tercero = $nit->getNombre();
			// 		}
			// 		if ($tipoComprob == 'E') {
			// 			$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' Factura: '.$movihead->getFacturaC().
			// 				' - ' . $tercero . ' | Orden Compra: '.$movihead->getNPedido();
			// 		} else {
			// 			$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' - '.$tercero;
			// 		}
			// 	} else {
			// 		if ($tipoComprob=='C' || $tipoComprob=='T') {
			// 			if ($movihead->getNPedido() > 0) {
			// 				$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' | Pedido: '.$movihead->getNPedido();
			// 			} else {
			// 				$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero().' | Sin Pedido';
			// 			}
			// 		} else {
			// 			$cabecera = $movihead->getComprob().' No. '.$movihead->getNumero();
			// 		}
			// 	}
			// 	$cabecera .= ' | Fecha: ' . $movihead->getFecha();

			// 	if ($movihead->getNumeroComprobContab()) {
			// 		$cabecera .= ' | C. Contable: ' . $movihead->getNumeroComprobContab();
			// 	}

			// 	if ($movihead->getObservaciones()) {
			// 		$cabecera .= ' | Observaciones: ' . $movihead->getObservaciones();
			// 	}

			// 	$cabeceraDocumento = new ReportRawColumn(array(
			// 		'value' => $cabecera,
			// 		'style' => $leftColumnBold,
			// 		'span' => 7
			// 	));

			// 	$report->addRawRow(array($cabeceraDocumento));
			// 	$conditions = "comprob='{$movihead->getComprob()}' AND numero='{$movihead->getNumero()}' AND almacen='{$movihead->getAlmacen()}'";
			// 	$detalle = $this->Movilin->find($conditions);
			// 	foreach ($detalle as $movilin) {

			// 		$item = BackCacher::getInve($movilin->getItem());
			// 		if ($item == false) {
			// 			$descripcion = 'NO EXISTE EL ITEM';
			// 			$unidad = 'NO EXISTE LA UNIDAD';
			// 		} else {
			// 			$descripcion = $item->getDescripcion();
			// 			$unidad = BackCacher::getUnidad($item->getUnidad());
			// 			if ($unidad == false) {
			// 				$unidad = 'NO EXISTE LA UNIDAD';
			// 			} else {
			// 				$unidad = $unidad->getNomUnidad();
			// 			}
			// 		}

			// 		if ($movilin->getCantidad() == 0) {
			// 			$valorUnitario = 0;
			// 		} else {
			// 			$valorUnitario = $movilin->getValor() / $movilin->getCantidad();
			// 		}

			// 		$row = array(
			// 			$movilin->getItem(),
			// 			$unidad,
			// 			$descripcion,
			// 			$movilin->getCantidad(),
			// 			$valorUnitario,
			// 			$movilin->getValor(),
			// 			$movilin->getIva()
			// 		);
			// 		$report->addRow($row);
			// 		$totalDocumento += $movilin->getValor();

			// 		if ($tipoComprob == 'E') {
			// 			$totalDocumentoTodos += $movilin->getValor();
			// 		} else {
			// 			if ($tipoComprob == 'C') {
			// 				$totalDocumentoTodos -= $movilin->getValor();
			// 			}
			// 		}
			// 	}

			// 	$totales = array($columnaTotalDocumento);

			// 	$totales[5] = new ReportRawColumn(array(
			// 		'value' => $totalDocumento,
			// 		'style' => $rightColumn,
			// 		'format' => $numberFormat,
			// 		'span' => 2
			// 	));

			// 	$totales[7] = new ReportRawColumn(array(
			// 		'value' => '',
			// 		'style' => $rightColumn,
			// 		'span' => 1
			// 	));
			// 	$report->addRawRow($totales);
			// }

			// $totales = array($columnaTotalDocumentoTodos);

			// $totales[5] = new ReportRawColumn(array(
			// 	'value' => $totalDocumentoTodos,
			// 	'style' => $rightColumn,
			// 	'format' => $numberFormat,
			// 	'span' => 2
			// ));

			// $totales[7] = new ReportRawColumn(array(
			// 	'value' => '',
			// 	'style' => $rightColumn,
			// 	'span' => 1
			// ));
			// $report->addRawRow($totales);

			// $report->finish();
			// $fileName = $report->outputToFile('public/temp/movimientos');

			// return array(
			// 	'status' => 'OK',
			// 	'file' => 'temp/'.$fileName
			// );
		}
		catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
