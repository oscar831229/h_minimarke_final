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
class AnalisiscomprasController extends ApplicationController
{

	private $movimientos = array(
		'E' => 'ENTRADAS',
		'C' => 'SALIDAS',
		'T' => 'TRASLADOS',
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
				$conditions[] = "movihead.almacen>='$almacenInicial' AND movihead.almacen<='$almacenFinal'";
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
				$conditions[] = "movihead.fecha>='$fechaInicial' AND movihead.fecha<='$fechaFinal'";
			}
			catch(DateException $e) {
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}

			$tipoMovimiento = 'E';
			if ($tipoMovimiento!='@') {
				if (!in_array($tipoMovimiento, array('A', 'E', 'C', 'T', 'R'))) {
					return array(
						'status' => 'FAILED',
						'message' => 'El tipo de movimiento no existe'
					);
				} else {
					$conditions[] = "movihead.comprob LIKE '$tipoMovimiento%'";
				}

				$detalle_movimiento = $this->movimientos[$tipoMovimiento];

			}else{
				return array(
					'status' => 'FAILED',
					'message' => 'Tipo de movimento sin definir.'
				);
			}

			$sql = "SELECT 	final.*, 
						CAST((final.total_saldo/final.cantidad_saldo) AS DECIMAL(20,2)) AS costo_saldo 
					FROM (
						SELECT
							temp.item,
							temp.item_name,
							temp.nom_unidad,
							CAST((temp.valor/temp.cantidad) AS DECIMAL(20,2)) AS valor,
							temp.cantidad,
							temp.valor AS total,
							temp.nit,
							temp.nombre,
							(SELECT SUM(saldo) FROM saldos WHERE item = temp.item  AND ano_mes = 0) AS cantidad_saldo,
							(SELECT SUM(costo) FROM saldos WHERE item = temp.item  AND ano_mes = 0) AS total_saldo
						FROM (
							SELECT
								movilin.item,
								inve.descripcion AS item_name,
								unidad.nom_unidad,
								SUM(movilin.cantidad) AS cantidad,
								SUM(movilin.valor) AS valor,
								movihead.nit,
								nits.nombre
							FROM movihead
							INNER JOIN movilin ON movilin.comprob = movihead.comprob AND movilin.numero = movihead.numero
							INNER JOIN inve ON inve.item = movilin.item
							LEFT JOIN nits ON nits.nit = movihead.nit
							LEFT JOIN unidad ON unidad.codigo = inve.unidad 
							WHERE 
								movihead.almacen>=1 AND movihead.almacen<=7
								AND movihead.comprob LIKE 'E%'
								AND movihead.fecha>='2021-08-01' AND movihead.fecha<='2021-08-30' 
							GROUP  BY movihead.nit, nits.nombre, movilin.item, inve.descripcion
						) AS temp 
					) AS final
					ORDER BY final.item, final.valor";
			
			$db = DbBase::rawConnect();

			$registros = $db->inQueryAssoc($sql);

			# PARAMETRIZAR ESTILOS REPORTE
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('REPORTE DE ANALISIS DE COMPRAS - '.strtoupper($detalle_movimiento), array(
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
				'fontSize' => 10,
				'fontWeight' => 'bold'
			));


			$StyleAlmacen = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'backgroundColor' => '#D6DBDF'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Analisis compras');
			$report->setColumnHeaders(array(
				'ID',
				'PRODUCTO',
				'CANTIDAD ACTUAL',
				'VALOR TOTAL',
				'COSTO PROMEDIO',
				'NIT',
				'PROVEEDOR',
				'UNIDAD',
				'CANTIDAD',
				'VALOR',
				'TOTAL'			
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->start(true);

			$almacen = [];
			$columns = 8;
			$items = [];
			foreach ($registros as $key => $value) {

				$value = (object) $value;

				#CREAR CABECERA ALMACEN
				if(!isset($items[$value->item])){

					$item = new ReportRawColumn(array(
						'value' => $value->item,
						'style' => $StyleAlmacen
					));

					$item_name = new ReportRawColumn(array(
						'value' => $value->item_name,
						'style' => $StyleAlmacen
					));

					$cantidad_saldo = new ReportRawColumn(array(
						'value' => $value->cantidad_saldo,
						'style' => $StyleAlmacen
					));

					$total_saldo = new ReportRawColumn(array(
						'value' => $value->total_saldo,
						'style' => $StyleAlmacen
					));

					$costo_saldo = new ReportRawColumn(array(
						'value' => $value->costo_saldo,
						'style' => $StyleAlmacen
					));

					$vacio = new ReportRawColumn(array(
						'value' => '',
						'style' => $StyleAlmacen
					));

					$report->addRawRow(array(
						$item, 
						$item_name, 
						$cantidad_saldo, 
						$total_saldo, 
						$costo_saldo,
						$vacio,
						$vacio,
						$vacio,
						$vacio,
						$vacio,
						$vacio,
					));

					$items[$value->item] = true;

				}

				$datos = [
					$value->item,
					'',
					'',
					'',
					'',
					$value->nit,
					$value->nombre,
					$value->nom_unidad,
					$value->cantidad,
					$value->valor,
					$value->total
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

			
		}
		catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
