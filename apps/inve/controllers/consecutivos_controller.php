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
 * ConsecutivosController
 *
 * Consecutivos de los Movimientos
 *
 */
class ConsecutivosController extends ApplicationController
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
		Tag::displayTo('fechaFinal', $fecha->getDate());
		$fecha->diffMonths(1);
		Tag::displayTo('fechaInicial', $fecha->getDate());

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('tipoMovimiento', array('A' => 'AJUSTES', 'E' => 'ENTRADAS', 'C' => 'SALIDAS', 'T' => 'TRASLADOS'));

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');
		try
		{
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if ($fechaInicial == '' || $fechaFinal == '') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del listado'
				);
			}
			if (Date::isLater($fechaInicial, $fechaFinal)) {
				return array(
					'status' => 'FAILED',
					'message' => 'La fecha final debe ser posterior a la fecha inicial'
				);
			}

			$tipoMovimiento = $this->getPostParam('tipoMovimiento', 'onechar');
			if(!in_array($tipoMovimiento, array('A','E','C','T'))){
				return array(
					'status' => 'FAILED',
					'message' => 'El tipo de movimiento no existe'
				);
			}
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('CONTROL DE CONSECUTIVOS DE MOVIMIENTOS', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Fechas: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Control de Consecutivos');
			$report->setColumnHeaders(array(
				'COMPROB',
				'NUMERO',
				'FECHA',
				'NOMBRE',
				'VALOR',
				'IVA 10%',
				'IVA 16%',
				'IVA RETENIDO',
				'RETENCIÓN',
				'TOTAL',
				'FACTURA'
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0, 1, 2, 3), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(range(4, 9), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(range(4, 9), $numberFormat);

			$report->setTotalizeColumns(range(4, 9));

			$report->start(true);

			$movimientos = $this->Movihead->find("fecha BETWEEN '$fechaInicial' AND '$fechaFinal' AND comprob like '$tipoMovimiento%'",'order: almacen');

			foreach ($movimientos as $movihead) {

				$nit = BackCacher::getTercero($movihead->getNit());
				if ($nit == false) {
					$tercero = 'NO EXISTE EL TERCERO';
				} else {
					$tercero = $nit->getNombre();
				}

				$row = array(
					$movihead->getComprob(),
					$movihead->getNumero(),
					$movihead->getFecha(),
					$tercero,
					$movihead->getVTotal(),
					$movihead->getIvad(),
					$movihead->getIva(),
					$movihead->getDescuento(),
					$movihead->getRetencion(),
					$movihead->getSaldo(),
					$movihead->getFacturaC()
				);
				$report->addRow($row);
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/consecutivos');

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
